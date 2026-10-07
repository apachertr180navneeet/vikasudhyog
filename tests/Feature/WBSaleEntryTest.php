<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Customer;
use App\Models\Item;
use App\Models\WBSale;
use App\Models\WBSaleItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class WBSaleEntryTest extends TestCase
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

    protected function createCustomer(): Customer
    {
        return Customer::create([
            'code' => 'CST-WB-001',
            'name' => 'Marwar Spices Trader',
            'status' => 'active',
            'current_balance' => 0.00,
        ]);
    }

    protected function createItem(float $stock = 150.00): Item
    {
        return Item::create([
            'code' => 'ITM-WBS-001',
            'name' => 'Senna Leaves Whole',
            'unit' => 'KG',
            'sale_rate' => 80.00,
            'current_stock' => $stock,
            'status' => 'active',
        ]);
    }

    public function test_wb_sales_index_page_can_be_rendered(): void
    {
        $user = $this->getAdminUser();

        $response = $this->actingAs($user)->get(route('admin.transactions.wb-sales-entry'));
        $response->assertStatus(200);
        $response->assertSee('WB Sales Entry');
        $response->assertSee('Total WB Sales Slips');
        $response->assertSee('Total Outward Net Weight');
    }

    public function test_wb_sales_create_page_can_be_rendered_without_status_field(): void
    {
        $user = $this->getAdminUser();

        $response = $this->actingAs($user)->get(route('admin.transactions.wb-sales-entry.create'));
        $response->assertStatus(200);
        $response->assertSee('New WB Sales Entry');
        $response->assertSee('1. Outward Slip &amp; Customer Identity', false);
        $response->assertSee('2. Outward Product Line Items &amp; Stock Availability', false);
        $response->assertSee('DISPATCH QTY');
        $response->assertSee('RATE (₹)');
        $response->assertSee('INWARD STOCK');
        // Critical requirement: Ensure NO editable status field in create form
        $response->assertDontSee('name="status"', false);
    }

    public function test_wb_sales_can_be_stored_and_deducts_outward_stock(): void
    {
        $user = $this->getAdminUser();
        $customer = $this->createCustomer();
        $item = $this->createItem(100.00); // 100 KG in stock

        $payload = [
            'slip_no' => 'WBS-2026-9001',
            'entry_date' => '2026-10-05',
            'customer_id' => $customer->id,
            'order_type' => 'Medium',
            'vehicle_no' => 'RJ-22-AA-9988',
            'gross_weight' => 2000,
            'tare_weight' => 1970,
            'deduction_weight' => 0,
            'net_weight' => 30,
            'paid_amount' => 2400.00,
            'payment_status' => 'paid',
            'payment_mode' => 'Cash',
            'items' => [
                [
                    'item_id' => $item->id,
                    'hsn_code' => '121190',
                    'unit' => 'KG',
                    'quantity' => 30.00,
                    'rate' => 80.00,
                ]
            ]
        ];

        $response = $this->actingAs($user)->post(route('admin.transactions.wb-sales-entry.store'), $payload);
        $response->assertRedirect(route('admin.transactions.wb-sales-entry'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('wb_sales', [
            'slip_no' => 'WBS-2026-9001',
            'customer_id' => $customer->id,
            'status' => 'dispatched',
        ]);

        // Stock was 100.00, deducted 30.00 -> remaining should be 70.00
        $this->assertEquals(70.00, (float)$item->fresh()->current_stock);
        // Total amount 30 * 80 = 2400
        $this->assertEquals(2400.00, (float)$customer->fresh()->current_balance);
    }

    public function test_wb_sales_fails_when_item_requested_exceeds_available_inward_stock(): void
    {
        $user = $this->getAdminUser();
        $customer = $this->createCustomer();
        $item = $this->createItem(20.00); // Only 20 KG in stock

        $payload = [
            'slip_no' => 'WBS-2026-9002',
            'entry_date' => '2026-10-05',
            'customer_id' => $customer->id,
            'order_type' => 'Medium',
            'items' => [
                [
                    'item_id' => $item->id,
                    'unit' => 'KG',
                    'quantity' => 50.00, // 50 KG requested, EXCEEDS 20 KG!
                    'rate' => 80.00,
                ]
            ]
        ];

        $response = $this->actingAs($user)->post(route('admin.transactions.wb-sales-entry.store'), $payload);
        $response->assertSessionHas('error');

        $this->assertDatabaseMissing('wb_sales', [
            'slip_no' => 'WBS-2026-9002',
        ]);

        // Stock remains unchanged at 20.00
        $this->assertEquals(20.00, (float)$item->fresh()->current_stock);
    }

    public function test_wb_sales_show_page_can_be_rendered(): void
    {
        $user = $this->getAdminUser();
        $customer = $this->createCustomer();
        $item = $this->createItem(100.00);

        $wbSale = WBSale::create([
            'slip_no' => 'WBS-2026-9003',
            'entry_date' => '2026-10-05',
            'customer_id' => $customer->id,
            'order_type' => 'Medium',
            'total_amount' => 1600.00,
            'status' => 'dispatched',
        ]);

        WBSaleItem::create([
            'wb_sale_id' => $wbSale->id,
            'item_id' => $item->id,
            'unit' => 'KG',
            'quantity' => 20.00,
            'rate' => 80.00,
            'amount' => 1600.00,
        ]);

        $response = $this->actingAs($user)->get(route('admin.transactions.wb-sales-entry.show', $wbSale));
        $response->assertStatus(200);
        $response->assertSee('WBS-2026-9003');
        $response->assertSee('Marwar Spices Trader');
    }

    public function test_wb_sales_edit_page_can_be_rendered_without_status_field(): void
    {
        $user = $this->getAdminUser();
        $customer = $this->createCustomer();
        $item = $this->createItem(100.00);

        $wbSale = WBSale::create([
            'slip_no' => 'WBS-2026-9004',
            'entry_date' => '2026-10-05',
            'customer_id' => $customer->id,
            'order_type' => 'Medium',
            'total_amount' => 1600.00,
            'status' => 'dispatched',
        ]);

        WBSaleItem::create([
            'wb_sale_id' => $wbSale->id,
            'item_id' => $item->id,
            'unit' => 'KG',
            'quantity' => 20.00,
            'rate' => 80.00,
            'amount' => 1600.00,
        ]);

        $response = $this->actingAs($user)->get(route('admin.transactions.wb-sales-entry.edit', $wbSale));
        $response->assertStatus(200);
        $response->assertSee('Edit WB Sales Slip');
        $response->assertDontSee('name="status"', false);
    }

    public function test_wb_sales_status_can_be_toggled(): void
    {
        $user = $this->getAdminUser();
        $customer = $this->createCustomer();
        $item = $this->createItem(50.00);

        $wbSale = WBSale::create([
            'slip_no' => 'WBS-2026-9005',
            'entry_date' => '2026-10-05',
            'customer_id' => $customer->id,
            'order_type' => 'Medium',
            'total_amount' => 1600.00,
            'status' => 'dispatched',
        ]);

        WBSaleItem::create([
            'wb_sale_id' => $wbSale->id,
            'item_id' => $item->id,
            'unit' => 'KG',
            'quantity' => 20.00,
            'rate' => 80.00,
            'amount' => 1600.00,
        ]);

        // Toggle dispatched -> cancelled: should restore 20 KG stock to 70 KG
        $response = $this->actingAs($user)->patch(route('admin.transactions.wb-sales-entry.toggle-status', $wbSale));
        $response->assertSessionHas('success');

        $this->assertEquals('cancelled', $wbSale->fresh()->status);
        $this->assertEquals(70.00, (float)$item->fresh()->current_stock);
    }

    public function test_wb_sales_status_lifecycle_can_be_updated_across_all_statuses(): void
    {
        $user = $this->getAdminUser();
        $customer = $this->createCustomer();
        $item = $this->createItem(50.00);

        $wbSale = WBSale::create([
            'slip_no' => 'WBS-2026-9006',
            'entry_date' => '2026-10-05',
            'customer_id' => $customer->id,
            'order_type' => 'Medium',
            'total_amount' => 1600.00,
            'status' => 'dispatched',
        ]);

        WBSaleItem::create([
            'wb_sale_id' => $wbSale->id,
            'item_id' => $item->id,
            'unit' => 'KG',
            'quantity' => 20.00,
            'rate' => 80.00,
            'amount' => 1600.00,
        ]);

        // 1. Update to 'delivered'
        $response = $this->actingAs($user)->patch(route('admin.transactions.wb-sales-entry.update-status', $wbSale), [
            'status' => 'delivered',
        ]);
        $response->assertSessionHas('success');
        $this->assertEquals('delivered', $wbSale->fresh()->status);

        // 2. Update to 'completed'
        $response = $this->actingAs($user)->patch(route('admin.transactions.wb-sales-entry.update-status', $wbSale), [
            'status' => 'completed',
        ]);
        $response->assertSessionHas('success');
        $this->assertEquals('completed', $wbSale->fresh()->status);

        // 3. Update to 'cancelled' (stock restored from 50 to 70)
        $response = $this->actingAs($user)->patch(route('admin.transactions.wb-sales-entry.update-status', $wbSale), [
            'status' => 'cancelled',
        ]);
        $response->assertSessionHas('success');
        $this->assertEquals('cancelled', $wbSale->fresh()->status);
        $this->assertEquals(70.00, (float)$item->fresh()->current_stock);

        // 4. Update back from 'cancelled' to 'ordered' (stock re-deducted from 70 to 50)
        $response = $this->actingAs($user)->patch(route('admin.transactions.wb-sales-entry.update-status', $wbSale), [
            'status' => 'ordered',
        ]);
        $response->assertSessionHas('success');
        $this->assertEquals('ordered', $wbSale->fresh()->status);
        $this->assertEquals(50.00, (float)$item->fresh()->current_stock);
    }
}
