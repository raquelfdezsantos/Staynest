<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Http\Requests\StoreReservationRequest;
use App\Models\Property;
use App\Models\Reservation;
use App\Models\RateCalendar;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReservationConfirmedMail;
use App\Mail\AdminNewReservationMail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Mail\ReservationUpdatedMail;
use App\Mail\ReservationCancelledMail;
use App\Mail\PaymentRefundIssuedMail;
use App\Mail\PaymentBalanceDueMail;
use Throwable;
use Illuminate\Support\Facades\Log;


/**
 * Controlador de reservas.
 *
 * Gestiona la creación, validación y visualización de reservas tanto
 * para clientes como para el administrador. Controla las reglas de negocio
 * sobre disponibilidad, estancia mínima, solapamientos y capacidad máxima.
 */
class ReservationController extends Controller
{
    /**
     * Muestra la ficha de una propiedad y su formulario de reserva.
     *
     * @param  string  $slug  Slug de la propiedad
     * @return \Illuminate\Contracts\View\View
     */
    public function create(string $slug)
    {
        $property = Property::with('photos')->where('slug', $slug)->firstOrFail();
        return view('property.show', compact('property'));
    }


    /** Listado de reservas del cliente */
    public function index()
    {
        $reservations = Reservation::with(['property', 'invoice'])
            ->where('user_id', \Auth::id())
            ->latest('check_in')
            ->paginate(10);

        $suggested = Property::select('id', 'slug', 'name')->first();

        return view('customer.bookings.index', compact('reservations', 'suggested'));
    }


    /**
     * Crea una reserva validando reglas de negocio y calculando el precio:
     * - Capacidad máxima del alojamiento
     * - Rango de fechas válido y estancia mínima
     * - Ausencia de solapamientos con reservas existentes
     * - Disponibilidad diaria en calendario de tarifas
     * - Cálculo del total sumando el precio de cada noche
     *
     * @param  \App\Http\Requests\StoreReservationRequest  $request  Datos validados de la reserva
     * @return \Illuminate\Http\RedirectResponse  Redirige al listado de reservas del cliente
     */
    public function store(StoreReservationRequest $request)
    {
        $data = $request->validated();

        $property = Property::findOrFail($data['property_id']);

        // Calcular huéspedes según desglose si viene informado
        $adults   = (int) ($data['adults']   ?? 0);
        $children = (int) ($data['children'] ?? 0);
        $pets     = (int) ($data['pets']     ?? 0);
        $guests   = ($adults + $children) > 0 ? ($adults + $children) : (int) $data['guests'];

        if ($guests > (int)$property->capacity) {
            return back()->withErrors(['guests' => "Máximo {$property->capacity} huéspedes."])->withInput();
        }

        $period = CarbonPeriod::create($data['check_in'], $data['check_out'])->excludeEndDate();
        $dates  = collect($period)->map(fn($d) => $d->toDateString());

        $nights = $dates->count();
        
        // Estancia mínima global
        $minStayGlobal = 2;
        if ($nights < $minStayGlobal) {
            return back()->withErrors(['check_in' => "La estancia mínima es de {$minStayGlobal} noches."])->withInput();
        }

        // --- Fallback: crear filas que falten en RateCalendar para el rango ---
        $missingDates = $dates->filter(function ($d) use ($property) {
            return !RateCalendar::where('property_id', $property->id)
                ->whereDate('date', $d)
                ->exists();
        });

        foreach ($missingDates as $d) {
            $dateObj = Carbon::parse($d);
            $price = $dateObj->isWeekend() ? 120.00 : 95.00;
            RateCalendar::create([
                'property_id'  => $property->id,
                'date'         => $d,
                'price'        => $price,
                'is_available' => true,
                'min_stay'     => 2,
            ]);
        }
        // ---------------------------------------------------------------------

        $overlap = Reservation::where('property_id', $property->id)
            ->whereNotIn('status', ['cancelled'])
            ->where(function ($q) use ($data) {
                $q->where('check_in',  '<', $data['check_out'])
                    ->where('check_out', '>', $data['check_in']);
            })
            ->exists();

        if ($overlap) {
            Log::info('Overlap detectado', [
                'nueva_check_in' => $data['check_in'],
                'nueva_check_out' => $data['check_out'],
            ]);
            return back()
                ->withErrors(['check_in' => 'Las fechas seleccionadas no están disponibles.'])
                ->withInput();
        }


        $rates = RateCalendar::where('property_id', $property->id)
            ->where(function($q) use ($dates) {
                foreach ($dates as $d) {
                    $q->orWhereDate('date', $d);
                }
            })
            ->get()
            ->keyBy(fn($r) => $r->date->toDateString());

        Log::info('Validando disponibilidad', [
            'dates' => $dates->toArray(),
            'rates_count' => $rates->count(),
            'rates_keys' => $rates->keys()->toArray(),
        ]);

        foreach ($dates as $d) {
            $rate = $rates->get($d);
            if (!$rate || !$rate->is_available) {
                Log::warning('Fecha no disponible', [
                    'date' => $d,
                    'rate_exists' => $rate ? 'sí' : 'no',
                    'is_available' => $rate ? $rate->is_available : 'N/A',
                ]);
                return back()->withErrors(['check_in' => 'No hay disponibilidad en alguna de las noches seleccionadas.'])->withInput();
            }
        }

        $minStayFromRates = $rates->pluck('min_stay')->filter()->min();
        if ($minStayFromRates && $nights < $minStayFromRates) {
            return back()->withErrors(['check_in' => "La estancia mínima para esas fechas es de {$minStayFromRates} noches."])->withInput();
        }

        $total = $rates->sum('price') * (int)$guests;

        // Transacción: crear reserva + marcar noches como NO disponibles
        $reservation = DB::transaction(function () use ($data, $property, $total, $guests, $adults, $children, $pets) {
            $reservation = Reservation::create([
                'user_id'     => Auth::id(),
                'property_id' => $property->id,
                'check_in'    => $data['check_in'],
                'check_out'   => $data['check_out'],
                'code'        => $this->generateReservationCode(),
                'guests'      => $guests,
                'adults'      => $adults,
                'children'    => $children,
                'pets'        => $pets,
                'notes'       => $data['notes'] ?? null,
                'status'      => 'pending',
                'total_price' => $total,
            ]);

            // Bloquear noches [check_in, check_out)
            $period = CarbonPeriod::create($data['check_in'], $data['check_out'])->excludeEndDate();
            foreach ($period as $d) {
                RateCalendar::where('property_id', $property->id)
                    ->whereDate('date', $d->toDateString())
                    ->update(['is_available' => false, 'blocked_by' => 'reservation']);
            }

            return $reservation;
        });

        // Cargar relaciones necesarias para los emails
        $reservation->loadMissing(['user', 'property']);

        // Emails (no romper si falla SMTP)
        try {
            Mail::to($reservation->user->email)->send(new ReservationConfirmedMail($reservation));
        } catch (Throwable $e) {
            report($e);
        }

        try {
            Mail::to(env('MAIL_ADMIN', 'admin@vut.test'))
                ->send(new AdminNewReservationMail($reservation));
        } catch (Throwable $e) {
            report($e);
        }

        return redirect()->route('reservas.index')
            ->with('status', 'Reserva creada. Total: ' . number_format($total, 2, ',', '.') . ' €');
    }



    /**
     * Lista las reservas del cliente autenticado (con paginación).
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function myBookings()
    {
        $reservations = Reservation::with('property')
            ->where('user_id', Auth::id())
            ->latest('check_in')
            ->paginate(10);

        return view('customer.bookings', compact('reservations'));
    }


    private function rangeDates(string $from, string $to): array
    {
        // [from, to) excluye la salida
        $period = CarbonPeriod::create($from, $to)->excludeEndDate();
        return collect($period)->map(fn($d) => $d->toDateString())->all();
    }

    /** Genera un código único con formato: SN-YYYY-XXXXXX (máx 20 chars). */
    private function generateReservationCode(): string
    {
        $prefix = 'SN-' . now()->format('Y') . '-'; // Ej: SN-2025-
        for ($i = 0; $i < 5; $i++) {
            $segment = Str::upper(Str::random(6));
            $code = $prefix . $segment; // Longitud típica: 8 + 6 = 14
            if (!Reservation::where('code', $code)->exists()) {
                return $code;
            }
        }
        // Fallback muy improbable: aumenta aleatoriedad manteniendo <= 20
        $segment = Str::upper(Str::random(12)); // 8 + 12 = 20
        return $prefix . $segment;
    }

    private function setAvailability(int $propertyId, array $dates, bool $available): void
    {
        if (empty($dates)) return;
        
        RateCalendar::where('property_id', $propertyId)
            ->where(function($q) use ($dates) {
                foreach ($dates as $d) {
                    $q->orWhereDate('date', $d);
                }
            })
            ->update(['is_available' => $available]);
    }

    /** Form de edición (cliente, permite pending y paid) */
    public function edit(Reservation $reservation)
    {
        $this->authorize('update', $reservation);
        if ($reservation->status === 'cancelled') {
            return back()->with('error', 'No puedes modificar reservas canceladas.');
        }
        $reservation->loadMissing('property');

        // Preparar datos de calendario y tarifas
        $property = $reservation->property;

        // Rango actual de la reserva (para permitir seleccionarlo aunque esté bloqueado)
        $currentPeriod = CarbonPeriod::create($reservation->check_in, $reservation->check_out)->excludeEndDate();
        $currentNights = collect($currentPeriod)->map(fn($d) => $d->toDateString())->all();

        // Fechas bloqueadas desde RateCalendar, excluyendo las noches de esta reserva
        $blockedDates = RateCalendar::query()
            ->where('property_id', $property->id)
            ->where('is_available', false)
            ->orderBy('date')
            ->pluck('date')
            ->map(fn($d) => Carbon::parse($d)->format('Y-m-d'))
            ->reject(fn($d) => in_array($d, $currentNights, true))
            ->values()
            ->toArray();

        // Fechas de check-in/checkout de otras reservas activas
        $otherReservations = Reservation::where('property_id', $property->id)
            ->where('id', '!=', $reservation->id)
            ->whereIn('status', ['pending', 'paid'])
            ->get();

        $checkinDates = $otherReservations->pluck('check_in')->map(fn($d) => Carbon::parse($d)->format('Y-m-d'))->unique()->values()->toArray();
        $checkoutDates = $otherReservations->pluck('check_out')->map(fn($d) => Carbon::parse($d)->format('Y-m-d'))->unique()->values()->toArray();

        // Todas las tarifas (incluyendo noches bloqueadas) como fecha => precio (Y-m-d)
        $rates = RateCalendar::where('property_id', $property->id)
            ->get()
            ->pluck('price', 'date')
            ->mapWithKeys(fn($price, $date) => [Carbon::parse($date)->format('Y-m-d') => $price])
            ->toArray();

        $maxCapacity = $property->capacity ?? 4;

        return view('customer.bookings.edit', compact(
            'reservation', 'property', 'blockedDates', 'checkinDates', 'checkoutDates', 'rates', 'maxCapacity'
        ));
    }

    /** Update fechas (cliente, permite pending y paid) */
    public function update(Request $request, Reservation $reservation)
    {
        $this->authorize('update', $reservation);
        if ($reservation->status === 'cancelled') {
            return back()->with('error', 'No puedes modificar reservas canceladas.');
        }

        $data = $request->validate([
            'check_in'  => ['required', 'date'],
            'check_out' => ['required', 'date', 'after_or_equal:check_in'],
            'guests'    => ['nullable', 'integer', 'min:1'],
            'adults'    => ['nullable', 'integer', 'min:0'],
            'children'  => ['nullable', 'integer', 'min:0'],
            'pets'      => ['nullable', 'integer', 'min:0'],
            'notes'     => ['nullable', 'string', 'max:1000'],
        ]);

        $property = $reservation->property()->firstOrFail();

        // Calcular huéspedes desde desglose si viene
        $adults   = (int) ($data['adults']   ?? 0);
        $children = (int) ($data['children'] ?? 0);
        $pets     = (int) ($data['pets']     ?? 0);
        $guests   = ($adults + $children) > 0 ? ($adults + $children) : (int) ($data['guests'] ?? $reservation->guests);

        if ((int)$guests > (int)$property->capacity) {
            return back()->withErrors(['guests' => "Máximo {$property->capacity} huéspedes."]);
        }

        $oldDates = $this->rangeDates($reservation->check_in->toDateString(), $reservation->check_out->toDateString());
        $newDates = $this->rangeDates($data['check_in'], $data['check_out']);

        if (empty($newDates)) {
            return back()->withErrors(['check_in' => 'La fecha de salida debe ser posterior a la de entrada.']);
        }

        // Validar solapes en [check_in, check_out)
        $overlap = Reservation::where('property_id', $property->id)
            ->where('id', '!=', $reservation->id)
            ->whereNotIn('status', ['cancelled'])
            ->where(function ($q) use ($data) {
                $q->where('check_in', '<', $data['check_out'])
                    ->where('check_out', '>', $data['check_in']);
            })
            ->exists();

        if ($overlap) {
            return back()->withErrors(['check_in' => 'Las nuevas fechas se solapan con otra reserva.']);
        }

        // Comprobar disponibilidad de noches nuevas
        $rates = RateCalendar::where('property_id', $property->id)
            ->where(function($q) use ($newDates) {
                foreach ($newDates as $d) {
                    $q->orWhereDate('date', $d);
                }
            })
            ->get()
            ->keyBy(fn($r) => $r->date->toDateString());

        foreach ($newDates as $d) {
            $rate = $rates->get($d);
            $inOldDates = in_array($d, $oldDates, true);
            
            Log::info('[UPDATE] Verificando fecha', [
                'date' => $d,
                'rate_exists' => !!$rate,
                'is_available' => $rate ?$rate->is_available : null,
                'in_old_dates' => $inOldDates,
                'old_dates' => $oldDates,
            ]);
            
            // Si el día nuevo no existe o no está libre y no pertenece al rango antiguo, error
            if (!$rate || (!$rate->is_available && !$inOldDates)) {
                Log::error('[UPDATE] No disponible', ['date' => $d]);
                return back()->withErrors(['check_in' => "No hay disponibilidad el día $d."]);
            }
        }

        // Reglas de min_stay (opcional)
        $nights = count($newDates);
        $minStay = $rates->pluck('min_stay')->filter()->min();
        if ($minStay && $nights < $minStay) {
            return back()->withErrors(['check_in' => "La estancia mínima para esas fechas es de {$minStay} noches."]);
        }

        $newTotal = $rates->sum('price') * (int)$guests;
        
        Log::info('[UPDATE] Nuevo total calculado', [
            'rates_sum' => $rates->sum('price'),
            'guests' => $data['guests'],
            'newTotal' => $newTotal,
            'rates' => $rates->pluck('price', 'date')->toArray(),
        ]);

        DB::transaction(function () use ($reservation, $property, $oldDates, $newDates, $newTotal, $data, $guests, $adults, $children, $pets) {
            // liberar antiguas y bloquear nuevas (excluye checkout)
            $this->setAvailability($property->id, $oldDates, true);
            $this->setAvailability($property->id, $newDates, false);

            $reservation->update([
                'check_in'    => $data['check_in'],
                'check_out'   => $data['check_out'],
                'guests'      => $guests,
                'adults'      => $adults,
                'children'    => $children,
                'pets'        => $pets,
                'notes'       => $data['notes'] ?? $reservation->notes,
                'total_price' => $newTotal,
            ]);
        });

        // Refrescar modelo después de la transacción
        $reservation->refresh();

        $paid   = $reservation->paidAmount(); // helper del modelo
        $diff   = $reservation->total_price - $paid; // >0 falta cobrar, <0 hay que devolver

        // Si falta cobrar (diff > 0) - enviar email normal de actualización
        // Si sobra dinero (diff < 0) - enviar email de modificación con devolución pendiente
        Log::info('Calculando diferencia', ['total_price' => $reservation->total_price, 'paid' => $paid, 'diff' => $diff]);
        
        if ($diff < 0) {
            // Hay que devolver dinero
            $refund = abs($diff);
            Log::info('Procesando refund', ['refund' => $refund]);
            
            // 1. Enviar email "Reserva modificada - devolución pendiente" (cliente y admin)
            Log::info('Enviando ReservationModifiedRefundPendingMail al cliente', ['email' => $reservation->user->email]);
            try {
                Mail::to($reservation->user->email)->send(
                    new \App\Mail\ReservationModifiedRefundPendingMail($reservation, $reservation->total_price, $refund)
                );
                Log::info('ReservationModifiedRefundPendingMail enviado al cliente');
            } catch (Throwable $e) {
                Log::error('Fallo ReservationModifiedRefundPendingMail cliente', ['msg' => $e->getMessage()]);
                report($e);
            }
            
            Log::info('Enviando ReservationModifiedRefundPendingMail al admin', ['email' => env('MAIL_ADMIN', 'admin@vut.test')]);
            try {
                Mail::to(env('MAIL_ADMIN', 'admin@vut.test'))->send(
                    new \App\Mail\ReservationModifiedRefundPendingMail($reservation, $reservation->total_price, $refund)
                );
                Log::info('ReservationModifiedRefundPendingMail enviado al admin');
            } catch (Throwable $e) {
                Log::error('Fallo ReservationModifiedRefundPendingMail admin', ['msg' => $e->getMessage()]);
                report($e);
            }
            
            // 2. Procesar la devolución
            DB::transaction(function () use ($reservation, $refund) {
                Payment::create([
                    'reservation_id' => $reservation->id,
                    'amount'        => -$refund, // negativo = devolución
                    'method'        => 'simulated',
                    'status'        => 'refunded',
                    'provider_ref'  => 'SIM-REF-' . Str::upper(Str::random(6)),
                ]);
            });

            // 3. Enviar email "Devolución completada" (cliente y admin)
            Log::info('Enviando PaymentRefundIssuedMail al cliente', ['email' => $reservation->user->email, 'refund' => $refund]);
            try {
                Mail::to($reservation->user->email)->send(new PaymentRefundIssuedMail($reservation, $refund));
                Log::info('PaymentRefundIssuedMail enviado al cliente');
            } catch (Throwable $e) {
                Log::error('Fallo enviando PaymentRefundIssuedMail cliente', ['msg' => $e->getMessage()]);
                report($e);
            }
            
            Log::info('Enviando AdminPaymentRefundIssuedMail al admin', ['email' => env('MAIL_ADMIN', 'admin@vut.test'), 'refund' => $refund]);
            try {
                Mail::to(env('MAIL_ADMIN', 'admin@vut.test'))->send(
                    new \App\Mail\AdminPaymentRefundIssuedMail($reservation, $refund)
                );
                Log::info('AdminPaymentRefundIssuedMail enviado al admin');
            } catch (Throwable $e) {
                Log::error('Fallo enviando AdminPaymentRefundIssuedMail admin', ['msg' => $e->getMessage()]);
                report($e);
            }
        } else {
            // No hay devolución - enviar email normal de actualización
            Log::info('Intentando enviar ReservationUpdatedMail al cliente', ['email' => $reservation->user->email]);
            try {
                Mail::to($reservation->user->email)->send(new ReservationUpdatedMail($reservation));
                Log::info('ReservationUpdatedMail enviado al cliente', ['email' => $reservation->user->email]);
            } catch (Throwable $e) {
                Log::error('Fallo ReservationUpdatedMail cliente', ['msg' => $e->getMessage()]);
                report($e);
            }
            Log::info('Intentando enviar ReservationUpdatedMail al admin', ['email' => env('MAIL_ADMIN', 'admin@vut.test')]);
            try {
                Mail::to(env('MAIL_ADMIN', 'admin@vut.test'))->send(new ReservationUpdatedMail($reservation));
                Log::info('ReservationUpdatedMail enviado al admin', ['email' => env('MAIL_ADMIN', 'admin@vut.test')]);
            } catch (Throwable $e) {
                Log::error('Fallo ReservationUpdatedMail admin', ['msg' => $e->getMessage()]);
                report($e);
            }
        }

        return redirect()->route('reservas.index')->with('success', 'Reserva actualizada correctamente.');
    }

    /** Cancelar (cliente, permite pending y paid) */
    public function cancel(Reservation $reservation)
    {
        $this->authorize('cancel', $reservation);
        if ($reservation->status === 'cancelled') {
            return back()->with('error', 'La reserva ya está cancelada.');
        }
        // Calcular posible reembolso según política (antes de mutar estado)
        $percent = $reservation->cancellationRefundPercent();
        $paid    = $reservation->paidAmount();
        $refundAmount = 0.0;
        if ($percent > 0 && $paid > 0) {
            $refundAmount = round(min($paid, $reservation->total_price) * ($percent / 100), 2);
        }

        DB::transaction(function () use ($reservation, $refundAmount) {
            $dates = $this->rangeDates($reservation->check_in->toDateString(), $reservation->check_out->toDateString());
            $this->setAvailability($reservation->property_id, $dates, true);
            $reservation->update(['status' => 'cancelled']);
            if ($refundAmount > 0) {
                Payment::create([
                    'reservation_id' => $reservation->id,
                    'amount'        => -$refundAmount,
                    'method'        => 'policy',
                    'status'        => 'refunded',
                    'provider_ref'  => 'POL-REF-' . Str::upper(Str::random(6)),
                ]);
            }
        });

        // Emails de cancelación
        try {
            Mail::to($reservation->user->email)->send(new ReservationCancelledMail($reservation));
        } catch (Throwable $e) { report($e); }
        try {
            Mail::to(env('MAIL_ADMIN', 'admin@vut.test'))->send(new ReservationCancelledMail($reservation));
        } catch (Throwable $e) { report($e); }

        // Email de reembolso si aplica
        if ($refundAmount > 0) {
            try { Mail::to($reservation->user->email)->send(new PaymentRefundIssuedMail($reservation, $refundAmount)); } catch (Throwable $e) { report($e); }
            try { Mail::to(env('MAIL_ADMIN', 'admin@vut.test'))->send(new PaymentRefundIssuedMail($reservation, $refundAmount)); } catch (Throwable $e) { report($e); }
        }

        $msg = 'Reserva cancelada y noches liberadas.';
        if ($refundAmount > 0) {
            $msg .= ' Reembolso aplicado: ' . number_format($refundAmount, 2, ',', '.') . '€ (' . $percent . '%).';
        } elseif ($percent === 0 && $reservation->status === 'paid') {
            $msg .= ' No procede reembolso (faltan menos de 7 días).';
        }

        return back()->with('success', $msg);
    }
}
