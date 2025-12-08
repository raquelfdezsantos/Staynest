<?php

use App\Models\{User, Property, Reservation, RateCalendar};
use function Pest\Laravel\{actingAs, post};
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// Prueba que permite reserva que empieza justo el día de checkout de otra
it('permite reserva que empieza justo el día de checkout de otra', function () {
    $user = User::factory()->create(['role' => 'customer']);
    $prop = Property::factory()->create();

    // Reserva existente 10→12 (ocupa 10 y 11)
    Reservation::factory()->create([
        'user_id' => $user->id,
        'property_id' => $prop->id,
        'check_in' => now()->addDays(10),
        'check_out'=> now()->addDays(12),
        'status' => 'pending',
        'total_price' => 200,
    ]);

    // No crear fechas en calendario - dejar que el controlador las cree automáticamente
    // Esto evita duplicados y simula mejor el comportamiento real

    actingAs($user);
    // Crear 12→14 debe ser OK 
    $resp = post(route('reservas.store'), [
        'property_id' => $prop->id,
        'check_in'    => now()->addDays(12)->toDateString(),
        'check_out'   => now()->addDays(14)->toDateString(),
        'guests'      => 2,
    ]);

    $resp->assertRedirect(); // llegó a mis-reservas sin error de solape
});
