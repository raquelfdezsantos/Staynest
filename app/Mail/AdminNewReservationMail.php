<?php

namespace App\Mail;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Mailable para notificar nueva reserva.
 */
class AdminNewReservationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param \App\Models\Reservation $reservation
     */
    public function __construct(public Reservation $reservation) {}

    /**
     * Define sobre del correo.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nueva reserva pendiente · ' . ($this->reservation->code ?? ('#' . $this->reservation->id)),
        );
    }

    /**
     * Define contenido del correo.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.admin_new_reservation',
            with: [
                'reservation' => $this->reservation->loadMissing(['user','property']),
            ],
        );
    }

    /**
     * Define adjuntos del correo.
     *
     * @return array
     */
    public function attachments(): array
    {
        return [];
    }
}
