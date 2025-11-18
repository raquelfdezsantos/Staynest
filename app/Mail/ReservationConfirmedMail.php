<?php

namespace App\Mail;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReservationConfirmedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Reservation $reservation) {}

    // Asunto
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reserva creada correctamente · ' . ($this->reservation->code ?? ('#' . $this->reservation->id)),
        );
    }

    // Vista + datos
    public function content(): Content
    {
        return new Content(
            view: 'emails.reservation_confirmed',
            with: [
                'reservation' => $this->reservation->loadMissing(['user', 'property']),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
