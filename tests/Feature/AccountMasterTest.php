<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Account;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class AccountMasterTest extends TestCase
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

    public function test_account_index_page_can_be_rendered(): void
    {
        $user = $this->getAdminUser();

        $response = $this->actingAs($user)->get(route('admin.masters.account'));
        $response->assertStatus(200);
        $response->assertSee('Account Master');
        $response->assertSee('Total Chart Ledgers');
        $response->assertSee('Total Bank Liquidity');
        $response->assertSee('Liquid Cash Reserves');
    }

    public function test_account_create_page_can_be_rendered(): void
    {
        $user = $this->getAdminUser();

        $response = $this->actingAs($user)->get(route('admin.masters.account.create'));
        $response->assertStatus(200);
        $response->assertSee('Add Account Ledger');
        $response->assertSee('Live Account Preview');
        // Critical requirement: Ensure no status field in create form
        $response->assertDontSee('name="status"', false);
    }

    public function test_account_can_be_stored_and_defaults_to_active(): void
    {
        $user = $this->getAdminUser();

        $payload = [
            'name'            => 'ICICI Bank - Export Current A/c',
            'code'            => 'BNK-99',
            'account_group'   => 'Bank Accounts',
            'opening_balance' => 350000.00,
            'balance_type'    => 'debit',
            'bank_name'       => 'ICICI Bank',
            'account_number'  => '9876543210',
            'ifsc_code'       => 'ICIC0001234',
            'branch_name'     => 'Jodhpur Mandi',
            'upi_id'          => 'vikasudhyog@icici',
            'notes'           => 'Dedicated foreign remittances and export receipts account',
        ];

        $response = $this->actingAs($user)->post(route('admin.masters.account.store'), $payload);
        $response->assertRedirect(route('admin.masters.account'));

        $this->assertDatabaseHas('accounts', [
            'name'            => 'ICICI Bank - Export Current A/c',
            'code'            => 'BNK-99',
            'account_group'   => 'Bank Accounts',
            'opening_balance' => 350000.00,
            'current_balance' => 350000.00,
            'status'          => 'active', // Automatically active per guidelines
        ]);
    }

    public function test_account_show_page_can_be_rendered(): void
    {
        $user = $this->getAdminUser();

        $account = Account::create([
            'name'            => 'State Bank of India',
            'code'            => 'BNK-01',
            'account_group'   => 'Bank Accounts',
            'opening_balance' => 500000.00,
            'current_balance' => 500000.00,
            'balance_type'    => 'debit',
            'bank_name'       => 'State Bank of India',
            'account_number'  => '1122334455',
            'status'          => 'active',
        ]);

        $response = $this->actingAs($user)->get(route('admin.masters.account.show', $account->id));
        $response->assertStatus(200);
        $response->assertSee('Account Ledger Dossier');
        $response->assertSee('State Bank of India');
        $response->assertSee('BNK-01');
    }

    public function test_account_edit_page_can_be_rendered(): void
    {
        $user = $this->getAdminUser();

        $account = Account::create([
            'name'            => 'Cash in Hand',
            'code'            => 'CSH-01',
            'account_group'   => 'Cash in Hand',
            'opening_balance' => 25000.00,
            'current_balance' => 25000.00,
            'balance_type'    => 'debit',
            'status'          => 'active',
        ]);

        $response = $this->actingAs($user)->get(route('admin.masters.account.edit', $account->id));
        $response->assertStatus(200);
        $response->assertSee('Edit Account Ledger');
        $response->assertSee('CSH-01');
        // Critical requirement: Ensure no status field in edit form
        $response->assertDontSee('name="status"', false);
    }

    public function test_account_can_be_updated(): void
    {
        $user = $this->getAdminUser();

        $account = Account::create([
            'name'            => 'Factory Maintenance Expense',
            'code'            => 'EXP-01',
            'account_group'   => 'Direct Expenses',
            'opening_balance' => 0.00,
            'current_balance' => 0.00,
            'balance_type'    => 'debit',
            'status'          => 'active',
        ]);

        $payload = [
            'name'            => 'Factory Machinery Repair & Maintenance',
            'code'            => 'EXP-01',
            'account_group'   => 'Direct Expenses',
            'opening_balance' => 5000.00,
            'current_balance' => 7500.00,
            'balance_type'    => 'debit',
            'notes'           => 'Updated maintenance ledger',
        ];

        $response = $this->actingAs($user)->put(route('admin.masters.account.update', $account->id), $payload);
        $response->assertRedirect(route('admin.masters.account'));

        $this->assertDatabaseHas('accounts', [
            'id'              => $account->id,
            'name'            => 'Factory Machinery Repair & Maintenance',
            'current_balance' => 7500.00,
        ]);
    }

    public function test_account_status_can_be_toggled(): void
    {
        $user = $this->getAdminUser();

        $account = Account::create([
            'name'            => 'General Sales',
            'code'            => 'INC-01',
            'account_group'   => 'Direct Incomes',
            'opening_balance' => 0.00,
            'current_balance' => 0.00,
            'balance_type'    => 'credit',
            'status'          => 'active',
        ]);

        $response = $this->actingAs($user)->patch(route('admin.masters.account.toggle-status', $account->id));
        $response->assertRedirect();
        $this->assertEquals('inactive', $account->fresh()->status);

        // Toggle back
        $this->actingAs($user)->patch(route('admin.masters.account.toggle-status', $account->id));
        $this->assertEquals('active', $account->fresh()->status);
    }

    public function test_account_can_be_soft_deleted(): void
    {
        $user = $this->getAdminUser();

        $account = Account::create([
            'name'            => 'Old Bank Account',
            'code'            => 'BNK-98',
            'account_group'   => 'Bank Accounts',
            'opening_balance' => 0.00,
            'current_balance' => 0.00,
            'balance_type'    => 'debit',
            'status'          => 'active',
        ]);

        $response = $this->actingAs($user)->delete(route('admin.masters.account.destroy', $account->id));
        $response->assertRedirect(route('admin.masters.account'));

        $this->assertSoftDeleted('accounts', ['id' => $account->id]);
    }

    public function test_account_code_generator_endpoint(): void
    {
        $user = $this->getAdminUser();

        $response = $this->actingAs($user)->getJson(route('admin.masters.account.generate-code', ['group' => 'Bank Accounts']));
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'code', 'prefix']);
        $this->assertTrue($response->json('success'));
        $this->assertStringStartsWith('BNK-', $response->json('code'));
    }
}
