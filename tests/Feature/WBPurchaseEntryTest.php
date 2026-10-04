<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Item;
use App\Models\WBPurchase;
use App\Models\WBPurchaseItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class WBPurchaseEntryTest extends TestCase
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
            'code' => 'VND-TEST-002',
            'name' => 'Mandi Farmer Supplier',
            'status' => 'active',
            'current_balance' => 0.00,
        ]);
    }

    protected function createItem(): Item
    {
        return Item::create([
            'code' => 'ITM-TEST-002',
            'name' => 'Raw Dry Henna Leaves',
            'unit' => 'KG',
            'purchase_rate' => 60.00,
            'current_stock' => 100.00,
            'status' => 'active',
        ]);
    }

    public function test_wb_purchase_index_page_can_be_rendered(): void
    {
        $user = $this->getAdminUser();

        $response = $this->actingAs($user)->get(route('admin.transactions.wb-purchase-entry'));
        $response->assertStatus(200);
        $response->assertSee('WB Purchase Entry (Without Bill)');
        $response->assertSee('Total WB Slips');
        $response->assertSee('Total Net Weight');
        $response->assertSee('Total Procurement Value');
    }

    public function test_wb_purchase_create_page_can_be_rendered_without_status_field(): void
    {
        $user = $this->getAdminUser();

        $response = $this->actingAs($user)->get(route('admin.transactions.wb-purchase-entry.create'));
        $response->assertStatus(200);
        $response->assertSee('New WB Purchase Entry (Without Bill)');
        $response->assertSee('1. Inward Slip &amp; Supplier Identity', false);
        $response->assertSee('2. Inward Product Line Items');
        $response->assertSee('RATE (₹)');
        $response->assertSee('LINE TOTAL (₹)');
        // Critical requirement: Ensure NO editable status field in create form
        $response->assertDontSee('name="status"', false);
    }

    public function test_wb_purchase_can_be_stored_and_adjusts_stock_and_vendor_balance(): void
    {
        $user = $this->getAdminUser();
        $vendor = $this->createVendor();
        $item = $this->createItem();

        $payload = [
            'slip_no' => 'WBP-2026-9999',
            'entry_date' => '2026-10-04',
            'vendor_id' => $vendor->id,
            'order_type' => 'Medium',
            'vehicle_no' => 'RJ-22-T-5555',
            'driver_name' => 'Ram Lal',
            'driver_phone' => '9829000000',
            'gross_weight' => 5000.00,
            'tare_weight' => 2000.00,
            'deduction_weight' => 100.00,
            'net_weight' => 2900.00,
            'payment_mode' => 'Cash',
            'notes' => 'Test weighbridge mandi arrival without bill',
            'items' => [
                [
                    'item_id' => $item->id,
                    'batch_no' => 'BAT-WB-01',
                    'unit' => 'KG',
                    'quantity' => 2900.000,
                    'rate' => 60.00,
                    'notes' => 'Dry leaves arrival',
                ]
            ]
        ];

        $response = $this->actingAs($user)->post(route('admin.transactions.wb-purchase-entry.store'), $payload);
        $response->assertRedirect(route('admin.transactions.wb-purchase-entry'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('wb_purchases', [
            'slip_no' => 'WBP-2026-9999',
            'vendor_id' => $vendor->id,
            'net_weight' => 2900.000,
            'total_amount' => 174000.00, // 2900 * 60
            'status' => 'received', // Auto-default
        ]);

        $this->assertDatabaseHas('wb_purchase_items', [
            'item_id' => $item->id,
            'quantity' => 2900.000,
            'rate' => 60.00,
        ]);

        // Stock should have increased from 100 to 3000
        $this->assertEquals(3000.00, $item->fresh()->current_stock);

        // Vendor balance should have increased by 174000
        $this->assertEquals(174000.00, $vendor->fresh()->current_balance);
    }

    public function test_wb_purchase_show_page_can_be_rendered(): void
    {
        $user = $this->getAdminUser();
        $vendor = $this->createVendor();
        $item = $this->createItem();

        $wb = WBPurchase::create([
            'slip_no' => 'WBP-2026-0077',
            'entry_date' => '2026-10-04',
            'vendor_id' => $vendor->id,
            'order_type' => 'Medium',
            'gross_weight' => 3000,
            'tare_weight' => 1500,
            'deduction_weight' => 50,
            'net_weight' => 1450,
            'total_amount' => 87000,
            'status' => 'received',
        ]);

        WBPurchaseItem::create([
            'wb_purchase_id' => $wb->id,
            'item_id' => $item->id,
            'unit' => 'KG',
            'quantity' => 1450,
            'rate' => 60,
            'amount' => 87000,
        ]);

        $response = $this->actingAs($user)->get(route('admin.transactions.wb-purchase-entry.show', $wb));
        $response->assertStatus(200);
        $response->assertSee('WBP-2026-0077');
        $response->assertSee('Total Procurement Value');
        $response->assertSee('Inward Product Line Items');
    }

    public function test_wb_purchase_edit_page_can_be_rendered_without_status_field(): void
    {
        $user = $this->getAdminUser();
        $vendor = $this->createVendor();
        $item = $this->createItem();

        $wb = WBPurchase::create([
            'slip_no' => 'WBP-2026-0078',
            'entry_date' => '2026-10-04',
            'vendor_id' => $vendor->id,
            'order_type' => 'Medium',
            'net_weight' => 500,
            'total_amount' => 30000,
            'status' => 'received',
        ]);

        WBPurchaseItem::create([
            'wb_purchase_id' => $wb->id,
            'item_id' => $item->id,
            'unit' => 'KG',
            'quantity' => 500,
            'rate' => 60,
            'amount' => 30000,
        ]);

        $response = $this->actingAs($user)->get(route('admin.transactions.wb-purchase-entry.edit', $wb));
        $response->assertStatus(200);
        $response->assertSee('Edit WB Purchase Slip');
        // Critical requirement: Ensure NO editable status field in edit form
        $response->assertDontSee('name="status"', false);
    }

    public function test_wb_purchase_can_be_updated(): void
    {
        $user = $this->getAdminUser();
        $vendor = $this->createVendor();
        $item = $this->createItem();

        $wb = WBPurchase::create([
            'slip_no' => 'WBP-2026-0079',
            'entry_date' => '2026-10-04',
            'vendor_id' => $vendor->id,
            'order_type' => 'Medium',
            'gross_weight' => 1000,
            'tare_weight' => 500,
            'deduction_weight' => 0,
            'net_weight' => 500,
            'total_amount' => 30000,
            'status' => 'received',
        ]);

        WBPurchaseItem::create([
            'wb_purchase_id' => $wb->id,
            'item_id' => $item->id,
            'unit' => 'KG',
            'quantity' => 500,
            'rate' => 60,
            'amount' => 30000,
        ]);

        $updatePayload = [
            'entry_date' => '2026-10-05',
            'vendor_id' => $vendor->id,
            'order_type' => 'Urgent',
            'vehicle_no' => 'RJ-22-PICKUP',
            'driver_name' => 'Bhanwar Singh',
            'gross_weight' => 1200,
            'tare_weight' => 500,
            'deduction_weight' => 50,
            'net_weight' => 650,
            'items' => [
                [
                    'item_id' => $item->id,
                    'batch_no' => 'BAT-WB-02',
                    'unit' => 'KG',
                    'quantity' => 650.000,
                    'rate' => 65.00,
                ]
            ]
        ];

        $response = $this->actingAs($user)->put(route('admin.transactions.wb-purchase-entry.update', $wb), $updatePayload);
        $response->assertRedirect(route('admin.transactions.wb-purchase-entry'));
        $this->assertDatabaseHas('wb_purchases', [
            'id' => $wb->id,
            'driver_name' => 'Bhanwar Singh',
            'net_weight' => 650.000,
            'total_amount' => 42250.00, // 650 * 65
        ]);
    }

    public function test_wb_purchase_status_can_be_toggled(): void
    {
        $user = $this->getAdminUser();
        $vendor = $this->createVendor();

        $wb = WBPurchase::create([
            'slip_no' => 'WBP-2026-0080',
            'entry_date' => '2026-10-04',
            'vendor_id' => $vendor->id,
            'order_type' => 'Medium',
            'status' => 'received',
        ]);

        $response = $this->actingAs($user)->patch(route('admin.transactions.wb-purchase-entry.toggle-status', $wb));
        $response->assertSessionHas('success');
        $this->assertEquals('completed', $wb->fresh()->status);

        // Once completed, status must be locked and cannot be changed back
        $response2 = $this->actingAs($user)->patch(route('admin.transactions.wb-purchase-entry.toggle-status', $wb));
        $response2->assertSessionHas('error');
        $this->assertEquals('completed', $wb->fresh()->status);
    }

    public function test_wb_purchase_can_be_deleted(): void
    {
        $user = $this->getAdminUser();
        $vendor = $this->createVendor();
        $item = $this->createItem();

        $wb = WBPurchase::create([
            'slip_no' => 'WBP-2026-0081',
            'entry_date' => '2026-10-04',
            'vendor_id' => $vendor->id,
            'order_type' => 'Medium',
            'status' => 'received',
            'total_amount' => 10000,
        ]);

        WBPurchaseItem::create([
            'wb_purchase_id' => $wb->id,
            'item_id' => $item->id,
            'unit' => 'KG',
            'quantity' => 100,
            'rate' => 100,
            'amount' => 10000,
        ]);

        $response = $this->actingAs($user)->delete(route('admin.transactions.wb-purchase-entry.destroy', $wb));
        $response->assertRedirect(route('admin.transactions.wb-purchase-entry'));
        $this->assertSoftDeleted('wb_purchases', ['id' => $wb->id]);
    }

    public function test_wb_generate_code_returns_json(): void
    {
        $user = $this->getAdminUser();

        $response = $this->actingAs($user)->get(route('admin.transactions.wb-purchase-entry.generate-code'));
        $response->assertStatus(200);
        $response->assertJsonStructure(['code']);
    }
}
