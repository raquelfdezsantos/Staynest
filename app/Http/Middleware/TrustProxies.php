<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

/**
 * Middleware que configura los proxies confiables para la aplicación.
 *
 * Permite definir las IPs y cabeceras que la aplicación debe considerar como confiables para proxies.
 */
class TrustProxies extends Middleware
{
    /**
     * Direcciones IP de los proxies confiables.
     *
     * @var array<int, string>|string|null IPs o rangos de proxies confiables.
     */
    protected $proxies;

    /**
     * Cabeceras utilizadas para detectar proxies confiables.
     *
     * @var int Máscara de cabeceras HTTP para proxies.
     */
    protected $headers =
        Request::HEADER_X_FORWARDED_FOR |
        Request::HEADER_X_FORWARDED_HOST |
        Request::HEADER_X_FORWARDED_PORT |
        Request::HEADER_X_FORWARDED_PROTO |
        Request::HEADER_X_FORWARDED_AWS_ELB;
}
