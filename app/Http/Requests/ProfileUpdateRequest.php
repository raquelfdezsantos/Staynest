<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Request para validar la actualización de perfil de usuario.
 *
 * Valida los campos de nombre, email, avatar, dirección y documento identificativo.
 */
class ProfileUpdateRequest extends FormRequest
{
    /**
     * Obtiene las reglas de validación para la actualización de perfil.
     *
     * @return array Reglas de validación para los campos del perfil.
     */
    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'avatar' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048'
            ],
            'address' => ['nullable','string','max:255'],
            'document_id' => ['nullable','string','max:50'],
        ];

        // Campos adicionales solo para administradores
        if ($this->user()->role === 'admin') {
            $rules['phone'] = ['nullable', 'string', 'max:30'];
            $rules['birth_date'] = ['nullable', 'date', 'before:today'];
            $rules['payment_method'] = ['nullable', 'string', 'in:stripe,bank_transfer,paypal'];
        }

        return $rules;
    }

    /**
     * Obtiene los mensajes personalizados de validación para la actualización de perfil.
     *
     * @return array Mensajes de error personalizados.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'name.max' => 'El nombre no puede tener más de :max caracteres.',
            'email.required' => 'El email es obligatorio.',
            'email.email' => 'Por favor, introduce un email válido.',
            'email.unique' => 'Este email ya está registrado.',
            'avatar.image' => 'El archivo debe ser una imagen.',
            'avatar.mimes' => 'La imagen debe ser JPG, PNG o WEBP.',
            'avatar.max' => 'La imagen no puede pesar más de 2MB.',
            'address.max' => 'La dirección no puede superar :max caracteres.',
            'document_id.max' => 'El NIF/CIF no puede superar :max caracteres.',
            'phone.max' => 'El teléfono no puede superar :max caracteres.',
            'birth_date.date' => 'La fecha de nacimiento debe ser una fecha válida.',
            'birth_date.before' => 'Debes haber nacido antes de hoy.',
            'payment_method.in' => 'El método de cobro seleccionado no es válido.',
        ];
    }

    /**
     * Obtiene los nombres personalizados de los atributos para los errores de validación.
     *
     * @return array Nombres personalizados de los campos.
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
