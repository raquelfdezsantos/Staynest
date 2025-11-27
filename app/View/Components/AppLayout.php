<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

/**
 * Componente AppLayout
 *
 * Define la estructura base de la aplicación web.
 */
class AppLayout extends Component
{
    /**
     * Obtiene la vista que representa el componente de layout principal.
     *
     * @return View Vista del layout de la aplicación
     */
    public function render(): View
    {
        return view('layouts.app');
    }
}
