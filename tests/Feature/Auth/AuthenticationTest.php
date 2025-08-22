<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{

    /**
     * Test the create method returns the login view.
     */
    public function test_create_method_displays_login_view(): void
    {
        // Hit the route that points to your create() method
        $response = $this->get('/login'); // Adjust the URL if different

        // Assert status is OK
        $response->assertStatus(200);

        // Assert it returns the correct view
        $response->assertViewIs('auth.login');
    }

    public function test_user_can_login_with_valid_credentials(): void
{
    $user = User::factory()->create([
        'password' => bcrypt($password = 'Password123'),
    ]);

    $response = $this->post(route('login'), [
        'email' => $user->email,
        'password' => $password,
    ]);

    // Check authentication
    $this->assertAuthenticatedAs($user);

    // Check redirect to home
    $response->assertRedirect(route('home', false));
}

public function test_login_fails_with_invalid_credentials(): void
{
    $user = User::factory()->create([
        'password' => bcrypt('Password123'),
    ]);

    $response = $this->from(route('login'))->post(route('login'), [
        'email' => $user->email,
        'password' => 'wrongpassword',
    ]);

    $response->assertRedirect(route('login'));
    $response->assertSessionHasErrors();
    $this->assertGuest();
}

public function test_user_can_logout(): void
{
    $user = User::factory()->create();

    // Simulate logged-in user
    $this->actingAs($user);

    $response = $this->post(route('logout'));

    // Assert user is logged out
    $this->assertGuest();

    // Assert redirect to home page ("/")
    $response->assertRedirect('/');
}


}