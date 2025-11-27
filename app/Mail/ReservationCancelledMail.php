<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Mailable para notificar al usuario sobre la cancelación de una reserva.
 *
 * Envía un correo con los datos de la reserva cancelada.
 */
class ReservationCancelledMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Constructor del mailable.
     *
     * @param mixed $reservation Instancia de la reserva cancelada.
     */
    public function __construct(
        public $reservation
    ) {}

    /**
     * Define el sobre del correo (asunto, destinatario, etc).
     *
     * @return Envelope Sobre del correo con el asunto personalizado.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reserva cancelada · #' . $this->reservation->id . ' (' . now()->format('d/m/Y H:i:s') . ')',
        );
    }

    /**
     * Define el contenido del correo (vista y datos).
     *
     * @return Content Contenido del correo con la vista y datos de la reserva cancelada.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.reservation_cancelled',
            with: [
                'reservation' => $this->reservation->loadMissing(['user','property']),
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
