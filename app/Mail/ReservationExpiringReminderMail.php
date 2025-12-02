<?php

namespace App\Mail;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Mailable para recordar al usuario que su reserva está por expirar.
 *
 * Envía un correo 1 hora antes de que expire la reserva pendiente de pago.
 */
class ReservationExpiringReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Constructor del mailable.
     *
     * @param Reservation $reservation Instancia de la reserva que está por expirar.
     */
    public function __construct(public Reservation $reservation) {}

    /**
     * Define el sobre del correo (asunto, destinatario, etc).
     *
     * @return Envelope Sobre del correo con el asunto personalizado.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '⚠️ Tu reserva expira pronto · ' . ($this->reservation->code ?? ('#' . $this->reservation->id)),
        );
    }

    /**
     * Define el contenido del correo (vista y datos).
     *
     * @return Content Contenido del correo con la vista y datos de la reserva.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.reservation_expiring_reminder',
            with: [
                'reservation' => $this->reservation->loadMissing(['user', 'property']),
            ],
        );
    }

    /**
     * Define los archivos adjuntos del correo (ninguno en este caso).
     *
     * @return array Lista de adjuntos vacía.
     */
    public function attachments(): array
    {
        return [];
    }
}
