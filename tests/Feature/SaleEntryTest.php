<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Customer;
use App\Models\Item;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class SaleEntryTest extends TestCase
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
            'code' => 'CST-TEST-001',
            'name' => 'Jaipur Herbals Ltd',
            'status' => 'active',
            'current_balance' => 0.00,
        ]);
    }

    protected function createItem(float $stock = 100.00): Item
    {
        return Item::create([
            'code' => 'ITM-SALE-001',
            'name' => 'Amla Powder Grade A',
            'unit' => 'KG',
            'sale_rate' => 150.00,
            'current_stock' => $stock,
            'status' => 'active',
        ]);
    }

    public function test_sales_index_page_can_be_rendered(): void
    {
        $user = $this->getAdminUser();

        $response = $this->actingAs($user)->get(route('admin.transactions.sales-entry'));
        $response->assertStatus(200);
        $response->assertSee('Sales Entry');
        $response->assertSee('Total Sales Invoices');
        $response->assertSee('Grand Total Sales Value');
    }

    public function test_sales_create_page_can_be_rendered_without_status_field(): void
    {
        $user = $this->getAdminUser();

        $response = $this->actingAs($user)->get(route('admin.transactions.sales-entry.create'));
        $response->assertStatus(200);
        $response->assertSee('New Sales Entry (Stock Outward)');
        $response->assertSee('1. Sales Invoice &amp; Customer Identity', false);
        $response->assertSee('2. Outward Product Line Items &amp; Stock Availability', false);
        $response->assertSee('DISPATCH QTY');
        $response->assertSee('INWARD STOCK');
        $response->assertSee('BILL RATE (₹)');
        $response->assertSee('U-B RATE (₹)');
        // Critical requirement: Ensure NO editable status field in create form
        $response->assertDontSee('name="status"', false);
    }

    public function test_sales_can_be_stored_and_deducts_outward_stock_and_increases_customer_balance(): void
    {
        $user = $this->getAdminUser();
        $customer = $this->createCustomer();
        $item = $this->createItem(100.00); // 100 KG available

        $payload = [
            'sale_no' => 'SAL-2026-9001',
            'invoice_no' => 'PO-CST-881',
            'sale_date' => '2026-10-05',
            'customer_id' => $customer->id,
            'order_type' => 'Medium',
            'vehicle_no' => 'RJ-19-GA-1122',
            'payment_terms' => '30 Days',
            'paid_amount' => 500.00,
            'payment_status' => 'partial',
            'notes' => 'Test outward sales consignment',
            'items' => [
                [
                    'item_id' => $item->id,
                    'batch_no' => 'BAT-001',
                    'hsn_code' => '121190',
                    'unit' => 'KG',
                    'quantity' => 20.00, // 20 KG outward (under 100 KG stock)
                    'bill_rate' => 100.00,
                    'ub_rate' => 20.00,
                    'gst_percent' => 5.00,
                ]
            ]
        ];

        $response = $this->actingAs($user)->post(route('admin.transactions.sales-entry.store'), $payload);
        $response->assertRedirect(route('admin.transactions.sales-entry'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('sales', [
            'sale_no' => 'SAL-2026-9001',
            'customer_id' => $customer->id,
            'status' => 'dispatched',
        ]);

        // Verify outward stock was deducted: 100.00 - 20.00 = 80.00
        $this->assertEquals(80.00, (float)$item->fresh()->current_stock);

        // Subtotal: 20 * 100 = 2000, Tax: 5% of 2000 = 100, BillTotal: 2100. UB: 20 * 20 = 400. GrandTotal: 2500.
        $this->assertEquals(2500.00, (float)$customer->fresh()->current_balance);
    }

    public function test_sales_fails_when_item_requested_exceeds_available_inward_stock(): void
    {
        $user = $this->getAdminUser();
        $customer = $this->createCustomer();
        $item = $this->createItem(15.00); // Only 15 KG in stock!

        $payload = [
            'sale_no' => 'SAL-2026-9002',
            'sale_date' => '2026-10-05',
            'customer_id' => $customer->id,
            'order_type' => 'Medium',
            'items' => [
                [
                    'item_id' => $item->id,
                    'unit' => 'KG',
                    'quantity' => 25.00, // 25 KG requested, which EXCEEDS 15 KG!
                    'bill_rate' => 100.00,
                    'ub_rate' => 0.00,
                    'gst_percent' => 5.00,
                ]
            ]
        ];

        $response = $this->actingAs($user)->post(route('admin.transactions.sales-entry.store'), $payload);

        // Must redirect back with error and not save
        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('sales', [
            'sale_no' => 'SAL-2026-9002',
        ]);

        // Stock must remain unchanged at 15.00
        $this->assertEquals(15.00, (float)$item->fresh()->current_stock);
    }

    public function test_sales_show_page_can_be_rendered(): void
    {
        $user = $this->getAdminUser();
        $customer = $this->createCustomer();
        $item = $this->createItem(100.00);

        $sale = Sale::create([
            'sale_no' => 'SAL-2026-9003',
            'sale_date' => '2026-10-05',
            'customer_id' => $customer->id,
            'order_type' => 'Medium',
            'subtotal' => 1000.00,
            'tax_amount' => 50.00,
            'bill_total' => 1050.00,
            'grand_total' => 1050.00,
            'status' => 'dispatched',
        ]);

        SaleItem::create([
            'sale_id' => $sale->id,
            'item_id' => $item->id,
            'unit' => 'KG',
            'quantity' => 10.00,
            'bill_rate' => 100.00,
            'total_amount' => 1050.00,
        ]);

        $response = $this->actingAs($user)->get(route('admin.transactions.sales-entry.show', $sale));
        $response->assertStatus(200);
        $response->assertSee('SAL-2026-9003');
        $response->assertSee('Jaipur Herbals Ltd');
    }

    public function test_sales_edit_page_can_be_rendered_without_status_field(): void
    {
        $user = $this->getAdminUser();
        $customer = $this->createCustomer();
        $item = $this->createItem(100.00);

        $sale = Sale::create([
            'sale_no' => 'SAL-2026-9004',
            'sale_date' => '2026-10-05',
            'customer_id' => $customer->id,
            'order_type' => 'Medium',
            'subtotal' => 1000.00,
            'tax_amount' => 50.00,
            'bill_total' => 1050.00,
            'grand_total' => 1050.00,
            'status' => 'dispatched',
        ]);

        SaleItem::create([
            'sale_id' => $sale->id,
            'item_id' => $item->id,
            'unit' => 'KG',
            'quantity' => 10.00,
            'bill_rate' => 100.00,
            'total_amount' => 1050.00,
        ]);

        $response = $this->actingAs($user)->get(route('admin.transactions.sales-entry.edit', $sale));
        $response->assertStatus(200);
        $response->assertSee('Edit Sales Invoice');
        $response->assertDontSee('name="status"', false);
    }

    public function test_sales_status_can_be_toggled(): void
    {
        $user = $this->getAdminUser();
        $customer = $this->createCustomer();
        $item = $this->createItem(50.00); // 50 KG in stock

        $sale = Sale::create([
            'sale_no' => 'SAL-2026-9005',
            'sale_date' => '2026-10-05',
            'customer_id' => $customer->id,
            'order_type' => 'Medium',
            'subtotal' => 1000.00,
            'tax_amount' => 50.00,
            'bill_total' => 1050.00,
            'grand_total' => 1050.00,
            'status' => 'dispatched',
        ]);

        SaleItem::create([
            'sale_id' => $sale->id,
            'item_id' => $item->id,
            'unit' => 'KG',
            'quantity' => 10.00,
            'bill_rate' => 100.00,
            'total_amount' => 1050.00,
        ]);

        // Toggle from dispatched -> cancelled (should restore 10 KG stock to 60 KG)
        $response = $this->actingAs($user)->patch(route('admin.transactions.sales-entry.toggle-status', $sale));
        $response->assertSessionHas('success');

        $this->assertEquals('cancelled', $sale->fresh()->status);
        $this->assertEquals(60.00, (float)$item->fresh()->current_stock);
    }
}
