<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\TrimStrings as Middleware;

/**
 * Middleware que recorta los espacios en blanco de los campos de entrada.
 *
 * Aplica el recorte a todos los campos excepto los especificados en $except.
 */
class TrimStrings extends Middleware
{
    /**
     * Lista de campos que no deben ser recortados por el middleware.
     *
     * @var array<int, string> Nombres de campos excluidos del recorte.
     */
    protected $except = [
        'current_password',
        'password',
        'password_confirmation',
    ];
}
