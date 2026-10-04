<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class VendorMasterTest extends TestCase
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

    public function test_vendor_index_page_can_be_rendered(): void
    {
        $user = $this->getAdminUser();

        $response = $this->actingAs($user)->get(route('admin.masters.vendor'));
        $response->assertStatus(200);
        $response->assertSee('Vendor Master');
        $response->assertSee('Total Suppliers');
    }

    public function test_vendor_create_page_can_be_rendered_without_status_field(): void
    {
        $user = $this->getAdminUser();

        $response = $this->actingAs($user)->get(route('admin.masters.vendor.create'));
        $response->assertStatus(200);
        $response->assertSee('Add New Vendor Profile');
        // Verify no status input exists in create form per AGENTS.md
        $response->assertDontSee('name="status"', false);
    }

    public function test_vendor_can_be_stored_and_defaults_to_active(): void
    {
        $user = $this->getAdminUser();

        $payload = [
            'code'            => 'VND-01',
            'name'            => 'Marwar Agriculture Henna Suppliers',
            'vendor_type'     => 'Raw Material',
            'contact_person'  => 'Mangi Lal',
            'phone'           => '9829012345',
            'email'           => 'mangi@marwarhenna.com',
            'gstin'           => '08AABCM1234F1Z9',
            'pan'             => 'AABCM1234F',
            'city'            => 'Sojat City',
            'state'           => 'Rajasthan',
            'pincode'         => '306104',
            'credit_limit'    => 500000.00,
            'payment_terms'   => '15 Days',
            'opening_balance' => 25000.00,
        ];

        $response = $this->actingAs($user)->post(route('admin.masters.vendor.store'), $payload);
        $response->assertRedirect(route('admin.masters.vendor'));

        $this->assertDatabaseHas('vendors', [
            'code'   => 'VND-01',
            'name'   => 'Marwar Agriculture Henna Suppliers',
            'status' => 'active', // must default to active
        ]);
    }

    public function test_vendor_show_page_can_be_rendered(): void
    {
        $user = $this->getAdminUser();

        $vendor = Vendor::create([
            'code'            => 'VND-02',
            'name'            => 'Pali Chemical Industries',
            'vendor_type'     => 'Chemicals',
            'phone'           => '9829054321',
            'opening_balance' => 0.00,
            'current_balance' => 0.00,
            'status'          => 'active',
        ]);

        $response = $this->actingAs($user)->get(route('admin.masters.vendor.show', $vendor));
        $response->assertStatus(200);
        $response->assertSee('Pali Chemical Industries');
        $response->assertSee('VND-02');
    }

    public function test_vendor_edit_page_can_be_rendered_without_status_field(): void
    {
        $user = $this->getAdminUser();

        $vendor = Vendor::create([
            'code'            => 'VND-03',
            'name'            => 'Jaipur Packaging Ltd',
            'vendor_type'     => 'Packaging',
            'phone'           => '9829077777',
            'opening_balance' => 1000.00,
            'current_balance' => 1000.00,
            'status'          => 'active',
        ]);

        $response = $this->actingAs($user)->get(route('admin.masters.vendor.edit', $vendor));
        $response->assertStatus(200);
        $response->assertSee('Edit Vendor Profile');
        // Verify no status input exists in edit form per AGENTS.md
        $response->assertDontSee('name="status"', false);
    }

    public function test_vendor_can_be_updated(): void
    {
        $user = $this->getAdminUser();

        $vendor = Vendor::create([
            'code'            => 'VND-04',
            'name'            => 'Old Vendor Name',
            'vendor_type'     => 'Raw Material',
            'phone'           => '9829088888',
            'opening_balance' => 5000.00,
            'current_balance' => 5000.00,
            'status'          => 'active',
        ]);

        $payload = [
            'code'            => 'VND-04',
            'name'            => 'Updated Vendor Name',
            'vendor_type'     => 'Agricultural Goods',
            'phone'           => '9829088888',
            'opening_balance' => 6000.00,
        ];

        $response = $this->actingAs($user)->put(route('admin.masters.vendor.update', $vendor), $payload);
        $response->assertRedirect(route('admin.masters.vendor'));

        $this->assertDatabaseHas('vendors', [
            'id'   => $vendor->id,
            'name' => 'Updated Vendor Name',
        ]);
    }

    public function test_vendor_status_can_be_toggled(): void
    {
        $user = $this->getAdminUser();

        $vendor = Vendor::create([
            'code'            => 'VND-05',
            'name'            => 'Status Toggle Vendor',
            'vendor_type'     => 'Raw Material',
            'phone'           => '9829099999',
            'opening_balance' => 0.00,
            'current_balance' => 0.00,
            'status'          => 'active',
        ]);

        $this->actingAs($user)->patch(route('admin.masters.vendor.toggle-status', $vendor));
        $this->assertEquals('inactive', $vendor->fresh()->status);
    }

    public function test_vendor_can_be_deleted(): void
    {
        $user = $this->getAdminUser();

        $vendor = Vendor::create([
            'code'            => 'VND-06',
            'name'            => 'Delete Vendor',
            'vendor_type'     => 'Raw Material',
            'phone'           => '9829011111',
            'opening_balance' => 0.00,
            'current_balance' => 0.00,
            'status'          => 'active',
        ]);

        $response = $this->actingAs($user)->delete(route('admin.masters.vendor.destroy', $vendor));
        $response->assertRedirect(route('admin.masters.vendor'));

        $this->assertSoftDeleted('vendors', ['id' => $vendor->id]);
    }

    public function test_vendor_generate_code_returns_json(): void
    {
        $user = $this->getAdminUser();

        $response = $this->actingAs($user)->get(route('admin.masters.vendor.generate-code'));
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'code']);
    }
}
