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
        return [
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
        ];
    }
}
