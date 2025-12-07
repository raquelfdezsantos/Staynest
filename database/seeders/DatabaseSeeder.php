<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Seeder principal de la base de datos.
 *
 * Ejecuta los seeders necesarios para inicializar el entorno de desarrollo
 * con usuarios, propiedades, fotos y calendario de tarifas.
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Ejecuta los seeders definidos en la aplicación.
     *
     * @return void
     */
    public function run(): void
    {
        // DemoDataSeeder: Crea entorno completo para la DEFENSA DEL TFG
        // - 2 admins (Luis y Ana) + 3 clientes
        // - 3 propiedades (Apartamento Nordeste REAL + 2 demo)
        // - Reservas en diferentes estados (pending, paid, cancelled)
        // - Calendario de tarifas y PropertyEnvironment para Nordeste
        $this->call(DemoDataSeeder::class);
    }
}
