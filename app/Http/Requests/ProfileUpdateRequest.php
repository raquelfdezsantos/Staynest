<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Request para validar actualización de perfil.
 */
class ProfileUpdateRequest extends FormRequest
{
    /**
     * Obtiene reglas de validación.
     *
     * @return array
     */
    public function rules(): array
    {
        $rules = [
            'name' => [
                'required', 
                'string', 
                'max:255',
                function ($attribute, $value, $fail) {
                    if (preg_match('/<[^>]*>/', $value)) {
                        $fail('El nombre contiene caracteres HTML no permitidos.');
                    }
                    if (preg_match('/[0-9]/', $value)) {
                        $fail('El nombre no puede contener números.');
                    }
                    // Permitir letras (incluidas tildes y ñ) y espacios
                    if (!preg_match('/^[\p{L}\s]+$/u', $value)) {
                        $fail('El nombre solo puede contener letras y espacios.');
                    }
                }
            ],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email:rfc',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'avatar' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
                'dimensions:min_width=100,min_height=100,max_width=4000,max_height=4000'
            ],
            'address' => [
                'nullable', 
                'string', 
                'max:255',
                function ($attribute, $value, $fail) {
                    if ($value && preg_match('/<[^>]*>/', $value)) {
                        $fail('La dirección contiene caracteres HTML no permitidos.');
                    }
                    // Permitir letras, números, espacios y caracteres comunes en direcciones
                    if ($value && !preg_match('/^[\p{L}\p{N}\s.,ºª\-]+$/u', $value)) {
                        $fail('La dirección contiene caracteres no permitidos.');
                    }
                }
            ],
            'document_id' => [
                'nullable', 
                'string', 
                'max:50',
                function ($attribute, $value, $fail) {
                    if ($value && !preg_match('/^[\p{L}0-9\-]+$/u', $value)) {
                        $fail('El NIF/CIF solo puede contener letras, números y guiones.');
                    }
                }
            ],
            'phone' => [
                'nullable', 
                'string', 
                'max:30',
                function ($attribute, $value, $fail) {
                    if ($value && !preg_match('/^[0-9\s\+\-\(\)]+$/', $value)) {
                        $fail('El teléfono solo puede contener números, espacios y los símbolos + - ( )');
                    }
                }
            ],
            'birth_date' => ['nullable', 'date', 'before:today', 'after:' . now()->subYears(120)->toDateString()],
        ];

        // Campo adicional solo para administradores
        if ($this->user()->role === 'admin') {
            $rules['payment_method'] = ['nullable', 'string', 'in:stripe,bank_transfer,paypal'];
        }

        return $rules;
    }

    /**
     * Obtiene mensajes personalizados de validación.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'name.max' => 'El nombre no puede tener más de :max caracteres.',
            'name.regex' => 'El nombre solo puede contener letras y espacios.',
            'email.required' => 'El email es obligatorio.',
            'email.email' => 'Por favor, introduce un email válido.',
            'email.unique' => 'Este email ya está registrado.',
            'avatar.image' => 'El archivo debe ser una imagen.',
            'avatar.mimes' => 'La imagen debe ser JPG, PNG o WEBP.',
            'avatar.max' => 'La imagen no puede pesar más de 2MB.',
            'avatar.dimensions' => 'La imagen debe tener entre 100x100 y 4000x4000 píxeles.',
            'address.max' => 'La dirección no puede superar :max caracteres.',
            'address.regex' => 'La dirección contiene caracteres no permitidos.',
            'document_id.max' => 'El NIF/CIF no puede superar :max caracteres.',
            'document_id.regex' => 'El NIF/CIF solo puede contener letras, números y guiones.',
            'phone.max' => 'El teléfono no puede superar :max caracteres.',
            'phone.regex' => 'El teléfono solo puede contener números, espacios y los símbolos + - ( )',
            'birth_date.date' => 'La fecha de nacimiento debe ser una fecha válida.',
            'birth_date.before' => 'Debes haber nacido antes de hoy.',
            'birth_date.after' => 'La fecha de nacimiento no es válida.',
            'payment_method.in' => 'El método de cobro seleccionado no es válido.',
        ];
    }

    /**
     * Obtiene nombres personalizados de atributos.
     *
     * @return array
     */
    public function attributes(): array
    {
        return [
            'name' => 'nombre',
            'email' => 'email',
            'avatar' => 'foto de perfil',
            'address' => 'dirección',
            'document_id' => 'NIF/CIF',
            'phone' => 'teléfono',
            'birth_date' => 'fecha de nacimiento',
            'payment_method' => 'método de cobro',
        ];
    }
}
