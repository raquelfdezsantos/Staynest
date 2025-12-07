<?php 

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Request para validar formulario de contacto.
 */
class ContactRequest extends FormRequest {
    /**
     * Obtiene reglas de validación.
     *
     * @return array
     */
    public function rules(): array {
        return [
            'name' => [
                'required', 
                'string', 
                'max:120', 
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚüÜñÑ ]+$/u',
                function ($attribute, $value, $fail) {
                    if (preg_match('/<[^>]*>/', $value)) {
                        $fail('El nombre contiene caracteres HTML no permitidos.');
                    }
                    if (preg_match('/[0-9]/', $value)) {
                        $fail('El nombre no puede contener números.');
                    }
                }
            ],
            'email' => ['required', 'email:rfc', 'max:255'],
            'message' => [
                'required', 
                'string', 
                'min:10', 
                'max:2000',
                function ($attribute, $value, $fail) {
                    // Rechazar HTML/scripts
                    if (preg_match('/<[^>]*>/', $value) || preg_match('/&lt;|&gt;/', $value)) {
                        $fail('El mensaje contiene código HTML o scripts no permitidos.');
                    }
                    // Solo permitir caracteres seguros
                    if (!preg_match('/^[a-zA-Z0-9áéíóúÁÉÍÓÚüÜñÑ .,;:!?¿¡()\'\"\-\n\r]+$/u', $value)) {
                        $fail('El mensaje contiene caracteres no permitidos.');
                    }
                }
            ],
            // anti-spam sencillo (honeypot)
            'website' => ['nullable', 'size:0'],
        ];
    }

    /**
     * Mensajes personalizados de validación.
     *
     * @return array
     */
    public function messages(): array {
        return [
            'name.regex' => 'El nombre solo puede contener letras y espacios.',
            'message.min' => 'El mensaje debe tener al menos 10 caracteres.',
            'message.regex' => 'El mensaje contiene caracteres no permitidos. Solo se permiten letras, números y puntuación básica.',
        ];
    }
    /**
     * Determina autorización.
     *
     * @return bool
     */
    public function authorize(): bool { return true; }
}
