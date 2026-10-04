<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class UserMasterTest extends TestCase
{
    use RefreshDatabase;

    protected function getAdminUser(): User
    {
        return User::firstOrCreate(
            ['username' => 'admin_test'],
            [
                'name'     => 'Admin Tester',
                'email'    => 'admin_test@vikasudhyog.com',
                'password' => Hash::make('password123'),
                'role'     => 'Super Administrator',
                'status'   => 'active',
            ]
        );
    }

    public function test_user_index_page_can_be_rendered(): void
    {
        $admin = $this->getAdminUser();

        $response = $this->actingAs($admin)->get(route('admin.masters.user'));
        $response->assertStatus(200);
        $response->assertSee('User Master');
        $response->assertSee('Total Users');
    }

    public function test_user_create_page_can_be_rendered(): void
    {
        $admin = $this->getAdminUser();

        $response = $this->actingAs($admin)->get(route('admin.masters.user.create'));
        $response->assertStatus(200);
        $response->assertSee('Add New User');
    }

    public function test_user_can_be_stored(): void
    {
        $admin = $this->getAdminUser();

        $payload = [
            'name'                  => 'Karan Sharma',
            'username'              => 'karansharma',
            'email'                 => 'karan@vikasudhyog.com',
            'phone'                 => '9829099111',
            'role'                  => 'Accounts Manager',
            'password'              => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
        ];

        $response = $this->actingAs($admin)->post(route('admin.masters.user.store'), $payload);
        $response->assertRedirect(route('admin.masters.user'));

        $this->assertDatabaseHas('users', [
            'username' => 'karansharma',
            'email'    => 'karan@vikasudhyog.com',
            'role'     => 'Accounts Manager',
            'status'   => 'active',
        ]);
    }

    public function test_user_show_page_can_be_rendered(): void
    {
        $admin = $this->getAdminUser();

        $testUser = User::create([
            'name'     => 'Pooja Verma',
            'username' => 'poojaverma',
            'email'    => 'pooja@vikasudhyog.com',
            'password' => Hash::make('secret123'),
            'role'     => 'Quality Inspector',
            'status'   => 'active',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.masters.user.show', $testUser));
        $response->assertStatus(200);
        $response->assertSee('Pooja Verma');
        $response->assertSee('poojaverma');
    }

    public function test_user_edit_page_can_be_rendered(): void
    {
        $admin = $this->getAdminUser();

        $testUser = User::create([
            'name'     => 'Vijay Kumar',
            'username' => 'vijaykumar',
            'email'    => 'vijay@vikasudhyog.com',
            'password' => Hash::make('secret123'),
            'role'     => 'Store Manager',
            'status'   => 'active',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.masters.user.edit', $testUser));
        $response->assertStatus(200);
        $response->assertSee('Vijay Kumar');
    }

    public function test_user_can_be_updated(): void
    {
        $admin = $this->getAdminUser();

        $testUser = User::create([
            'name'     => 'Nitesh Mali',
            'username' => 'niteshmali',
            'email'    => 'nitesh@vikasudhyog.com',
            'password' => Hash::make('secret123'),
            'role'     => 'Sales Executive',
            'status'   => 'active',
        ]);

        $payload = [
            'name'     => 'Nitesh Kumar Mali',
            'username' => 'niteshmali',
            'email'    => 'nitesh.mali@vikasudhyog.com',
            'phone'    => '9829033333',
            'role'     => 'Sales Manager',
        ];

        $response = $this->actingAs($admin)->put(route('admin.masters.user.update', $testUser), $payload);
        $response->assertRedirect(route('admin.masters.user'));

        $this->assertDatabaseHas('users', [
            'id'    => $testUser->id,
            'name'  => 'Nitesh Kumar Mali',
            'email' => 'nitesh.mali@vikasudhyog.com',
            'role'  => 'Sales Manager',
        ]);
    }

    public function test_user_status_can_be_toggled(): void
    {
        $admin = $this->getAdminUser();

        $testUser = User::create([
            'name'     => 'Toggle Status User',
            'username' => 'togglestatus',
            'email'    => 'toggle@vikasudhyog.com',
            'password' => Hash::make('secret123'),
            'role'     => 'Inventory Clerk',
            'status'   => 'active',
        ]);

        $this->actingAs($admin)->patch(route('admin.masters.user.toggle-status', $testUser));
        $this->assertEquals('inactive', $testUser->fresh()->status);
    }

    public function test_user_can_be_deleted(): void
    {
        $admin = $this->getAdminUser();

        $testUser = User::create([
            'name'     => 'Delete User',
            'username' => 'deleteuser',
            'email'    => 'delete@vikasudhyog.com',
            'password' => Hash::make('secret123'),
            'role'     => 'Inventory Clerk',
            'status'   => 'active',
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.masters.user.destroy', $testUser));
        $response->assertRedirect(route('admin.masters.user'));

        $this->assertSoftDeleted('users', ['id' => $testUser->id]);
    }
}
