<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware que redirige a los usuarios autenticados.
 *
 * Si el usuario ya está autenticado, lo redirige según su rol a la ruta correspondiente.
 */
class RedirectIfAuthenticated
{
    /**
     * Maneja la redirección de usuarios autenticados.
     *
     * Si el usuario accede a /login o /register estando autenticado, lo redirige a la ruta adecuada según su rol.
     *
     * @param Request $request Solicitud HTTP.
     * @param Closure $next Siguiente middleware o controlador.
     * @param string[] $guards Guardias de autenticación.
     * @return Response Redirección o continuación del request.
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        if ($user = $request->user()) {
        // Para diferenciar por rol:
        return $user->role === 'admin'
            ? redirect()->route('admin.dashboard')
            : redirect()->route('reservas.index'); 
    }

    return $next($request);
    }
}
