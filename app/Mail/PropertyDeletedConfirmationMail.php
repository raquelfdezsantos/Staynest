<?php

namespace App\Mail;

use App\Models\Property;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Mailable para confirmar la baja de una propiedad.
 *
 * Envía un correo con el nombre de la propiedad, reservas canceladas y total reembolsado.
 */
class PropertyDeletedConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Nombre de la propiedad dada de baja.
     * @var string
     */
    public $propertyName;
    /**
     * Número de reservas canceladas.
     * @var int
     */
    public $cancelledReservations;
    /**
     * Total reembolsado por la baja.
     * @var float
     */
    public $totalRefunded;

    /**
     * Constructor del mailable.
     *
     * @param string $propertyName Nombre de la propiedad dada de baja.
     * @param int $cancelledReservations Número de reservas canceladas.
     * @param float $totalRefunded Total reembolsado por la baja.
     */
    public function __construct(string $propertyName, int $cancelledReservations, float $totalRefunded)
    {
        $this->propertyName = $propertyName;
        $this->cancelledReservations = $cancelledReservations;
        $this->totalRefunded = $totalRefunded;
    }

    /**
     * Define el sobre del correo (asunto, destinatario, etc).
     *
     * @return Envelope Sobre del correo con el asunto personalizado.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Confirmación: Propiedad dada de baja',
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
            view: 'emails.property-deleted-confirmation',
        );
    }

    /**
     * Define los archivos adjuntos del correo (ninguno en este caso).
     *
     * @return array Lista de adjuntos vacía.
     */
    public function attachments(): array
    {
        return [];
    }
}
