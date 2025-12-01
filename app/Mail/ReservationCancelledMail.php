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
 * Mailable para notificar al usuario sobre la cancelación de una reserva.
 *
 * Envía un correo con los datos de la reserva cancelada.
 */
class ReservationCancelledMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Constructor del mailable.
     *
     * @param mixed $reservation Instancia de la reserva cancelada.
     * @param bool $isAdmin Si es true, usa la vista para admin.
     * @param mixed|null $invoice Factura rectificativa (opcional).
     */
    public function __construct(
        public $reservation,
        public bool $isAdmin = false,
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
            subject: 'Reserva cancelada · #' . $this->reservation->id . ' (' . now()->format('d/m/Y H:i:s') . ')',
        );
    }

    /**
     * Define el contenido del correo (vista y datos).
     *
     * @return Content Contenido del correo con la vista y datos de la reserva cancelada.
     */
    public function content(): Content
    {
        $view = $this->isAdmin ? 'emails.admin_reservation_cancelled' : 'emails.reservation_cancelled';
        
        return new Content(
            view: $view,
            with: [
                'reservation' => $this->reservation->loadMissing(['user','property']),
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
