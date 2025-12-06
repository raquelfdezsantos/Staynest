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
            'password' => ['required', 'confirmed', 'min:8', Password::defaults()],
            'phone' => [
                'nullable', 
                'string', 
                'max:20',
                function ($attribute, $value, $fail) {
                    if ($value && !preg_match('/^[0-9\s\+\-\(\)]+$/', $value)) {
                        $fail('El teléfono solo puede contener números, espacios y los símbolos + - ( )');
                    }
                }
            ],
            'birth_date' => ['required', 'date', 'before:today', 'after:' . now()->subYears(120)->toDateString()],
        ], [
            'birth_date.after' => 'La fecha de nacimiento no es válida.',
        ]);

        // Validar que sea mayor de edad
        $birthDate = Carbon::parse($request->birth_date);
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
                $property = Property::findOrFail($pendingData['property_id']);
                $checkIn = Carbon::parse($pendingData['check_in']);
                $checkOut = Carbon::parse($pendingData['check_out']);
                
                // Calcular precio usando rate_calendar
                $period = CarbonPeriod::create($checkIn, $checkOut)->excludeEndDate();
                $dates = collect($period)->map(fn($d) => $d->toDateString());
                $rates = RateCalendar::where('property_id', $property->id)
                    ->whereIn('date', $dates)
                    ->get()
                    ->keyBy('date');
                $totalPrice = $rates->sum('price') * (int)($pendingData['guests'] ?? 1);

                $reservation = Reservation::create([
                    'user_id' => $user->id,
                    'property_id' => $pendingData['property_id'],
                    'check_in' => $pendingData['check_in'],
                    'check_out' => $pendingData['check_out'],
                    'guests' => $pendingData['guests'],
                    'adults' => $pendingData['adults'] ?? 0,
                    'children' => $pendingData['children'] ?? 0,
                    'pets' => $pendingData['pets'] ?? 0,
                    'notes' => $pendingData['notes'] ?? null,
                    'status' => 'pending',
                    'total_price' => $totalPrice,
                    'expires_at' => now()->addHours(24),
                ]);

                // Enviar emails de confirmación
                try {
                    Mail::to($user->email)->send(new ReservationConfirmedMail($reservation));
                    Mail::to($property->user->email)->send(new AdminNewReservationMail($reservation));
                } catch (Exception $e) {
                    Log::error('Error enviando emails de confirmación de reserva: ' . $e->getMessage());
                }

                // Limpiar sesión
                session()->forget(['pending_reservation', 'pending_reservation_auto', 'url.intended']);

                // Redirigir a mis reservas de la propiedad específica
                return redirect()->route('properties.reservas.index', $property->slug)->with('success', 'Registro completado. Tu reserva ha sido creada.');
            } catch (\Exception $e) {
                // Registrar el error para debugging
                Log::error('Error creando reserva tras registro: ' . $e->getMessage(), [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'pending_data' => $pendingData ?? null,
                ]);
                
                // Si falla la creación de la reserva, redirigir a mis reservas con error
                $property = Property::find($pendingData['property_id']);
                session()->forget(['pending_reservation', 'pending_reservation_auto', 'url.intended']);
                
                if ($property) {
                    return redirect()->route('properties.reservas.index', $property->slug)
                        ->with('error', 'Registro completado pero hubo un problema al crear la reserva. Por favor, inténtalo de nuevo desde Reservar.');
                }
            }
        }

        // Detectar propiedad desde parámetro o sesión
        $propertySlug = $request->input('property') ?: session('current_property_slug');

        // Si viene desde una propiedad específica, redirigir a la ficha pública de esa propiedad
        if ($propertySlug) {
            $property = Property::where('slug', $propertySlug)->whereNull('deleted_at')->first();
            if ($property) {
                session(['current_property_slug' => $property->slug]);
                return redirect()->route('properties.show', $property->slug);
            }
        }
        return redirect()->route('home');
    }
}