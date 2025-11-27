<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;


/**
 * Controlador para la confirmación de contraseña.
 *
 * Permite mostrar la vista de confirmación y validar la contraseña del usuario antes de realizar acciones sensibles.
 */
class ConfirmablePasswordController extends Controller
{
    /**
     * Muestra la vista para confirmar la contraseña.
     *
     * @return View Vista de confirmación de contraseña.
     */
    public function show(): View
    {
        return view('auth.confirm-password');
    }

    /**
     * Valida la contraseña del usuario para confirmar su identidad.
     *
     * @param Request $request Solicitud HTTP con la contraseña a validar.
     * @return RedirectResponse Redirección al dashboard si la contraseña es correcta.
     * @throws ValidationException Si la contraseña es incorrecta.
     */
    public function store(Request $request): RedirectResponse
    {
        if (! Auth::guard('web')->validate([
            'email' => $request->user()->email,
            'password' => $request->password,
        ])) {
            throw ValidationException::withMessages([
                'password' => __('auth.password'),
            ]);
        }

        $request->session()->put('auth.password_confirmed_at', time());

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
