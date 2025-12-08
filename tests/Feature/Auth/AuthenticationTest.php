<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    // Verifica que la página de login se carga correctamente
    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    // Prueba login exitoso y redirección
    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $property = \App\Models\Property::factory()->create();
        $user = User::factory()->create(['role' => 'customer']);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

    $this->assertAuthenticated();
    // Tras login, redirige a home si no está verificado
    $response->assertRedirect(route('home'));
    }

    // Prueba login fallido con contraseña incorrecta
    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    // Prueba logout y redirección
    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }
}
