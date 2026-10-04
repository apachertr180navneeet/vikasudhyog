<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\Company;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultCompany = Company::where('is_default', true)->first() ?? Company::first();
        $companyId = $defaultCompany ? $defaultCompany->id : null;

        $items = [
            [
                'code'            => 'ITM-01',
                'name'            => 'Pure Sojat Mehndi Powder (Super Fine)',
                'category'        => 'Mehndi / Henna',
                'unit'            => 'KG',
                'hsn_code'        => '1404',
                'gst_rate'        => 5.00,
                'purchase_rate'   => 85.00,
                'sale_rate'       => 140.00,
                'opening_stock'   => 2500.00,
                'current_stock'   => 2500.00,
                'min_stock_alert' => 300.00,
                'batch_no'        => 'MHN-2026-01',
                'company_id'      => $companyId,
                'status'          => 'active',
                'notes'           => 'Triple-filtered premium export quality natural green henna powder from Sojat farms.',
            ],
            [
                'code'            => 'ITM-02',
                'name'            => 'Natural Henna Cone Ready Paste',
                'category'        => 'Finished Product',
                'unit'            => 'BOX',
                'hsn_code'        => '3305',
                'gst_rate'        => 18.00,
                'purchase_rate'   => 120.00,
                'sale_rate'       => 210.00,
                'opening_stock'   => 450.00,
                'current_stock'   => 450.00,
                'min_stock_alert' => 50.00,
                'batch_no'        => 'CNE-2026-08',
                'company_id'      => $companyId,
                'status'          => 'active',
                'notes'           => 'Pre-mixed herbal body art paste in retail display box (12 cones per box).',
            ],
            [
                'code'            => 'ITM-03',
                'name'            => 'Amla Dry Fruit Herb Powder',
                'category'        => 'Herbal Powder',
                'unit'            => 'KG',
                'hsn_code'        => '1404',
                'gst_rate'        => 5.00,
                'purchase_rate'   => 110.00,
                'sale_rate'       => 175.00,
                'opening_stock'   => 800.00,
                'current_stock'   => 800.00,
                'min_stock_alert' => 100.00,
                'batch_no'        => 'AML-2026-04',
                'company_id'      => $companyId,
                'status'          => 'active',
                'notes'           => 'Rich in Vitamin C, air-classified micronized herbal powder for hair and scalp formulations.',
            ],
            [
                'code'            => 'ITM-04',
                'name'            => 'Shikakai Pod Powder (Hair Care Grade)',
                'category'        => 'Herbal Powder',
                'unit'            => 'KG',
                'hsn_code'        => '1404',
                'gst_rate'        => 5.00,
                'purchase_rate'   => 95.00,
                'sale_rate'       => 155.00,
                'opening_stock'   => 650.00,
                'current_stock'   => 650.00,
                'min_stock_alert' => 80.00,
                'batch_no'        => 'SHK-2026-02',
                'company_id'      => $companyId,
                'status'          => 'active',
                'notes'           => 'Acacia concinna seedless pod powder for Ayurvedic natural shampoos and hair cleansers.',
            ],
            [
                'code'            => 'ITM-05',
                'name'            => 'Organic Neem Leaf Powder',
                'category'        => 'Herbal Powder',
                'unit'            => 'KG',
                'hsn_code'        => '1404',
                'gst_rate'        => 5.00,
                'purchase_rate'   => 70.00,
                'sale_rate'       => 125.00,
                'opening_stock'   => 18.00,
                'current_stock'   => 18.00, // Below min_stock_alert (30.00) to demonstrate low stock indicator
                'min_stock_alert' => 30.00,
                'batch_no'        => 'NEM-2026-03',
                'company_id'      => $companyId,
                'status'          => 'active',
                'notes'           => 'Shade-dried Azadirachta indica leaves, therapeutic skin pack & medicinal herb raw material.',
            ],
            [
                'code'            => 'ITM-06',
                'name'            => 'Senna Leaves (Cassia Angustifolia) Prime',
                'category'        => 'Ayurvedic Raw Material',
                'unit'            => 'BAG',
                'hsn_code'        => '1211',
                'gst_rate'        => 5.00,
                'purchase_rate'   => 1400.00,
                'sale_rate'       => 2100.00,
                'opening_stock'   => 120.00,
                'current_stock'   => 120.00,
                'min_stock_alert' => 25.00,
                'batch_no'        => 'SNA-2026-07',
                'company_id'      => $companyId,
                'status'          => 'active',
                'notes'           => 'Graded high-sennoside whole leaf bags (40 KG per bag) for pharmaceutical export.',
            ],
            [
                'code'            => 'ITM-07',
                'name'            => 'Laminated Printed Mehndi Pouch (100g)',
                'category'        => 'Packaging Material',
                'unit'            => 'PACKET',
                'hsn_code'        => '3923',
                'gst_rate'        => 18.00,
                'purchase_rate'   => 2.20,
                'sale_rate'       => 3.50,
                'opening_stock'   => 12000.00,
                'current_stock'   => 12000.00,
                'min_stock_alert' => 2000.00,
                'batch_no'        => 'PKG-2026-11',
                'company_id'      => $companyId,
                'status'          => 'active',
                'notes'           => 'Multi-layer barrier foil pouch with zipper lock for premium retail herbal packaging.',
            ],
        ];

        foreach ($items as $data) {
            Item::updateOrCreate(
                ['code' => $data['code']],
                $data
            );
        }
    }
}
