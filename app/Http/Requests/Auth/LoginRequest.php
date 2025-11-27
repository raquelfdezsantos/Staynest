<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Request para gestionar el login de usuarios.
 *
 * Valida credenciales, controla el rate limit y personaliza los mensajes de error.
 */
class LoginRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para realizar la solicitud de login.
     *
     * @return bool Siempre true, permite el acceso.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Obtiene las reglas de validación para el login.
     *
     * @return array Reglas de validación para email y password.
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Obtiene los mensajes personalizados de validación para el login.
     *
     * @return array Mensajes de error personalizados.
     */
    public function messages(): array
    {
        return [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Por favor, introduce un correo electrónico válido.',
            'password.required' => 'La contraseña es obligatoria.',
        ];
    }

    /**
     * Intenta autenticar las credenciales proporcionadas en la solicitud.
     *
     * @throws \Illuminate\Validation\ValidationException Si las credenciales no son válidas o hay rate limit.
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => 'Las credenciales no coinciden con nuestros registros.',
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Verifica que la solicitud de login no esté limitada por intentos excesivos.
     *
     * @throws \Illuminate\Validation\ValidationException Si se excede el número de intentos permitidos.
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => 'Demasiados intentos de inicio de sesión. Por favor, inténtalo de nuevo en ' . ceil($seconds / 60) . ' minutos.',
        ]);
    }

    /**
     * Obtiene la clave de rate limit para la solicitud de login.
     *
     * @return string Clave única para controlar el rate limit por email e IP.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
