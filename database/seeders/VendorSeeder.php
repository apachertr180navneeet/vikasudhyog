<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vendor;
use App\Models\Company;

class VendorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultCompany = Company::where('is_default', true)->first() ?? Company::first();
        $companyId = $defaultCompany ? $defaultCompany->id : null;

        $vendors = [
            [
                'code'            => 'VND-01',
                'name'            => 'Natural Herbs Pvt Ltd',
                'contact_person'  => 'Ramesh Patel',
                'phone'           => '9876543210',
                'email'           => 'info@naturalherbs.com',
                'gstin'           => '08AAACN1234A1Z1',
                'pan'             => 'AAACN1234A',
                'address'         => 'Plot 42, RIICO Industrial Area',
                'city'            => 'Udaipur',
                'state'           => 'Rajasthan',
                'pincode'         => '313001',
                'opening_balance' => 184200.00,
                'current_balance' => 184200.00,
                'payment_terms'   => '30 Days',
                'bank_name'       => 'State Bank of India',
                'bank_account_no' => '30982245891',
                'bank_ifsc'       => 'SBIN0001234',
                'bank_branch'     => 'Industrial Area Udaipur',
                'company_id'      => $companyId,
                'status'          => 'active',
                'notes'           => 'Primary vendor for raw henna leaf bags and herbal powders.',
            ],
            [
                'code'            => 'VND-02',
                'name'            => 'Shree Herbal Suppliers',
                'contact_person'  => 'Mahesh Kumar',
                'phone'           => '9876543211',
                'email'           => 'shreeherbal@gmail.com',
                'gstin'           => '08BBBCS5678B1Z2',
                'pan'             => 'BBBCS5678B',
                'address'         => 'Mandi Road, Near Krishi Upaj Mandi',
                'city'            => 'Nagaur',
                'state'           => 'Rajasthan',
                'pincode'         => '341001',
                'opening_balance' => 90150.00,
                'current_balance' => 90150.00,
                'payment_terms'   => '15 Days',
                'bank_name'       => 'HDFC Bank',
                'bank_account_no' => '50100234567891',
                'bank_ifsc'       => 'HDFC0001890',
                'bank_branch'     => 'Nagaur Main',
                'company_id'      => $companyId,
                'status'          => 'active',
                'notes'           => 'Specialized supplier for Amla and Shikakai raw dried berries.',
            ],
            [
                'code'            => 'VND-03',
                'name'            => 'Green Earth Traders',
                'contact_person'  => 'Vikram Singh',
                'phone'           => '9876543212',
                'email'           => 'greenearth@traders.com',
                'gstin'           => '08CCCGT9012C1Z3',
                'pan'             => 'CCCGT9012C',
                'address'         => 'Station Road, GIDC',
                'city'            => 'Barmer',
                'state'           => 'Rajasthan',
                'pincode'         => '344001',
                'opening_balance' => 0.00,
                'current_balance' => 0.00,
                'payment_terms'   => 'Immediate',
                'bank_name'       => 'ICICI Bank',
                'bank_account_no' => '023405001234',
                'bank_ifsc'       => 'ICIC0000234',
                'bank_branch'     => 'Barmer City',
                'company_id'      => $companyId,
                'status'          => 'active',
                'notes'           => 'Supplier for Multani Mitti lumps and clay materials.',
            ],
            [
                'code'            => 'VND-04',
                'name'            => 'Ayurveda Raw Materials',
                'contact_person'  => 'Dinesh Agarwal',
                'phone'           => '9876543213',
                'email'           => 'ayurvedaraw@gmail.com',
                'gstin'           => '08DDDAR3456D1Z4',
                'pan'             => 'DDDAR3456D',
                'address'         => 'B-12, Vishwakarma Industrial Area',
                'city'            => 'Jaipur',
                'state'           => 'Rajasthan',
                'pincode'         => '302013',
                'opening_balance' => 45000.00,
                'current_balance' => 45000.00,
                'payment_terms'   => '30 Days',
                'bank_name'       => 'Bank of Baroda',
                'bank_account_no' => '05430200004567',
                'bank_ifsc'       => 'BARB0VKIJAI',
                'bank_branch'     => 'VKIA Jaipur',
                'company_id'      => $companyId,
                'status'          => 'active',
                'notes'           => 'Packaging pouches, corrugated outer cartons, and printed labels.',
            ],
        ];

        foreach ($vendors as $v) {
            $vendor = Vendor::withTrashed()->where('code', $v['code'])->first();
            if ($vendor) {
                $vendor->restore();
                $vendor->update($v);
            } else {
                Vendor::create($v);
            }
        }
    }
}
