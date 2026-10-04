<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\RolePermission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class AccessLevelTest extends TestCase
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

    public function test_access_level_index_page_can_be_rendered(): void
    {
        $user = $this->getAdminUser();

        $response = $this->actingAs($user)->get(route('admin.masters.access-level'));
        $response->assertStatus(200);
        $response->assertSee('Access Level');
        $response->assertSee('Super Administrator');
    }

    public function test_custom_role_can_be_created(): void
    {
        $user = $this->getAdminUser();

        $payload = [
            'name'        => 'Custom Mandi Officer',
            'icon'        => 'fa-wheat-awn',
            'color'       => '#5B841E',
            'bg'          => '#F0FDF4',
            'badge'       => 'Procurement Role',
            'description' => 'Handles direct Mandi purchases',
        ];

        $response = $this->actingAs($user)->post(route('admin.masters.access-level.store'), $payload);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('roles', [
            'name' => 'Custom Mandi Officer',
            'slug' => 'custom-mandi-officer',
        ]);
    }

    public function test_role_permissions_can_be_saved(): void
    {
        $user = $this->getAdminUser();

        $role = Role::create([
            'name'      => 'Test Auditor',
            'slug'      => 'test-auditor',
            'icon'      => 'fa-calculator',
            'color'     => '#059669',
            'bg'        => '#ECFDF5',
            'is_system' => false,
            'status'    => 'active',
        ]);

        $payload = [
            'permissions' => [
                'company' => [
                    'can_view'   => 1,
                    'can_add'    => 0,
                    'can_edit'   => 0,
                    'can_delete' => 0,
                    'can_export' => 1,
                ],
                'purchase_entry' => [
                    'can_view'   => 1,
                    'can_add'    => 0,
                    'can_edit'   => 0,
                    'can_delete' => 0,
                    'can_export' => 1,
                ],
            ]
        ];

        $response = $this->actingAs($user)->post(
            route('admin.masters.access-level.save-permissions', $role),
            $payload
        );

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('role_permissions', [
            'role_id'    => $role->id,
            'module_key' => 'company',
            'can_view'   => true,
            'can_add'    => false,
            'can_export' => true,
        ]);
    }

    public function test_role_status_can_be_toggled(): void
    {
        $user = $this->getAdminUser();

        $role = Role::create([
            'name'      => 'Toggleable Role',
            'slug'      => 'toggleable-role',
            'is_system' => false,
            'status'    => 'active',
        ]);

        $response = $this->actingAs($user)->patch(
            route('admin.masters.access-level.toggle-status', $role)
        );

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertEquals('inactive', $role->fresh()->status);
    }
}
