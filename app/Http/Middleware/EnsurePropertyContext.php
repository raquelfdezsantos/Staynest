<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Property;

/**
 * Middleware para asegurar el contexto de propiedad en la sesión y las vistas.
 *
 * Establece la propiedad actual según la sesión, la query o por defecto, y la comparte con las vistas.
 */
class EnsurePropertyContext
{
    /**
     * Maneja la verificación y establecimiento del contexto de propiedad.
     *
     * Busca la propiedad en sesión, query o por defecto y la comparte con las vistas.
     * Redirige si es necesario para establecer la URL canónica.
     *
     * @param Request $request Solicitud HTTP.
     * @param Closure $next Siguiente middleware o controlador.
     * @return mixed Respuesta HTTP o redirección.
     */
    public function handle(Request $request, Closure $next)
    {
        // Si ya hay contexto en sesión, intentar cargarlo
        $currentSlug = session('current_property_slug');
        $property = null;

        if ($currentSlug) {
            $property = Property::where('slug', $currentSlug)->whereNull('deleted_at')->first();
            // Si se borró o ya no existe, limpiar y forzar nuevo contexto
            if (!$property) {
                session()->forget('current_property_slug');
                $currentSlug = null;
            }
        }

        // Si no hay propiedad en sesión y llega ?property=slug (primer acceso o cambio explícito)
        if (!$property && $request->query('property')) {
            $candidate = Property::where('slug', $request->query('property'))
                ->whereNull('deleted_at')
                ->first();
            if ($candidate) {
                $property = $candidate;
                session(['current_property_slug' => $candidate->slug]);
            }
        }

        // Si seguimos sin propiedad y estamos en root y no hay contexto, usar la por defecto
        if (!$property && $request->is('/')) {
            $property = Property::where('slug', 'piso-turistico-centro')
                ->whereNull('deleted_at')
                ->first();
            if (!$property) {
                $property = Property::whereNull('deleted_at')->first();
            }
            if ($property) {
                session(['current_property_slug' => $property->slug]);
                // Redirigir a su ficha para establecer URL canónica
                return redirect()->route('properties.show', $property);
            }
        }

        // Compartir la propiedad con las vistas si existe
        if ($property) {
            view()->share('currentProperty', $property);
        }

        return $next($request);
    }
}
