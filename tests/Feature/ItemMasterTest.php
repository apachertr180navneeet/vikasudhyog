<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class ItemMasterTest extends TestCase
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

    public function test_item_index_page_can_be_rendered(): void
    {
        $user = $this->getAdminUser();

        $response = $this->actingAs($user)->get(route('admin.masters.item'));
        $response->assertStatus(200);
        $response->assertSee('Item Master');
        $response->assertSee('Total Catalog Items');
        $response->assertSee('Total Stock Valuation');
        $response->assertSee('Low Stock Alerts');
    }

    public function test_item_create_page_can_be_rendered(): void
    {
        $user = $this->getAdminUser();

        $response = $this->actingAs($user)->get(route('admin.masters.item.create'));
        $response->assertStatus(200);
        $response->assertSee('Add New Product / Raw Material');
        $response->assertSee('Live Item Preview');
        // Critical requirement: Ensure no status field in create form
        $response->assertDontSee('name="status"', false);
    }

    public function test_item_can_be_stored_and_defaults_to_active(): void
    {
        $user = $this->getAdminUser();

        $payload = [
            'code'            => 'ITM-99',
            'name'            => 'Natural Henna Cone 30g Box',
            'category'        => 'Finished Product',
            'unit'            => 'BOX',
            'hsn_code'        => '330499',
            'gst_rate'        => 5.00,
            'purchase_rate'   => 120.00,
            'sale_rate'       => 180.00,
            'opening_stock'   => 500.00,
            'min_stock_alert' => 50.00,
            'batch_no'        => 'CONE-2026-B1',
            'notes'           => 'Keep stored in cool room.',
        ];

        $response = $this->actingAs($user)->post(route('admin.masters.item.store'), $payload);
        $response->assertRedirect(route('admin.masters.item'));

        $this->assertDatabaseHas('items', [
            'code'            => 'ITM-99',
            'name'            => 'Natural Henna Cone 30g Box',
            'category'        => 'Finished Product',
            'status'          => 'active', // Automatically active per guidelines
            'current_stock'   => 500.00,
            'purchase_rate'   => 120.00,
            'sale_rate'       => 180.00,
        ]);
    }

    public function test_item_show_page_can_be_rendered(): void
    {
        $user = $this->getAdminUser();

        $item = Item::create([
            'code'            => 'ITM-01',
            'name'            => 'Sojat Pure Henna Powder',
            'category'        => 'Mehndi / Henna',
            'unit'            => 'KG',
            'hsn_code'        => '330499',
            'gst_rate'        => 5.00,
            'purchase_rate'   => 150.00,
            'sale_rate'       => 210.00,
            'opening_stock'   => 1000.00,
            'current_stock'   => 1000.00,
            'min_stock_alert' => 100.00,
            'status'          => 'active',
        ]);

        $response = $this->actingAs($user)->get(route('admin.masters.item.show', $item->id));
        $response->assertStatus(200);
        $response->assertSee('Product &amp; Material Dossier', false);
        $response->assertSee('Sojat Pure Henna Powder');
        $response->assertSee('ITM-01');
    }

    public function test_item_edit_page_can_be_rendered(): void
    {
        $user = $this->getAdminUser();

        $item = Item::create([
            'code'            => 'ITM-02',
            'name'            => 'Senna Leaves T-Cut',
            'category'        => 'Herbal Powder',
            'unit'            => 'BAG',
            'hsn_code'        => '121190',
            'gst_rate'        => 5.00,
            'purchase_rate'   => 85.00,
            'sale_rate'       => 120.00,
            'opening_stock'   => 200.00,
            'current_stock'   => 200.00,
            'min_stock_alert' => 20.00,
            'status'          => 'active',
        ]);

        $response = $this->actingAs($user)->get(route('admin.masters.item.edit', $item->id));
        $response->assertStatus(200);
        $response->assertSee('Edit Product / Raw Material');
        $response->assertSee('Senna Leaves T-Cut');
        // Critical requirement: Ensure no status field in edit form
        $response->assertDontSee('name="status"', false);
    }

    public function test_item_can_be_updated(): void
    {
        $user = $this->getAdminUser();

        $item = Item::create([
            'code'            => 'ITM-03',
            'name'            => 'Amla Powder Raw',
            'category'        => 'Ayurvedic Raw Material',
            'unit'            => 'KG',
            'hsn_code'        => '121190',
            'gst_rate'        => 5.00,
            'purchase_rate'   => 90.00,
            'sale_rate'       => 130.00,
            'opening_stock'   => 150.00,
            'current_stock'   => 150.00,
            'min_stock_alert' => 25.00,
            'status'          => 'active',
        ]);

        $payload = [
            'code'            => 'ITM-03',
            'name'            => 'Amla Powder Premium Triple Filtered',
            'category'        => 'Ayurvedic Raw Material',
            'unit'            => 'KG',
            'hsn_code'        => '121190',
            'gst_rate'        => 5.00,
            'purchase_rate'   => 95.00,
            'sale_rate'       => 145.00,
            'opening_stock'   => 150.00,
            'current_stock'   => 180.00,
            'min_stock_alert' => 30.00,
            'batch_no'        => 'AML-2026-P1',
            'notes'           => 'Updated moisture test passed.',
        ];

        $response = $this->actingAs($user)->put(route('admin.masters.item.update', $item->id), $payload);
        $response->assertRedirect(route('admin.masters.item'));

        $this->assertDatabaseHas('items', [
            'id'            => $item->id,
            'name'          => 'Amla Powder Premium Triple Filtered',
            'purchase_rate' => 95.00,
            'sale_rate'     => 145.00,
            'current_stock' => 180.00,
            'batch_no'      => 'AML-2026-P1',
        ]);
    }

    public function test_item_status_can_be_toggled(): void
    {
        $user = $this->getAdminUser();

        $item = Item::create([
            'code'            => 'ITM-04',
            'name'            => 'Multani Mitti Lump',
            'category'        => 'Ayurvedic Raw Material',
            'unit'            => 'QUINTAL',
            'hsn_code'        => '250810',
            'gst_rate'        => 5.00,
            'purchase_rate'   => 450.00,
            'sale_rate'       => 650.00,
            'opening_stock'   => 10.00,
            'current_stock'   => 10.00,
            'min_stock_alert' => 2.00,
            'status'          => 'active',
        ]);

        $response = $this->actingAs($user)->patch(route('admin.masters.item.toggle-status', $item->id));
        $response->assertRedirect();

        $this->assertEquals('inactive', $item->fresh()->status);

        // Toggle back to active
        $this->actingAs($user)->patch(route('admin.masters.item.toggle-status', $item->id));
        $this->assertEquals('active', $item->fresh()->status);
    }

    public function test_item_can_be_soft_deleted(): void
    {
        $user = $this->getAdminUser();

        $item = Item::create([
            'code'            => 'ITM-05',
            'name'            => 'Corrugated Box 20kg',
            'category'        => 'Packaging Material',
            'unit'            => 'BOX',
            'hsn_code'        => '481910',
            'gst_rate'        => 12.00,
            'purchase_rate'   => 45.00,
            'sale_rate'       => 55.00,
            'opening_stock'   => 500.00,
            'current_stock'   => 500.00,
            'min_stock_alert' => 50.00,
            'status'          => 'active',
        ]);

        $response = $this->actingAs($user)->delete(route('admin.masters.item.destroy', $item->id));
        $response->assertRedirect(route('admin.masters.item'));

        $this->assertSoftDeleted('items', ['id' => $item->id]);
    }

    public function test_item_code_generation_endpoint(): void
    {
        $user = $this->getAdminUser();

        $response = $this->actingAs($user)->getJson(route('admin.masters.item.generate-code'));
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'code']);
        $this->assertTrue($response->json('success'));
        $this->assertStringStartsWith('ITM-', $response->json('code'));
    }

    public function test_item_belongs_to_unit_and_unit_has_many_items(): void
    {
        $unit = \App\Models\Unit::create([
            'name' => 'Kilogram Test',
            'code' => 'KG-T',
            'is_base_unit' => true,
            'status' => 'active',
        ]);

        $item = Item::create([
            'code'            => 'ITM-REL-01',
            'name'            => 'Relation Test Herb',
            'category'        => 'Herbal Powder',
            'unit'            => $unit->code,
            'unit_id'         => $unit->id,
            'hsn_code'        => '121190',
            'gst_rate'        => 5.00,
            'purchase_rate'   => 50.00,
            'sale_rate'       => 80.00,
            'opening_stock'   => 100.00,
            'current_stock'   => 100.00,
            'min_stock_alert' => 10.00,
            'status'          => 'active',
        ]);

        $this->assertNotNull($item->unitRelation);
        $this->assertEquals($unit->id, $item->unitRelation->id);
        $this->assertEquals('Kilogram Test', $item->unitRelation->name);

        $this->assertTrue($unit->items->contains($item));
    }

    public function test_item_relations_with_purchases_and_sales(): void
    {
        $vendor = \App\Models\Vendor::create([
            'name' => 'Test Vendor',
            'code' => 'VND-T1',
            'status' => 'active',
        ]);

        $customer = \App\Models\Customer::create([
            'name' => 'Test Customer',
            'code' => 'CUS-T1',
            'status' => 'active',
        ]);

        $item = Item::create([
            'code'            => 'ITM-REL-02',
            'name'            => 'Transaction Rel Herb',
            'category'        => 'Herbal Powder',
            'unit'            => 'KG',
            'hsn_code'        => '121190',
            'gst_rate'        => 5.00,
            'purchase_rate'   => 100.00,
            'sale_rate'       => 150.00,
            'opening_stock'   => 50.00,
            'current_stock'   => 50.00,
            'min_stock_alert' => 5.00,
            'status'          => 'active',
        ]);

        $purchase = \App\Models\Purchase::create([
            'purchase_no' => 'PUR-TEST-001',
            'invoice_date' => now()->toDateString(),
            'vendor_id' => $vendor->id,
            'order_type' => 'Medium',
            'subtotal' => 1000.00,
            'grand_total' => 1050.00,
            'status' => 'received',
        ]);

        $purchaseItem = \App\Models\PurchaseItem::create([
            'purchase_id' => $purchase->id,
            'item_id' => $item->id,
            'unit' => 'KG',
            'quantity' => 10,
            'bill_rate' => 100.00,
            'total_amount' => 1000.00,
        ]);

        $sale = \App\Models\Sale::create([
            'sale_no' => 'SAL-TEST-001',
            'sale_date' => now()->toDateString(),
            'customer_id' => $customer->id,
            'order_type' => 'Standard',
            'subtotal' => 750.00,
            'grand_total' => 787.50,
            'status' => 'dispatched',
        ]);

        $saleItem = \App\Models\SaleItem::create([
            'sale_id' => $sale->id,
            'item_id' => $item->id,
            'unit' => 'KG',
            'quantity' => 5,
            'bill_rate' => 150.00,
            'total_amount' => 750.00,
        ]);

        // Verify relationships from item side
        $this->assertTrue($item->purchaseItems->contains($purchaseItem));
        $this->assertTrue($item->purchases->contains($purchase));
        $this->assertTrue($item->saleItems->contains($saleItem));
        $this->assertTrue($item->sales->contains($sale));

        // Verify relationships from purchase & sale side
        $this->assertTrue($purchase->productItems->contains($item));
        $this->assertTrue($sale->productItems->contains($item));
    }
}
