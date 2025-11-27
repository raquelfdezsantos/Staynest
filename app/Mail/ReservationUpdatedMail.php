<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Mailable para notificar al usuario sobre la modificación de su reserva.
 *
 * Envía un correo con los datos de la reserva, el total anterior y la diferencia.
 */
class ReservationUpdatedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Constructor del mailable.
     *
     * @param \App\Models\Reservation $reservation Instancia de la reserva modificada.
     * @param float $previousTotal Importe total anterior.
     * @param float $difference Diferencia entre el total anterior y el nuevo.
     */
    public function __construct(
        public \App\Models\Reservation $reservation,
        public float $previousTotal = 0,
        public float $difference = 0
    ) {}

    /**
     * Define el sobre del correo (asunto, destinatario, etc).
     *
     * @return Envelope Sobre del correo con el asunto personalizado.
     */
    public function envelope(): Envelope
    {
    return new Envelope(subject: 'Tu reserva ha sido modificada (' . now()->format('d/m/Y H:i:s') . ')');
    }

    /**
     * Define el contenido del correo (vista y datos).
     *
     * @return Content Contenido del correo con la vista, datos de la reserva, total anterior y diferencia.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.reservation_updated',
            with: [
                'reservation' => $this->reservation->loadMissing(['user','property']),
                'previousTotal' => $this->previousTotal,
                'difference' => $this->difference
            ]
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
