<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Mailable para notificar al usuario sobre la modificación de la reserva y devolución pendiente.
 *
 * Envía un correo con los datos de la reserva modificada, el nuevo total y el importe a devolver.
 */
class ReservationModifiedRefundPendingMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Constructor del mailable.
     *
     * @param mixed $reservation Instancia de la reserva modificada.
     * @param mixed $newTotal Nuevo importe total de la reserva.
     * @param mixed $refundAmount Importe pendiente de devolución.
     */
    public function __construct(
        public $reservation,
        public $newTotal,
        public $refundAmount
    ) {}

    /**
     * Define el sobre del correo (asunto, destinatario, etc).
     *
     * @return Envelope Sobre del correo con el asunto personalizado.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reserva ' . ($this->reservation->code ?? ('#' . $this->reservation->id)) . ' modificada - Devolución pendiente',
        );
    }

    /**
     * Define el contenido del correo (vista y datos).
     *
     * @return Content Contenido del correo con la vista, datos de la reserva, nuevo total y devolución.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.reservation_modified_refund_pending',
            with: [
                'reservation' => $this->reservation->loadMissing(['user', 'property']),
                'newTotal' => $this->newTotal,
                'refundAmount' => $this->refundAmount,
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
