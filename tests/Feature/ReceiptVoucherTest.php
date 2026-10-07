<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Customer;
use App\Models\ReceiptVoucher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ReceiptVoucherTest extends TestCase
{
    use RefreshDatabase;

    protected function getAdminUser(): User
    {
        return User::firstOrCreate(
            ['username' => 'admin_tester'],
            [
                'name'     => 'Admin Tester',
                'email'    => 'admin_tester@vikasudhyog.com',
                'password' => Hash::make('password123'),
                'role'     => 'Super Administrator',
                'status'   => 'active',
            ]
        );
    }

    protected function createCustomer(float $balance = 50000.00): Customer
    {
        return Customer::create([
            'code'            => 'CST-TEST-001',
            'name'            => 'Marwar Herbals Ltd',
            'status'          => 'active',
            'opening_balance' => $balance,
            'current_balance' => $balance,
        ]);
    }

    protected function createAccount(string $type = 'Bank Accounts', float $balance = 100000.00): Account
    {
        return Account::create([
            'code'            => 'BNK-TEST-01',
            'name'            => 'State Bank of India - Current A/c',
            'account_group'   => $type,
            'opening_balance' => $balance,
            'current_balance' => $balance,
            'balance_type'    => 'Dr',
            'status'          => 'active',
        ]);
    }

    public function test_receipt_voucher_index_page_can_be_rendered(): void
    {
        $user = $this->getAdminUser();

        $response = $this->actingAs($user)->get(route('admin.transactions.receipt-voucher'));
        $response->assertStatus(200);
        $response->assertSee('Receipt Voucher');
        $response->assertSee('Total Receipts');
        $response->assertSee('Total Funds Received');
        $response->assertSee('Customer Collections');
        $response->assertSee('Direct Incomes');
    }

    public function test_receipt_voucher_create_page_can_be_rendered_without_status_field(): void
    {
        $user = $this->getAdminUser();

        $response = $this->actingAs($user)->get(route('admin.transactions.receipt-voucher.create'));
        $response->assertStatus(200);
        $response->assertSee('New Receipt Voucher');
        $response->assertSee('1. Voucher Header &amp; Payer Identity', false);
        $response->assertSee('2. Financial Particulars &amp; Banking Settlement', false);
        $response->assertSee('Received Amount (₹)', false);

        // Strict Rule 4: Status must NEVER appear as an input/select on create form
        $response->assertDontSee('name="status"', false);
    }

    public function test_receipt_voucher_customer_collection_can_be_stored_and_deducts_customer_balance_and_increases_account_balance(): void
    {
        $user = $this->getAdminUser();
        $customer = $this->createCustomer(50000.00); // 50k outstanding
        $account = $this->createAccount('Bank Accounts', 100000.00); // 100k balance

        $payload = [
            'voucher_no'      => 'RCP-2026-9001',
            'voucher_date'    => '2026-10-07',
            'receipt_type'    => 'Customer',
            'customer_id'     => $customer->id,
            'account_id'      => $account->id,
            'amount'          => 20000.00,
            'payment_mode'    => 'Bank Transfer',
            'reference_no'    => 'UTR-SBIN-889911',
            'reference_date'  => '2026-10-07',
            'against_invoice' => 'SAL-2026-0001',
            'notes'           => 'Received on account against invoice 0001',
        ];

        $response = $this->actingAs($user)->post(route('admin.transactions.receipt-voucher.store'), $payload);
        $response->assertRedirect(route('admin.transactions.receipt-voucher'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('receipt_vouchers', [
            'voucher_no'   => 'RCP-2026-9001',
            'receipt_type' => 'Customer',
            'customer_id'  => $customer->id,
            'amount'       => 20000.00,
            'status'       => 'active', // Rule 4: defaults to active
        ]);

        // Customer debt reduces by 20,000: 50,000 - 20,000 = 30,000
        $this->assertEquals(30000.00, (float)$customer->fresh()->current_balance);

        // Receiving account increases by 20,000: 100,000 + 20,000 = 120,000
        $this->assertEquals(120000.00, (float)$account->fresh()->current_balance);
    }

    public function test_receipt_voucher_direct_income_can_be_stored_and_increases_account_balance(): void
    {
        $user = $this->getAdminUser();
        $account = $this->createAccount('Cash in Hand', 25000.00);

        $payload = [
            'voucher_no'      => 'RCP-2026-9002',
            'voucher_date'    => '2026-10-07',
            'receipt_type'    => 'Income',
            'income_source'   => 'Scrap & Waste Sale',
            'account_id'      => $account->id,
            'amount'          => 4500.00,
            'payment_mode'    => 'Cash',
            'notes'           => 'Henna packing bag waste scrap disposal',
        ];

        $response = $this->actingAs($user)->post(route('admin.transactions.receipt-voucher.store'), $payload);
        $response->assertRedirect(route('admin.transactions.receipt-voucher'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('receipt_vouchers', [
            'voucher_no'    => 'RCP-2026-9002',
            'receipt_type'  => 'Income',
            'income_source' => 'Scrap & Waste Sale',
            'amount'        => 4500.00,
            'status'        => 'active',
        ]);

        // Cash account increases by 4,500: 25,000 + 4,500 = 29,500
        $this->assertEquals(29500.00, (float)$account->fresh()->current_balance);
    }

    public function test_receipt_voucher_show_page_can_be_rendered(): void
    {
        $user = $this->getAdminUser();
        $customer = $this->createCustomer();
        $account = $this->createAccount();

        $voucher = ReceiptVoucher::create([
            'voucher_no'   => 'RCP-2026-9003',
            'voucher_date' => '2026-10-07',
            'receipt_type' => 'Customer',
            'customer_id'  => $customer->id,
            'account_id'   => $account->id,
            'amount'       => 15000.00,
            'payment_mode' => 'UPI',
            'reference_no' => 'UPI-99228811',
            'status'       => 'active',
        ]);

        $response = $this->actingAs($user)->get(route('admin.transactions.receipt-voucher.show', $voucher));
        $response->assertStatus(200);
        $response->assertSee('RCP-2026-9003');
        $response->assertSee('Marwar Herbals Ltd');
        $response->assertSee('15,000.00');
    }

    public function test_receipt_voucher_edit_page_can_be_rendered_without_status_field(): void
    {
        $user = $this->getAdminUser();
        $customer = $this->createCustomer();
        $account = $this->createAccount();

        $voucher = ReceiptVoucher::create([
            'voucher_no'   => 'RCP-2026-9004',
            'voucher_date' => '2026-10-07',
            'receipt_type' => 'Customer',
            'customer_id'  => $customer->id,
            'account_id'   => $account->id,
            'amount'       => 15000.00,
            'payment_mode' => 'UPI',
            'status'       => 'active',
        ]);

        $response = $this->actingAs($user)->get(route('admin.transactions.receipt-voucher.edit', $voucher));
        $response->assertStatus(200);
        $response->assertSee('Edit Receipt Voucher');
        $response->assertSee('RCP-2026-9004');

        // Strict Rule 4: Status must NEVER appear as an input/select on edit form
        $response->assertDontSee('name="status"', false);
    }

    public function test_receipt_voucher_can_be_updated_and_reconciles_balances(): void
    {
        $user = $this->getAdminUser();
        $customer = $this->createCustomer(50000.00); // 50,000
        $account = $this->createAccount('Bank Accounts', 100000.00); // 100,000

        // Initial store: 10,000
        $this->actingAs($user)->post(route('admin.transactions.receipt-voucher.store'), [
            'voucher_no'   => 'RCP-2026-9005',
            'voucher_date' => '2026-10-07',
            'receipt_type' => 'Customer',
            'customer_id'  => $customer->id,
            'account_id'   => $account->id,
            'amount'       => 10000.00,
            'payment_mode' => 'Cheque',
        ]);

        $voucher = ReceiptVoucher::where('voucher_no', 'RCP-2026-9005')->first();
        $this->assertEquals(40000.00, (float)$customer->fresh()->current_balance);
        $this->assertEquals(110000.00, (float)$account->fresh()->current_balance);

        // Update amount from 10,000 to 15,000
        $response = $this->actingAs($user)->put(route('admin.transactions.receipt-voucher.update', $voucher), [
            'voucher_no'   => 'RCP-2026-9005',
            'voucher_date' => '2026-10-07',
            'receipt_type' => 'Customer',
            'customer_id'  => $customer->id,
            'account_id'   => $account->id,
            'amount'       => 15000.00, // Updated amount
            'payment_mode' => 'Cheque',
            'reference_no' => 'CHQ-554433',
        ]);

        $response->assertRedirect(route('admin.transactions.receipt-voucher'));
        $response->assertSessionHas('success');

        // Balances properly reconciled with difference of +5,000
        // Customer: 50,000 - 15,000 = 35,000
        $this->assertEquals(35000.00, (float)$customer->fresh()->current_balance);
        // Account: 100,000 + 15,000 = 115,000
        $this->assertEquals(115000.00, (float)$account->fresh()->current_balance);
    }

    public function test_receipt_voucher_status_can_be_toggled_and_reverses_balances(): void
    {
        $user = $this->getAdminUser();
        $customer = $this->createCustomer(50000.00);
        $account = $this->createAccount('Bank Accounts', 100000.00);

        // Initial store: 10,000
        $this->actingAs($user)->post(route('admin.transactions.receipt-voucher.store'), [
            'voucher_no'   => 'RCP-2026-9006',
            'voucher_date' => '2026-10-07',
            'receipt_type' => 'Customer',
            'customer_id'  => $customer->id,
            'account_id'   => $account->id,
            'amount'       => 10000.00,
            'payment_mode' => 'Bank Transfer',
        ]);

        $voucher = ReceiptVoucher::where('voucher_no', 'RCP-2026-9006')->first();
        $this->assertEquals('active', $voucher->status);

        // Toggle to cancelled: reverses customer balance & account balance
        $response = $this->actingAs($user)->patch(route('admin.transactions.receipt-voucher.toggle-status', $voucher));
        $response->assertSessionHas('success');

        $this->assertEquals('cancelled', $voucher->fresh()->status);
        $this->assertEquals(50000.00, (float)$customer->fresh()->current_balance);
        $this->assertEquals(100000.00, (float)$account->fresh()->current_balance);

        // Toggle back to active: re-applies balances
        $response = $this->actingAs($user)->patch(route('admin.transactions.receipt-voucher.toggle-status', $voucher));
        $response->assertSessionHas('success');

        $this->assertEquals('active', $voucher->fresh()->status);
        $this->assertEquals(40000.00, (float)$customer->fresh()->current_balance);
        $this->assertEquals(110000.00, (float)$account->fresh()->current_balance);
    }

    public function test_receipt_voucher_can_be_deleted_and_reverses_balances(): void
    {
        $user = $this->getAdminUser();
        $customer = $this->createCustomer(50000.00);
        $account = $this->createAccount('Bank Accounts', 100000.00);

        // Initial store: 10,000
        $this->actingAs($user)->post(route('admin.transactions.receipt-voucher.store'), [
            'voucher_no'   => 'RCP-2026-9007',
            'voucher_date' => '2026-10-07',
            'receipt_type' => 'Customer',
            'customer_id'  => $customer->id,
            'account_id'   => $account->id,
            'amount'       => 10000.00,
            'payment_mode' => 'Bank Transfer',
        ]);

        $voucher = ReceiptVoucher::where('voucher_no', 'RCP-2026-9007')->first();

        // Delete active voucher: should revert customer balance and account balance
        $response = $this->actingAs($user)->delete(route('admin.transactions.receipt-voucher.destroy', $voucher));
        $response->assertRedirect(route('admin.transactions.receipt-voucher'));
        $response->assertSessionHas('success');

        $this->assertSoftDeleted('receipt_vouchers', ['id' => $voucher->id]);
        $this->assertEquals(50000.00, (float)$customer->fresh()->current_balance);
        $this->assertEquals(100000.00, (float)$account->fresh()->current_balance);
    }

    public function test_receipt_voucher_generate_code_returns_valid_json(): void
    {
        $user = $this->getAdminUser();

        $response = $this->actingAs($user)->get(route('admin.transactions.receipt-voucher.generate-code'));
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'code']);
        $this->assertTrue($response->json('success'));
        $this->assertStringStartsWith('RCP-', $response->json('code'));
    }
}
