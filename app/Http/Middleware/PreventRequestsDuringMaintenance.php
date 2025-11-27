<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance as Middleware;

/**
 * Middleware que previene solicitudes durante el modo de mantenimiento.
 *
 * Permite definir rutas que seguirán siendo accesibles aunque la aplicación esté en mantenimiento.
 */
class PreventRequestsDuringMaintenance extends Middleware
{
    /**
     * URIs que deben ser accesibles durante el modo de mantenimiento.
     *
     * @var array<int, string> Lista de rutas excluidas del bloqueo por mantenimiento.
     */
    protected $except = [
        //
    ];
}
