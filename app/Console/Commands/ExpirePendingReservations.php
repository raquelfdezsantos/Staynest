<?php

namespace App\Console\Commands;

use App\Mail\ReservationExpiredMail;
use App\Models\Reservation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ExpirePendingReservations extends Command
{
    /**
     * Comando para expirar reservas pendientes.
     */
    protected $signature = 'reservations:expire-pending';

    /**
     * Descripción del comando.
     */
    protected $description = 'Cancela automáticamente las reservas pendientes que han expirado';

    /**
     * Ejecuta el comando para expirar reservas pendientes.
     * Busca reservas con estado 'pending' cuya fecha de expiración haya pasado,
     * las marca como canceladas, libera el calendario y notifica por email.
     *
     * @return int
     */
    public function handle()
    {
        $expiredReservations = Reservation::where('status', 'pending')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->with(['user', 'property'])
            ->get();

        if ($expiredReservations->isEmpty()) {
            $this->info('No hay reservas pendientes expiradas.');
            return 0;
        }

        $count = 0;
        foreach ($expiredReservations as $reservation) {
            try {
                $reservation->update(['status' => 'cancelled']);

                // Liberar fechas del calendario (rango [check_in, check_out))
                $checkIn = is_string($reservation->check_in) ? $reservation->check_in : $reservation->check_in->toDateString();
                $checkOut = is_string($reservation->check_out) ? $reservation->check_out : $reservation->check_out->toDateString();
                
                \App\Models\RateCalendar::where('property_id', $reservation->property_id)
                    ->where('date', '>=', $checkIn)
                    ->where('date', '<', $checkOut)
                    ->where('blocked_by', 'reservation')
                    ->update([
                        'is_available' => true,
                        'blocked_by' => null
                    ]);

                Mail::to($reservation->user->email)->send(new ReservationExpiredMail($reservation, false));

                if ($reservation->property && $reservation->property->user) {
                    Mail::to($reservation->property->user->email)->send(new ReservationExpiredMail($reservation, true));
                }

                $this->info("Reserva #{$reservation->id} expirada y notificada.");
                Log::info("Reserva expirada automáticamente", [
                    'reservation_id' => $reservation->id,
                    'code' => $reservation->code,
                    'user_email' => $reservation->user->email,
                    'admin_email' => $reservation->property->user->email ?? 'N/A',
                ]);

                $count++;
            } catch (\Exception $e) {
                $this->error("Error al expirar reserva #{$reservation->id}: {$e->getMessage()}");
                Log::error("Error al expirar reserva", [
                    'reservation_id' => $reservation->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->info("Total de reservas expiradas: {$count}");
        return 0;
    }
}
