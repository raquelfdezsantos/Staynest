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
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reservations:expire-pending';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cancela automáticamente las reservas pendientes que han expirado';

    /**
     * Execute the console command.
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
                // Cambiar estado a cancelled
                $reservation->update([
                    'status' => 'cancelled'
                ]);

                // Enviar email al cliente
                Mail::to($reservation->user->email)->send(new ReservationExpiredMail($reservation));

                // Enviar email al admin de la propiedad
                if ($reservation->property && $reservation->property->user) {
                    Mail::to($reservation->property->user->email)->send(new ReservationExpiredMail($reservation));
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
