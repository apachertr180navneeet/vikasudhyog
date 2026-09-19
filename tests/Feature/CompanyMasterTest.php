<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class CompanyMasterTest extends TestCase
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

    public function test_company_index_page_can_be_rendered(): void
    {
        $user = $this->getAdminUser();

        $response = $this->actingAs($user)->get('/admin/masters/company');
        $response->assertStatus(200);
        $response->assertSee('Company Master');
    }

    public function test_company_create_page_can_be_rendered(): void
    {
        $user = $this->getAdminUser();

        $response = $this->actingAs($user)->get('/admin/masters/company/create');
        $response->assertStatus(200);
        $response->assertSee('Add New Company');
        $response->assertSee('Add New Company Profile');
    }

    public function test_company_edit_page_can_be_rendered(): void
    {
        $user = $this->getAdminUser();
        $company = Company::firstOrCreate(
            ['name' => 'Test Company For Edit Page'],
            ['city' => 'Sojat City', 'state' => 'Rajasthan', 'status' => 'active']
        );

        $response = $this->actingAs($user)->get('/admin/masters/company/' . $company->id . '/edit');
        $response->assertStatus(200);
        $response->assertSee('Edit Company:');
        $response->assertSee($company->name);
    }

    public function test_company_show_page_can_be_rendered(): void
    {
        $user = $this->getAdminUser();
        $company = Company::firstOrCreate(
            ['name' => 'Test Company For Show Page'],
            ['city' => 'Sojat City', 'state' => 'Rajasthan', 'status' => 'active']
        );

        $response = $this->actingAs($user)->get('/admin/masters/company/' . $company->id);
        $response->assertStatus(200);
        $response->assertSee($company->name);
    }

    public function test_can_create_new_company(): void
    {
        $user = $this->getAdminUser();

        $response = $this->actingAs($user)->post('/admin/masters/company', [
            'name'            => 'Vikas Natural Organics',
            'code'            => 'VNO-NEW',
            'gstin'           => '08AABCV9999K1Z4',
            'pan'             => 'AABCV9999K',
            'phone'           => '+91 94140 55555',
            'email'           => 'organics@vikasudhyog.com',
            'city'            => 'Sojat City',
            'state'           => 'Rajasthan',
            'pincode'         => '306104',
            'status'          => 'active',
            'is_default'      => '0',
        ]);

        $response->assertRedirect('/admin/masters/company');
        $this->assertDatabaseHas('companies', [
            'name' => 'Vikas Natural Organics',
            'code' => 'VNO-NEW',
        ]);
    }

    public function test_can_view_company_json(): void
    {
        $user = $this->getAdminUser();
        $company = Company::firstOrCreate(
            ['name' => 'Test Company For View'],
            ['city' => 'Sojat City', 'state' => 'Rajasthan', 'status' => 'active']
        );

        $response = $this->actingAs($user)->getJson('/admin/masters/company/' . $company->id);
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'company' => [
                'id' => $company->id,
                'name' => $company->name,
            ]
        ]);
    }

    public function test_can_update_company(): void
    {
        $user = $this->getAdminUser();
        $company = Company::firstOrCreate(
            ['name' => 'Test Company For Update'],
            ['city' => 'Sojat City', 'state' => 'Rajasthan', 'status' => 'active']
        );

        $response = $this->actingAs($user)->put('/admin/masters/company/' . $company->id, [
            'name'            => $company->name,
            'code'            => 'VU-UPDATED',
            'city'            => 'Sojat City',
            'state'           => 'Rajasthan',
            'status'          => 'active',
        ]);

        $response->assertRedirect('/admin/masters/company');
        $this->assertDatabaseHas('companies', [
            'id'   => $company->id,
            'code' => 'VU-UPDATED',
        ]);
    }

    public function test_can_toggle_company_status(): void
    {
        $user = $this->getAdminUser();
        $company = Company::firstOrCreate(
            ['name' => 'Test Company For Toggle'],
            ['city' => 'Sojat City', 'state' => 'Rajasthan', 'status' => 'active']
        );
        $initialStatus = $company->status;

        $response = $this->actingAs($user)->patch('/admin/masters/company/' . $company->id . '/toggle-status');
        $response->assertStatus(302);

        $expectedStatus = $initialStatus === 'active' ? 'inactive' : 'active';
        $this->assertEquals($expectedStatus, $company->fresh()->status);
    }

    public function test_can_set_company_as_default(): void
    {
        $user = $this->getAdminUser();
        $company = Company::firstOrCreate(
            ['name' => 'Test Company For Default'],
            ['city' => 'Sojat City', 'state' => 'Rajasthan', 'status' => 'active', 'is_default' => false]
        );

        $response = $this->actingAs($user)->patch('/admin/masters/company/' . $company->id . '/set-default');
        $response->assertStatus(302);

        $this->assertTrue($company->fresh()->is_default);
    }
}
