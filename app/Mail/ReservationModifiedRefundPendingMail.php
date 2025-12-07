<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

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
     * @param mixed $invoice Factura actualizada (opcional).
     * @param bool $isAdmin Si el email se envía al admin de la propiedad.
     */
    public function __construct(
        public $reservation,
        public $newTotal,
        public $refundAmount,
        public $invoice = null,
        public bool $isAdmin = false
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
                'invoice' => $this->invoice,
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
