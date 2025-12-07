<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * Request de validación de reservas.
 */
class StoreReservationRequest extends FormRequest
{
    /**
     * Determina autorización.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Solo los usuarios autenticados pueden realizar reservas.
        return Auth::check();
    }
    
    /**
     * Reglas de validación.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'property_id' => ['required', 'exists:properties,id'],
            'check_in'    => ['required', 'date', 'after_or_equal:today'],
            'check_out'   => ['required', 'date', 'after:check_in'],
            'guests'      => ['required', 'integer', 'min:1', 'max:4'], 
            // Desglose opcional: adultos, niños y mascotas (gratis)
            'adults'      => ['nullable', 'integer', 'min:0'],
            'children'    => ['nullable', 'integer', 'min:0'],
            'pets'        => ['nullable', 'integer', 'min:0'],
            // Notas del huésped
            'notes'       => ['nullable', 'string', 'max:1000', 'regex:/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s.,;:!?¿¡()\-]+$/u'],
        ];
    }

    /**
     * Mensajes de error personalizados.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'check_out.after' => 'La fecha de salida debe ser posterior a la de entrada.',
            'notes.regex' => 'Las notas contienen caracteres no permitidos. Solo se permiten letras, números y puntuación básica.',
        ];
    }

    /**
     * Prepara datos para validación.
     */
    protected function prepareForValidation(): void
    {
        if ($this->notes) {
            $this->merge([
                'notes' => strip_tags(trim($this->notes)),
            ]);
        }
    }
}
