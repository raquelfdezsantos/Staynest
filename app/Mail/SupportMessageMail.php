<?php 

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Mailable para enviar mensajes de soporte.
 */
class SupportMessageMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Datos del formulario.
     *
     * @var array
     */
    public array $data;

    /**
     * Crea una nueva instancia del mailable.
     *
     * @param array $data Datos del formulario
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Construye el mensaje de correo.
     *
     * @return $this Instancia configurada
     */
    public function build()
    {
        $subject = $this->data['subject'] ?? 'Nueva solicitud de soporte';
        return $this->subject($subject)
            ->view('emails.support')
            ->with(['data' => $this->data]);
    }
}
