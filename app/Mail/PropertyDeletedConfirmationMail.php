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
 * Mailable para confirmar baja de propiedad.
 */
class PropertyDeletedConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Nombre de la propiedad.
     * @var string
     */
    public $propertyName;
    /**
     * Número de reservas canceladas.
     * @var int
     */
    public $cancelledReservations;
    /**
     * Total reembolsado.
     * @var float
     */
    public $totalRefunded;

    /**
     * Crea una nueva instancia del mailable.
     *
     * @param string $propertyName Nombre de la propiedad
     * @param int $cancelledReservations Número de reservas canceladas
     * @param float $totalRefunded Total reembolsado
     */
    public function __construct(string $propertyName, int $cancelledReservations, float $totalRefunded)
    {
        $this->propertyName = $propertyName;
        $this->cancelledReservations = $cancelledReservations;
        $this->totalRefunded = $totalRefunded;
    }

    /**
     * Define el sobre del correo.
     *
     * @return \Illuminate\Mail\Mailables\Envelope Sobre del correo
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Confirmación: Propiedad dada de baja',
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
            view: 'emails.property-deleted-confirmation',
        );
    }

    /**
     * Define los adjuntos del correo.
     *
     * @return array Lista de adjuntos
     */
    public function attachments(): array
    {
        return [];
    }
}
