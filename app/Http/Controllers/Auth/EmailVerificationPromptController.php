<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;


/**
 * Controlador para mostrar el aviso de verificación de correo electrónico.
 *
 * Muestra la vista de verificación si el usuario no ha verificado su email, o redirige al dashboard si ya está verificado.
 */
class EmailVerificationPromptController extends Controller
{
    /**
     * Muestra el aviso de verificación de correo electrónico.
     *
     * @param Request $request Solicitud HTTP del usuario autenticado.
     * @return RedirectResponse|View Redirección al dashboard si el email está verificado, o vista de verificación si no lo está.
     */
    public function __invoke(Request $request): RedirectResponse|View
    {
        return $request->user()->hasVerifiedEmail()
                    ? redirect()->intended(route('dashboard', absolute: false))
                    : view('auth.verify-email');
    }
}
