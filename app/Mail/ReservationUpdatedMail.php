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
 * Mailable para notificar modificación de reserva.
 */
class ReservationUpdatedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Crea una nueva instancia del mailable.
     *
     * @param \App\Models\Reservation $reservation Instancia de la reserva
     * @param float $previousTotal Importe total anterior
     * @param float $difference Diferencia de importe
     * @param bool $isAdmin Si es para admin
     * @param mixed|null $invoice Factura opcional
     */
    public function __construct(
        public \App\Models\Reservation $reservation,
        public float $previousTotal = 0,
        public float $difference = 0,
        public bool $isAdmin = false,
        public $invoice = null
    ) {}

    /**
     * Define el sobre del correo.
     *
     * @return \Illuminate\Mail\Mailables\Envelope Sobre del correo
     */
    public function envelope(): Envelope
    {
    return new Envelope(subject: 'Tu reserva ha sido modificada (' . now()->format('d/m/Y H:i:s') . ')');
    }

    /**
     * Define el contenido del correo.
     *
     * @return \Illuminate\Mail\Mailables\Content Contenido del correo
     */
    public function content(): Content
    {
        $view = $this->isAdmin ? 'emails.admin_reservation_updated' : 'emails.reservation_updated';
        
        return new Content(
            view: $view,
            with: [
                'reservation' => $this->reservation->loadMissing(['user','property']),
                'previousTotal' => $this->previousTotal,
                'difference' => $this->difference,
                'invoice' => $this->invoice
            ]
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
