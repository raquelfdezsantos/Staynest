<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;


/**
 * Controlador para la gestión de sesiones de usuario.
 * Maneja login, logout y redirecciones basadas en rol y contexto de propiedad.
 */
class AuthenticatedSessionController extends Controller
{
    /**
     * Muestra el formulario de inicio de sesión.
     *
     * @return \Illuminate\View\View
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Procesa el login del usuario.
     * Gestiona reservas pendientes, detecta contexto de propiedad
     * y redirige según el rol (admin/cliente) y propiedad.
     *
     * @param \App\Http\Requests\Auth\LoginRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = $request->user();
        
        // Crear reserva automáticamente si el cliente tenía una pendiente
        if ($user->role === 'customer' && session()->has('pending_reservation') && session('pending_reservation_auto')) {
            $pendingData = session('pending_reservation');
            
            try {
                $property = \App\Models\Property::findOrFail($pendingData['property_id']);
                $checkIn = \Carbon\Carbon::parse($pendingData['check_in']);
                $checkOut = \Carbon\Carbon::parse($pendingData['check_out']);
                
                // Calcular precio total desde el calendario de tarifas
                $period = \Carbon\CarbonPeriod::create($checkIn, $checkOut)->excludeEndDate();
                $dates = collect($period)->map(function (\Carbon\Carbon $d) {
                    return $d->toDateString();
                });
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
                    'adults' => $pendingData['adults'] ?? 0,
                    'children' => $pendingData['children'] ?? 0,
                    'pets' => $pendingData['pets'] ?? 0,
                    'notes' => $pendingData['notes'] ?? null,
                    'status' => 'pending',
                    'total_price' => $totalPrice,
                    'expires_at' => now()->addMinutes(5),
                ]);

                // Bloquear fechas en el calendario (rango [check_in, check_out))
                foreach ($dates as $dateStr) {
                    \App\Models\RateCalendar::where('property_id', $property->id)
                        ->where('date', $dateStr)
                        ->update(['is_available' => false, 'blocked_by' => 'reservation']);
                }

                try {
                    \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\ReservationConfirmedMail($reservation));
                    \Illuminate\Support\Facades\Mail::to($property->user->email)->send(new \App\Mail\AdminNewReservationMail($reservation));
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Error enviando emails de confirmación de reserva: ' . $e->getMessage());
                }

                session()->forget(['pending_reservation', 'pending_reservation_auto', 'url.intended']);
                return redirect()->route('properties.reservas.index', $property->slug)->with('success', 'Tu reserva ha sido creada.');
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Error creando reserva tras login: ' . $e->getMessage(), [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'pending_data' => $pendingData ?? null,
                ]);
                
                $property = \App\Models\Property::find($pendingData['property_id'] ?? null);
                session()->forget(['pending_reservation', 'pending_reservation_auto', 'url.intended']);
                
                if ($property) {
                    return redirect()->route('properties.reservas.index', $property->slug)
                        ->with('error', 'Hubo un problema al crear la reserva. Por favor, inténtalo de nuevo.');
                }
            }
        }
        
        // Detectar contexto de propiedad (query > sesión > URL intended)
        $propertySlug = $request->input('property')
            ?: session('current_property_slug');

        if (!$propertySlug) {
            $intendedUrl = $request->session()->get('url.intended', '');
            if ($intendedUrl && preg_match('/\/propiedades\/([^\/]+)/', $intendedUrl, $matches)) {
                $propertySlug = $matches[1];
            }
        }

        // Redirección para administradores
        if ($user->role === 'admin') {
            if ($propertySlug) {
                $property = \App\Models\Property::where('slug', $propertySlug)->whereNull('deleted_at')->first();
                if ($property) {
                    session(['current_property_slug' => $property->slug]);
                    $intendedUrl = $request->session()->get('url.intended', '');
                    if ($intendedUrl && str_contains($intendedUrl, "/propiedades/{$property->slug}")) {
                        return redirect($intendedUrl);
                    }
                    if ($property->user_id === $user->id) {
                        return redirect()->route('admin.property.dashboard', $property->slug);
                    }
                    return redirect()->route('properties.show', $property->slug);
                }
            }
            $ownProperty = \App\Models\Property::where('user_id', $user->id)->whereNull('deleted_at')->first();
            if ($ownProperty) {
                session(['current_property_slug' => $ownProperty->slug]);
                return redirect()->route('admin.property.dashboard', $ownProperty->slug);
            }
            return redirect()->intended(route('home'));
        }

        // Redirección para clientes
        if ($propertySlug) {
            $property = \App\Models\Property::where('slug', $propertySlug)->whereNull('deleted_at')->first();
            if ($property) {
                session(['current_property_slug' => $property->slug]);
                $intendedUrl = $request->session()->get('url.intended', '');
                if ($intendedUrl && str_contains($intendedUrl, "/propiedades/{$property->slug}")) {
                    return redirect($intendedUrl);
                }
                return redirect()->route('properties.show', $property);
            }
        }

        return redirect()->intended(route('home'));
    }

    /**
     * Cierra la sesión del usuario.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
