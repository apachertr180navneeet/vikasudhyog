<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Unit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class UnitMasterTest extends TestCase
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

    public function test_unit_index_page_can_be_rendered(): void
    {
        $user = $this->getAdminUser();

        $response = $this->actingAs($user)->get(route('admin.masters.unit'));
        $response->assertStatus(200);
        $response->assertSee('Unit Master');
        $response->assertSee('Total Defined Units');
        $response->assertSee('Primary Base Units');
        $response->assertSee('Conversion Relations');
    }

    public function test_unit_create_page_can_be_rendered(): void
    {
        $user = $this->getAdminUser();

        $response = $this->actingAs($user)->get(route('admin.masters.unit.create'));
        $response->assertStatus(200);
        $response->assertSee('Add Measurement Unit');
        $response->assertSee('Unit Conversion &amp; Relation Logic', false);
        // Critical requirement: Ensure no status field in create form
        $response->assertDontSee('name="status"', false);
    }

    public function test_base_unit_can_be_created_and_defaults_to_active(): void
    {
        $user = $this->getAdminUser();

        $payload = [
            'name'           => 'Meter',
            'code'           => 'M',
            'uqc_code'       => 'MTR',
            'decimal_places' => 2,
            'description'    => 'Standard linear meter',
        ];

        $response = $this->actingAs($user)->post(route('admin.masters.unit.store'), $payload);
        $response->assertRedirect(route('admin.masters.unit'));

        $this->assertDatabaseHas('units', [
            'name'              => 'Meter',
            'code'              => 'M',
            'uqc_code'          => 'MTR',
            'decimal_places'    => 2,
            'is_base_unit'      => 1,
            'base_unit_id'      => null,
            'conversion_factor' => 1.0000,
            'status'            => 'active', // Automatically active
        ]);
    }

    public function test_derived_unit_relation_can_be_created_100_cm_equals_1_m(): void
    {
        $user = $this->getAdminUser();

        // 1. Create base unit Meter
        $meter = Unit::create([
            'name'              => 'Meter',
            'code'              => 'M',
            'uqc_code'          => 'MTR',
            'decimal_places'    => 2,
            'is_base_unit'      => true,
            'conversion_factor' => 1.0000,
            'status'            => 'active',
        ]);

        // 2. Create derived unit Centimeter with 100 cm = 1 m relation
        $payload = [
            'name'              => 'Centimeter',
            'code'              => 'CM',
            'uqc_code'          => 'CMS',
            'decimal_places'    => 2,
            'base_unit_id'      => $meter->id,
            'conversion_factor' => 100.0000,
            'operator'          => '/',
            'description'       => '100 cm = 1 m conversion relation',
        ];

        $response = $this->actingAs($user)->post(route('admin.masters.unit.store'), $payload);
        $response->assertRedirect(route('admin.masters.unit'));

        $cm = Unit::where('code', 'CM')->first();
        $this->assertNotNull($cm);
        $this->assertEquals($meter->id, $cm->base_unit_id);
        $this->assertFalse($cm->is_base_unit);
        $this->assertEquals('100 CM = 1 M', $cm->relation_formula);
        $this->assertEquals(1.0, $cm->toBase(100)); // 100 CM -> 1 M
        $this->assertEquals(2.5, $cm->toBase(250)); // 250 CM -> 2.5 M
        $this->assertEquals(100.0, $cm->fromBase(1)); // 1 M -> 100 CM
    }

    public function test_unit_show_page_can_be_rendered(): void
    {
        $user = $this->getAdminUser();

        $unit = Unit::create([
            'name'              => 'Kilogram',
            'code'              => 'KG',
            'uqc_code'          => 'KGS',
            'decimal_places'    => 2,
            'is_base_unit'      => true,
            'conversion_factor' => 1.0000,
            'status'            => 'active',
        ]);

        $response = $this->actingAs($user)->get(route('admin.masters.unit.show', $unit->id));
        $response->assertStatus(200);
        $response->assertSee('Unit Dossier &amp; Conversion Relation', false);
        $response->assertSee('Kilogram');
        $response->assertSee('KG');
    }

    public function test_unit_edit_page_can_be_rendered(): void
    {
        $user = $this->getAdminUser();

        $unit = Unit::create([
            'name'              => 'Box',
            'code'              => 'BOX',
            'uqc_code'          => 'BOX',
            'decimal_places'    => 0,
            'is_base_unit'      => true,
            'conversion_factor' => 1.0000,
            'status'            => 'active',
        ]);

        $response = $this->actingAs($user)->get(route('admin.masters.unit.edit', $unit->id));
        $response->assertStatus(200);
        $response->assertSee('Edit Measurement Unit');
        $response->assertSee('BOX');
        // Critical requirement: Ensure no status field in edit form
        $response->assertDontSee('name="status"', false);
    }

    public function test_unit_can_be_updated(): void
    {
        $user = $this->getAdminUser();

        $unit = Unit::create([
            'name'              => 'Gram',
            'code'              => 'GM',
            'uqc_code'          => 'GMS',
            'decimal_places'    => 2,
            'is_base_unit'      => true,
            'conversion_factor' => 1.0000,
            'status'            => 'active',
        ]);

        $payload = [
            'name'           => 'Gram Standard Metric',
            'code'           => 'GM',
            'uqc_code'       => 'GMS',
            'decimal_places' => 3,
            'description'    => 'Updated precision to 3 decimals',
        ];

        $response = $this->actingAs($user)->put(route('admin.masters.unit.update', $unit->id), $payload);
        $response->assertRedirect(route('admin.masters.unit'));

        $this->assertDatabaseHas('units', [
            'id'             => $unit->id,
            'name'           => 'Gram Standard Metric',
            'decimal_places' => 3,
        ]);
    }

    public function test_unit_status_can_be_toggled(): void
    {
        $user = $this->getAdminUser();

        $unit = Unit::create([
            'name'              => 'Quintal',
            'code'              => 'QTL',
            'uqc_code'          => 'QTL',
            'decimal_places'    => 2,
            'is_base_unit'      => true,
            'conversion_factor' => 1.0000,
            'status'            => 'active',
        ]);

        $response = $this->actingAs($user)->patch(route('admin.masters.unit.toggle-status', $unit->id));
        $response->assertRedirect();
        $this->assertEquals('inactive', $unit->fresh()->status);

        // Toggle back
        $this->actingAs($user)->patch(route('admin.masters.unit.toggle-status', $unit->id));
        $this->assertEquals('active', $unit->fresh()->status);
    }

    public function test_unit_can_be_soft_deleted(): void
    {
        $user = $this->getAdminUser();

        $unit = Unit::create([
            'name'              => 'Dozen',
            'code'              => 'DOZ',
            'uqc_code'          => 'DOZ',
            'decimal_places'    => 0,
            'is_base_unit'      => true,
            'conversion_factor' => 1.0000,
            'status'            => 'active',
        ]);

        $response = $this->actingAs($user)->delete(route('admin.masters.unit.destroy', $unit->id));
        $response->assertRedirect(route('admin.masters.unit'));

        $this->assertSoftDeleted('units', ['id' => $unit->id]);
    }
}
