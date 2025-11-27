<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

/**
 * Middleware que verifica si el usuario está autenticado.
 *
 * Redirige a la página de login si el usuario no está autenticado.
 */
class Authenticate extends Middleware
{
    /**
     * Determina la ruta a la que se debe redirigir si el usuario no está autenticado.
     *
     * @param Request $request Solicitud HTTP.
     * @return string|null Ruta de redirección o null si espera JSON.
     */
    protected function redirectTo(Request $request): ?string
    {
        if (! $request->expectsJson()) {
            return route('login'); // Breeze crea la ruta 'login'
        }

        return null;
    }
}
