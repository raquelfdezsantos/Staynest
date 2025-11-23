<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\SupportMessageMail;

class SupportController extends Controller
{
    public function index(Request $request)
    {
        // Usar contexto existente en sesión, solo aceptar query si no hay contexto
        $property = null;
        if (session('current_property_slug')) {
            $property = \App\Models\Property::where('slug', session('current_property_slug'))
                ->whereNull('deleted_at')
                ->first();
        } elseif ($request->query('property')) {
            $property = \App\Models\Property::where('slug', $request->query('property'))
                ->whereNull('deleted_at')
                ->first();
            if ($property) {
                session(['current_property_slug' => $property->slug]);
            }
        }
        return view('support.index', compact('property'));
    }

    public function store(Request $request)
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

        // Asociar propiedad actual si existe
        $property = null;
        if (session('current_property_slug')) {
            $property = \App\Models\Property::where('slug', session('current_property_slug'))
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
