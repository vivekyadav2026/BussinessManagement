<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'organization_name' => 'Test Org',
            'business_type' => 'business',
            'business_phone' => '9876543210',
            'country' => 'India',
            'state' => 'Delhi',
            'pincode' => '110001',
            'address' => '123 Main St',
            'name' => 'Test User',
            'admin_phone' => '9876543210',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }
}
