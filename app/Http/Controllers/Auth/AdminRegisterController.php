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
            'name' => [
                'required', 
                'string', 
                'max:255',
                function ($attribute, $value, $fail) {
                    if (preg_match('/<[^>]*>/', $value)) {
                        $fail('El nombre contiene caracteres HTML no permitidos.');
                    }
                    if (preg_match('/[0-9]/', $value)) {
                        $fail('El nombre no puede contener números.');
                    }
                    // Permitir letras (incluidas tildes y ñ) y espacios
                    if (!preg_match('/^[\p{L}\s]+$/u', $value)) {
                        $fail('El nombre solo puede contener letras y espacios.');
                    }
                }
            ],
            'email' => ['required', 'string', 'lowercase', 'email:rfc', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', 'min:8', Rules\Password::defaults()],
            'phone' => [
                'required', 
                'string', 
                'max:20',
                function ($attribute, $value, $fail) {
                    if (!preg_match('/^[0-9\s\+\-\(\)]+$/', $value)) {
                        $fail('El teléfono solo puede contener números, espacios y los símbolos + - ( )');
                    }
                }
            ],
            'birth_date' => ['required', 'date', 'before:today', 'after:' . now()->subYears(120)->toDateString()],
            'address' => [
                'required', 
                'string', 
                'max:255',
                function ($attribute, $value, $fail) {
                    if (preg_match('/<[^>]*>/', $value)) {
                        $fail('La dirección contiene caracteres HTML no permitidos.');
                    }
                    // Permitir letras, números, espacios y caracteres comunes en direcciones
                    if (!preg_match('/^[\p{L}\p{N}\s.,ºª\-]+$/u', $value)) {
                        $fail('La dirección contiene caracteres no permitidos.');
                    }
                }
            ],
            'document_id' => [
                'required', 
                'string', 
                'max:20', 
                'unique:'.User::class.',document_id',
                function ($attribute, $value, $fail) {
                    if (!preg_match('/^[A-Z0-9\-]+$/i', $value)) {
                        $fail('El NIF/CIF solo puede contener letras, números y guiones.');
                    }
                }
            ],

            // Datos del alojamiento
            'property_name' => [
                'required', 
                'string', 
                'max:150',
                function ($attribute, $value, $fail) {
                    if (preg_match('/<[^>]*>/', $value)) {
                        $fail('El nombre de la propiedad contiene caracteres HTML no permitidos.');
                    }
                    // Permitir letras, números, espacios, puntos y guiones
                    if (!preg_match('/^[\p{L}\p{N}\s.\-]+$/u', $value)) {
                        $fail('El nombre de la propiedad contiene caracteres no permitidos.');
                    }
                }
            ],
            'property_address' => [
                'required', 
                'string', 
                'max:200',
                function ($attribute, $value, $fail) {
                    if (preg_match('/<[^>]*>/', $value)) {
                        $fail('La dirección de la propiedad contiene caracteres HTML no permitidos.');
                    }
                    // Permitir letras, números, espacios y caracteres comunes en direcciones
                    if (!preg_match('/^[\p{L}\p{N}\s.,ºª\-]+$/u', $value)) {
                        $fail('La dirección de la propiedad contiene caracteres no permitidos.');
                    }
                }
            ],
            'city' => [
                'required', 
                'string', 
                'max:100',
                function ($attribute, $value, $fail) {
                    if (preg_match('/<[^>]*>/', $value)) {
                        $fail('La ciudad contiene caracteres HTML no permitidos.');
                    }
                    // Permitir letras, espacios y guiones
                    if (!preg_match('/^[\p{L}\s\-]+$/u', $value)) {
                        $fail('La ciudad solo puede contener letras, espacios y guiones.');
                    }
                }
            ],
            'postal_code' => [
                'required', 
                'string', 
                'max:10',
                function ($attribute, $value, $fail) {
                    if (!preg_match('/^[0-9]{5}$/', $value)) {
                        $fail('El código postal debe tener exactamente 5 dígitos.');
                    }
                }
            ],
            'province' => [
                'required', 
                'string', 
                'max:100',
                function ($attribute, $value, $fail) {
                    if (preg_match('/<[^>]*>/', $value)) {
                        $fail('La provincia contiene caracteres HTML no permitidos.');
                    }
                    // Permitir letras, espacios y guiones
                    if (!preg_match('/^[\p{L}\s\-]+$/u', $value)) {
                        $fail('La provincia solo puede contener letras, espacios y guiones.');
                    }
                }
            ],
            'capacity' => ['required', 'integer', 'min:1', 'max:50'],
            'tourism_license' => [
                'required', 
                'string', 
                'max:100',
                function ($attribute, $value, $fail) {
                    if (!preg_match('/^[A-Z0-9\/\-]+$/i', $value)) {
                        $fail('La licencia turística solo puede contener letras, números, barras y guiones.');
                    }
                }
            ],
            'rental_registration' => [
                'required', 
                'string', 
                'max:100',
                function ($attribute, $value, $fail) {
                    if (!preg_match('/^[A-Z0-9\/\-]+$/i', $value)) {
                        $fail('El registro de alquiler solo puede contener letras, números, barras y guiones.');
                    }
                }
            ],

            // Método de cobro (fingido)
            'payment_method' => ['required', 'string', 'in:stripe,bank_transfer,paypal'],
        ], [
            'birth_date.after' => 'La fecha de nacimiento no es válida.',
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