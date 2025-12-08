<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

/**
 * Middleware que configura proxies confiables.
 */
class TrustProxies extends Middleware
{
    /**
     * @var array<int, string>|string|null
     */
    protected $proxies = '*';

    /**
     * @var int
     */
    protected $headers = Request::HEADER_X_FORWARDED_ALL;
}
