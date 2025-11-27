<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Http\Middleware\EnsureUserHasRole;
use Illuminate\Support\Facades\View;

/**
 * Provider AppServiceProvider
 *
 * Registra servicios y configuraciones globales de la aplicación.
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Registra bindings y servicios en el contenedor de la aplicación.
     *
     * @return void
     */
    public function register(): void
    {
        //
    }

    /**
     * Realiza configuraciones y registros al iniciar la aplicación.
     *
     * @return void
     */
    public function boot(): void
    {
        // Registro explícito del alias 'role' por si el Kernel no se carga 
        $this->app['router']->aliasMiddleware('role', EnsureUserHasRole::class);

        // Compartir la propiedad con el componente de navegación
        View::composer('components.nav-public', function ($view) {
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
