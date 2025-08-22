<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_route_displays_register_view(): void
    {
        $response = $this->get(route('register'));
        $response->assertStatus(200);
        $response->assertViewIs('auth.register');
    }

    public function test_user_can_register_with_valid_data(): void
    {
        $response = $this->post(route('register'), [
            'username' => 'TestUser',
            'email' => 'test@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
            'role' => 'borrower',
            'age' => 22,
            'phonenumber' => '0123456789',
        ]);

        // User exists in DB
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'username' => 'TestUser',
            'role' => 'borrower',
            'age' => 22,
            'phonenumber' => '0123456789',
        ]);

        // User should be logged in
        $user = User::first();
        $this->assertAuthenticatedAs($user);

        // Redirect to home
        $response->assertRedirect(route('home', false));
    }

    public function test_registration_fails_with_invalid_data(): void
    {
        $response = $this->from(route('register'))->post(route('register'), [
            'username' => '',                  
            'email' => 'not-an-email',         
            'password' => 'lowercase',         
            'password_confirmation' => 'different',
            'role' => 'invalid-role',          
            'age' => 'not-an-integer',         
            'phonenumber' => '',               
        ]);

        $response->assertRedirect(route('register'));
        $response->assertSessionHasErrors([
            'username', 'email', 'password', 'role', 'age', 'phonenumber'
        ]);

        $this->assertDatabaseCount('users', 0);
        $this->assertGuest();
    }
}
