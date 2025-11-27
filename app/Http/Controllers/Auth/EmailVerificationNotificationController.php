<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;


/**
 * Controlador para el envío de notificaciones de verificación de correo electrónico.
 *
 * Permite enviar un nuevo correo de verificación al usuario si aún no ha verificado su email.
 */
class EmailVerificationNotificationController extends Controller
{
    /**
     * Envía una nueva notificación de verificación de correo electrónico.
     *
     * @param Request $request Solicitud HTTP del usuario autenticado.
     * @return RedirectResponse Redirección al dashboard si el email ya está verificado, o de vuelta con estado si se envía el enlace.
     */
    public function store(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false));
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    }
}
