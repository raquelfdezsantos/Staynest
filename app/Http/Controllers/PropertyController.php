<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\RateCalendar;
use App\Models\Reservation;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

/**
 * Controlador de propiedades.
 *
 * Gestiona la visualización pública del alojamiento y sus detalles:
 * fotos, precios, calendario de disponibilidad y ficha completa.
 */
class PropertyController extends Controller
{
    /**
     * Muestra la HOME adaptativa según el número de propiedades.
     * 
     * - Si hay 1 propiedad: muestra ficha completa
     * - Si hay 2+: muestra grid de propiedades
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function home()
    {
        $totalProperties = Property::whereNull('deleted_at')->count();

        if ($totalProperties === 0) {
            abort(404, 'No hay propiedades disponibles');
        }

        if ($totalProperties === 1) {
            // Solo hay 1 propiedad: mostrar ficha completa
            $property = Property::with(['photos', 'rateCalendar'])
                ->whereNull('deleted_at')
                ->firstOrFail();

            return view('home-single', compact('property', 'totalProperties'));
        }

        // Hay múltiples propiedades: mostrar grid
        $properties = Property::with('photos')
            ->whereNull('deleted_at')
            ->latest()
            ->get();

        return view('home-multi', compact('properties', 'totalProperties'));
    }

    /**
     * Muestra el listado de propiedades disponibles.
     *
     * Obtiene las propiedades junto con sus fotos asociadas,
     * ordenadas de la más reciente a la más antigua, y las pagina.
     *
     * @return \Illuminate\Contracts\View\View Vista con el listado de propiedades.
     */
    public function index()
    {
        $properties = Property::with('photos')->latest()->paginate(6);
        return view('properties.index', compact('properties'));
    }

    /**
     * Muestra la ficha detallada de una propiedad.
     *
     * Carga las fotos y el calendario de tarifas disponibles
     * (solo fechas futuras con disponibilidad).
     *
     * @param \App\Models\Property $property Propiedad seleccionada.
     * @return \Illuminate\Contracts\View\View Vista con los detalles de la propiedad.
     */
    public function show(Property $property)
    {
        // Guardar la propiedad actual en sesión
        session(['current_property_slug' => $property->slug]);
        $property->load([
            'photos',
            'rateCalendar' => function ($q) {
                $q->where('is_available', true)
                    ->whereDate('date', '>=', now()->toDateString())
                    ->orderBy('date');
            },
        ]);

        $fromPrice = optional($property->rateCalendar->first())->price ?? null;

        // Cargar fechas bloqueadas SOLO desde reservas activas
        // Bloquear las NOCHES ocupadas: [check_in, check_out) - excluye el día de check-out
        $reservations = Reservation::where('property_id', $property->id)
            ->whereNotIn('status', ['cancelled'])
            ->whereDate('check_out', '>', now()->toDateString())
            ->get();

        $blockedDates = $reservations->flatMap(function ($reservation) {
            $period = CarbonPeriod::create($reservation->check_in, $reservation->check_out)->excludeEndDate();
            return collect($period)->map(function($d) {
                return is_object($d) && method_exists($d, 'toDateString') ? $d->toDateString() : (string) $d;
            });
        })
            ->unique()
            ->values()
            ->toArray();

        // Días donde hay check-in (noche ocupada, aunque haya check-out también)
        $checkinDates = $reservations->map(fn($r) => Carbon::parse($r->check_in)->format('Y-m-d'))
            ->unique()
            ->values()
            ->toArray();

        // Días donde hay check-out (noche potencialmente libre)
        $checkoutDates = $reservations->map(fn($r) => Carbon::parse($r->check_out)->format('Y-m-d'))
            ->unique()
            ->values()
            ->toArray();

        return view('home-single', compact('property', 'fromPrice', 'blockedDates', 'checkinDates', 'checkoutDates'));
    }

    /**
     * Muestra la página de entorno de una propiedad específica.
     *
     * @param \App\Models\Property $property
     * @return \Illuminate\Contracts\View\View
     */
    public function entorno(Property $property)
    {
        // Fijar contexto al acceder directamente sin pasar por show
        session(['current_property_slug' => $property->slug]);
        
        // Cargar información del entorno
        $property->load('environment');
        
        return view('entorno.index', compact('property'));
    }

    /**
     * Muestra la página de reservar de una propiedad específica.
     *
     * @param \App\Models\Property $property
     * @return \Illuminate\Contracts\View\View
     */
    public function reservar(Property $property)
    {
        // Fijar contexto al acceder directamente sin pasar por show
        session(['current_property_slug' => $property->slug]);
        $property->load('photos');

        // Fechas bloqueadas desde RateCalendar
        $blockedDates = RateCalendar::query()
            ->where('property_id', $property->id)
            ->where('is_available', false)
            ->orderBy('date')
            ->pluck('date')
            ->map(fn($d) => Carbon::parse($d)->format('Y-m-d'))
            ->toArray();

        // Opcional: marcar días de entrada/salida reales de reservas
        $reservations = Reservation::where('property_id', $property->id)
            ->whereIn('status', ['pending', 'paid'])
            ->get();

        $checkinDates = $reservations->pluck('check_in')->map(fn($d) => Carbon::parse($d)->format('Y-m-d'))->unique()->values()->toArray();
        $checkoutDates = $reservations->pluck('check_out')->map(fn($d) => Carbon::parse($d)->format('Y-m-d'))->unique()->values()->toArray();

        // Precio "desde"
        $fromPrice = RateCalendar::where('property_id', $property->id)
            ->where('is_available', true)
            ->min('price');

        // Pasar todas las tarifas como objeto fecha => precio
        $ratesCollection = RateCalendar::where('property_id', $property->id)
            ->where('is_available', true)
            ->get();
        
        $rates = [];
        foreach ($ratesCollection as $rate) {
            $dateKey = Carbon::parse($rate->date)->format('Y-m-d');
            $rates[$dateKey] = (float) $rate->price;
        }

        // Datos preseleccionados guardados en sesión al forzar login
        $prefill = session('pending_reservation', []);
        $auto = session('pending_reservation_auto', false);
        // Consumir flag para que no se repita
        session()->forget('pending_reservation_auto');

        return view('reservar.index', compact(
            'property',
            'fromPrice',
            'blockedDates',
            'checkinDates',
            'checkoutDates',
            'rates',
            'prefill',
            'auto'
        ));
    }

    /**
     * Muestra todas las propiedades de un administrador específico.
     *
     * @param int $userId ID del usuario admin
     * @return \Illuminate\Contracts\View\View
     */
    public function byOwner($userId)
    {
        $properties = Property::with('photos')
            ->where('user_id', $userId)
            ->whereNull('deleted_at')
            ->get();

        if ($properties->isEmpty()) {
            abort(404, 'No se encontraron propiedades para este propietario');
        }

        $ownerName = $properties->first()->user->name ?? 'Este propietario';

        return view('properties.by-owner', compact('properties', 'ownerName'));
    }

    /**
     * Página institucional: Descubre Staynest
     * 
     * Muestra información sobre el modelo de negocio y un grid
     * con TODAS las propiedades disponibles en la plataforma.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function discover(Request $request)
    {
        $properties = Property::with(['photos', 'user'])
            ->whereNull('deleted_at')
            ->latest()
            ->get();

        // Intentar obtener la propiedad desde la sesión o query string
        $property = null;
        
        if ($request->has('property')) {
            $property = Property::where('slug', $request->input('property'))->first();
        } elseif (session('current_property_slug')) {
            $property = Property::where('slug', session('current_property_slug'))->first();
        }

        return view('discover', compact('properties', 'property'));
    }
}
