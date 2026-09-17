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

    public function test_login_page_has_register_button_and_no_demo_credentials(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Buat Akun Baru');
        $response->assertDontSee('Akun Demo Gudang (1-Klik)');
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

    public function test_guest_can_view_registration_page(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
        $response->assertSee('Buat Akun Baru');
        $response->assertSee('Konfirmasi Password');
    }

    public function test_guest_can_register_new_account(): void
    {
        $response = $this->post('/register', [
            'name' => 'Budi Santoso',
            'username' => 'budi_gudang',
            'email' => 'budi@example.com',
            'role' => 'cabang',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'username' => 'budi_gudang',
            'email' => 'budi@example.com',
            'role' => 'cabang',
        ]);
    }

    public function test_registration_requires_valid_data(): void
    {
        $response = $this->post('/register', []);

        $response->assertSessionHasErrors(['name', 'username', 'email', 'role', 'password']);
        $this->assertGuest();
    }

    public function test_registration_rejects_duplicate_username_or_email(): void
    {
        User::factory()->create([
            'username' => 'budi_gudang',
            'email' => 'budi@example.com',
        ]);

        $response = $this->post('/register', [
            'name' => 'Budi Baru',
            'username' => 'budi_gudang',
            'email' => 'budi@example.com',
            'role' => 'cabang',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response->assertSessionHasErrors(['username', 'email']);
        $this->assertGuest();
    }
}
