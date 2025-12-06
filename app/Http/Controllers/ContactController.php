<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Mail\ContactMessageMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Models\Property;

/**
 * Controlador para el formulario de contacto.
 *
 * Permite mostrar el formulario y procesar el envío de mensajes de contacto asociados a una propiedad.
 */
class ContactController extends Controller
{
    /**
     * Muestra el formulario de contacto para una propiedad específica.
     *
     * @param Property $property Propiedad asociada al mensaje de contacto.
     * @return \Illuminate\Contracts\View\View
     */
    public function create(Property $property)
    {
        return view('contact.form', compact('property'));
    }

    /**
     * Procesa el envío del formulario de contacto y envía el mensaje por email al administrador.
     *
     * @param Property $property Propiedad asociada al mensaje de contacto.
     * @param Request $request Solicitud HTTP con los datos del formulario.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Property $property, Request $request)
    {
        $data = $request->validate([
            'name'    => [
                'required', 
                'string', 
                'max:100',
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
            'email'   => ['required', 'email:rfc', 'max:150'],
            'subject' => [
                'required', 
                'string', 
                'max:150',
                function ($attribute, $value, $fail) {
                    if (preg_match('/<[^>]*>/', $value) || preg_match('/&lt;|&gt;/', $value)) {
                        $fail('El asunto contiene código HTML no permitido.');
                    }
                    // Permitir letras, números y puntuación básica
                    if (!preg_match('/^[\p{L}\p{N}\s.,;:!?¿¡()\-]+$/u', $value)) {
                        $fail('El asunto contiene caracteres no permitidos.');
                    }
                }
            ],
            'message' => [
                'required', 
                'string',
                'min:10',
                'max:2000',
                function ($attribute, $value, $fail) {
                    if (preg_match('/<[^>]*>/', $value) || preg_match('/&lt;|&gt;/', $value)) {
                        $fail('El mensaje contiene código HTML o scripts no permitidos.');
                    }
                    // Permitir letras, números, espacios, puntuación y saltos de línea
                    if (!preg_match('/^[\p{L}\p{N}\s.,;:!?¿¡()\'\"\-\n\r]+$/u', $value)) {
                        $fail('El mensaje contiene caracteres no permitidos.');
                    }
                }
            ],
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'name.max' => 'El nombre no puede superar los 100 caracteres.',
            'email.required' => 'El email es obligatorio.',
            'email.email' => 'Por favor, introduce un email válido.',
            'email.max' => 'El email no puede superar los 150 caracteres.',
            'subject.required' => 'El asunto es obligatorio.',
            'subject.max' => 'El asunto no puede superar los 150 caracteres.',
            'message.required' => 'El mensaje es obligatorio.',
            'message.min' => 'El mensaje debe tener al menos 10 caracteres.',
            'message.max' => 'El mensaje no puede superar los 2000 caracteres.',
        ]);

        // Agregar el nombre de la propiedad a los datos para mostrarlo en el email
        $data['property_name'] = $property->name;

        // Enviar el email al propietario de la propiedad
        Mail::to($property->user->email)
            ->send(new ContactMessageMail($data));

        return back()->with('success', '¡Gracias! Te responderemos pronto.');
    }
}
