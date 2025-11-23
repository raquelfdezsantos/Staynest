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
        
        // Detectar propiedad desde el parámetro property
        $propertySlug = $request->input('property');
        
        // Si no viene el parámetro, intentar extraer de la URL intended
        if (!$propertySlug) {
            $intendedUrl = $request->session()->get('url.intended', '');
            if ($intendedUrl && preg_match('/\/propiedades\/([^\/]+)/', $intendedUrl, $matches)) {
                $propertySlug = $matches[1];
            }
        }
        
        // Para admin: verificar si es su propiedad o no
        if ($user->role === 'admin') {
            // Si viene desde una propiedad específica, verificar ownership
            if ($propertySlug) {
                $property = \App\Models\Property::where('slug', $propertySlug)->whereNull('deleted_at')->first();
                
                // Si es su propiedad, ir al panel admin
                if ($property && $property->user_id === $user->id) {
                    return redirect()->intended(route('admin.dashboard'));
                }
                
                // Si no es su propiedad, tratarlo como cliente y redirigir a mis-reservas de esa propiedad
                return redirect(route('properties.reservas.index', $propertySlug));
            }
            
            // Si no viene desde una propiedad, ir al panel admin
            return redirect()->intended(route('admin.dashboard'));
        }
        
        // Para clientes
        // Redirigir a mis-reservas de la propiedad detectada
        if ($propertySlug) {
            // Verificar si hay una URL intended que sea de esta misma propiedad
            $intendedUrl = $request->session()->get('url.intended', '');
            if ($intendedUrl && str_contains($intendedUrl, "/propiedades/{$propertySlug}")) {
                return redirect($intendedUrl);
            }
            // Si no hay intended o no es de esta propiedad, ir a mis-reservas
            return redirect(route('properties.reservas.index', $propertySlug));
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
