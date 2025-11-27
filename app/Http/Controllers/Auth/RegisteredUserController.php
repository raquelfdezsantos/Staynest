<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;


/**
 * Controlador para el registro de usuarios.
 *
 * Permite mostrar la vista de registro y procesar la solicitud de alta de nuevos usuarios.
 */
class RegisteredUserController extends Controller
{
    /**
     * Muestra la vista de registro de usuario.
     *
     * @return View Vista de registro de usuario.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Procesa la solicitud de registro de un nuevo usuario.
     *
     * Valida los datos, crea el usuario y lo autentica.
     *
     * @param Request $request Solicitud HTTP con los datos de registro.
     * @return RedirectResponse Redirección al dashboard tras el registro.
     * @throws \Illuminate\Validation\ValidationException Si la validación de los datos falla.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
