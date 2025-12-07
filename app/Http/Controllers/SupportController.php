<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\SupportMessageMail;
use App\Models\Property;

/**
 * Controlador para soporte y contacto.
 */
class SupportController extends Controller
{
    /**
     * Muestra formulario de soporte.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        // Usar contexto existente en sesión, solo aceptar query si no hay contexto
        $property = null;
        if (session('current_property_slug')) {
            $property = Property::where('slug', session('current_property_slug'))
                ->whereNull('deleted_at')
                ->first();
        } elseif ($request->query('property')) {
            $property = Property::where('slug', $request->query('property'))
                ->whereNull('deleted_at')
                ->first();
            if ($property) {
                session(['current_property_slug' => $property->slug]);
            }
        }
        return view('support.index', compact('property'));
    }

    /**
     * Procesa envío de formulario soporte.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
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
            ],
        [
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

        // Asociar propiedad actual si existe
        $property = null;
        if (session('current_property_slug')) {
            $property = Property::where('slug', session('current_property_slug'))
                ->whereNull('deleted_at')
                ->first();
        }
        if ($property) {
            $data['property_slug'] = $property->slug;
            $data['property_name'] = $property->name;
            $data['property_id'] = $property->id;
        }


        Mail::to(env('MAIL_SUPPORT', 'soporte@staynest.com'))
            ->send(new SupportMessageMail($data));

        return back()->with('success', '¡Gracias por contactar con soporte! Te responderemos lo antes posible.');
    }
}
