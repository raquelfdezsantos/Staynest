<?php 

namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Mailable para gestionar el envío de mensajes de contacto desde la web.
 *
 * Envía un correo con los datos del formulario de contacto al equipo de soporte.
 */
class ContactMessageMail extends Mailable {
    use Queueable, SerializesModels;

    /**
     * Datos del formulario de contacto.
     *
     * @var array
     */
    public array $data;

    /**
     * Constructor del mailable.
     *
     * @param array $data Datos del formulario de contacto.
     */
    public function __construct(array $data){ $this->data = $data; }

    /**
     * Construye el mensaje de correo electrónico.
     *
     * Define el asunto, la vista y los datos del mensaje.
     *
     * @return $this Instancia del mailable configurado.
     */
    public function build(){
        $subject = $this->data['subject'] ?? 'Nueva consulta desde la web';
        return $this->subject($subject)
            ->view('emails.contact')
            ->with(['data' => $this->data]);
    }
}