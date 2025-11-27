<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

/**
 * Componente GuestLayout
 *
 * Define la estructura base para vistas de usuarios invitados.
 */
class GuestLayout extends Component
{
    /**
     * Obtiene la vista que representa el componente de layout para invitados.
     *
     * @return View Vista del layout de invitados
     */
    public function render(): View
    {
        return view('layouts.guest');
    }
}
