<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Http\Middleware\EnsureUserHasRole;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Registro explícito del alias 'role' por si el Kernel no se carga 
        $this->app['router']->aliasMiddleware('role', EnsureUserHasRole::class);

        // Compartir la propiedad con el componente de navegación
        \Illuminate\Support\Facades\View::composer('components.nav-public', function ($view) {
            $property = null;
            
            // Intentar obtener la propiedad desde la vista actual si existe
            if ($view->offsetExists('property')) {
                $property = $view->offsetGet('property');
            } 
            // Si estamos en cualquier ruta de propiedad, obtener desde la ruta
            else if (request()->route('property')) {
                $property = request()->route('property');
            }
            
            $view->with('property', $property);
        });
    }
}
