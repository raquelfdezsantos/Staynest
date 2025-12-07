<?php 

namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Mailable para enviar mensajes de contacto.
 */
class ContactMessageMail extends Mailable {
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
    public function __construct(array $data){ $this->data = $data; }

    /**
     * Construye el mensaje de correo.
     *
     * @return $this Instancia configurada
     */
    public function build(){
        $subject = $this->data['subject'] ?? 'Nueva consulta desde la web';
        return $this->subject($subject)
            ->view('emails.contact')
            ->with(['data' => $this->data]);
    }
}