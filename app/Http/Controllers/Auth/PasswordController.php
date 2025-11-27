<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;


/**
 * Controlador para la actualización de la contraseña del usuario.
 *
 * Permite validar y actualizar la contraseña actual del usuario autenticado.
 */
class PasswordController extends Controller
{
    /**
     * Actualiza la contraseña del usuario autenticado.
     *
     * @param Request $request Solicitud HTTP con los datos de la contraseña actual y la nueva.
     * @return RedirectResponse Redirección de vuelta con estado tras la actualización.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ], [
            'current_password.required' => 'La contraseña actual es obligatoria.',
            'current_password.current_password' => 'La contraseña actual es incorrecta.',
            'password.required' => 'La nueva contraseña es obligatoria.',
            'password.min' => 'La nueva contraseña debe tener al menos :min caracteres.',
            'password.confirmed' => 'La confirmación de la nueva contraseña no coincide.',
        ], [
            'current_password' => 'contraseña actual',
            'password' => 'nueva contraseña',
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('status', 'password-updated');
    }
}
