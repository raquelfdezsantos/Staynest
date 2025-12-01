<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

/**
 * Mailable para notificar al usuario sobre una devolución emitida.
 *
 * Envía un correo con los datos de la reserva, el importe devuelto y la factura si existe.
 */
class PaymentRefundIssuedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Constructor del mailable.
     *
     * @param mixed $reservation Instancia de la reserva.
     * @param mixed $refund Importe devuelto.
     * @param mixed|null $invoice Factura asociada (opcional).
     */
    public function __construct(
        public $reservation,
        public $refund,
        public $invoice = null
    ) {}

    /**
     * Define el sobre del correo (asunto, destinatario, etc).
     *
     * @return Envelope Sobre del correo con el asunto personalizado.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Devolución emitida · Reserva ' . ($this->reservation->code ?? ('#' . $this->reservation->id)) . ' (' . now()->format('d/m/Y H:i:s') . ')',
        );
    }

    /**
     * Define el contenido del correo (vista y datos).
     *
     * @return Content Contenido del correo con la vista, datos de la reserva, importe y factura.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.payment_refund_issued',
            with: [
                'reservation' => $this->reservation->loadMissing(['user','property']),
                'refund' => $this->refund,
                'invoice' => $this->invoice,
            ],
        );
    }

    /**
     * Define los archivos adjuntos del correo (PDF de la factura si existe).
     *
     * @return array Lista de adjuntos (puede incluir la factura en PDF).
     */
    public function attachments(): array
    {
        $attachments = [];

        if ($this->invoice) {
            if (!empty($this->invoice->pdf_path)) {
                $attachments[] = Attachment::fromStorage($this->invoice->pdf_path)
                    ->as($this->invoice->number . '.pdf')
                    ->withMime('application/pdf');
            } else {
                $pdf = PDF::loadView('invoices.pdf', ['invoice' => $this->invoice]);
                $attachments[] = Attachment::fromData(fn () => $pdf->output(), $this->invoice->number . '.pdf')
                    ->withMime('application/pdf');
            }
        }

        return $attachments;
    }
}
