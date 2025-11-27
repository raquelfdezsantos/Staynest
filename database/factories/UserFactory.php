<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Factory UserFactory
 *
 * Genera datos simulados para el modelo User en pruebas y seeders.
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Contraseña actual utilizada por la factory.
     *
     * @var string|null
     */
    protected static ?string $password;

    /**
     * Define el estado por defecto del modelo User.
     *
     * @return array<string, mixed> Datos simulados para un usuario
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indica que el email del usuario debe estar sin verificar.
     *
     * @return static Estado del usuario con email no verificado
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
