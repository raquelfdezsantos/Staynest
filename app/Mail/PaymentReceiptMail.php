<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Reservation;
use App\Models\Invoice;

/**
 * Mailable para enviar el recibo de pago al usuario.
 *
 * Envía un correo con los datos de la reserva y la factura asociada tras el pago.
 */
class PaymentReceiptMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Constructor del mailable.
     *
     * @param Reservation $reservation Instancia de la reserva pagada.
     * @param Invoice $invoice Instancia de la factura asociada.
     */
    public function __construct(
        public Reservation $reservation,
        public Invoice $invoice
    ) {}

    /**
     * Define el sobre del correo (asunto, destinatario, etc).
     *
     * @return Envelope Sobre del correo con el asunto personalizado.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pago confirmado · ' . $this->invoice->number,
        );
    }

    /**
     * Define el contenido del correo (vista y datos).
     *
     * @return Content Contenido del correo con la vista y datos de la reserva y factura.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.payment_receipt',
            with: [
                'reservation' => $this->reservation->loadMissing(['user','property']),
                'invoice'     => $this->invoice,
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
