<?php

namespace App\Console\Commands;

use App\Mail\ReservationExpiringReminderMail;
use App\Models\Reservation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendExpirationReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reservations:send-expiration-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envía recordatorios a usuarios con reservas que expiran en 1 minuto';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Buscar reservas pendientes que expiren en aproximadamente 1 minuto
        // (entre 30 y 90 segundos para dar margen de ejecución del cron)
        $oneMinuteFromNow = now()->addSeconds(30);
        $oneMinuteThirtyFromNow = now()->addSeconds(90);

        $expiringReservations = Reservation::where('status', 'pending')
            ->whereNotNull('expires_at')
            ->whereBetween('expires_at', [$oneMinuteFromNow, $oneMinuteThirtyFromNow])
            ->whereNotExists(function ($query) {
                // Verificar que no se haya enviado ya el recordatorio
                // (usando una columna reminder_sent_at si la agregas, o basándote en logs)
                // Por ahora, enviamos una vez por ejecución
            })
            ->with(['user', 'property'])
            ->get();

        if ($expiringReservations->isEmpty()) {
            $this->info('No hay reservas próximas a expirar en el siguiente minuto.');
            return 0;
        }

        $count = 0;
        foreach ($expiringReservations as $reservation) {
            try {
                // Enviar email de recordatorio
                Mail::to($reservation->user->email)->send(new ReservationExpiringReminderMail($reservation));

                $this->info("Recordatorio enviado para reserva #{$reservation->id}");
                Log::info("Recordatorio de expiración enviado", [
                    'reservation_id' => $reservation->id,
                    'code' => $reservation->code,
                    'user_email' => $reservation->user->email,
                    'expires_at' => $reservation->expires_at,
                ]);

                $count++;
            } catch (\Exception $e) {
                $this->error("Error al enviar recordatorio para reserva #{$reservation->id}: {$e->getMessage()}");
                Log::error("Error al enviar recordatorio de expiración", [
                    'reservation_id' => $reservation->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->info("Total de recordatorios enviados: {$count}");
        return 0;
    }
}
