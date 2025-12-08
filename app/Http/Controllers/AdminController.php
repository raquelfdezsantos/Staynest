<?php


/**
 * Controlador del área de administración.
 * Gestiona reservas, propiedades, fotos, calendario y precios.
 */

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\RateCalendar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\CarbonPeriod;
use Carbon\Carbon;
use App\Models\Payment;
use App\Models\Invoice;
use Illuminate\Support\Str;
use App\Models\Property;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Models\Photo;
use App\Mail\ReservationCancelledMail;
use App\Mail\PaymentRefundIssuedMail;
use App\Mail\AdminPaymentRefundIssuedMail;
use App\Mail\ReservationUpdatedMail;
use App\Mail\PropertyDeletedConfirmationMail;

class AdminController extends Controller
{
    /**
     * Muestra el dashboard del administrador con el listado de reservas.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $adminId = Auth::id();
        
        $query = Reservation::with(['user', 'property', 'invoice', 'invoices'])
            ->whereHas('property', function($q) use ($adminId) {
                $q->where('user_id', $adminId);
            })
            ->latest();

        if ($status = $request->string('status')->toString()) {
            $query->where('status', $status);
        }

        if ($from = $request->date('from')) {
            $query->whereDate('check_in', '>=', $from);
        }
        if ($to = $request->date('to')) {
            $query->whereDate('check_out', '<=', $to);
        }

        $reservations = $query->paginate(10)->withQueryString();

        // Estadísticas para el dashboard
        $stats = [
            // Reservas activas (pending + paid)
            'activeReservations' => Reservation::whereIn('status', ['pending', 'paid'])
                ->whereHas('property', function($q) use ($adminId) {
                    $q->where('user_id', $adminId);
                })
                ->count(),
            
            // Ingresos totales (solo reservas pagadas)
            'totalRevenue' => Reservation::where('status', 'paid')
                ->whereHas('property', function($q) use ($adminId) {
                    $q->where('user_id', $adminId);
                })
                ->sum('total_price'),
            
            // Ocupación del mes actual (%)
            'occupancyRate' => $this->calculateOccupancyRate(),
            
            // Próximas 5 reservas
            'upcomingReservations' => Reservation::with(['user', 'property'])
                ->whereIn('status', ['pending', 'paid'])
                ->whereHas('property', function($q) use ($adminId) {
                    $q->where('user_id', $adminId);
                })
                ->where('check_in', '>=', now())
                ->orderBy('check_in')
                ->limit(5)
                ->get(),
        ];

        return view('admin.dashboard', compact('reservations', 'stats'));
    }

    /**
     * Muestra el dashboard filtrado por una propiedad específica.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Property $property
     * @return \Illuminate\Contracts\View\View
     */
    public function propertyDashboardFiltered(Request $request, Property $property)
    {
        // Verificar que la propiedad pertenece al admin
        if ($property->user_id !== Auth::id()) {
            abort(403, 'No autorizado');
        }

        $query = Reservation::with(['user', 'property', 'invoice', 'invoices'])
            ->where('property_id', $property->id)
            ->latest();

        if ($status = $request->string('status')->toString()) {
            $query->where('status', $status);
        }

        if ($from = $request->date('from')) {
            $query->whereDate('check_in', '>=', $from);
        }
        if ($to = $request->date('to')) {
            $query->whereDate('check_out', '<=', $to);
        }

        $reservations = $query->paginate(10)->withQueryString();

        // Estadísticas para el dashboard SOLO de esta propiedad
        $stats = [
            // Reservas activas (pending + paid)
            'activeReservations' => Reservation::whereIn('status', ['pending', 'paid'])
                ->where('property_id', $property->id)
                ->count(),
            
            // Ingresos totales (solo reservas pagadas)
            'totalRevenue' => Reservation::where('status', 'paid')
                ->where('property_id', $property->id)
                ->sum('total_price'),
            
            // Ocupación del mes actual (%)
            'occupancyRate' => $this->calculateOccupancyRateForProperty($property->id),
            
            // Próximas 5 reservas
            'upcomingReservations' => Reservation::with(['user', 'property'])
                ->whereIn('status', ['pending', 'paid'])
                ->where('property_id', $property->id)
                ->where('check_in', '>=', now())
                ->orderBy('check_in')
                ->limit(5)
                ->get(),
        ];

        return view('admin.dashboard', compact('reservations', 'stats', 'property'));
    }

    /**
     * Calcula el porcentaje de ocupación del mes actual.
     *
     * @return float
     */
    private function calculateOccupancyRate(): float
    {
        $adminId = Auth::id();
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();
        $daysInMonth = $startOfMonth->daysInMonth;

        // Contar noches reservadas en el mes (status = paid o pending)
        $bookedNights = Reservation::whereIn('status', ['pending', 'paid'])
            ->whereHas('property', function($q) use ($adminId) {
                $q->where('user_id', $adminId);
            })
            ->where(function ($q) use ($startOfMonth, $endOfMonth) {
                $q->whereBetween('check_in', [$startOfMonth, $endOfMonth])
                  ->orWhereBetween('check_out', [$startOfMonth, $endOfMonth])
                  ->orWhere(function ($q2) use ($startOfMonth, $endOfMonth) {
                      $q2->where('check_in', '<=', $startOfMonth)
                         ->where('check_out', '>=', $endOfMonth);
                  });
            })
            ->get()
            ->sum(function ($reservation) use ($startOfMonth, $endOfMonth) {
                $checkIn = $reservation->check_in->max($startOfMonth);
                $checkOut = $reservation->check_out->min($endOfMonth);
                return $checkIn->diffInDays($checkOut);
            });

        return $daysInMonth > 0 ? round(($bookedNights / $daysInMonth) * 100, 1) : 0;
    }

    /**
     * Calcula el porcentaje de ocupación del mes actual para una propiedad específica.
     *
     * @param int $propertyId
     * @return float
     */
    private function calculateOccupancyRateForProperty(int $propertyId): float
    {
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();
        $daysInMonth = $startOfMonth->daysInMonth;

        // Contar noches reservadas en el mes (status = paid o pending)
        $bookedNights = Reservation::whereIn('status', ['pending', 'paid'])
            ->where('property_id', $propertyId)
            ->where(function ($q) use ($startOfMonth, $endOfMonth) {
                $q->whereBetween('check_in', [$startOfMonth, $endOfMonth])
                  ->orWhereBetween('check_out', [$startOfMonth, $endOfMonth])
                  ->orWhere(function ($q2) use ($startOfMonth, $endOfMonth) {
                      $q2->where('check_in', '<=', $startOfMonth)
                         ->where('check_out', '>=', $endOfMonth);
                  });
            })
            ->get()
            ->sum(function ($reservation) use ($startOfMonth, $endOfMonth) {
                $checkIn = $reservation->check_in->max($startOfMonth);
                $checkOut = $reservation->check_out->min($endOfMonth);
                return $checkIn->diffInDays($checkOut);
            });

        return $daysInMonth > 0 ? round(($bookedNights / $daysInMonth) * 100, 1) : 0;
    }

    /**
     * Cancela una reserva pendiente y repone las noches al calendario.
     *
     * @param int $reservationId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancel(int $reservationId)
    {
        $adminId = Auth::id();
        
        $reservation = Reservation::query()
            ->where('id', $reservationId)
            ->with('property', 'user')
            ->whereHas('property', function($q) use ($adminId) {
                $q->where('user_id', $adminId);
            })
            ->firstOrFail();

        // Solo cancelamos si está pendiente o pagada
        if (!in_array($reservation->status, ['pending', 'paid'])) {
            return back()->with('error', 'Solo es posible cancelar reservas pendientes o pagadas.');
        }

        $invoice = null;
        $wasPaid = $reservation->status === 'paid';

        DB::transaction(function () use ($reservation, &$invoice, $wasPaid) {
            // 1) Actualiza estado de la reserva
            $reservation->update(['status' => 'cancelled']);

            // 2) Restaura disponibilidad en RateCalendar
            $start = $reservation->check_in->copy();
            $end   = $reservation->check_out->copy();

            // Reponemos cada día del rango [check_in, check_out)
            for ($date = $start->copy(); $date->lt($end); $date->addDay()) {
                RateCalendar::where('property_id', $reservation->property_id)
                    ->where('date', $date->toDateString())
                    ->update(['is_available' => true, 'blocked_by' => null]);
            }

            // 3) Si estaba pagada, crear factura rectificativa
            if ($wasPaid) {
                $lastInvoice = Invoice::where('reservation_id', $reservation->id)
                    ->where('type', 'invoice')
                    ->latest()
                    ->first();

                $invoice = Invoice::create([
                    'number' => Invoice::generateUniqueNumber('RECT'),
                    'user_id' => $reservation->user_id,
                    'reservation_id' => $reservation->id,
                    'property_id' => $reservation->property_id,
                    'type' => 'rectificative',
                    'amount' => -$reservation->total_price,
                    'issued_at' => now(),
                    'date' => now(),
                    'context' => 'admin_cancellation',
                    'details' => [
                        'original_invoice' => $lastInvoice?->number,
                        'reason' => 'Cancelación por administrador',
                        'check_in' => $reservation->check_in->toDateString(),
                        'check_out' => $reservation->check_out->toDateString(),
                        'guests' => $reservation->guests,
                        'adults' => $reservation->adults,
                        'children' => $reservation->children,
                        'pets' => $reservation->pets,
                    ],
                ]);
                
                // Cargar relaciones necesarias para el PDF
                $invoice->load(['reservation.user', 'reservation.property']);
            }
        });

        // Notificaciones de cancelación (cliente y admin)
        Log::info('Intentando enviar ReservationCancelledMail al cliente', ['email' => $reservation->user->email]);
        try {
            Mail::to($reservation->user->email)->send(new ReservationCancelledMail($reservation, false, $invoice));
            Log::info('ReservationCancelledMail enviado al cliente', ['email' => $reservation->user->email]);
        } catch (\Throwable $e) {
            Log::error('Fallo ReservationCancelledMail cliente', ['msg' => $e->getMessage()]);
            report($e);
        }
        Log::info('Intentando enviar ReservationCancelledMail al admin', ['email' => $reservation->property->user->email]);
        try {
            Mail::to($reservation->property->user->email)->send(new ReservationCancelledMail($reservation, true, $invoice));
            Log::info('ReservationCancelledMail enviado al admin', ['email' => $reservation->property->user->email]);
        } catch (\Throwable $e) {
            Log::error('Fallo ReservationCancelledMail admin', ['msg' => $e->getMessage()]);
            report($e);
        }

        return back()->with('success', 'Reserva cancelada, noches repuestas y notificada.');
    }



    /**
     * Genera un array de fechas entre dos días (excluyendo el último).
     *
     * @param string $from
     * @param string $to
     * @return array
     */
    private function rangeDates(string $from, string $to): array
    {
        $period = CarbonPeriod::create($from, $to)->excludeEndDate();
        return collect($period)->map(function($d) {
            return is_object($d) && method_exists($d, 'toDateString') ? $d->toDateString() : (string) $d;
        })->all();
    }

    /**
     * Establece la disponibilidad de un conjunto de fechas para una propiedad.
     *
     * @param int $propertyId
     * @param array $dates
     * @param bool $available
     * @return void
     */
    private function setAvailability(int $propertyId, array $dates, bool $available): void
    {
        if (empty($dates)) return;
        RateCalendar::where('property_id', $propertyId)
            ->whereIn('date', $dates)
            ->update(['is_available' => $available]);
    }

    /**
     * Muestra el formulario de edición de una reserva para el admin.
     *
     * @param int $id
     * @return \Illuminate\Contracts\View\View
     */
    public function edit(int $id)
    {
        $adminId = Auth::id();
        
        $reservation = Reservation::with('property', 'user')
            ->whereHas('property', function($q) use ($adminId) {
                $q->where('user_id', $adminId);
            })
            ->findOrFail($id);
        return view('admin.reservations.edit', compact('reservation')); // crea vista simple
    }

    /**
     * Muestra la vista de detalles de una reserva para el admin.
     *
     * @param \App\Models\Reservation $reservation
     * @return \Illuminate\Contracts\View\View
     */
    public function show(Reservation $reservation)
    {
        $adminId = Auth::id();
        
        // Verificar que la reserva pertenece a una propiedad del admin
        if ($reservation->property->user_id !== $adminId) {
            abort(403, 'No autorizado');
        }
        
        $reservation->loadMissing('property', 'user');
        return view('admin.reservations.show', compact('reservation'));
    }

    /**
     * Actualiza una reserva (solo estados pending/paid) y notifica cambios.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, int $id)
    {
        $adminId = Auth::id();
        
        $reservation = Reservation::with('property')
            ->whereHas('property', function($q) use ($adminId) {
                $q->where('user_id', $adminId);
            })
            ->findOrFail($id);

        $data = $request->validate([
            'check_in'  => ['required', 'date'],
            'check_out' => ['required', 'date', 'after:check_in'],
            'guests'    => ['required', 'integer', 'min:1'],
        ]);

        $property = $reservation->property;
        if ((int)$data['guests'] > (int)$property->capacity) {
            return back()->withErrors(['guests' => "Máximo {$property->capacity} huéspedes."]);
        }

        $oldDates = $this->rangeDates($reservation->check_in->toDateString(), $reservation->check_out->toDateString());
        $newDates = $this->rangeDates($data['check_in'], $data['check_out']);

        // Solapes con otras reservas
        $overlap = Reservation::where('property_id', $property->id)
            ->where('id', '!=', $reservation->id)
            ->whereNotIn('status', ['cancelled'])
            ->where(function ($q) use ($data) {
                $q->where('check_in', '<', $data['check_out'])
                    ->where('check_out', '>', $data['check_in']);
            })
            ->exists();
        if ($overlap) {
            return back()->withErrors(['check_in' => 'Solapa con otra reserva.']);
        }

        $rates = RateCalendar::where('property_id', $property->id)
            ->where(function($q) use ($newDates) {
                foreach ($newDates as $d) {
                    $q->orWhere('date', $d);
                }
            })
            ->get()
            ->keyBy(fn($r) => is_object($r->date) && method_exists($r->date, 'toDateString') ? $r->date->toDateString() : (string) $r->date);

        foreach ($newDates as $d) {
            $rate = $rates->get($d);
            if (!$rate || (!$rate->is_available && !in_array($d, $oldDates, true))) {
                return back()->withErrors(['check_in' => "No hay disponibilidad el día $d."]);
            }
        }

        $newTotal = $rates->sum('price') * (int)$data['guests'];

        DB::transaction(function () use ($reservation, $property, $oldDates, $newDates, $newTotal, $data) {
            $this->setAvailability($property->id, $oldDates, true);
            $this->setAvailability($property->id, $newDates, false);

            $reservation->update([
                'check_in'    => $data['check_in'],
                'check_out'   => $data['check_out'],
                'guests'      => $data['guests'],
                'total_price' => $newTotal,
            ]);
        });

        // Notificaciones por email (cliente y admin)
        Log::info('Intentando enviar ReservationUpdatedMail al cliente', ['email' => $reservation->user->email]);
        try {
            Mail::to($reservation->user->email)->send(new ReservationUpdatedMail($reservation));
            Log::info('ReservationUpdatedMail enviado al cliente', ['email' => $reservation->user->email]);
        } catch (\Throwable $e) {
            Log::error('Fallo ReservationUpdatedMail cliente', ['msg' => $e->getMessage()]);
            report($e);
        }
        Log::info('Intentando enviar ReservationUpdatedMail al admin', ['email' => $reservation->property->user->email]);
        try {
            Mail::to($reservation->property->user->email)->send(new ReservationUpdatedMail($reservation, 0, 0, true));
            Log::info('ReservationUpdatedMail enviado al admin', ['email' => $reservation->property->user->email]);
        } catch (\Throwable $e) {
            Log::error('Fallo ReservationUpdatedMail admin', ['msg' => $e->getMessage()]);
            report($e);
        }

        // Si hay diferencia a devolver, simular refund y notificar
        $paid = method_exists($reservation, 'paidAmount') ? $reservation->paidAmount() : 0;
        $diff = $reservation->total_price - $paid;
        if ($diff < 0) {
            $refund = abs($diff);
            DB::transaction(function () use ($reservation, $refund) {
                Payment::create([
                    'reservation_id' => $reservation->id,
                    'amount'        => -$refund,
                    'method'        => 'simulated',
                    'status'        => 'refunded',
                    'provider_ref'  => 'SIM-REF-' . Str::upper(Str::random(6)),
                ]);
            });
            try {
                Mail::to($reservation->user->email)->send(new PaymentRefundIssuedMail($reservation, $refund));
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return redirect()->route('admin.dashboard')->with('success', 'Reserva actualizada y notificada.');
    }


    /**
     * Procesa el reembolso de una reserva pagada, cancela la reserva y genera factura rectificativa.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function refund(int $id)
    {
        $adminId = Auth::id();
        
        $reservation = Reservation::with('property')
            ->whereHas('property', function($q) use ($adminId) {
                $q->where('user_id', $adminId);
            })
            ->findOrFail($id);

        if ($reservation->status !== 'paid') {
            return back()->with('error', 'Solo reservas pagadas pueden reembolsarse.');
        }

        $refund = $reservation->total_price;
        $refundInvoice = DB::transaction(function () use ($reservation, $refund) {
            // 1) Cancelar y reponer noches
            $reservation->update(['status' => 'cancelled']);

            for ($d = $reservation->check_in->copy(); $d->lt($reservation->check_out); $d->addDay()) {
                RateCalendar::where('property_id', $reservation->property_id)
                    ->where('date', $d->toDateString())
                    ->update(['is_available' => true, 'blocked_by' => null]);
            }

            // 2) Registrar “reembolso” simulado
            Payment::create([
                'reservation_id' => $reservation->id,
                'amount'         => $refund, // total reembolsado
                'method'         => 'simulated',
                'status'         => 'refunded',
                'provider_ref'   => 'REF-' . Str::upper(Str::random(8)),
            ]);

            // 3) Generar factura rectificativa
            $invoiceNumber = Invoice::generateUniqueNumber('RECT');
            
            return Invoice::create([
                'reservation_id' => $reservation->id,
                'number' => $invoiceNumber,
                'pdf_path' => null,
                'issued_at' => now(),
                'amount' => -$refund, // Negativo para reembolso
            ]);
        });

        // Notificaciones de cancelación y reembolso (cliente y admin)
        try {
            Mail::to($reservation->user->email)->send(new ReservationCancelledMail($reservation, false, $refundInvoice));
        } catch (\Throwable $e) {
            report($e);
        }
        try {
            Mail::to($reservation->property->user->email)->send(new ReservationCancelledMail($reservation, true, $refundInvoice));
        } catch (\Throwable $e) {
            report($e);
        }
        try {
            Mail::to($reservation->user->email)->send(new PaymentRefundIssuedMail($reservation, $refund, $refundInvoice));
        } catch (\Throwable $e) {
            report($e);
        }
        try {
            Mail::to(env('MAIL_ADMIN', 'admin@vut.test'))->send(new AdminPaymentRefundIssuedMail($reservation, $refund, $refundInvoice));
        } catch (\Throwable $e) {
            report($e);
        }

        return back()->with('success', 'Reembolso procesado, factura rectificativa generada y notificaciones enviadas.');
    }


    /**
     * Bloquea noches en el calendario de una propiedad.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Property $property
     * @return \Illuminate\Http\RedirectResponse
     */
    public function blockDates(Request $request, Property $property)
    {
        $data = $request->validate([
            'start'       => ['required', 'date'],
            'end'         => ['required', 'date', 'after_or_equal:start'], // end INCLUSIVO
        ]);

        // Verificar que la propiedad pertenece al admin
        if ($property->user_id !== Auth::id()) {
            abort(403, 'No autorizado');
        }

        $prop = $property;
        $start  = Carbon::parse($data['start'])->startOfDay();
        $end    = Carbon::parse($data['end'])->startOfDay(); // rango [start, end] INCLUSIVO

        // 1) No permitir bloquear si hay reservas (pending/paid) que solapen
        $overlap = Reservation::where('property_id', $prop->id)
            ->whereIn('status', ['pending', 'paid'])
            ->where('check_in', '<=', $end->copy()->addDay())   // Ajuste para rango inclusivo
            ->where('check_out', '>', $start)
            ->exists();

        if ($overlap) {
            return back()->with('error', 'No se puede bloquear: existen reservas que solapan el rango.');
        }

        // 2) Marcar is_available=false día a día (rango INCLUSIVO)
        DB::transaction(function () use ($prop, $start, $end) {
            for ($d = $start->clone(); $d->lte($end); $d->addDay()) { // lte = INCLUSIVO
                RateCalendar::updateOrCreate(
                    ['property_id' => $prop->id, 'date' => $d->toDateString()],
                    // Conserva price/min_stay si existe; si no, pon defaults
                    ['is_available' => false, 'blocked_by' => 'admin'] + (function () use ($prop, $d) {
                        $rc = RateCalendar::where('property_id', $prop->id)
                            ->where('date', $d->toDateString())->first();
                        return $rc ? [] : ['price' => 100, 'min_stay' => 2]; // defaults simples
                    })()
                );
            }
        });

        return back()->with('success', 'Noches bloqueadas correctamente.');
    }

    /**
     * Desbloquea noches en el calendario de una propiedad.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Property $property
     * @return \Illuminate\Http\RedirectResponse
     */
    public function unblockDates(Request $request, Property $property)
    {
        $data = $request->validate([
            'start'       => ['required', 'date'],
            'end'         => ['required', 'date', 'after_or_equal:start'], // end INCLUSIVO
        ]);

        // Verificar que la propiedad pertenece al admin
        if ($property->user_id !== Auth::id()) {
            abort(403, 'No autorizado');
        }

        $prop = $property;
        $start = Carbon::parse($data['start'])->startOfDay();
        $end   = Carbon::parse($data['end'])->startOfDay();

        DB::transaction(function () use ($prop, $start, $end) {
            for ($d = $start->clone(); $d->lte($end); $d->addDay()) { // lte = INCLUSIVO
                RateCalendar::where('property_id', $prop->id)
                    ->where('date', $d->toDateString())
                    ->update(['is_available' => true, 'blocked_by' => null]);
                // si no existe fila, no hace nada (queda disponible por ausencia)
            }
        }); 

        return back()->with('success', 'Noches desbloqueadas correctamente.');
    }

    /**
     * Elimina (soft delete) una propiedad y cancela sus reservas futuras.
     *
     * @param \App\Models\Property $property
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyProperty(Property $property)
    {
        // Verificar que la propiedad pertenece al admin
        if ($property->user_id !== Auth::id()) {
            abort(403, 'No autorizado');
        }
        
        DB::beginTransaction();

        try {
            // 1. Obtener reservas futuras activas (pending o paid)
            $futureReservations = Reservation::where('property_id', $property->id)
                ->where('check_in', '>=', now())
                ->whereIn('status', ['pending', 'paid'])
                ->get();

            $cancelledCount = 0;
            $totalRefunded = 0.0;

            // 2. Cancelar cada reserva futura
            foreach ($futureReservations as $reservation) {
                // Liberar fechas del calendario
                $period = CarbonPeriod::create($reservation->check_in, $reservation->check_out->subDay());
                foreach ($period as $date) {
                    RateCalendar::where('property_id', $property->id)
                        ->where('date', $date->toDateString())
                        ->update(['is_available' => true, 'blocked_by' => null]);
                }

                // Si estaba pagada, registrar reembolso
                if ($reservation->status === 'paid') {
                    $refund = Payment::create([
                        'reservation_id' => $reservation->id,
                        'amount' => -$reservation->total_price,
                        'method' => 'refund',
                        'status' => 'refunded',
                        'provider_ref' => 'refund_' . Str::uuid(),
                    ]);

                    $totalRefunded += $reservation->total_price;

                    // Enviar email de reembolso al cliente
                    if ($reservation->user && $reservation->user->email) {
                        Mail::to($reservation->user->email)->send(
                            new PaymentRefundIssuedMail($reservation, $refund)
                        );
                    }
                }

                // Marcar reserva como cancelada
                $reservation->update(['status' => 'cancelled']);

                // Enviar email de cancelación al cliente
                if ($reservation->user && $reservation->user->email) {
                    Mail::to($reservation->user->email)->send(
                        new ReservationCancelledMail($reservation)
                    );
                }

                $cancelledCount++;
            }

            // 3. Soft delete de la propiedad
            $property->delete();

            // 4. Enviar email de confirmación al admin
            $admin = Auth::user();
            if ($admin && $admin->email) {
                Mail::to($admin->email)->send(
                    new PropertyDeletedConfirmationMail(
                        $property->name ?? 'Propiedad',
                        $cancelledCount,
                        $totalRefunded
                    )
                );
            }

            DB::commit();

            return redirect()
                ->route('admin.dashboard')
                ->with('success', 
                    "Propiedad dada de baja. Canceladas {$cancelledCount} reserva(s). Reembolsado: " . 
                    number_format($totalRefunded, 2, ',', '.') . " €"
                );

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al dar de baja la propiedad: ' . $e->getMessage());
        }
    }

    /**
     * Muestra el formulario de edición de la propiedad.
     *
     * @param \App\Models\Property $property
     * @return \Illuminate\Contracts\View\View
     */
    public function propertyEdit(Property $property)
    {
        // Verificar que la propiedad pertenece al admin
        if ($property->user_id !== Auth::id()) {
            abort(403, 'No autorizado');
        }

        // Cargar relación de entorno
        $property->load('environment');

        // Contar reservas futuras activas
        $futureReservationsCount = Reservation::where('property_id', $property->id)
            ->where('check_in', '>=', now())
            ->whereIn('status', ['pending', 'paid'])
            ->count();

        return view('admin.property.index', compact('property', 'futureReservationsCount'));
    }

    /**
     * Actualiza los datos de la propiedad.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Property $property
     * @return \Illuminate\Http\RedirectResponse
     */
    public function propertyUpdate(Request $request, Property $property)
    {
        // Verificar que la propiedad pertenece al admin
        if ($property->user_id !== Auth::id()) {
            abort(403, 'No autorizado');
        }
        
        $validated = $request->validate([
            'name' => [
                'required', 
                'string', 
                'max:150',
                function ($attribute, $value, $fail) {
                    if (preg_match('/<[^>]*>/', $value) || preg_match('/&lt;|&gt;/', $value)) {
                        $fail('El nombre de la propiedad contiene caracteres HTML no permitidos.');
                    }
                    // Permitir letras, espacios, puntos y guiones (sin números)
                    if (!preg_match('/^[\p{L}\s.\-]+$/u', $value)) {
                        $fail('El nombre de la propiedad contiene caracteres no permitidos. Solo se permiten letras, espacios, puntos y guiones.');
                    }
                }
            ],
            'description' => [
                'nullable', 
                'string', 
                'max:5000',
                function ($attribute, $value, $fail) {
                    if ($value && (preg_match('/<[^>]*>/', $value) || preg_match('/&lt;|&gt;/', $value))) {
                        $fail('La descripción contiene código HTML o scripts no permitidos.');
                    }
                    // Permitir letras, números, espacios, puntuación y saltos de línea
                    if ($value && !preg_match('/^[\p{L}\p{N}\s.,;:!?¿¡()"\'\-\n\r]+$/u', $value)) {
                        $fail('La descripción contiene caracteres no permitidos.');
                    }
                }
            ],
            'address' => [
                'nullable', 
                'string', 
                'max:200',
                function ($attribute, $value, $fail) {
                    if ($value && (preg_match('/<[^>]*>/', $value) || preg_match('/&lt;|&gt;/', $value))) {
                        $fail('La dirección contiene caracteres HTML no permitidos.');
                    }
                    // Permitir letras, números, espacios y caracteres comunes en direcciones
                    if ($value && !preg_match('/^[\p{L}\p{N}\s.,ºª\-]+$/u', $value)) {
                        $fail('La dirección contiene caracteres no permitidos.');
                    }
                }
            ],
            'city' => [
                'nullable', 
                'string', 
                'max:100',
                function ($attribute, $value, $fail) {
                    if ($value && (preg_match('/<[^>]*>/', $value) || preg_match('/&lt;|&gt;/', $value))) {
                        $fail('La ciudad contiene caracteres HTML no permitidos.');
                    }
                    // Permitir letras, espacios y guiones
                    if ($value && !preg_match('/^[\p{L}\s\-]+$/u', $value)) {
                        $fail('La ciudad solo puede contener letras, espacios y guiones.');
                    }
                }
            ],
            'postal_code' => [
                'nullable', 
                'string', 
                'max:10',
                function ($attribute, $value, $fail) {
                    if ($value && !preg_match('/^[0-9]{5}$/', $value)) {
                        $fail('El código postal debe tener exactamente 5 dígitos.');
                    }
                }
            ],
            'province' => [
                'nullable', 
                'string', 
                'max:100',
                function ($attribute, $value, $fail) {
                    if ($value && (preg_match('/<[^>]*>/', $value) || preg_match('/&lt;|&gt;/', $value))) {
                        $fail('La provincia contiene caracteres HTML no permitidos.');
                    }
                    // Permitir letras, espacios y guiones
                    if ($value && !preg_match('/^[\p{L}\s\-]+$/u', $value)) {
                        $fail('La provincia solo puede contener letras, espacios y guiones.');
                    }
                }
            ],
            'capacity' => ['required', 'integer', 'min:1', 'max:50'],
            'tourism_license' => [
                'required', 
                'string', 
                'max:100',
                function ($attribute, $value, $fail) {
                    if (preg_match('/<[^>]*>/', $value) || preg_match('/&lt;|&gt;/', $value)) {
                        $fail('La licencia turística contiene caracteres HTML no permitidos.');
                    }
                    if (!preg_match('/^[A-Z0-9\/\-\s]+$/i', $value)) {
                        $fail('La licencia turística solo puede contener letras, números, barras, guiones y espacios.');
                    }
                }
            ],
            'rental_registration' => [
                'required', 
                'string', 
                'max:100',
                function ($attribute, $value, $fail) {
                    if (preg_match('/<[^>]*>/', $value) || preg_match('/&lt;|&gt;/', $value)) {
                        $fail('El registro de alquiler contiene caracteres HTML no permitidos.');
                    }
                    if (!preg_match('/^[A-Z0-9\/\-\s]+$/i', $value)) {
                        $fail('El registro de alquiler solo puede contener letras, números, barras, guiones y espacios.');
                    }
                }
            ],
            'services' => ['nullable', 'array'],
            'services.*' => [
                'string', 
                'in:wifi,parking,pool,washer,dishwasher,heating,air_conditioning,hairdryer,first_aid_kit,pets_allowed,smoking_allowed,tv,kitchen,towels,bed_linen,terrace,elevator,crib'
            ],
        ]);

        // Sanitizar datos antes de actualizar
        $validated['name'] = strip_tags($validated['name']);
        $validated['description'] = strip_tags($validated['description'] ?? '');
        $validated['address'] = strip_tags($validated['address'] ?? '');
        $validated['city'] = strip_tags($validated['city'] ?? '');
        $validated['province'] = strip_tags($validated['province'] ?? '');
        $validated['tourism_license'] = strip_tags($validated['tourism_license']);
        $validated['rental_registration'] = strip_tags($validated['rental_registration']);

        $property->update($validated);

        return back()->with('success', 'Propiedad actualizada correctamente.');
    }

    /**
     * Actualiza la información del entorno de una propiedad.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Property $property
     * @return \Illuminate\Http\RedirectResponse
     */
    public function environmentUpdate(Request $request, Property $property)
    {
        // Verificar que la propiedad pertenece al admin
        if ($property->user_id !== Auth::id()) {
            abort(403, 'No autorizado');
        }

        $validated = $request->validate([
            'env_title' => 'nullable|string|max:100',
            'env_subtitle' => 'nullable|string|max:500',
            'env_summary' => 'nullable|string|max:1000',
            'env_hero_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'env_nature_description' => 'nullable|string|max:1000',
            'env_nature_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'env_culture_description' => 'nullable|string|max:1000',
            'env_culture_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'env_activities_description' => 'nullable|string|max:1000',
            'env_activities_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'env_services_description' => 'nullable|string|max:1000',
            'env_services_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        // Obtener o crear el registro de entorno
        $environment = $property->environment ?? new \App\Models\PropertyEnvironment(['property_id' => $property->id]);

        // Actualizar campos de texto
        $environment->title = $validated['env_title'] ?? $environment->title;
        $environment->subtitle = $validated['env_subtitle'] ?? $environment->subtitle;
        $environment->summary = $validated['env_summary'] ?? $environment->summary;
        $environment->nature_description = $validated['env_nature_description'] ?? $environment->nature_description;
        $environment->culture_description = $validated['env_culture_description'] ?? $environment->culture_description;
        $environment->activities_description = $validated['env_activities_description'] ?? $environment->activities_description;
        $environment->services_description = $validated['env_services_description'] ?? $environment->services_description;

        // Procesar fotos si se subieron
        if ($request->hasFile('env_hero_photo')) {
            // Eliminar foto anterior si existe
            if ($environment->hero_photo && !str_starts_with($environment->hero_photo, 'http')) {
                Storage::disk('public')->delete($environment->hero_photo);
            }
            $environment->hero_photo = $request->file('env_hero_photo')->store('environment', 'public');
        }

        if ($request->hasFile('env_nature_photo')) {
            if ($environment->nature_photo && !str_starts_with($environment->nature_photo, 'http')) {
                Storage::disk('public')->delete($environment->nature_photo);
            }
            $environment->nature_photo = $request->file('env_nature_photo')->store('environment', 'public');
        }

        if ($request->hasFile('env_culture_photo')) {
            if ($environment->culture_photo && !str_starts_with($environment->culture_photo, 'http')) {
                Storage::disk('public')->delete($environment->culture_photo);
            }
            $environment->culture_photo = $request->file('env_culture_photo')->store('environment', 'public');
        }

        if ($request->hasFile('env_activities_photo')) {
            if ($environment->activities_photo && !str_starts_with($environment->activities_photo, 'http')) {
                Storage::disk('public')->delete($environment->activities_photo);
            }
            $environment->activities_photo = $request->file('env_activities_photo')->store('environment', 'public');
        }

        if ($request->hasFile('env_services_photo')) {
            if ($environment->services_photo && !str_starts_with($environment->services_photo, 'http')) {
                Storage::disk('public')->delete($environment->services_photo);
            }
            $environment->services_photo = $request->file('env_services_photo')->store('environment', 'public');
        }

        $environment->save();

        return back()->with('success', 'Información del entorno actualizada correctamente.');
    }

    /**
     * Lista las fotos de la propiedad y permite gestionarlas.
     *
     * @param \App\Models\Property $property
     * @return \Illuminate\Contracts\View\View
     */
    public function photosIndex(Property $property)
    {
        // Verificar que la propiedad pertenece al admin
        if ($property->user_id !== Auth::id()) {
            abort(403, 'No autorizado');
        }

        // Cargar fotos ordenadas por sort_order
        $photos = $property->photos()->orderBy('sort_order')->get();

        return view('admin.photos.index', compact('property', 'photos'));
    }

    /**
     * Sube una o varias fotos a la propiedad.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Property $property
     * @return \Illuminate\Http\RedirectResponse
     */
    public function photosStore(Request $request, Property $property)
    {
        $adminId = Auth::id();
        
        // Verificar que la propiedad pertenece al admin autenticado
        if ($property->user_id !== $adminId) {
            abort(403, 'No tienes permiso para gestionar esta propiedad.');
        }

        $request->validate([
            'photos' => 'required|array|min:1|max:30',
            'photos.*' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120', // 5MB máximo
        ]);

        // Obtener el último sort_order
        $lastSortOrder = $property->photos()->max('sort_order') ?? 0;

        foreach ($request->file('photos') as $index => $photo) {
            // Guardar archivo en storage/app/public/photos
            $path = $photo->store('photos', 'public');

            // Crear registro en BD
            $property->photos()->create([
                'url' => $path,
                'sort_order' => $lastSortOrder + $index + 1,
                'is_cover' => false, // Por defecto no es portada
            ]);
        }

        $count = count($request->file('photos'));
        return back()->with('success', "{$count} foto(s) subida(s) correctamente.");
    }

    /**
     * Elimina una foto de una propiedad.
     *
     * @param int $photoId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function photosDestroy($photoId)
    {
        $adminId = Auth::id();
        
        $photo = Photo::with('property')
            ->whereHas('property', function($q) use ($adminId) {
                $q->where('user_id', $adminId);
            })
            ->findOrFail($photoId);

        // Eliminar archivo físico solo si es local (no URL externa)
        if (!str_starts_with($photo->url, 'http')) {
            Storage::disk('public')->delete($photo->url);
        }

        // Eliminar registro de BD
        $photo->delete();

        return back()->with('success', 'Foto eliminada correctamente.');
    }

    /**
     * Actualiza el orden de las fotos de una propiedad.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function photosReorder(Request $request)
    {
        $adminId = Auth::id();
        
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'required|integer|exists:photos,id',
        ]);

        foreach ($request->order as $index => $photoId) {
            Photo::whereHas('property', function($q) use ($adminId) {
                $q->where('user_id', $adminId);
            })
            ->where('id', $photoId)
            ->update(['sort_order' => $index + 1]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Marca una foto como portada (is_cover) de una propiedad.
     *
     * @param \App\Models\Property $property
     * @param int $photo
     * @return \Illuminate\Http\RedirectResponse
     */
    public function photosSetCover(Property $property, $photo)
    {
        $adminId = Auth::id();
        
        // Verificar que la propiedad pertenece al admin
        if ($property->user_id !== $adminId) {
            abort(403);
        }
        
        $photoModel = Photo::where('property_id', $property->id)
            ->findOrFail($photo);
        
        // Quitar is_cover de todas las fotos de la propiedad
        Photo::where('property_id', $property->id)->update(['is_cover' => false]);
        
        // Marcar la seleccionada
        $photoModel->update(['is_cover' => true]);

        return back()->with('success', 'Foto marcada como portada.');
    }

    /**
     * Muestra el listado de todas las propiedades (activas y borradas) del admin.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function propertiesIndex()
    {
        $properties = Property::withTrashed()
            ->where('user_id', Auth::id())
            ->with('photos')
            ->latest()
            ->get();

        return view('admin.properties.index', compact('properties'));
    }

    /**
     * Muestra el dashboard de una propiedad específica con sus reservas.
     *
     * @param \App\Models\Property $property
     * @return \Illuminate\Contracts\View\View
     */
    public function propertyDashboard(Property $property)
    {
        // Verificar que la propiedad pertenece al admin
        if ($property->user_id !== Auth::id()) {
            abort(403, 'No autorizado');
        }
        
        // Obtener reservas de esta propiedad
        $query = Reservation::with(['user', 'invoice', 'invoices'])
            ->where('property_id', $property->id)
            ->latest();

        $reservations = $query->paginate(10);

        return view('admin.properties.dashboard', compact('property', 'reservations'));
    }

    /**
     * Muestra el formulario para crear una nueva propiedad.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function propertiesCreate()
    {
        return view('admin.properties.create');
    }

    /**
     * Almacena una nueva propiedad.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function propertiesStore(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required', 
                'string', 
                'max:150',
                function ($attribute, $value, $fail) {
                    if (preg_match('/<[^>]*>/', $value)) {
                        $fail('El nombre de la propiedad contiene caracteres HTML no permitidos.');
                    }
                    if (!preg_match('/^[\p{L}\s.\-]+$/u', $value)) {
                        $fail('El nombre de la propiedad contiene caracteres no permitidos. Solo se permiten letras, espacios, puntos y guiones.');
                    }
                }
            ],
            'slug' => 'required|string|max:150|unique:properties,slug',
            'description' => [
                'nullable', 
                'string', 
                'max:5000',
                function ($attribute, $value, $fail) {
                    if ($value && preg_match('/<[^>]*>/', $value)) {
                        $fail('La descripción contiene código HTML o scripts no permitidos.');
                    }
                    if ($value && !preg_match('/^[\p{L}\p{N}\s.,;:!?¿¡()"\'\-\n\r]+$/u', $value)) {
                        $fail('La descripción contiene caracteres no permitidos.');
                    }
                }
            ],
            'services' => ['nullable', 'array'],
            'services.*' => [
                'string', 
                'in:wifi,parking,pool,washer,dishwasher,heating,air_conditioning,hairdryer,first_aid_kit,pets_allowed,smoking_allowed,tv,kitchen,towels,bed_linen,terrace,elevator,crib'
            ],
            'address' => [
                'nullable', 
                'string', 
                'max:200',
                function ($attribute, $value, $fail) {
                    if ($value && (preg_match('/<[^>]*>/', $value) || preg_match('/&lt;|&gt;/', $value))) {
                        $fail('La dirección contiene caracteres HTML no permitidos.');
                    }
                    if ($value && !preg_match('/^[\p{L}\p{N}\s.,ºª\-]+$/u', $value)) {
                        $fail('La dirección contiene caracteres no permitidos.');
                    }
                }
            ],
            'city' => [
                'nullable', 
                'string', 
                'max:100',
                function ($attribute, $value, $fail) {
                    if ($value && (preg_match('/<[^>]*>/', $value) || preg_match('/&lt;|&gt;/', $value))) {
                        $fail('La ciudad contiene caracteres HTML no permitidos.');
                    }
                    if ($value && !preg_match('/^[\p{L}\s\-]+$/u', $value)) {
                        $fail('La ciudad solo puede contener letras, espacios y guiones.');
                    }
                }
            ],
            'postal_code' => [
                'nullable', 
                'string', 
                'max:10',
                function ($attribute, $value, $fail) {
                    if ($value && !preg_match('/^[0-9]{5}$/', $value)) {
                        $fail('El código postal debe tener exactamente 5 dígitos.');
                    }
                }
            ],
            'province' => [
                'nullable', 
                'string', 
                'max:100',
                function ($attribute, $value, $fail) {
                    if ($value && (preg_match('/<[^>]*>/', $value) || preg_match('/&lt;|&gt;/', $value))) {
                        $fail('La provincia contiene caracteres HTML no permitidos.');
                    }
                    if ($value && !preg_match('/^[\p{L}\s\-]+$/u', $value)) {
                        $fail('La provincia solo puede contener letras, espacios y guiones.');
                    }
                }
            ],
            'capacity' => ['required', 'integer', 'min:1', 'max:50'],
            'tourism_license' => [
                'required', 
                'string', 
                'max:100',
                function ($attribute, $value, $fail) {
                    if (preg_match('/<[^>]*>/', $value) || preg_match('/&lt;|&gt;/', $value)) {
                        $fail('La licencia turística contiene caracteres HTML no permitidos.');
                    }
                    if (!preg_match('/^[A-Z0-9\/\-\s]+$/i', $value)) {
                        $fail('La licencia turística solo puede contener letras, números, barras, guiones y espacios.');
                    }
                }
            ],
            'rental_registration' => [
                'required', 
                'string', 
                'max:100',
                function ($attribute, $value, $fail) {
                    if (preg_match('/<[^>]*>/', $value) || preg_match('/&lt;|&gt;/', $value)) {
                        $fail('El registro de alquiler contiene caracteres HTML no permitidos.');
                    }
                    if (!preg_match('/^[A-Z0-9\/\-\s]+$/i', $value)) {
                        $fail('El registro de alquiler solo puede contener letras, números, barras, guiones y espacios.');
                    }
                }
            ],
            // Campos de entorno
            'env_title' => 'nullable|string|max:100',
            'env_subtitle' => 'nullable|string|max:500',
            'env_summary' => 'nullable|string|max:1000',
            'env_hero_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'env_nature_description' => 'nullable|string|max:1000',
            'env_nature_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'env_culture_description' => 'nullable|string|max:1000',
            'env_culture_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'env_activities_description' => 'nullable|string|max:1000',
            'env_activities_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'env_services_description' => 'nullable|string|max:1000',
            'env_services_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        // Sanitizar datos antes de crear
        $validated['user_id'] = Auth::id();
        $validated['name'] = strip_tags($validated['name']);
        $validated['description'] = strip_tags($validated['description'] ?? '');
        $validated['address'] = strip_tags($validated['address'] ?? '');
        $validated['city'] = strip_tags($validated['city'] ?? '');
        $validated['province'] = strip_tags($validated['province'] ?? '');
        $validated['tourism_license'] = strip_tags($validated['tourism_license']);
        $validated['rental_registration'] = strip_tags($validated['rental_registration']);
        
        $property = Property::create($validated);

        // Crear el entorno si hay datos
        if ($request->hasAny(['env_title', 'env_subtitle', 'env_summary', 'env_nature_description', 'env_culture_description', 'env_activities_description', 'env_services_description'])) {
            $environmentData = ['property_id' => $property->id];
            
            // Procesar campos de texto
            foreach (['title' => 'env_title', 'subtitle' => 'env_subtitle', 'summary' => 'env_summary', 
                      'nature_description' => 'env_nature_description', 'culture_description' => 'env_culture_description',
                      'activities_description' => 'env_activities_description', 'services_description' => 'env_services_description'] as $key => $field) {
                if ($request->filled($field)) {
                    $environmentData[$key] = $request->input($field);
                }
            }
            
            // Procesar fotos
            foreach (['hero_photo' => 'env_hero_photo', 'nature_photo' => 'env_nature_photo', 'culture_photo' => 'env_culture_photo',
                      'activities_photo' => 'env_activities_photo', 'services_photo' => 'env_services_photo'] as $key => $field) {
                if ($request->hasFile($field)) {
                    $path = $request->file($field)->store('properties/environment', 'public');
                    $environmentData[$key] = $path;
                }
            }
            
            \App\Models\PropertyEnvironment::create($environmentData);
        }

        return redirect()
            ->route('admin.properties.index')
            ->with('success', 'Propiedad creada correctamente.');
    }

    /**
     * Restaura una propiedad eliminada (soft delete).
     *
     * @param int $propertyId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function propertiesRestore($propertyId)
    {
        $adminId = Auth::id();
        
        $property = Property::withTrashed()
            ->where('user_id', $adminId)
            ->findOrFail($propertyId);
        
        if (!$property->trashed()) {
            return back()->with('error', 'La propiedad no está dada de baja.');
        }

        $property->restore();

        return back()->with('success', 'Propiedad restaurada correctamente.');
    }

    /**
     * Muestra el calendario con la propiedad pre-seleccionada si se pasa property_id.
     *
     * @param \App\Models\Property $property
     * @return \Illuminate\Contracts\View\View
     */
    public function calendarIndex(Property $property)
    {
        // Verificar que la propiedad pertenece al admin
        if ($property->user_id !== Auth::id()) {
            abort(403, 'No autorizado');
        }
        
        $selectedPropertyId = $property->id;
        $blockedDates = [];
        
        // 1. Fechas bloqueadas manualmente
        $manuallyBlocked = RateCalendar::where('property_id', $selectedPropertyId)
            ->where('is_available', false)
            ->pluck('date')
            ->map(fn($d) => Carbon::parse($d)->format('Y-m-d'))
            ->toArray();

        // 2. Fechas ocupadas por reservas activas
        $occupiedDates = Reservation::where('property_id', $selectedPropertyId)
            ->whereIn('status', ['pending', 'paid'])
            ->get()
            ->flatMap(function ($reservation) {
                $dates = [];
                for ($d = $reservation->check_in->copy(); $d->lt($reservation->check_out); $d->addDay()) {
                    $dates[] = $d->format('Y-m-d');
                }
                return $dates;
            })
            ->toArray();

        // 3. Combinar y eliminar duplicados
        $blockedDates = array_values(array_unique(array_merge($manuallyBlocked, $occupiedDates)));
        sort($blockedDates);
        
        return view('admin.calendar.index', compact('selectedPropertyId', 'blockedDates', 'property'));
    }

    /**
     * Establece el precio por noche para un rango de fechas en una propiedad.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Property $property
     * @return \Illuminate\Http\RedirectResponse
     */
    public function setPrice(Request $request, Property $property)
    {
        $request->validate([
            'price' => 'required|numeric|min:0',
            'start' => 'required|date',
            'end' => 'required|date|after_or_equal:start',
        ]);

        // Verificar que la propiedad pertenece al admin
        if ($property->user_id !== Auth::id()) {
            abort(403, 'No autorizado');
        }

        $propertyId = $property->id;
        $price = $request->input('price');
        $start = Carbon::parse($request->input('start'));
        $end = Carbon::parse($request->input('end'));

        $datesUpdated = 0;
        $current = $start->copy();

        while ($current->lte($end)) {
            RateCalendar::updateOrCreate(
                [
                    'property_id' => $propertyId,
                    'date' => $current->format('Y-m-d'),
                ],
                [
                    'price' => $price,
                    'is_available' => true,
                ]
            );
            $datesUpdated++;
            $current->addDay();
        }

        return back()->with('success', "Precio actualizado para {$datesUpdated} noche(s): {$price}€/noche.");
    }
}

