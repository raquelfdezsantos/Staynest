<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    // Verifica que la página de registro se carga
    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    // Prueba registro exitoso de nuevo usuario cliente
    public function test_new_users_can_register_client(): void
    {
        $response = $this->post('/register/client', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'birth_date' => now()->subYears(25)->toDateString(),
            'phone' => '600123123'
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('home'));
    }
}
