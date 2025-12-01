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
            'name'    => ['required', 'string', 'max:100'],
            'email'   => ['required', 'email', 'max:150'],
            'subject' => ['required', 'string', 'max:150'],
            'message' => ['required', 'string', 'max:2000'],
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'name.max' => 'El nombre no puede superar los 100 caracteres.',
            'email.required' => 'El email es obligatorio.',
            'email.email' => 'Por favor, introduce un email válido.',
            'email.max' => 'El email no puede superar los 150 caracteres.',
            'subject.required' => 'El asunto es obligatorio.',
            'subject.max' => 'El asunto no puede superar los 150 caracteres.',
            'message.required' => 'El mensaje es obligatorio.',
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
