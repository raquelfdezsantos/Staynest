<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentRefundIssuedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public $reservation,
        public $refund,
        public $invoice = null
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Devolución emitida · Reserva ' . ($this->reservation->code ?? ('#' . $this->reservation->id)) . ' (' . now()->format('d/m/Y H:i:s') . ')',
        );
    }

    /**
     * Get the message content definition.
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
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $attachments = [];

        if ($this->invoice && $this->invoice->pdf_path) {
            $attachments[] = \Illuminate\Mail\Mailables\Attachment::fromStorage($this->invoice->pdf_path)
                ->as($this->invoice->number . '.pdf')
                ->withMime('application/pdf');
        }

        return $attachments;
    }
}
