<?php

namespace App\Providers;

use App\Models\Reservation;
use App\Policies\ReservationPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

/**
 * Provider AuthServiceProvider
 *
 * Registra las policies de autorización para los modelos.
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
     * Método de arranque para registrar policies y Gates personalizados.
     *
     * @return void
     */
    public function boot(): void
    {
        // Si más adelante se definen Gates personalizados, irían aquí.
    }
}

