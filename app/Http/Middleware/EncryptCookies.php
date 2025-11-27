<?php

namespace App\Http\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;

/**
 * Middleware para encriptar cookies.
 *
 * Se encarga de encriptar automáticamente todas las cookies de la aplicación, excepto las especificadas en $except.
 */
class EncryptCookies extends Middleware
{
    /**
     * Lista de cookies que no deben ser encriptadas por el middleware.
     *
     * @var array<int, string> Nombres de cookies excluidas de encriptación.
     */
    protected $except = [
        //
    ];
}
