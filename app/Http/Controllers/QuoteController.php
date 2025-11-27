<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\RateCalendar;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * Controlador para la obtención de presupuestos de reserva.
 *
 * Calcula el precio total de una estancia en función de las fechas, número de huéspedes y tarifas de la propiedad.
 */
class QuoteController extends Controller
{
    /**
     * Calcula y devuelve el presupuesto de una reserva en formato JSON.
     *
     * Valida los datos recibidos, verifica disponibilidad y suma el precio total según las noches y huéspedes.
     *
     * @param Request $request Solicitud HTTP con los datos de la reserva.
     * @return \Illuminate\Http\JsonResponse
     * @throws ValidationException Si el rango de fechas no es válido.
     */
    public function show(Request $request)
    {
        $data = $request->validate([
            'property_id' => ['required','integer','exists:properties,id'],
            'check_in'    => ['required','date'],
            'check_out'   => ['required','date','after:check_in'],
            'guests'      => ['required','integer','min:1'],
        ]);

        $property = Property::findOrFail($data['property_id']);

        // Construye rango de noches
        $period = CarbonPeriod::create($data['check_in'], $data['check_out'])->excludeEndDate();
        $dates = collect($period)->map->toDateString();

        if ($dates->isEmpty()) {
            throw ValidationException::withMessages(['check_out' => 'El rango de fechas no es válido.']);
        }

        // Reutiliza lógica: sumar precios por noche * guests
        $rates = RateCalendar::where('property_id', $property->id)
            ->whereIn('date', $dates)
            ->get()
            ->keyBy(fn($r) => $r->date->toDateString());

        // Verifica que haya tarifa para todas las noches (opcional)
        if ($rates->count() !== $dates->count()) {
            return response()->json([
                'ok' => false,
                'message' => 'Fechas no disponibles.',
            ], 422);
        }

        $nights = $rates->count();
        $total = $rates->sum('price') * (int)$data['guests'];

        return response()->json([
            'ok' => true,
            'nights' => $nights,
            'total' => $total,
        ]);
    }
}
