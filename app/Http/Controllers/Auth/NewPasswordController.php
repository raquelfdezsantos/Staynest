<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;


/**
 * Controlador para el restablecimiento de contraseña.
 *
 * Permite mostrar la vista de restablecimiento y procesar la solicitud para establecer una nueva contraseña.
 */
class NewPasswordController extends Controller
{
    /**
     * Muestra la vista para restablecer la contraseña.
     *
     * @param Request $request Solicitud HTTP con los datos necesarios para la vista.
     * @return View Vista de restablecimiento de contraseña.
     */
    public function create(Request $request): View
    {
        return view('auth.reset-password', ['request' => $request]);
    }

    /**
     * Procesa la solicitud para establecer una nueva contraseña.
     *
     * Valida los datos, intenta restablecer la contraseña y redirige según el resultado.
     *
     * @param Request $request Solicitud HTTP con los datos de restablecimiento.
     * @return RedirectResponse Redirección al login si el cambio es exitoso, o de vuelta con errores si falla.
     * @throws \Illuminate\Validation\ValidationException Si la validación de los datos falla.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Intentar restablecer la contraseña del usuario. Si es exitoso, se actualiza y se guarda en la base de datos.
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        // Si la contraseña se restablece correctamente, redirigir al login. Si hay error, volver con mensaje.
        return $status == Password::PASSWORD_RESET
                    ? redirect()->route('login')->with('status', __($status))
                    : back()->withInput($request->only('email'))
                        ->withErrors(['email' => __($status)]);
    }
}
