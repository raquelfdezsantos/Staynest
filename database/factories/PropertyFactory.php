<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * Factory PropertyFactory
 *
 * Genera datos simulados para el modelo Property en pruebas y seeders.
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Property>
 */
class PropertyFactory extends Factory
{
    /**
     * Define el estado por defecto del modelo Property.
     *
     * @return array<string, mixed> Datos simulados para una propiedad
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'slug' => Str::slug($this->faker->unique()->words(3, true)),
            'capacity' => $this->faker->numberBetween(1, 6),
            'description' => $this->faker->paragraph(),
            'address' => $this->faker->address(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
