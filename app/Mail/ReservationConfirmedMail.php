<?php

namespace App\Mail;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Mailable para notificar al usuario la confirmación de su reserva.
 *
 * Envía un correo con los datos de la reserva confirmada.
 */
class ReservationConfirmedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Constructor del mailable.
     *
     * @param Reservation $reservation Instancia de la reserva confirmada.
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
            subject: 'Reserva creada correctamente · ' . ($this->reservation->code ?? ('#' . $this->reservation->id)),
        );
    }

    /**
     * Define el contenido del correo (vista y datos).
     *
     * @return Content Contenido del correo con la vista y datos de la reserva confirmada.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.reservation_confirmed',
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
