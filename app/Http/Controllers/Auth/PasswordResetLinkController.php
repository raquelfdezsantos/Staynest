<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;


/**
 * Controlador para la solicitud de enlace de restablecimiento de contraseña.
 *
 * Permite mostrar la vista de solicitud y procesar el envío del enlace de restablecimiento al correo del usuario.
 */
class PasswordResetLinkController extends Controller
{
    /**
     * Muestra la vista para solicitar el enlace de restablecimiento de contraseña.
     *
     * @return View Vista de solicitud de restablecimiento.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Procesa la solicitud de envío de enlace de restablecimiento de contraseña.
     *
     * Valida el correo y envía el enlace si es correcto.
     *
     * @param Request $request Solicitud HTTP con el correo del usuario.
     * @return RedirectResponse Redirección con estado según el resultado del envío.
     * @throws \Illuminate\Validation\ValidationException Si la validación del correo falla.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // Se envía el enlace de restablecimiento de contraseña al usuario.
        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status == Password::RESET_LINK_SENT
                    ? back()->with('status', __($status))
                    : back()->withInput($request->only('email'))
                        ->withErrors(['email' => __($status)]);
    }
}
