<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;


/**
 * Controlador para la verificación de correo electrónico del usuario autenticado.
 *
 * Marca el correo como verificado y redirige al dashboard.
 */
class VerifyEmailController extends Controller
{
    /**
     * Marca el correo electrónico del usuario autenticado como verificado.
     *
     * @param EmailVerificationRequest $request Solicitud de verificación de correo.
     * @return RedirectResponse Redirección al dashboard con estado de verificación.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false).'?verified=1');
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return redirect()->intended(route('dashboard', absolute: false).'?verified=1');
    }
}
