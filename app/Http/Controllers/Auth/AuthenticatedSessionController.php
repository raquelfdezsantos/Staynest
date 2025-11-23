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
        
        // Detectar propiedad: prioridad query, luego sesión, luego URL intended
        $propertySlug = $request->input('property')
            ?: session('current_property_slug');

        if (!$propertySlug) {
            $intendedUrl = $request->session()->get('url.intended', '');
            if ($intendedUrl && preg_match('/\/propiedades\/([^\/]+)/', $intendedUrl, $matches)) {
                $propertySlug = $matches[1];
            }
        }
        
        // Para admin: verificar si es su propiedad o no
        if ($user->role === 'admin') {
            if ($propertySlug) {
                $property = \App\Models\Property::where('slug', $propertySlug)->whereNull('deleted_at')->first();
                if ($property) {
                    session(['current_property_slug' => $property->slug]);
                    $intendedUrl = $request->session()->get('url.intended', '');
                    if ($intendedUrl && str_contains($intendedUrl, "/propiedades/{$property->slug}")) {
                        return redirect($intendedUrl);
                    }
                    // Si el admin ES propietario de la propiedad del contexto -> dashboard específico
                    if ($property->user_id === $user->id) {
                        return redirect()->route('admin.property.dashboard', $property->slug);
                    }
                    // Admin no propietario (visitando otra propiedad): permanecer en vista pública de esa propiedad
                    return redirect()->route('properties.show', $property->slug);
                }
            }
            // Sin slug válido: si tiene alguna propiedad propia, usar la primera de él como contexto
            $ownProperty = \App\Models\Property::where('user_id', $user->id)->whereNull('deleted_at')->first();
            if ($ownProperty) {
                session(['current_property_slug' => $ownProperty->slug]);
                return redirect()->route('admin.property.dashboard', $ownProperty->slug);
            }
            return redirect()->intended(route('home'));
        }
        
        // Para clientes
        // Redirigir a mis-reservas de la propiedad detectada
        if ($propertySlug) {
            $property = \App\Models\Property::where('slug', $propertySlug)->whereNull('deleted_at')->first();
            if ($property) {
                session(['current_property_slug' => $property->slug]);
                $intendedUrl = $request->session()->get('url.intended', '');
                if ($intendedUrl && str_contains($intendedUrl, "/propiedades/{$property->slug}")) {
                    return redirect($intendedUrl);
                }
                // Ficha pública como aterrizaje consistente
                return redirect()->route('properties.show', $property);
            }
        }
        
        // Si no hay propiedad detectada, ir a la URL intended o home
        return redirect()->intended(route('home'));
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
