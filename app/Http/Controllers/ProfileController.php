<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\Reservation;
use App\Models\Payment;
use App\Models\RateCalendar;
use App\Mail\ReservationCancelledMail;
use App\Mail\PaymentRefundIssuedMail;
use Illuminate\Support\Str;

/**
 * Controlador de perfil de usuario.
 *
 * Permite a los clientes editar sus datos personales, subir un avatar
 * y consultar sus reservas y facturas en la sección "Mi perfil".
 */
class ProfileController extends Controller
{
    /**
     * Muestra el formulario de edición del perfil del usuario autenticado.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\View\View
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Actualiza el perfil del usuario autenticado con los datos validados.
     * Si el email cambia, se reinicia la verificación.
     *
     * @param  \App\Http\Requests\ProfileUpdateRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // avatar (opcional)
        if ($request->hasFile('avatar')) {
            $request->validate([
                'avatar' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            ]);

            // borrar anterior si existe
            if ($user->avatar_path) {
                Storage::disk('public')->delete($user->avatar_path);
            }

            $path = $request->file('avatar')->store('avatars', 'public'); // storage/app/public/avatars
            $user->avatar_path = $path;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Elimina la cuenta del usuario autenticado tras validar la contraseña.
     * Cancela todas las reservas activas, calcula reembolsos, libera noches.
     * Cierra sesión, invalida la sesión y regenera el token CSRF.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ], [
            'password.required' => 'La contraseña es obligatoria.',
            'password.current_password' => 'La contraseña es incorrecta.',
        ]);

        $user = $request->user();

        // Cancelar todas las reservas activas del usuario
        if ($user->role === 'customer') {
            $this->cancelAllUserReservations($user);
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/')->with('success', 'Tu cuenta ha sido eliminada. Todas tus reservas han sido canceladas.');
    }

    /**
     * Cancela todas las reservas activas de un usuario, libera noches y procesa reembolsos.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    private function cancelAllUserReservations($user): void
    {
        // Obtener todas las reservas activas (pending, paid)
        $activeReservations = Reservation::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'paid'])
            ->get();

        foreach ($activeReservations as $reservation) {
            try {
                // Calcular reembolso según política
                $percent = $reservation->cancellationRefundPercent();
                $paid = $reservation->paidAmount();
                $refundAmount = 0.0;
                
                if ($percent > 0 && $paid > 0) {
                    $refundAmount = round(min($paid, $reservation->total_price) * ($percent / 100), 2);
                }

                $refundInvoice = null;
                DB::transaction(function () use ($reservation, $refundAmount, $user, &$refundInvoice) {
                    // Liberar noches
                    $dates = $this->rangeDates(
                        $reservation->check_in->toDateString(),
                        $reservation->check_out->toDateString()
                    );
                    $this->setAvailability($reservation->property_id, $dates, true);

                    // Marcar reserva como cancelada
                    $reservation->update(['status' => 'cancelled']);

                    // Registrar reembolso y crear factura rectificativa si aplica
                    if ($refundAmount > 0) {
                        Payment::create([
                            'reservation_id' => $reservation->id,
                            'amount'        => -$refundAmount,
                            'method'        => 'account_deletion',
                            'status'        => 'refunded',
                            'provider_ref'  => 'ACCDEL-' . Str::upper(Str::random(6)),
                        ]);

                        // Crear factura rectificativa de cancelación
                        $invoiceNumber = \App\Models\Invoice::generateUniqueNumber('RECT');
                        $refundInvoice = \App\Models\Invoice::create([
                            'reservation_id' => $reservation->id,
                            'number'         => $invoiceNumber,
                            'pdf_path'       => null,
                            'issued_at'      => now(),
                            'amount'         => -$refundAmount,
                            'details'        => [
                                'context'        => 'cancellation',
                                'refund_percent' => $percent,
                                'refund_reason'  => 'Cancelación por eliminación de cuenta',
                                'check_in'       => $reservation->check_in->format('Y-m-d'),
                                'check_out'      => $reservation->check_out->format('Y-m-d'),
                                'guests'         => $reservation->guests,
                                'adults'         => $reservation->adults ?? 0,
                                'children'       => $reservation->children ?? 0,
                                'pets'           => $reservation->pets ?? 0,
                            ],
                        ]);
                    }
                });

                // Enviar emails de cancelación
                try {
                    Mail::to($user->email)->send(new ReservationCancelledMail($reservation));
                } catch (\Throwable $e) {
                    Log::error('Error enviando email de cancelación al cliente: ' . $e->getMessage());
                }

                try {
                    Mail::to($reservation->property->user->email)->send(new ReservationCancelledMail($reservation, true));
                } catch (\Throwable $e) {
                    Log::error('Error enviando email de cancelación al admin: ' . $e->getMessage());
                }

                // Enviar email de reembolso si aplica
                if ($refundAmount > 0 && $refundInvoice) {
                    try {
                        Mail::to($user->email)->send(new PaymentRefundIssuedMail($reservation, $refundAmount, $refundInvoice));
                    } catch (\Throwable $e) {
                        Log::error('Error enviando email de reembolso: ' . $e->getMessage());
                    }
                }

                Log::info('Reserva cancelada por eliminación de cuenta', [
                    'reservation_id' => $reservation->id,
                    'user_id' => $user->id,
                    'refund_amount' => $refundAmount,
                    'refund_percent' => $percent
                ]);

            } catch (\Throwable $e) {
                Log::error('Error cancelando reserva en eliminación de cuenta: ' . $e->getMessage(), [
                    'reservation_id' => $reservation->id,
                    'user_id' => $user->id
                ]);
            }
        }
    }

    /**
     * Genera un array de fechas entre check_in y check_out (excluyendo checkout).
     *
     * @param  string  $checkIn
     * @param  string  $checkOut
     * @return array<string>
     */
    private function rangeDates(string $checkIn, string $checkOut): array
    {
        $period = \Carbon\CarbonPeriod::create($checkIn, $checkOut)->excludeEndDate();
        return collect($period)->map(fn($d) => $d->toDateString())->toArray();
    }

    /**
     * Marca las fechas como disponibles/no disponibles en el calendario.
     *
     * @param  int  $propertyId
     * @param  array<string>  $dates
     * @param  bool  $available
     * @return void
     */
    private function setAvailability(int $propertyId, array $dates, bool $available): void
    {
        foreach ($dates as $d) {
            RateCalendar::where('property_id', $propertyId)
                ->whereDate('date', $d)
                ->update(['is_available' => $available]);
        }
    }
}
