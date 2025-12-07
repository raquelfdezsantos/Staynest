<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance as Middleware;

/**
 * Middleware que previene solicitudes en mantenimiento.
 */
class PreventRequestsDuringMaintenance extends Middleware
{
    /**
     * @var array<int, string>
     */
    protected $except = [
        //
    ];
}
