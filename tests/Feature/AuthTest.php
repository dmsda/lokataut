<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    private Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Admin::create([
            'name' => 'Dimas Aditya',
            'email' => 'dimas@lokataut.com',
            'password' => Hash::make('SecretAdmin123!@#'),
        ]);
    }

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('LokaTaut');
    }

    public function test_admin_can_authenticate_with_valid_credentials(): void
    {
        $response = $this->post('/login', [
            'email' => 'dimas@lokataut.com',
            'password' => 'SecretAdmin123!@#',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/admin/dashboard');
    }

    public function test_admin_cannot_authenticate_with_invalid_password(): void
    {
        $response = $this->post('/login', [
            'email' => 'dimas@lokataut.com',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_unauthenticated_user_cannot_access_admin_dashboard(): void
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_admin_can_logout(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/login');
    }

    public function test_admin_can_change_password_with_valid_current_password(): void
    {
        $this->actingAs($this->admin);

        $response = $this->put('/admin/profile/password', [
            'current_password' => 'SecretAdmin123!@#',
            'new_password' => 'NewSecurePassword#2026!',
            'new_password_confirmation' => 'NewSecurePassword#2026!',
        ]);

        $response->assertRedirect('/admin/profile');
        $response->assertSessionHas('success');

        $this->admin->refresh();
        $this->assertTrue(Hash::check('NewSecurePassword#2026!', $this->admin->password));
    }

    public function test_admin_cannot_change_password_with_invalid_current_password(): void
    {
        $this->actingAs($this->admin);

        $response = $this->put('/admin/profile/password', [
            'current_password' => 'IncorrectCurrentPassword!',
            'new_password' => 'NewSecurePassword#2026!',
            'new_password_confirmation' => 'NewSecurePassword#2026!',
        ]);

        $response->assertSessionHasErrors('current_password');
    }
}

