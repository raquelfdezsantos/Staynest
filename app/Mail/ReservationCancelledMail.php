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
 * Mailable para notificar cancelación de reserva.
 */
class ReservationCancelledMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Crea una nueva instancia del mailable.
     *
     * @param \App\Models\Reservation $reservation Instancia de la reserva
     * @param bool $isAdmin Si es para admin
     * @param mixed|null $invoice Factura opcional
     */
    public function __construct(
        public $reservation,
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
        return new Envelope(
            subject: 'Reserva cancelada · #' . $this->reservation->id . ' (' . now()->format('d/m/Y H:i:s') . ')',
        );
    }

    /**
     * Define el contenido del correo.
     *
     * @return \Illuminate\Mail\Mailables\Content Contenido del correo
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
     * Define los adjuntos del correo.
     *
     * @return array Lista de adjuntos
     */
    public function attachments(): array
    {
        $attachments = [];
        
        \Log::info('ReservationCancelledMail attachments - invoice:', [
            'has_invoice' => $this->invoice ? 'yes' : 'no',
            'invoice_id' => $this->invoice->id ?? null,
            'invoice_number' => $this->invoice->number ?? null,
        ]);
        
        if ($this->invoice) {
            if (!empty($this->invoice->pdf_path)) {
                $attachments[] = Attachment::fromStorage($this->invoice->pdf_path)
                    ->as($this->invoice->number . '.pdf')
                    ->withMime('application/pdf');
            } else {
                // Cargar relaciones necesarias para la vista PDF
                $invoice = $this->invoice->loadMissing(['reservation.user', 'reservation.property']);
                $pdf = PDF::loadView('invoices.pdf', ['invoice' => $invoice]);
                $attachments[] = Attachment::fromData(fn () => $pdf->output(), $this->invoice->number . '.pdf')
                    ->withMime('application/pdf');
                \Log::info('PDF generado dinámicamente para factura', ['invoice_number' => $this->invoice->number]);
            }
        }
        return $attachments;
    }
}
