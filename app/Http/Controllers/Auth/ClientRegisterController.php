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

class ClientRegisterController extends Controller
{
    /**
     * Display the client registration view.
     */
    public function create(): View
    {
        return view('auth.register-client');
    }

    /**
     * Handle an incoming client registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => ['nullable', 'string', 'max:20'],
            'birth_date' => ['required', 'date', 'before:today'],
        ]);

        // Validar que sea mayor de edad
        $birthDate = \Carbon\Carbon::parse($request->birth_date);
        $age = $birthDate->age;
        if ($age < 18) {
            return back()->withErrors(['birth_date' => 'Debes ser mayor de 18 años para registrarte.'])->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'birth_date' => $request->birth_date,
            'role' => 'customer',
        ]);

        event(new Registered($user));

        Auth::login($user);

        // Detectar propiedad desde el parámetro property
        $propertySlug = $request->input('property');
        
        // Si viene desde una propiedad específica, redirigir a mis-reservas de esa propiedad
        if ($propertySlug) {
            return redirect(route('properties.reservas.index', $propertySlug));
        }
        
        // Si no hay propiedad detectada, ir a home
        return redirect(route('home'));
    }
}