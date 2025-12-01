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
use App\Mail\ReservationModifiedRefundPendingMail;
use App\Mail\AdminPaymentRefundIssuedMail;
use App\Mail\PaymentReceiptMail;
use Throwable;
use App\Models\Photo;
use App\Models\Invoice;
use Illuminate\Support\Facades\Storage;
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
     * Guarda en sesión los datos de una reserva iniciada por invitado y redirige a login.
     * Tras iniciar sesión/registro, el usuario vuelve a /reservar con los datos preseleccionados.
     */
    public function prepare(Request $request)
    {
        // Recoger sin forzar reglas de negocio aquí; solo persistimos la selección del formulario
        $payload = $request->only([
            'property_id', 'check_in', 'check_out', 'guests', 'adults', 'children', 'pets', 'notes'
        ]);

        // Obtener la propiedad para poder redirigir correctamente
        $property = Property::findOrFail($request->property_id);

        // Guardar en sesión para repoblar el formulario al volver a /reservar
        $request->session()->put('pending_reservation', array_filter($payload, fn($v) => $v !== null && $v !== ''));
        // Flag para auto-crear tras login
        $request->session()->put('pending_reservation_auto', true);

        // NO guardar url.intended porque los Auth controllers ya tienen su lógica
        // Simplemente redirigir al login
        return redirect()->guest(route('login'));
    }
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


    /**
     * Muestra el listado de reservas del cliente para una propiedad específica.
     *
     * Si el usuario es admin y dueño de la propiedad, redirige al panel de administración.
     *
     * @param Property $property Propiedad asociada a las reservas.
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index(Property $property)
    {
        // Si es admin y es dueño de la propiedad, redirigir al panel admin
        if (Auth::user()->role === 'admin' && $property->user_id === Auth::id()) {
            return redirect()->route('admin.dashboard');
        }
        
        $reservations = Reservation::with(['property', 'invoice'])
            ->where('user_id', Auth::id())
            ->latest('check_in')
            ->paginate(10);

        $suggested = Property::select('id', 'slug', 'name')->first();

        return view('customer.bookings.index', compact('reservations', 'suggested', 'property'));
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
        $dates  = collect($period)->map(function($d) {
            return is_object($d) && method_exists($d, 'toDateString') ? $d->toDateString() : (string) $d;
        });

        $nights = $dates->count();
        
        // Estancia mínima global
        $minStayGlobal = 2;
        if ($nights < $minStayGlobal) {
            return back()->withErrors(['check_in' => "La estancia mínima es de {$minStayGlobal} noches."])->withInput();
        }

        // Fallback: crear filas que falten en RateCalendar para el rango
        $missingDates = $dates->filter(function ($d) use ($property) {
            return !RateCalendar::where('property_id', $property->id)
                ->where('date', is_object($d) && method_exists($d, 'toDateString') ? $d->toDateString() : (string) $d)
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

        // Verificar solapamientos con reservas existentes
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
            ->keyBy(fn($r) => is_object($r->date) && method_exists($r->date, 'toDateString') ? $r->date->toDateString() : (string) $r->date);

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
                    ->where('date', is_object($d) && method_exists($d, 'toDateString') ? $d->toDateString() : (string) $d)
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
            Mail::to($reservation->property->user->email)
                ->send(new AdminNewReservationMail($reservation));
        } catch (Throwable $e) {
            report($e);
        }

        // Limpiar datos temporales de selección tras crear la reserva
        try { $request->session()->forget('pending_reservation'); } catch (Throwable $e) { /* no-op */ }

        return redirect()->route('properties.reservas.index', $property->slug)
            ->with('success', 'Reserva creada correctamente. Total: ' . number_format($total, 2, ',', '.') . ' €');
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


    /**
     * Genera un array de fechas entre dos días (excluyendo el último).
     * Utiliza CarbonPeriod para crear el rango y devuelve las fechas en formato string.
     *
     * @param string $from Fecha de inicio (YYYY-MM-DD).
     * @param string $to Fecha de fin (YYYY-MM-DD).
     * @return array Array de fechas en formato string.
     */
    private function rangeDates(string $from, string $to): array
    {
        // [from, to) excluye la salida
        $period = CarbonPeriod::create($from, $to)->excludeEndDate();
        return collect($period)->map(function($d) {
            return is_object($d) && method_exists($d, 'toDateString') ? $d->toDateString() : (string) $d;
        })->all();
    }

    /** Genera un código único con formato: SN-YYYY-XXXXXX (máx 20 chars). */
    /**
     * Genera un código único para la reserva con formato SN-YYYY-XXXXXX.
     * Realiza varios intentos para evitar colisiones y asegura longitud máxima de 20 caracteres.
     *
     * @return string Código único de reserva.
     */
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

    /**
     * Establece la disponibilidad de un conjunto de fechas para una propiedad.
     * Actualiza el campo is_available en RateCalendar para las fechas indicadas.
     *
     * @param int $propertyId ID de la propiedad.
     * @param array $dates Fechas a modificar.
     * @param bool $available Estado de disponibilidad.
     * @return void
     */
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

    /**
     * Muestra el formulario de edición de una reserva para el cliente (solo estados pending y paid).
     *
     * @param Reservation $reservation Reserva a editar.
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
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
        $currentNights = collect($currentPeriod)->map(function($d) {
            return is_object($d) && method_exists($d, 'toDateString') ? $d->toDateString() : (string) $d;
        })->all();

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

    /**
     * Actualiza las fechas y datos de una reserva para el cliente (solo estados pending y paid).
     *
     * Valida solapamientos, disponibilidad y reglas de negocio. Gestiona pagos y reembolsos si hay diferencia.
     *
     * @param Request $request Solicitud HTTP con los datos de la reserva.
     * @param Reservation $reservation Reserva a actualizar.
     * @return \Illuminate\Http\RedirectResponse
     */
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
            ->keyBy(fn($r) => is_object($r->date) && method_exists($r->date, 'toDateString') ? $r->date->toDateString() : (string) $r->date);

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
        $previousTotal = $reservation->total_price; // Guardar el total anterior
        
        // IMPORTANTE: Capturar valores originales ANTES de la transacción
        $originalCheckIn = $reservation->check_in;
        $originalCheckOut = $reservation->check_out;
        $originalGuests = $reservation->guests;
        $originalAdults = $reservation->adults ?? 0;
        $originalChildren = $reservation->children ?? 0;
        $originalPets = $reservation->pets ?? 0;
        
        Log::info('[UPDATE] Nuevo total calculado', [
            'rates_sum' => $rates->sum('price'),
            'guests' => $guests,
            'previousTotal' => $previousTotal,
            'newTotal' => $newTotal,
            'originalCheckIn' => $originalCheckIn->format('Y-m-d'),
            'originalCheckOut' => $originalCheckOut->format('Y-m-d'),
            'originalGuests' => $originalGuests,
            'originalAdults' => $originalAdults,
            'originalChildren' => $originalChildren,
            'originalPets' => $originalPets,
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

            // No modificar la factura original aquí para no sobreescribir importes previos
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
            // Generar factura rectificativa y devolución
            $refundInvoice = null;
            try {
                $refundInvoice = \DB::transaction(function () use ($reservation, $refund, $previousTotal, $originalCheckIn, $originalCheckOut, $originalGuests, $originalAdults, $originalChildren, $originalPets) {
                    Payment::create([
                        'reservation_id' => $reservation->id,
                        'amount'        => -$refund, // negativo = devolución
                        'method'        => 'simulated',
                        'status'        => 'refunded',
                        'provider_ref'  => 'SIM-REF-' . Str::upper(Str::random(6)),
                    ]);

                    $invoiceNumber = \App\Models\Invoice::generateUniqueNumber('RECT');
                    return \App\Models\Invoice::create([
                        'reservation_id' => $reservation->id,
                        'number'         => $invoiceNumber,
                        'pdf_path'       => null,
                        'issued_at'      => now(),
                        'amount'         => -$refund,
                        'details'        => [
                            'context'           => 'decrease_update',
                            'previous_total'    => round((float)$previousTotal, 2),
                            'difference'        => -round((float)$refund, 2),
                            'new_total'         => round((float)$reservation->total_price, 2),
                            'previous_check_in' => $originalCheckIn->format('Y-m-d'),
                            'previous_check_out'=> $originalCheckOut->format('Y-m-d'),
                            'new_check_in'      => $reservation->check_in->format('Y-m-d'),
                            'new_check_out'     => $reservation->check_out->format('Y-m-d'),
                            'previous_guests'   => (int)$originalGuests,
                            'new_guests'        => (int)$reservation->guests,
                            'previous_adults'   => (int)$originalAdults,
                            'previous_children' => (int)$originalChildren,
                            'previous_pets'     => (int)$originalPets,
                            'new_adults'        => (int)($reservation->adults ?? 0),
                            'new_children'      => (int)($reservation->children ?? 0),
                            'new_pets'          => (int)($reservation->pets ?? 0),
                        ],
                    ]);
                });
            } catch (\Throwable $e) {
                Log::error('Error generando refund e invoice rectificativa', ['msg' => $e->getMessage()]);
                report($e);
            }

            try {
                Mail::to($reservation->user->email)->send(
                    new ReservationModifiedRefundPendingMail($reservation, $reservation->total_price, $refund, $refundInvoice)
                );
                Log::info('ReservationModifiedRefundPendingMail enviado al cliente');
            } catch (Throwable $e) {
                Log::error('Fallo ReservationModifiedRefundPendingMail cliente', ['msg' => $e->getMessage()]);
                report($e);
            }
            
            Log::info('Enviando ReservationModifiedRefundPendingMail al admin', ['email' => $reservation->property->user->email]);
            try {
                Mail::to($reservation->property->user->email)->send(
                    new ReservationModifiedRefundPendingMail($reservation, $reservation->total_price, $refund, $refundInvoice)
                );
                Log::info('ReservationModifiedRefundPendingMail enviado al admin');
            } catch (Throwable $e) {
                Log::error('Fallo ReservationModifiedRefundPendingMail admin', ['msg' => $e->getMessage()]);
                report($e);
            }

            // 2. Enviar email "Devolución completada" (cliente y admin) con enlace a factura
            Log::info('Enviando PaymentRefundIssuedMail al cliente', ['email' => $reservation->user->email, 'refund' => $refund]);
            try {
                Mail::to($reservation->user->email)->send(new PaymentRefundIssuedMail($reservation, $refund, $refundInvoice));
                Log::info('PaymentRefundIssuedMail enviado al cliente');
            } catch (Throwable $e) {
                Log::error('Fallo enviando PaymentRefundIssuedMail cliente', ['msg' => $e->getMessage()]);
                report($e);
            }
            
            Log::info('Enviando AdminPaymentRefundIssuedMail al admin', ['email' => $reservation->property->user->email, 'refund' => $refund]);
            try {
                Mail::to($reservation->property->user->email)->send(
                    new AdminPaymentRefundIssuedMail($reservation, $refund, $refundInvoice)
                );
                Log::info('AdminPaymentRefundIssuedMail enviado al admin');
            } catch (Throwable $e) {
                Log::error('Fallo enviando AdminPaymentRefundIssuedMail admin', ['msg' => $e->getMessage()]);
                report($e);
            }
            
            return redirect()->route('properties.reservas.index', $reservation->property->slug)
                ->with('success', 'Reserva actualizada. Se ha procesado una devolución de ' . number_format($refund, 2, ',', '.') . ' €');
        } else {
            // No hay devolución - puede ser incremento o sin cambio
            // Si hay incremento (diff > 0), NO crear pago ni factura hasta que el usuario pague
            $updateInvoice = null;
            if ($diff > 0) {
                // Guardar los datos originales en la sesión para usarlos cuando se pague la diferencia
                session()->put('pending_balance_details_' . $reservation->id, [
                    'previous_total'     => round((float)$previousTotal, 2),
                    'difference'         => round((float)$diff, 2),
                    'previous_check_in'  => $originalCheckIn->format('Y-m-d'),
                    'previous_check_out' => $originalCheckOut->format('Y-m-d'),
                    'new_check_in'       => $reservation->check_in->format('Y-m-d'),
                    'new_check_out'      => $reservation->check_out->format('Y-m-d'),
                    'previous_guests'    => (int)$originalGuests,
                    'new_guests'         => (int)$reservation->guests,
                    'previous_adults'    => (int)$originalAdults,
                    'previous_children'  => (int)$originalChildren,
                    'previous_pets'      => (int)$originalPets,
                    'new_adults'         => (int)($reservation->adults ?? 0),
                    'new_children'       => (int)($reservation->children ?? 0),
                    'new_pets'           => (int)($reservation->pets ?? 0),
                ]);
                
                Log::info('Modificación con incremento de precio pendiente de pago', [
                    'reservation_id' => $reservation->id,
                    'diff' => $diff,
                    'previous_total' => $previousTotal,
                    'new_total' => $reservation->total_price,
                    'details_saved_to_session' => true
                ]);
            }
            
            // Enviar email de actualización con información de la diferencia
            // La factura rectificativa se generará cuando se pague la diferencia
            Log::info('Intentando enviar ReservationUpdatedMail al cliente', ['email' => $reservation->user->email]);
            try {
                Mail::to($reservation->user->email)->send(new ReservationUpdatedMail($reservation, $previousTotal, $diff, false, null));
                Log::info('ReservationUpdatedMail enviado al cliente', ['email' => $reservation->user->email]);
            } catch (Throwable $e) {
                Log::error('Fallo ReservationUpdatedMail cliente', ['msg' => $e->getMessage()]);
                report($e);
            }
            Log::info('Intentando enviar ReservationUpdatedMail al admin', ['email' => $reservation->property->user->email]);
            try {
                Mail::to($reservation->property->user->email)->send(new ReservationUpdatedMail($reservation, $previousTotal, $diff, true, null));
                Log::info('ReservationUpdatedMail enviado al admin', ['email' => $reservation->property->user->email]);
            } catch (Throwable $e) {
                Log::error('Fallo ReservationUpdatedMail admin', ['msg' => $e->getMessage()]);
                report($e);
            }
        }

        return redirect()->route('properties.reservas.index', $reservation->property->slug)
            ->with('success', 'Reserva actualizada correctamente.');
    }

    /**
     * Cancela una reserva para el cliente (solo estados pending y paid).
     *
     * Calcula el posible reembolso según la política y libera las noches en el calendario.
     *
     * @param Reservation $reservation Reserva a cancelar.
     * @return \Illuminate\Http\RedirectResponse
     */
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

        $refundInvoice = DB::transaction(function () use ($reservation, $refundAmount) {
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

                // Generar factura rectificativa asociada a la cancelación
                $invoiceNumber = \App\Models\Invoice::generateUniqueNumber('RECT');

                return \App\Models\Invoice::create([
                    'reservation_id' => $reservation->id,
                    'number'         => $invoiceNumber,
                    'pdf_path'       => null,
                    'issued_at'      => now(),
                    'amount'         => -$refundAmount,
                ]);
            }
            return null;
        });

        // Emails de cancelación
        try {
            Mail::to($reservation->user->email)->send(new ReservationCancelledMail($reservation, false, $refundInvoice));
        } catch (Throwable $e) { report($e); }
        try {
            Mail::to($reservation->property->user->email)->send(new ReservationCancelledMail($reservation, true, $refundInvoice));
        } catch (Throwable $e) { report($e); }

        // Email de reembolso si aplica
        if ($refundAmount > 0) {
            try { Mail::to($reservation->user->email)->send(new PaymentRefundIssuedMail($reservation, $refundAmount, $refundInvoice)); } catch (Throwable $e) { report($e); }
            try { Mail::to($reservation->property->user->email)->send(new AdminPaymentRefundIssuedMail($reservation, $refundAmount, $refundInvoice)); } catch (Throwable $e) { report($e); }
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
