<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Customer;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class CustomerMasterTest extends TestCase
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

    public function test_customer_index_page_can_be_rendered(): void
    {
        $user = $this->getAdminUser();

        $response = $this->actingAs($user)->get(route('admin.masters.customer'));
        $response->assertStatus(200);
        $response->assertSee('Customer Master');
        $response->assertSee('Total Customers');
        $response->assertSee('Total Receivables');
    }

    public function test_customer_create_page_can_be_rendered(): void
    {
        $user = $this->getAdminUser();

        $response = $this->actingAs($user)->get(route('admin.masters.customer.create'));
        $response->assertStatus(200);
        $response->assertSee('Add New Customer Profile');
        $response->assertSee('Approved Credit Limit');
        // Verify no status input exists in create form
        $response->assertDontSee('name="status"', false);
    }

    public function test_customer_can_be_stored(): void
    {
        $user = $this->getAdminUser();

        $payload = [
            'code'            => 'CST-99',
            'name'            => 'Shree Krishna Herbal Stores',
            'customer_type'   => 'Wholesaler',
            'contact_person'  => 'Krishna Gopal',
            'phone'           => '9829099887',
            'email'           => 'krishnaherbal@gmail.com',
            'gstin'           => '08AABCK1234F1Z9',
            'pan'             => 'AABCK1234F',
            'address'         => 'Shop 10, Mandi Complex',
            'city'            => 'Sojat',
            'state'           => 'Rajasthan',
            'pincode'         => '306104',
            'credit_limit'    => 450000.00,
            'payment_terms'   => '30 Days',
            'opening_balance' => 15000.00,
            'bank_name'       => 'SBI',
            'bank_account_no' => '1234567890',
            'bank_ifsc'       => 'SBIN0001234',
            'bank_branch'     => 'Sojat',
            'notes'           => 'Primary mehndi retailer',
        ];

        $response = $this->actingAs($user)->post(route('admin.masters.customer.store'), $payload);
        $response->assertRedirect(route('admin.masters.customer'));

        $this->assertDatabaseHas('customers', [
            'code'            => 'CST-99',
            'name'            => 'Shree Krishna Herbal Stores',
            'status'          => 'active', // Automatically active
            'current_balance' => 15000.00,
        ]);
    }

    public function test_customer_show_page_can_be_rendered(): void
    {
        $user = $this->getAdminUser();
        $customer = Customer::create([
            'code'            => 'CST-01',
            'name'            => 'Marwar Distributors',
            'customer_type'   => 'Distributor',
            'city'            => 'Jodhpur',
            'status'          => 'active',
            'credit_limit'    => 500000,
            'opening_balance' => 0,
            'current_balance' => 0,
        ]);

        $response = $this->actingAs($user)->get(route('admin.masters.customer.show', $customer->id));
        $response->assertStatus(200);
        $response->assertSee('Marwar Distributors');
        $response->assertSee('CST-01');
    }

    public function test_customer_edit_page_can_be_rendered(): void
    {
        $user = $this->getAdminUser();
        $customer = Customer::create([
            'code'            => 'CST-02',
            'name'            => 'Raj Traders',
            'customer_type'   => 'Wholesaler',
            'city'            => 'Sojat',
            'status'          => 'active',
            'credit_limit'    => 300000,
            'opening_balance' => 0,
            'current_balance' => 0,
        ]);

        $response = $this->actingAs($user)->get(route('admin.masters.customer.edit', $customer->id));
        $response->assertStatus(200);
        $response->assertSee('Edit Customer:');
        $response->assertSee('Raj Traders');
        // Verify no status input exists in edit form
        $response->assertDontSee('name="status"', false);
    }

    public function test_customer_can_be_updated(): void
    {
        $user = $this->getAdminUser();
        $customer = Customer::create([
            'code'            => 'CST-03',
            'name'            => 'Old Customer Name',
            'customer_type'   => 'Retailer',
            'city'            => 'Pali',
            'status'          => 'active',
            'credit_limit'    => 100000,
            'opening_balance' => 0,
            'current_balance' => 0,
        ]);

        $response = $this->actingAs($user)->put(route('admin.masters.customer.update', $customer->id), [
            'code'            => 'CST-03',
            'name'            => 'Updated Customer Name',
            'customer_type'   => 'Retailer',
            'credit_limit'    => 200000,
            'payment_terms'   => '15 Days',
            'city'            => 'Pali',
            'state'           => 'Rajasthan',
        ]);

        $response->assertRedirect(route('admin.masters.customer'));

        $this->assertDatabaseHas('customers', [
            'id'           => $customer->id,
            'name'         => 'Updated Customer Name',
            'credit_limit' => 200000.00,
        ]);
    }

    public function test_customer_status_can_be_toggled(): void
    {
        $user = $this->getAdminUser();
        $customer = Customer::create([
            'code'            => 'CST-04',
            'name'            => 'Status Test Customer',
            'customer_type'   => 'Direct Client',
            'status'          => 'active',
            'credit_limit'    => 100000,
            'opening_balance' => 0,
            'current_balance' => 0,
        ]);

        $this->actingAs($user)->patch(route('admin.masters.customer.toggle-status', $customer->id));
        $this->assertEquals('inactive', $customer->fresh()->status);

        $this->actingAs($user)->patch(route('admin.masters.customer.toggle-status', $customer->id));
        $this->assertEquals('active', $customer->fresh()->status);
    }

    public function test_customer_can_be_archived(): void
    {
        $user = $this->getAdminUser();
        $customer = Customer::create([
            'code'            => 'CST-05',
            'name'            => 'Archive Test Customer',
            'customer_type'   => 'Retailer',
            'status'          => 'active',
            'credit_limit'    => 100000,
            'opening_balance' => 0,
            'current_balance' => 0,
        ]);

        $response = $this->actingAs($user)->delete(route('admin.masters.customer.destroy', $customer->id));
        $response->assertRedirect(route('admin.masters.customer'));

        $this->assertSoftDeleted('customers', ['id' => $customer->id]);
    }

    public function test_customer_generate_code_api_returns_unique_code(): void
    {
        $user = $this->getAdminUser();

        $response = $this->actingAs($user)->getJson(route('admin.masters.customer.generate-code'));
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'code']);
        $this->assertTrue(str_starts_with($response->json('code'), 'CST-'));
    }
}
