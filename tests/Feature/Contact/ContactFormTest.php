<?php

use Illuminate\Support\Facades\Mail;
use App\Mail\ContactMessageMail;
use function Pest\Laravel\{post};
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('envía email de contacto con datos válidos', function () {
    Mail::fake();

    $user = \App\Models\User::factory()->create(['email' => 'owner@example.com']);
    $property = \App\Models\Property::factory()->for($user)->create();

    post(route('properties.contact.store', $property->slug), [
        'name' => 'Juan Pérez',
        'email' => 'juan@example.com',
        'subject' => 'Consulta tarifas',
        'message' => 'Me gustaría saber más sobre las tarifas.',
    ])->assertRedirect();

    Mail::assertSent(ContactMessageMail::class, function ($mail) use ($user) {
        return $mail->hasTo($user->email);
    });
});

it('bloquea después de 6 intentos (rate limit)', function () {
    $user = \App\Models\User::factory()->create(['email' => 'owner@example.com']);
    $property = \App\Models\Property::factory()->for($user)->create();
    for ($i = 0; $i < 5; $i++) {
        post(route('properties.contact.store', $property->slug), [
            'name' => "Usuario $i",
            'email' => "user$i@example.com",
            'subject' => 'Prueba',
            'message' => 'Mensaje de prueba',
        ])->assertStatus(302); // Debería redirigir correctamente
    }

    // El 6º intento debería ser bloqueado
    post(route('properties.contact.store', $property->slug), [
        'name' => 'Usuario 6',
        'email' => 'user6@example.com',
        'subject' => 'Prueba límite',
        'message' => 'Este debería fallar',
    ])->assertStatus(429); // Too Many Requests
});
