<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = $request->user();
        
        if ($user->role === 'admin') {
            return redirect()->intended(route('admin.dashboard'));
        }
        
        // Para clientes, detectar propiedad desde el parámetro property
        $propertySlug = $request->input('property');
        
        // Si no viene el parámetro, intentar extraer de la URL intended
        if (!$propertySlug) {
            $intendedUrl = $request->session()->get('url.intended', '');
            if ($intendedUrl && preg_match('/\/propiedades\/([^\/]+)/', $intendedUrl, $matches)) {
                $propertySlug = $matches[1];
            }
        }
        
        // Si no se detectó slug, usar la primera propiedad disponible
        if (!$propertySlug) {
            $property = \App\Models\Property::whereNull('deleted_at')->first();
            $propertySlug = $property ? $property->slug : null;
        }
        
        // Redirigir a mis-reservas de la propiedad detectada
        $target = $propertySlug 
            ? route('properties.reservas.index', $propertySlug)
            : route('home');

        return redirect($target);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
