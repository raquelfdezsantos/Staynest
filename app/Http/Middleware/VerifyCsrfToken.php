<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

/**
 * Middleware que verifica los tokens CSRF en las solicitudes entrantes.
 *
 * Permite definir rutas exentas de la verificación CSRF para casos especiales.
 */
class VerifyCsrfToken extends Middleware
{
    /**
     * URIs exentas de la verificación CSRF.
     *
     * @var array<int, string> Lista de rutas excluidas de la protección CSRF.
     */
    protected $except = [
        'login',
    ];
}
