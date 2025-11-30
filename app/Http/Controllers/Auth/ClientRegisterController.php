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
 * Controlador para el registro de clientes.
 *
 * Gestiona la visualización del formulario de registro y el proceso de alta de nuevos clientes,
 * incluyendo la validación de datos personales y la redirección según el contexto de la propiedad.
 */
class ClientRegisterController extends Controller
{
    /**
     * Muestra la vista de registro de cliente.
     *
     * @return View Vista de registro de cliente.
     */
    public function create(): View
    {
        return view('auth.register-client');
    }

    /**
     * Procesa la solicitud de registro de un nuevo cliente.
     *
     * Valida los datos personales, crea el usuario y redirige según la propiedad detectada.
     *
     * @param Request $request Solicitud HTTP con los datos de registro.
     * @return RedirectResponse Redirección a la ficha pública de la propiedad o a la página principal.
     * @throws \Illuminate\Validation\ValidationException Si la validación de los datos falla.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^[0-9\s\+\-\(\)]+$/'],
            'birth_date' => ['required', 'date', 'before:today'],
        ], [
            'phone.regex' => 'El teléfono solo puede contener números, espacios y los símbolos + - ( )',
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

        // Verificar si hay una reserva pendiente en sesión
        if (session()->has('pending_reservation') && session('pending_reservation_auto')) {
            $pendingData = session('pending_reservation');
            
            // Crear la reserva automáticamente
            try {
                $property = \App\Models\Property::findOrFail($pendingData['property_id']);
                $checkIn = \Carbon\Carbon::parse($pendingData['check_in']);
                $checkOut = \Carbon\Carbon::parse($pendingData['check_out']);
                
                // Calcular precio usando rate_calendar
                $period = \Carbon\CarbonPeriod::create($checkIn, $checkOut)->excludeEndDate();
                $dates = collect($period)->map(fn($d) => $d->toDateString());
                $rates = \App\Models\RateCalendar::where('property_id', $property->id)
                    ->whereIn('date', $dates)
                    ->get()
                    ->keyBy('date');
                $totalPrice = $rates->sum('price') * (int)($pendingData['guests'] ?? 1);

                $reservation = \App\Models\Reservation::create([
                    'user_id' => $user->id,
                    'property_id' => $pendingData['property_id'],
                    'check_in' => $pendingData['check_in'],
                    'check_out' => $pendingData['check_out'],
                    'guests' => $pendingData['guests'],
                    'adults' => $pendingData['adults'] ?? null,
                    'children' => $pendingData['children'] ?? null,
                    'pets' => $pendingData['pets'] ?? null,
                    'notes' => $pendingData['notes'] ?? null,
                    'status' => 'pending',
                    'total_price' => $totalPrice,
                ]);

                // Limpiar sesión
                session()->forget(['pending_reservation', 'pending_reservation_auto', 'url.intended']);

                // Redirigir a mis reservas
                return redirect()->route('reservas.index')->with('success', 'Registro completado. Tu reserva ha sido creada.');
            } catch (\Exception $e) {
                // Si falla la creación de la reserva, redirigir a la página de reservar
                $property = \App\Models\Property::find($pendingData['property_id']);
                if ($property) {
                    session()->forget(['pending_reservation', 'pending_reservation_auto']);
                    return redirect()->route('properties.reservar', $property->slug)
                        ->with('warning', 'Registro completado. Por favor, completa tu reserva.');
                }
            }
        }

        // Detectar propiedad desde parámetro o sesión
        $propertySlug = $request->input('property') ?: session('current_property_slug');

        // Si viene desde una propiedad específica, redirigir a la ficha pública de esa propiedad
        if ($propertySlug) {
            $property = \App\Models\Property::where('slug', $propertySlug)->whereNull('deleted_at')->first();
            if ($property) {
                session(['current_property_slug' => $property->slug]);
                return redirect()->route('properties.show', $property->slug);
            }
        }
        return redirect()->route('home');
    }
}