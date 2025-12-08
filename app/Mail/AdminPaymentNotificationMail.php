<?php

namespace App\Mail;

use App\Models\Reservation;
use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailables\Attachment;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

/**
 * Mailable para notificar pago recibido.
 */
class AdminPaymentNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param \App\Models\Reservation $reservation
     * @param \App\Models\Invoice $invoice
     */
    public function __construct(
        public Reservation $reservation,
        public Invoice $invoice
    ) {}

    /**
     * Define sobre del correo.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pago recibido · ' . $this->invoice->number
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
            view: 'emails.admin_payment_notification',
            with: [
                'reservation' => $this->reservation->loadMissing(['user','property']),
                'invoice'     => $this->invoice,
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
