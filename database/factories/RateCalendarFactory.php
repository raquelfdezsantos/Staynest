<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Property;
use Carbon\Carbon;

/**
 * Factory RateCalendarFactory
 *
 * Genera datos simulados para el modelo RateCalendar en pruebas y seeders.
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RateCalendar>
 */
class RateCalendarFactory extends Factory
{
    /**
     * Define el estado por defecto del modelo RateCalendar.
     *
     * @return array<string, mixed> Datos simulados para un calendario de tarifas
     */
    public function definition(): array
    {
        return [
            'property_id' => Property::factory(),
            'date' => Carbon::now()->addDays($this->faker->numberBetween(1, 30))->toDateString(),
            'price' => $this->faker->randomFloat(2, 50, 200),
            'is_available' => true,
            'min_stay' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
