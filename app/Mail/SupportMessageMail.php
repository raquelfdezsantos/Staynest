<?php 

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Mailable para gestionar el envío de mensajes de soporte desde la web.
 *
 * Envía un correo con los datos del formulario de soporte al equipo correspondiente.
 */
class SupportMessageMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Datos del formulario de soporte.
     *
     * @var array
     */
    public array $data;

    /**
     * Constructor del mailable.
     *
     * @param array $data Datos del formulario de soporte.
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Construye el mensaje de correo electrónico.
     *
     * Define el asunto, la vista y los datos del mensaje.
     *
     * @return $this Instancia del mailable configurado.
     */
    public function build()
    {
        $subject = $this->data['subject'] ?? 'Nueva solicitud de soporte';
        return $this->subject($subject)
            ->view('emails.support')
            ->with(['data' => $this->data]);
    }
}
