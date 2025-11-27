<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * Request de validación de reservas.
 *
 * Contiene las reglas necesarias para validar las solicitudes de reserva:
 * fechas válidas, capacidad máxima, estancia mínima y disponibilidad del alojamiento.
 */
class StoreReservationRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para realizar la reserva.
     *
     * Solo permite reservas de usuarios autenticados.
     *
     * @return bool True si el usuario está autenticado.
     */
    public function authorize(): bool
    {
        // Solo los usuarios autenticados pueden realizar reservas.
        return Auth::check();
    }
    
    /**
     * Reglas de validación aplicadas a la solicitud de reserva.
     *
     * Valida fechas, capacidad, desglose de huéspedes y notas.
     *
     * @return array Reglas de validación para la reserva.
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
            'notes'       => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Mensajes de error personalizados para las reglas de validación.
     *
     * @return array Mensajes de error personalizados.
     */
    public function messages(): array
    {
        return [
            'check_out.after' => 'La fecha de salida debe ser posterior a la de entrada.',
        ];
    }
}
