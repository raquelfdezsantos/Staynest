<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware que redirige usuarios autenticados.
 */
class RedirectIfAuthenticated
{
    /**
     * Maneja redirección de usuarios autenticados.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param string ...$guards
     * @return \Symfony\Component\HttpFoundation\Response
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
