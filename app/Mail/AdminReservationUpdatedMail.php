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
 * Mailable para notificar al administrador sobre la actualización de una reserva.
 *
 * Envía un correo informativo al administrador cuando una reserva es modificada.
 */
class AdminReservationUpdatedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Constructor del mailable.
     *
     * @param \App\Models\Reservation $reservation Instancia de la reserva modificada.
     * @param float $previousTotal Importe total anterior.
     * @param float $difference Diferencia entre el total anterior y el nuevo.
     * @param mixed|null $invoice Factura actualizada (opcional).
     */
    public function __construct(
        public \App\Models\Reservation $reservation,
        public float $previousTotal = 0,
        public float $difference = 0,
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
            subject: 'Reserva modificada en tu propiedad · #' . $this->reservation->id,
        );
    }

    /**
     * Define el contenido del correo (vista y datos).
     *
     * @return Content Contenido del correo con la vista correspondiente.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.admin_reservation_updated',
            with: [
                'reservation' => $this->reservation->loadMissing(['user', 'property']),
                'previousTotal' => $this->previousTotal,
                'difference' => $this->difference,
                'invoice' => $this->invoice
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
