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
            'name'                  => 'Test User',
            'email'                 => 'test@example.com',
            'nim'                   => '223443999',
            'role'                  => 'mahasiswa',
            'prodi'                 => 'informatika',
            'kelas_register'        => '3A',
            'password'              => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_new_dosen_registration_requires_admin_approval(): void
    {
        $response = $this->post('/register', [
            'name'                  => 'Dosen Baru',
            'email'                 => 'dosen-baru@example.com',
            'nim'                   => '19880101',
            'role'                  => 'dosen',
            'password'              => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertGuest();
        $response->assertRedirect(route('login', absolute: false));
        $this->assertDatabaseHas('users', [
            'email'  => 'dosen-baru@example.com',
            'role'   => 'dosen',
            'status' => 'pending',
        ]);
    }
}
