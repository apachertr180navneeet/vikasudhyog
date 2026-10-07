<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\PaymentVoucher;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PaymentVoucherTest extends TestCase
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

    protected function createVendor(float $balance = 60000.00): Vendor
    {
        return Vendor::create([
            'code'            => 'VND-TEST-001',
            'name'            => 'Shekhawati Agro Traders',
            'status'          => 'active',
            'opening_balance' => $balance,
            'current_balance' => $balance,
        ]);
    }

    protected function createAccount(string $type = 'Bank Accounts', float $balance = 150000.00): Account
    {
        return Account::create([
            'code'            => 'BNK-PAY-01',
            'name'            => 'HDFC Bank - Current A/c',
            'account_group'   => $type,
            'opening_balance' => $balance,
            'current_balance' => $balance,
            'balance_type'    => 'Dr',
            'status'          => 'active',
        ]);
    }

    public function test_payment_voucher_index_page_can_be_rendered(): void
    {
        $user = $this->getAdminUser();

        $response = $this->actingAs($user)->get(route('admin.transactions.payment-voucher'));
        $response->assertStatus(200);
        $response->assertSee('Payment Voucher');
        $response->assertSee('Total Payment Vouchers');
        $response->assertSee('Total Funds Disbursed');
        $response->assertSee('Vendor Disbursements');
        $response->assertSee('Direct Expenses');
    }

    public function test_payment_voucher_create_page_can_be_rendered_without_status_field(): void
    {
        $user = $this->getAdminUser();

        $response = $this->actingAs($user)->get(route('admin.transactions.payment-voucher.create'));
        $response->assertStatus(200);
        $response->assertSee('New Payment Voucher');
        $response->assertSee('1. Voucher Header &amp; Beneficiary Identity', false);
        $response->assertSee('2. Financial Particulars &amp; Banking Settlement', false);
        $response->assertSee('Paid Amount (₹)', false);

        // Strict Rule 4: Status must NEVER appear as an input/select on create form
        $response->assertDontSee('name="status"', false);
    }

    public function test_payment_voucher_vendor_disbursement_can_be_stored_and_deducts_vendor_debt_and_deducts_account_balance(): void
    {
        $user = $this->getAdminUser();
        $vendor = $this->createVendor(60000.00); // 60,000 payable debt
        $account = $this->createAccount('Bank Accounts', 150000.00); // 150,000 balance

        $payload = [
            'voucher_no'      => 'PAY-2026-9001',
            'voucher_date'    => '2026-10-07',
            'payment_type'    => 'Vendor',
            'vendor_id'       => $vendor->id,
            'account_id'      => $account->id,
            'amount'          => 25000.00,
            'payment_mode'    => 'RTGS',
            'reference_no'    => 'UTR-HDFC-991122',
            'reference_date'  => '2026-10-07',
            'against_invoice' => 'PUR-2026-0001',
            'notes'           => 'Paid towards raw material henna dispatch',
        ];

        $response = $this->actingAs($user)->post(route('admin.transactions.payment-voucher.store'), $payload);
        $response->assertRedirect(route('admin.transactions.payment-voucher'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('payment_vouchers', [
            'voucher_no'   => 'PAY-2026-9001',
            'payment_type' => 'Vendor',
            'vendor_id'    => $vendor->id,
            'amount'       => 25000.00,
            'status'       => 'active', // Rule 4: defaults to active
        ]);

        // Vendor payable debt decreases by 25,000: 60,000 - 25,000 = 35,000
        $this->assertEquals(35000.00, (float)$vendor->fresh()->current_balance);

        // Paying account decreases by 25,000: 150,000 - 25,000 = 125,000
        $this->assertEquals(125000.00, (float)$account->fresh()->current_balance);
    }

    public function test_payment_voucher_direct_expense_can_be_stored_and_deducts_account_balance(): void
    {
        $user = $this->getAdminUser();
        $account = $this->createAccount('Cash in Hand', 20000.00);

        $payload = [
            'voucher_no'      => 'PAY-2026-9002',
            'voucher_date'    => '2026-10-07',
            'payment_type'    => 'Expense',
            'expense_head'    => 'Freight & Transportation',
            'account_id'      => $account->id,
            'amount'          => 3200.00,
            'payment_mode'    => 'Cash',
            'notes'           => 'Paid freight to truck driver for raw bags delivery',
        ];

        $response = $this->actingAs($user)->post(route('admin.transactions.payment-voucher.store'), $payload);
        $response->assertRedirect(route('admin.transactions.payment-voucher'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('payment_vouchers', [
            'voucher_no'   => 'PAY-2026-9002',
            'payment_type' => 'Expense',
            'expense_head' => 'Freight & Transportation',
            'amount'       => 3200.00,
            'status'       => 'active',
        ]);

        // Cash account reduces by 3,200: 20,000 - 3,200 = 16,800
        $this->assertEquals(16800.00, (float)$account->fresh()->current_balance);
    }

    public function test_payment_voucher_show_page_can_be_rendered(): void
    {
        $user = $this->getAdminUser();
        $vendor = $this->createVendor();
        $account = $this->createAccount();

        $voucher = PaymentVoucher::create([
            'voucher_no'   => 'PAY-2026-9003',
            'voucher_date' => '2026-10-07',
            'payment_type' => 'Vendor',
            'vendor_id'    => $vendor->id,
            'account_id'   => $account->id,
            'amount'       => 18000.00,
            'payment_mode' => 'UPI',
            'reference_no' => 'UPI-TXN-778899',
            'status'       => 'active',
        ]);

        $response = $this->actingAs($user)->get(route('admin.transactions.payment-voucher.show', $voucher));
        $response->assertStatus(200);
        $response->assertSee('PAY-2026-9003');
        $response->assertSee('Shekhawati Agro Traders');
        $response->assertSee('18,000.00');
    }

    public function test_payment_voucher_edit_page_can_be_rendered_without_status_field(): void
    {
        $user = $this->getAdminUser();
        $vendor = $this->createVendor();
        $account = $this->createAccount();

        $voucher = PaymentVoucher::create([
            'voucher_no'   => 'PAY-2026-9004',
            'voucher_date' => '2026-10-07',
            'payment_type' => 'Vendor',
            'vendor_id'    => $vendor->id,
            'account_id'   => $account->id,
            'amount'       => 18000.00,
            'payment_mode' => 'UPI',
            'status'       => 'active',
        ]);

        $response = $this->actingAs($user)->get(route('admin.transactions.payment-voucher.edit', $voucher));
        $response->assertStatus(200);
        $response->assertSee('Edit Payment Voucher');
        $response->assertSee('PAY-2026-9004');

        // Strict Rule 4: Status must NEVER appear as an input/select on edit form
        $response->assertDontSee('name="status"', false);
    }

    public function test_payment_voucher_can_be_updated_and_reconciles_balances(): void
    {
        $user = $this->getAdminUser();
        $vendor = $this->createVendor(60000.00); // 60,000
        $account = $this->createAccount('Bank Accounts', 150000.00); // 150,000

        // Initial store: 10,000
        $this->actingAs($user)->post(route('admin.transactions.payment-voucher.store'), [
            'voucher_no'   => 'PAY-2026-9005',
            'voucher_date' => '2026-10-07',
            'payment_type' => 'Vendor',
            'vendor_id'    => $vendor->id,
            'account_id'   => $account->id,
            'amount'       => 10000.00,
            'payment_mode' => 'Cheque',
        ]);

        $voucher = PaymentVoucher::where('voucher_no', 'PAY-2026-9005')->first();
        $this->assertEquals(50000.00, (float)$vendor->fresh()->current_balance);
        $this->assertEquals(140000.00, (float)$account->fresh()->current_balance);

        // Update amount from 10,000 to 15,000
        $response = $this->actingAs($user)->put(route('admin.transactions.payment-voucher.update', $voucher), [
            'voucher_no'   => 'PAY-2026-9005',
            'voucher_date' => '2026-10-07',
            'payment_type' => 'Vendor',
            'vendor_id'    => $vendor->id,
            'account_id'   => $account->id,
            'amount'       => 15000.00, // Updated amount
            'payment_mode' => 'Cheque',
            'reference_no' => 'CHQ-887766',
        ]);

        $response->assertRedirect(route('admin.transactions.payment-voucher'));
        $response->assertSessionHas('success');

        // Balances properly reconciled with difference of +5,000 disbursed
        // Vendor debt: 60,000 - 15,000 = 45,000
        $this->assertEquals(45000.00, (float)$vendor->fresh()->current_balance);
        // Account balance: 150,000 - 15,000 = 135,000
        $this->assertEquals(135000.00, (float)$account->fresh()->current_balance);
    }

    public function test_payment_voucher_status_can_be_toggled_and_reverses_balances(): void
    {
        $user = $this->getAdminUser();
        $vendor = $this->createVendor(60000.00);
        $account = $this->createAccount('Bank Accounts', 150000.00);

        // Initial store: 10,000
        $this->actingAs($user)->post(route('admin.transactions.payment-voucher.store'), [
            'voucher_no'   => 'PAY-2026-9006',
            'voucher_date' => '2026-10-07',
            'payment_type' => 'Vendor',
            'vendor_id'    => $vendor->id,
            'account_id'   => $account->id,
            'amount'       => 10000.00,
            'payment_mode' => 'Bank Transfer',
        ]);

        $voucher = PaymentVoucher::where('voucher_no', 'PAY-2026-9006')->first();
        $this->assertEquals('active', $voucher->status);

        // Toggle to cancelled: reverses vendor balance & refunds account balance
        $response = $this->actingAs($user)->patch(route('admin.transactions.payment-voucher.toggle-status', $voucher));
        $response->assertSessionHas('success');

        $this->assertEquals('cancelled', $voucher->fresh()->status);
        $this->assertEquals(60000.00, (float)$vendor->fresh()->current_balance);
        $this->assertEquals(150000.00, (float)$account->fresh()->current_balance);

        // Toggle back to active: re-applies disbursements
        $response = $this->actingAs($user)->patch(route('admin.transactions.payment-voucher.toggle-status', $voucher));
        $response->assertSessionHas('success');

        $this->assertEquals('active', $voucher->fresh()->status);
        $this->assertEquals(50000.00, (float)$vendor->fresh()->current_balance);
        $this->assertEquals(140000.00, (float)$account->fresh()->current_balance);
    }

    public function test_payment_voucher_can_be_deleted_and_reverses_balances(): void
    {
        $user = $this->getAdminUser();
        $vendor = $this->createVendor(60000.00);
        $account = $this->createAccount('Bank Accounts', 150000.00);

        // Initial store: 10,000
        $this->actingAs($user)->post(route('admin.transactions.payment-voucher.store'), [
            'voucher_no'   => 'PAY-2026-9007',
            'voucher_date' => '2026-10-07',
            'payment_type' => 'Vendor',
            'vendor_id'    => $vendor->id,
            'account_id'   => $account->id,
            'amount'       => 10000.00,
            'payment_mode' => 'Bank Transfer',
        ]);

        $voucher = PaymentVoucher::where('voucher_no', 'PAY-2026-9007')->first();

        // Delete active voucher: should refund vendor payable debt and account balance
        $response = $this->actingAs($user)->delete(route('admin.transactions.payment-voucher.destroy', $voucher));
        $response->assertRedirect(route('admin.transactions.payment-voucher'));
        $response->assertSessionHas('success');

        $this->assertSoftDeleted('payment_vouchers', ['id' => $voucher->id]);
        $this->assertEquals(60000.00, (float)$vendor->fresh()->current_balance);
        $this->assertEquals(150000.00, (float)$account->fresh()->current_balance);
    }

    public function test_payment_voucher_generate_code_returns_valid_json(): void
    {
        $user = $this->getAdminUser();

        $response = $this->actingAs($user)->get(route('admin.transactions.payment-voucher.generate-code'));
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'code']);
        $this->assertTrue($response->json('success'));
        $this->assertStringStartsWith('PAY-', $response->json('code'));
    }
}
