<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class ServerMigrationRunnerTest extends TestCase
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

    public function test_run_migration_route_is_accessible(): void
    {
        $response = $this->get('/run-migration?action=status');
        $response->assertStatus(200);
        $response->assertSee('Server Database Migration Runner');
        $response->assertSee('migrate:status');
    }

    public function test_run_migration_migrate_only_action(): void
    {
        $response = $this->get('/run-migration?action=migrate-only');
        $response->assertStatus(200);
        $response->assertSee('Execution Completed Successfully');
        $response->assertSee('php artisan migrate');
    }

    public function test_run_migration_returns_json_when_requested(): void
    {
        $response = $this->getJson('/run-migration?action=status');
        $response->assertStatus(200);
        $response->assertJsonStructure(['status', 'message', 'outputs']);
        $this->assertEquals('success', $response->json('status'));
    }

    public function test_backup_restore_page_shows_migration_card(): void
    {
        $user = $this->getAdminUser();

        $response = $this->actingAs($user)->get(route('admin.settings.backup-restore'));
        $response->assertStatus(200);
        $response->assertSee('Server Database Migration');
        $response->assertSee('Run Updated Migrations Only');
    }
}
