<?php

namespace App\Mail;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Mailable para notificar al usuario que su reserva ha expirado.
 *
 * Envía un correo cuando una reserva pendiente de pago ha sido cancelada por expiración.
 */
class ReservationExpiredMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Constructor del mailable.
     *
     * @param Reservation $reservation Instancia de la reserva expirada.
     * @param bool $isAdmin Si el email se envía al admin de la propiedad.
     */
    public function __construct(public Reservation $reservation, public bool $isAdmin = false) {}

    /**
     * Define el sobre del correo (asunto, destinatario, etc).
     *
     * @return Envelope Sobre del correo con el asunto personalizado.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reserva expirada · ' . ($this->reservation->code ?? ('#' . $this->reservation->id)),
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
            view: 'emails.reservation_expired',
            with: [
                'reservation' => $this->reservation->loadMissing(['user', 'property']),
                'isAdmin' => $this->isAdmin,
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
