<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class PurchaseEntryTest extends TestCase
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

    protected function createVendor(): Vendor
    {
        return Vendor::create([
            'code' => 'VND-TEST-001',
            'name' => 'Sojat Test Vendor',
            'status' => 'active',
            'current_balance' => 0.00,
        ]);
    }

    protected function createItem(): Item
    {
        return Item::create([
            'code' => 'ITM-TEST-001',
            'name' => 'Henna Leaves Test',
            'unit' => 'KG',
            'purchase_rate' => 100.00,
            'current_stock' => 50.00,
            'status' => 'active',
        ]);
    }

    public function test_purchase_index_page_can_be_rendered(): void
    {
        $user = $this->getAdminUser();

        $response = $this->actingAs($user)->get(route('admin.transactions.purchase-entry'));
        $response->assertStatus(200);
        $response->assertSee('Purchase Entry');
        $response->assertSee('Total Purchases');
        $response->assertSee('Official Billing Total');
        $response->assertSee('Grand Total Procured');
    }

    public function test_purchase_create_page_can_be_rendered_without_status_field(): void
    {
        $user = $this->getAdminUser();

        $response = $this->actingAs($user)->get(route('admin.transactions.purchase-entry.create'));
        $response->assertStatus(200);
        $response->assertSee('New Purchase Entry');
        $response->assertSee('Purchase Voucher Details');
        $response->assertSee('Purchase Line Items');
        // Critical requirement: Ensure NO editable status field in create form
        $response->assertDontSee('name="status"', false);
    }

    public function test_purchase_can_be_stored_and_adjusts_stock_and_vendor_balance(): void
    {
        $user = $this->getAdminUser();
        $vendor = $this->createVendor();
        $item = $this->createItem();

        $payload = [
            'purchase_no' => 'PUR-2026-9999',
            'invoice_no' => 'INV-TEST-441',
            'invoice_date' => '2026-10-04',
            'vendor_id' => $vendor->id,
            'order_type' => 'Medium',
            'vehicle_no' => 'RJ-22-T-1234',
            'payment_terms' => '30 Days',
            'payment_status' => 'unpaid',
            'notes' => 'Test herbal raw material procurement consignment',
            'items' => [
                [
                    'item_id' => $item->id,
                    'batch_no' => 'BAT-01',
                    'hsn_code' => '1404',
                    'unit' => 'KG',
                    'quantity' => 100.000,
                    'actual_rate' => 120.00,
                    'bill_rate' => 60.00,
                    'ub_rate' => 60.00,
                    'gst_percent' => 5.00,
                ]
            ]
        ];

        $response = $this->actingAs($user)->post(route('admin.transactions.purchase-entry.store'), $payload);
        $response->assertRedirect(route('admin.transactions.purchase-entry'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('purchases', [
            'purchase_no' => 'PUR-2026-9999',
            'invoice_no' => 'INV-TEST-441',
            'vendor_id' => $vendor->id,
            'status' => 'received', // Automatically received
        ]);

        $this->assertDatabaseHas('purchase_items', [
            'item_id' => $item->id,
            'quantity' => 100.000,
            'bill_rate' => 60.00,
        ]);

        // Stock should have increased from 50 to 150
        $this->assertEquals(150.00, $item->fresh()->current_stock);

        // Vendor balance should have increased by grand_total (6000 + 300 GST + 6000 UB = 12300)
        $this->assertEquals(12300.00, $vendor->fresh()->current_balance);
    }

    public function test_purchase_show_page_can_be_rendered(): void
    {
        $user = $this->getAdminUser();
        $vendor = $this->createVendor();
        $item = $this->createItem();

        $purchase = Purchase::create([
            'purchase_no' => 'PUR-2026-0088',
            'invoice_no' => 'INV-88',
            'invoice_date' => '2026-10-04',
            'vendor_id' => $vendor->id,
            'order_type' => 'Medium',
            'subtotal' => 6000.00,
            'tax_amount' => 300.00,
            'bill_total' => 6300.00,
            'under_billing_total' => 6000.00,
            'grand_total' => 12300.00,
            'status' => 'received',
        ]);

        PurchaseItem::create([
            'purchase_id' => $purchase->id,
            'item_id' => $item->id,
            'unit' => 'KG',
            'quantity' => 100,
            'actual_rate' => 120,
            'bill_rate' => 60,
            'ub_rate' => 60,
            'gst_percent' => 5,
            'tax_amount' => 300,
            'bill_amount' => 6300,
            'under_amount' => 6000,
            'total_amount' => 12300,
        ]);

        $response = $this->actingAs($user)->get(route('admin.transactions.purchase-entry.show', $purchase));
        $response->assertStatus(200);
        $response->assertSee('PUR-2026-0088');
        $response->assertSee('Grand Total Payable');
        $response->assertSee('Official Billing Amount');
    }

    public function test_purchase_edit_page_can_be_rendered_without_status_field(): void
    {
        $user = $this->getAdminUser();
        $vendor = $this->createVendor();
        $item = $this->createItem();

        $purchase = Purchase::create([
            'purchase_no' => 'PUR-2026-0089',
            'invoice_no' => 'INV-89',
            'invoice_date' => '2026-10-04',
            'vendor_id' => $vendor->id,
            'order_type' => 'Medium',
            'status' => 'received',
        ]);

        PurchaseItem::create([
            'purchase_id' => $purchase->id,
            'item_id' => $item->id,
            'unit' => 'KG',
            'quantity' => 10,
            'actual_rate' => 100,
            'bill_rate' => 50,
            'ub_rate' => 50,
            'gst_percent' => 5,
            'tax_amount' => 25,
            'bill_amount' => 525,
            'under_amount' => 500,
            'total_amount' => 1025,
        ]);

        $response = $this->actingAs($user)->get(route('admin.transactions.purchase-entry.edit', $purchase));
        $response->assertStatus(200);
        $response->assertSee('Edit Purchase Entry');
        // Critical requirement: Ensure NO editable status field in edit form
        $response->assertDontSee('name="status"', false);
    }

    public function test_purchase_can_be_updated(): void
    {
        $user = $this->getAdminUser();
        $vendor = $this->createVendor();
        $item = $this->createItem();

        $purchase = Purchase::create([
            'purchase_no' => 'PUR-2026-0090',
            'invoice_no' => 'INV-90',
            'invoice_date' => '2026-10-04',
            'vendor_id' => $vendor->id,
            'order_type' => 'Medium',
            'subtotal' => 500.00,
            'tax_amount' => 25.00,
            'bill_total' => 525.00,
            'under_billing_total' => 500.00,
            'grand_total' => 1025.00,
            'status' => 'received',
        ]);

        PurchaseItem::create([
            'purchase_id' => $purchase->id,
            'item_id' => $item->id,
            'unit' => 'KG',
            'quantity' => 10,
            'actual_rate' => 100,
            'bill_rate' => 50,
            'ub_rate' => 50,
            'gst_percent' => 5,
            'bill_amount' => 525,
            'under_amount' => 500,
            'total_amount' => 1025,
        ]);

        $updatePayload = [
            'invoice_no' => 'INV-90-REVISED',
            'invoice_date' => '2026-10-05',
            'vendor_id' => $vendor->id,
            'order_type' => 'Urgent',
            'vehicle_no' => 'RJ-22-T-9999',
            'items' => [
                [
                    'item_id' => $item->id,
                    'batch_no' => 'BAT-02',
                    'hsn_code' => '1404',
                    'unit' => 'KG',
                    'quantity' => 20.000,
                    'actual_rate' => 120.00,
                    'bill_rate' => 60.00,
                    'ub_rate' => 60.00,
                    'gst_percent' => 5.00,
                ]
            ]
        ];

        $response = $this->actingAs($user)->put(route('admin.transactions.purchase-entry.update', $purchase), $updatePayload);
        $response->assertRedirect(route('admin.transactions.purchase-entry'));
        $this->assertDatabaseHas('purchases', [
            'id' => $purchase->id,
            'invoice_no' => 'INV-90-REVISED',
            'order_type' => 'Urgent',
        ]);
    }

    public function test_purchase_status_can_be_toggled(): void
    {
        $user = $this->getAdminUser();
        $vendor = $this->createVendor();

        $purchase = Purchase::create([
            'purchase_no' => 'PUR-2026-0091',
            'invoice_date' => '2026-10-04',
            'vendor_id' => $vendor->id,
            'order_type' => 'Medium',
            'status' => 'received',
        ]);

        $response = $this->actingAs($user)->patch(route('admin.transactions.purchase-entry.toggle-status', $purchase));
        $response->assertSessionHas('success');
        $this->assertEquals('completed', $purchase->fresh()->status);
    }

    public function test_purchase_can_be_deleted_and_stock_reverted(): void
    {
        $user = $this->getAdminUser();
        $vendor = $this->createVendor();
        $item = $this->createItem(); // stock = 50.00

        $purchase = Purchase::create([
            'purchase_no' => 'PUR-2026-0092',
            'invoice_date' => '2026-10-04',
            'vendor_id' => $vendor->id,
            'order_type' => 'Medium',
            'grand_total' => 1025.00,
            'status' => 'received',
        ]);

        PurchaseItem::create([
            'purchase_id' => $purchase->id,
            'item_id' => $item->id,
            'unit' => 'KG',
            'quantity' => 10,
            'actual_rate' => 100,
            'bill_rate' => 50,
            'ub_rate' => 50,
            'gst_percent' => 5,
            'total_amount' => 1025,
        ]);

        $response = $this->actingAs($user)->delete(route('admin.transactions.purchase-entry.destroy', $purchase));
        $response->assertRedirect(route('admin.transactions.purchase-entry'));
        $this->assertSoftDeleted('purchases', ['id' => $purchase->id]);
    }

    public function test_generate_code_returns_valid_json(): void
    {
        $user = $this->getAdminUser();

        $response = $this->actingAs($user)->get(route('admin.transactions.purchase-entry.generate-code'));
        $response->assertStatus(200);
        $response->assertJsonStructure(['code']);
    }
}
