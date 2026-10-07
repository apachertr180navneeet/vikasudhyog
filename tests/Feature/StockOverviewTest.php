<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class StockOverviewTest extends TestCase
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

    protected function createItems(): void
    {
        // Normal in-stock item
        Item::create([
            'code'            => 'ITM-01',
            'name'            => 'Natural Henna Powder 20kg',
            'category'        => 'Henna Products',
            'unit'            => 'KG',
            'purchase_rate'   => 120.00,
            'sale_rate'       => 160.00,
            'opening_stock'   => 500.00,
            'current_stock'   => 450.00,
            'min_stock_alert' => 50.00,
            'status'          => 'active',
        ]);

        // Low stock item (current <= min alert)
        Item::create([
            'code'            => 'ITM-02',
            'name'            => 'Amla Herbal Extract Grade A',
            'category'        => 'Raw Materials',
            'unit'            => 'KG',
            'purchase_rate'   => 250.00,
            'sale_rate'       => 320.00,
            'opening_stock'   => 100.00,
            'current_stock'   => 15.00, // <= 20.00!
            'min_stock_alert' => 20.00,
            'status'          => 'active',
        ]);

        // Out of stock item (current <= 0)
        Item::create([
            'code'            => 'ITM-03',
            'name'            => 'Shikakai Whole Pods',
            'category'        => 'Raw Materials',
            'unit'            => 'KG',
            'purchase_rate'   => 90.00,
            'sale_rate'       => 130.00,
            'opening_stock'   => 0.00,
            'current_stock'   => 0.00,
            'min_stock_alert' => 10.00,
            'status'          => 'active',
        ]);
    }

    public function test_stock_overview_page_can_be_rendered(): void
    {
        $user = $this->getAdminUser();

        $response = $this->actingAs($user)->get(route('admin.inventory.stock-overview'));
        $response->assertStatus(200);
        $response->assertSee('Stock Overview');
        $response->assertSee('Total Catalog Items');
        $response->assertSee('Total Inventory Valuation');
        $response->assertSee('Total Physical Stock Qty');
        $response->assertSee('Low Stock Alerts');
    }

    public function test_stock_overview_displays_kpis_and_items(): void
    {
        $user = $this->getAdminUser();
        $this->createItems();

        $response = $this->actingAs($user)->get(route('admin.inventory.stock-overview'));
        $response->assertStatus(200);

        // Check products listed
        $response->assertSee('Natural Henna Powder 20kg');
        $response->assertSee('Amla Herbal Extract Grade A');
        $response->assertSee('Shikakai Whole Pods');

        // Check Valuation: (450 * 120 = 54,000) + (15 * 250 = 3,750) + (0 * 90 = 0) = 57,750.00
        $response->assertSee('57,750.00');

        // Check Low Stock badge
        $response->assertSee('Low Stock');
        $response->assertSee('Out of Stock');
        $response->assertSee('In Stock');
    }

    public function test_stock_overview_filters_by_search_query(): void
    {
        $user = $this->getAdminUser();
        $this->createItems();

        $response = $this->actingAs($user)->get(route('admin.inventory.stock-overview', ['search' => 'Amla']));
        $response->assertStatus(200);
        $response->assertSee('Amla Herbal Extract Grade A');
        $response->assertDontSee('Natural Henna Powder 20kg');
        $response->assertDontSee('Shikakai Whole Pods');
    }

    public function test_stock_overview_filters_by_category(): void
    {
        $user = $this->getAdminUser();
        $this->createItems();

        $response = $this->actingAs($user)->get(route('admin.inventory.stock-overview', ['category' => 'Henna Products']));
        $response->assertStatus(200);
        $response->assertSee('Natural Henna Powder 20kg');
        $response->assertDontSee('Amla Herbal Extract Grade A');
    }

    public function test_stock_overview_filters_by_low_stock_status(): void
    {
        $user = $this->getAdminUser();
        $this->createItems();

        $response = $this->actingAs($user)->get(route('admin.inventory.stock-overview', ['stock_status' => 'low_stock']));
        $response->assertStatus(200);
        $response->assertSee('Amla Herbal Extract Grade A');
        $response->assertDontSee('Natural Henna Powder 20kg');
        $response->assertDontSee('Shikakai Whole Pods');
    }

    public function test_stock_overview_filters_by_out_of_stock_status(): void
    {
        $user = $this->getAdminUser();
        $this->createItems();

        $response = $this->actingAs($user)->get(route('admin.inventory.stock-overview', ['stock_status' => 'out_of_stock']));
        $response->assertStatus(200);
        $response->assertSee('Shikakai Whole Pods');
        $response->assertDontSee('Natural Henna Powder 20kg');
        $response->assertDontSee('Amla Herbal Extract Grade A');
    }

    public function test_stock_overview_exports_csv(): void
    {
        $user = $this->getAdminUser();
        $this->createItems();

        $response = $this->actingAs($user)->get(route('admin.inventory.stock-overview', ['export' => 'csv']));
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('"Item Code","Item Name"', $response->streamedContent());
        $this->assertStringContainsString('Natural Henna Powder 20kg', $response->streamedContent());
    }
}
