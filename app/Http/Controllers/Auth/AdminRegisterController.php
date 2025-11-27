<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Property;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules;
use Illuminate\View\View;


/**
 * Controlador para el registro de administradores.
 *
 * Gestiona la visualización del formulario de registro y el proceso de alta de nuevos administradores,
 * incluyendo la validación de datos personales y de la propiedad, así como la creación de usuario y alojamiento.
 */
class AdminRegisterController extends Controller
{
    /**
     * Muestra la vista de registro de administrador.
     *
     * @return View Vista de registro de administrador.
     */
    public function create(): View
    {
        return view('auth.register-admin');
    }

    /**
     * Procesa la solicitud de registro de un nuevo administrador.
     *
     * Valida los datos personales y de la propiedad, crea el usuario y la propiedad asociada,
     * y redirige al dashboard correspondiente.
     *
     * @param Request $request Solicitud HTTP con los datos de registro.
     * @return RedirectResponse Redirección al dashboard de la propiedad o a la página principal.
     * @throws \Illuminate\Validation\ValidationException Si la validación de los datos falla.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            // Datos personales
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => ['required', 'string', 'max:20'],
            'birth_date' => ['required', 'date', 'before:today'],
            'address' => ['required', 'string', 'max:255'],
            'document_id' => ['required', 'string', 'max:20', 'unique:'.User::class.',document_id'],

            // Datos del alojamiento
            'property_name' => ['required', 'string', 'max:150'],
            'property_address' => ['required', 'string', 'max:200'],
            'city' => ['required', 'string', 'max:100'],
            'postal_code' => ['required', 'string', 'max:10'],
            'province' => ['required', 'string', 'max:100'],
            'capacity' => ['required', 'integer', 'min:1', 'max:50'],
            'tourism_license' => ['required', 'string', 'max:100'],
            'rental_registration' => ['required', 'string', 'max:100'],

            // Método de cobro (fingido)
            'payment_method' => ['required', 'string', 'in:stripe,bank_transfer,paypal'],
        ]);

        // Validar que no sea menor de edad
        $birthDate = \Carbon\Carbon::parse($request->birth_date);
        $age = $birthDate->age;
        if ($age < 18) {
            return back()->withErrors(['birth_date' => 'Debes ser mayor de 18 años para registrarte.']);
        }

        DB::transaction(function () use ($request) {
            // Crear usuario administrador
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'birth_date' => $request->birth_date,
                'address' => $request->address,
                'document_id' => $request->document_id,
                'role' => 'admin',
            ]);

            // Crear propiedad asociada al usuario
            $property = Property::create([
                'user_id' => $user->id,
                'name' => $request->property_name,
                'slug' => \Illuminate\Support\Str::slug($request->property_name),
                'address' => $request->property_address,
                'city' => $request->city,
                'postal_code' => $request->postal_code,
                'province' => $request->province,
                'capacity' => $request->capacity,
                'tourism_license' => $request->tourism_license,
                'rental_registration' => $request->rental_registration,
                'owner_name' => $request->name,
                'owner_tax_id' => $request->document_id,
            ]);

            // Iniciar sesión con el usuario recién creado
            Auth::login($user);
        });

        // Tras el registro, crear la propiedad, fijar el contexto y redirigir al dashboard de la nueva propiedad
        $property = \App\Models\Property::where('user_id', Auth::id())
            ->whereNull('deleted_at')
            ->latest('id')
            ->first();
        if ($property) {
            session(['current_property_slug' => $property->slug]);
            return redirect()->route('admin.property.dashboard', $property->slug);
        }
        return redirect()->route('home');
    }
}