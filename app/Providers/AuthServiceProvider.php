<?php

namespace App\Providers;

use App\Models\Reservation;
use App\Policies\ReservationPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

/**
 * Provider para autenticación y policies.
 */
class AuthServiceProvider extends ServiceProvider
{
    /**
     * Array de policies asociadas a los modelos.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Reservation::class => ReservationPolicy::class,
    ];

    /**
     * Registra policies y Gates personalizados.
     *
     * @return void
     */
    public function boot(): void
    {
        // Si más adelante se definen Gates personalizados, irían aquí.
    }
}

