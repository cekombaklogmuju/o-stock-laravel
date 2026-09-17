<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'username' => 'testuser',
            'email' => 'user@ostock.com',
            'password' => bcrypt('password123'),
            'role' => 'admin',
        ]);

        $response = $this->post('/login', [
            'login' => 'testuser',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_cannot_login_with_invalid_password(): void
    {
        User::factory()->create([
            'username' => 'testuser',
            'email' => 'user@ostock.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'login' => 'testuser',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('login');
        $this->assertGuest();
    }

    public function test_non_admin_cannot_access_branch_management(): void
    {
        $user = User::factory()->create([
            'role' => 'cabang',
        ]);

        $response = $this->actingAs($user)->get('/kantor');
        $response->assertStatus(403);
    }
}
