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
 * Mailable para notificar modificación de reserva con devolución pendiente.
 */
class ReservationModifiedRefundPendingMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Crea una nueva instancia del mailable.
     *
     * @param \App\Models\Reservation $reservation Instancia de la reserva
     * @param float $newTotal Nuevo importe total
     * @param float $refundAmount Importe de devolución
     * @param mixed|null $invoice Factura opcional
     * @param bool $isAdmin Si es para admin
     */
    public function __construct(
        public $reservation,
        public $newTotal,
        public $refundAmount,
        public $invoice = null,
        public bool $isAdmin = false
    ) {}

    /**
     * Define el sobre del correo.
     *
     * @return \Illuminate\Mail\Mailables\Envelope Sobre del correo
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reserva ' . ($this->reservation->code ?? ('#' . $this->reservation->id)) . ' modificada - Devolución pendiente',
        );
    }

    /**
     * Define el contenido del correo.
     *
     * @return \Illuminate\Mail\Mailables\Content Contenido del correo
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
     * Define los adjuntos del correo.
     *
     * @return array Lista de adjuntos
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
