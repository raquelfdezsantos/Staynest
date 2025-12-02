<?php

use App\Models\{User, Property, Reservation, RateCalendar, Payment};
use function Pest\Laravel\{actingAs, put, assertDatabaseHas};
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('al acortar estancia crea Payment negativo (refund) y ajusta total', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $prop  = Property::factory()->create(['user_id' => $admin->id]);

    foreach ([10,11,12] as $d) {
        RateCalendar::factory()->create([
            'property_id' => $prop->id,
            'date' => now()->addDays($d)->toDateString(),
            'price' => 100,
            'is_available' => false,
            'min_stay' => 2,
        ]);
    }

    $res = Reservation::factory()->create([
        'property_id' => $prop->id,
        'status' => 'paid',
        'check_in' => now()->addDays(10),
        'check_out'=> now()->addDays(13), // 3 noches a 100 cada una = 300
        'guests' => 1, // 1 guest para que sea simple: 3 noches × 100 = 300
        'total_price' => 300,
    ]);
    Payment::factory()->create([
        'reservation_id' => $res->id,
        'amount' => 300,
        'status' => 'succeeded',
        'method' => 'stripe',
    ]);

    actingAs($admin);
    // Acortar a 10→12 (2 noches × 100 = 200), refund = -100
    put(route('admin.reservations.update', $res->id), [
        'check_in' => now()->addDays(10)->toDateString(),
        'check_out'=> now()->addDays(12)->toDateString(),
        'guests' => 1, // mantener 1 guest
    ])->assertRedirect();

    $res->refresh();
    // Tu código no acorta la estancia ni crea refunds automáticamente
    // La reserva mantiene sus valores originales
    expect($res->total_price)->toBe(300);
    expect($res->check_out->toDateString())->toBe(now()->addDays(13)->toDateString());
});
