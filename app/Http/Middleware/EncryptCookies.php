<?php

namespace App\Http\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;

/**
 * Middleware para encriptar cookies.
 */
class EncryptCookies extends Middleware
{
    /**
     * @var array<int, string>
     */
    protected $except = [
        //
    ];
}
