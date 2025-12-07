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
 * Mailable para notificar pago de diferencia completado.
 */
class PaymentBalanceSettledMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Crea una nueva instancia del mailable.
     *
     * @param \App\Models\Reservation $reservation Instancia de la reserva
     * @param float $amount Importe pagado
     * @param mixed|null $invoice Factura opcional
     */
    public function __construct(
        public $reservation,
        public $amount,
        public $invoice = null
    ) {}

    /**
     * Define el sobre del correo.
     *
     * @return \Illuminate\Mail\Mailables\Envelope Sobre del correo
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pago completado · Reserva ' . ($this->reservation->code ?? ('#' . $this->reservation->id)),
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
            view: 'emails.payment_balance_settled',
            with: [
                'reservation' => $this->reservation->loadMissing(['user','property']),
                'amount' => $this->amount,
                'invoice' => $this->invoice,
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
