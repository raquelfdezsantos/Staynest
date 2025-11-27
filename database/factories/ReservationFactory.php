<?php

namespace Database\Factories;
use App\Models\Property;
use App\Models\User;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory ReservationFactory
 *
 * Genera datos simulados para el modelo Reservation en pruebas y seeders.
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reservation>
 */
class ReservationFactory extends Factory
{
    /**
     * Define el estado por defecto del modelo Reservation.
     *
     * @return array<string, mixed> Datos simulados para una reserva
     */
    public function definition(): array
    {
        $checkIn = now()->addDays(7)->startOfDay();
        $checkOut = (clone $checkIn)->addDays(3);
        return [
            'user_id' => User::factory(),
            'property_id' => Property::factory(),
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'guests' => 2,
            'status' => 'pending',
            'total_price' => 300.00,
        ];
    }
}
