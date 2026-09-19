<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/admin/login');
        $response->assertStatus(200);
        $response->assertSee('VIKAS UDHYOG');
    }

    public function test_unauthenticated_user_is_redirected_from_dashboard(): void
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect('/admin/login');
    }

    public function test_user_can_authenticate_with_username(): void
    {
        $user = User::updateOrCreate(
            ['username' => 'testadmin'],
            [
                'name'     => 'Test Admin',
                'email'    => 'testadmin@vikasudhyog.com',
                'password' => Hash::make('secret123'),
                'role'     => 'Super Administrator',
                'status'   => 'active',
            ]
        );

        $response = $this->post('/admin/login', [
            'username' => 'testadmin',
            'password' => 'secret123',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect('/admin/dashboard');
    }

    public function test_user_can_authenticate_with_email(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'manager_test@vikasudhyog.com'],
            [
                'name'     => 'Manager Test',
                'username' => 'manager_test',
                'password' => Hash::make('secret123'),
                'role'     => 'Plant Manager',
                'status'   => 'active',
            ]
        );

        $response = $this->post('/admin/login', [
            'username' => 'manager_test@vikasudhyog.com',
            'password' => 'secret123',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect('/admin/dashboard');
    }

    public function test_users_cannot_authenticate_with_invalid_password(): void
    {
        $response = $this->post('/admin/login', [
            'username' => 'admin',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_user_can_logout(): void
    {
        $user = User::where('username', 'admin')->first();
        if (!$user) {
            $user = User::create([
                'name'     => 'Administrator',
                'username' => 'admin',
                'email'    => 'admin@vikasudhyog.com',
                'password' => Hash::make('admin123'),
                'role'     => 'Super Administrator',
                'status'   => 'active',
            ]);
        }

        $response = $this->actingAs($user)->post('/admin/logout');
        $this->assertGuest();
        $response->assertRedirect('/admin/login');
    }
}
