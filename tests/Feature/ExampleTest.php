<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use \Illuminate\Foundation\Testing\RefreshDatabase;
    /**
     * A basic test example.
     */
    // Prueba que la aplicación redirige al home cuando existe una propiedad
    public function test_the_application_home_redirects_when_property_exists(): void
    {
        \App\Models\Property::factory()->create();
        $response = $this->get('/');
        $response->assertRedirect();
    }
}
