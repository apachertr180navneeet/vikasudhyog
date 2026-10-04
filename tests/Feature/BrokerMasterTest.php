<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Broker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class BrokerMasterTest extends TestCase
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

    public function test_broker_index_page_can_be_rendered(): void
    {
        $user = $this->getAdminUser();

        $response = $this->actingAs($user)->get(route('admin.masters.broker'));
        $response->assertStatus(200);
        $response->assertSee('Broker Master');
        $response->assertSee('Total Brokers');
    }

    public function test_broker_create_page_can_be_rendered_without_status_field(): void
    {
        $user = $this->getAdminUser();

        $response = $this->actingAs($user)->get(route('admin.masters.broker.create'));
        $response->assertStatus(200);
        $response->assertSee('Add New Broker Profile');
        // Verify no status input exists in create form per AGENTS.md
        $response->assertDontSee('name="status"', false);
    }

    public function test_broker_can_be_stored_and_defaults_to_active(): void
    {
        $user = $this->getAdminUser();

        $payload = [
            'code'            => 'BRK-01',
            'name'            => 'Mandi Broker Ram Prasad',
            'contact_person'  => 'Ram Prasad',
            'phone'           => '9829011223',
            'email'           => 'ramprasad@gmail.com',
            'commission_rate' => 1.50,
            'brokerage_type'  => 'Percentage (%)',
            'pan'             => 'BRKPR1234F',
            'city'            => 'Sojat',
            'state'           => 'Rajasthan',
            'pincode'         => '306104',
            'opening_balance' => 0.00,
        ];

        $response = $this->actingAs($user)->post(route('admin.masters.broker.store'), $payload);
        $response->assertRedirect(route('admin.masters.broker'));

        $this->assertDatabaseHas('brokers', [
            'code'            => 'BRK-01',
            'name'            => 'Mandi Broker Ram Prasad',
            'commission_rate' => 1.50,
            'status'          => 'active',
        ]);
    }

    public function test_broker_show_page_can_be_rendered(): void
    {
        $user = $this->getAdminUser();

        $broker = Broker::create([
            'code'            => 'BRK-02',
            'name'            => 'Show Broker',
            'phone'           => '9829022334',
            'commission_rate' => 2.00,
            'opening_balance' => 0.00,
            'current_balance' => 0.00,
            'status'          => 'active',
        ]);

        $response = $this->actingAs($user)->get(route('admin.masters.broker.show', $broker));
        $response->assertStatus(200);
        $response->assertSee('Show Broker');
        $response->assertSee('BRK-02');
    }

    public function test_broker_edit_page_can_be_rendered_without_status_field(): void
    {
        $user = $this->getAdminUser();

        $broker = Broker::create([
            'code'            => 'BRK-03',
            'name'            => 'Edit Broker',
            'phone'           => '9829033445',
            'commission_rate' => 1.00,
            'opening_balance' => 500.00,
            'current_balance' => 500.00,
            'status'          => 'active',
        ]);

        $response = $this->actingAs($user)->get(route('admin.masters.broker.edit', $broker));
        $response->assertStatus(200);
        $response->assertSee('Edit Broker Profile');
        // Verify no status input exists in edit form per AGENTS.md
        $response->assertDontSee('name="status"', false);
    }

    public function test_broker_can_be_updated(): void
    {
        $user = $this->getAdminUser();

        $broker = Broker::create([
            'code'            => 'BRK-04',
            'name'            => 'Original Broker',
            'phone'           => '9829044556',
            'commission_rate' => 1.00,
            'opening_balance' => 0.00,
            'current_balance' => 0.00,
            'status'          => 'active',
        ]);

        $payload = [
            'code'            => 'BRK-04',
            'name'            => 'Updated Broker Name',
            'phone'           => '9829044556',
            'commission_rate' => 2.50,
            'brokerage_type'  => 'Percentage (%)',
            'opening_balance' => 0.00,
        ];

        $response = $this->actingAs($user)->put(route('admin.masters.broker.update', $broker), $payload);
        $response->assertRedirect(route('admin.masters.broker'));

        $this->assertDatabaseHas('brokers', [
            'id'              => $broker->id,
            'name'            => 'Updated Broker Name',
            'commission_rate' => 2.50,
        ]);
    }

    public function test_broker_status_can_be_toggled(): void
    {
        $user = $this->getAdminUser();

        $broker = Broker::create([
            'code'            => 'BRK-05',
            'name'            => 'Toggle Broker',
            'phone'           => '9829055667',
            'commission_rate' => 1.00,
            'opening_balance' => 0.00,
            'current_balance' => 0.00,
            'status'          => 'active',
        ]);

        $this->actingAs($user)->patch(route('admin.masters.broker.toggle-status', $broker));
        $this->assertEquals('inactive', $broker->fresh()->status);
    }

    public function test_broker_can_be_deleted(): void
    {
        $user = $this->getAdminUser();

        $broker = Broker::create([
            'code'            => 'BRK-06',
            'name'            => 'Delete Broker',
            'phone'           => '9829066778',
            'commission_rate' => 1.00,
            'opening_balance' => 0.00,
            'current_balance' => 0.00,
            'status'          => 'active',
        ]);

        $response = $this->actingAs($user)->delete(route('admin.masters.broker.destroy', $broker));
        $response->assertRedirect(route('admin.masters.broker'));

        $this->assertSoftDeleted('brokers', ['id' => $broker->id]);
    }

    public function test_broker_generate_code_returns_json(): void
    {
        $user = $this->getAdminUser();

        $response = $this->actingAs($user)->get(route('admin.masters.broker.generate-code'));
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'code']);
    }
}
