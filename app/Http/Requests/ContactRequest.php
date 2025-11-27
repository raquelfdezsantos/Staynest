<?php 

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Request para validar el formulario de contacto.
 *
 * Valida los campos requeridos y aplica honeypot para evitar spam.
 */
class ContactRequest extends FormRequest {
    /**
     * Obtiene las reglas de validación para el formulario de contacto.
     *
     * @return array Reglas de validación para nombre, email, mensaje y honeypot.
     */
    public function rules(): array {
        return [
            'name' => ['required','string','max:120'],
            'email' => ['required','email'],
            'message' => ['required','string','max:2000'],
            // anti-spam sencillo (honeypot)
            'website' => ['nullable','size:0'],
        ];
    }
    /**
     * Determina si el usuario está autorizado para enviar el formulario de contacto.
     *
     * @return bool Siempre true, permite el acceso.
     */
    public function authorize(): bool { return true; }
}
