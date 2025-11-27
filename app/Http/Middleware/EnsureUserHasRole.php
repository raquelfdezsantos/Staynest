<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware que asegura que el usuario autenticado tenga un rol específico.
 *
 * Verifica el rol del usuario y muestra un error 403 si no cumple el requisito.
 */
class EnsureUserHasRole
{
    /**
     * Maneja la verificación del rol del usuario autenticado.
     *
     * Si el usuario no tiene el rol requerido, aborta con error 403.
     *
     * @param Request $request Solicitud HTTP.
     * @param Closure $next Siguiente middleware o controlador.
     * @param string $role Rol requerido para acceder.
     * @return Response Respuesta HTTP o error 403.
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();

        if (!$user || $user->role !== $role) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        return $next($request);
    }
}
