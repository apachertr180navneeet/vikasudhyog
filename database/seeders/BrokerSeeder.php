<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Broker;
use App\Models\Company;

class BrokerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultCompany = Company::where('is_default', true)->first() ?? Company::first();
        $companyId = $defaultCompany ? $defaultCompany->id : null;

        $brokers = [
            [
                'code'            => 'BRK-01',
                'name'            => 'Shree Ganesh Mandi Brokerage',
                'contact_person'  => 'Radheshyam Sharma',
                'phone'           => '9829012345',
                'email'           => 'shreeganesh.mandi@gmail.com',
                'commission_rate' => 1.50,
                'brokerage_type'  => 'Percentage (%)',
                'pan'             => 'AAPSR1234A',
                'gstin'           => '08AAPSR1234A1Z5',
                'address'         => 'Shop No. 14, Krishi Upaj Mandi Yard',
                'city'            => 'Sojat City',
                'state'           => 'Rajasthan',
                'pincode'         => '306104',
                'opening_balance' => 24500.00,
                'current_balance' => 24500.00,
                'bank_name'       => 'State Bank of India',
                'bank_account_no' => '31289456781',
                'bank_ifsc'       => 'SBIN0031124',
                'bank_branch'     => 'Main Mandi Branch Sojat',
                'company_id'      => $companyId,
                'status'          => 'active',
                'notes'           => 'Senior mandi commission agent handling bulk henna leaf consignments from Pali farmers.',
            ],
            [
                'code'            => 'BRK-02',
                'name'            => 'Marwar Henna Agency & Commission',
                'contact_person'  => 'Bhanwar Lal Gehlot',
                'phone'           => '9829023456',
                'email'           => 'marwar.henna.broker@gmail.com',
                'commission_rate' => 2.00,
                'brokerage_type'  => 'Percentage (%)',
                'pan'             => 'ABPLG5678B',
                'gstin'           => '08ABPLG5678B1Z8',
                'address'         => 'Station Road, Near Railway Goods Shed',
                'city'            => 'Sojat Road',
                'state'           => 'Rajasthan',
                'pincode'         => '306103',
                'opening_balance' => 15200.00,
                'current_balance' => 15200.00,
                'bank_name'       => 'Bank of Baroda',
                'bank_account_no' => '12090100045678',
                'bank_ifsc'       => 'BARB0SOJATR',
                'bank_branch'     => 'Sojat Road Station',
                'company_id'      => $companyId,
                'status'          => 'active',
                'notes'           => 'Specialist in out-of-state distributor sales order intermediation.',
            ],
            [
                'code'            => 'BRK-03',
                'name'            => 'Kisan Mandi Dalal & Trading',
                'contact_person'  => 'Mohanlal Choudhary',
                'phone'           => '9829034567',
                'email'           => 'kisan.mandi.sojat@yahoo.com',
                'commission_rate' => 1.25,
                'brokerage_type'  => 'Percentage (%)',
                'pan'             => 'ACMCH9012C',
                'gstin'           => null,
                'address'         => 'B-Block, Krishi Mandi Complex',
                'city'            => 'Sojat City',
                'state'           => 'Rajasthan',
                'pincode'         => '306104',
                'opening_balance' => 8400.00,
                'current_balance' => 8400.00,
                'bank_name'       => 'Punjab National Bank',
                'bank_account_no' => '0876000100987654',
                'bank_ifsc'       => 'PUNB0087600',
                'bank_branch'     => 'Krishi Mandi Yard',
                'company_id'      => null, // Global
                'status'          => 'active',
                'notes'           => 'Direct farmer procurement broker for raw green henna crop.',
            ],
            [
                'code'            => 'BRK-04',
                'name'            => 'Pali Ayurvedic Brokerage Associates',
                'contact_person'  => 'Surendra Singh',
                'phone'           => '9829045678',
                'email'           => 'pali.ayurvedic.brokers@gmail.com',
                'commission_rate' => 1.75,
                'brokerage_type'  => 'Percentage (%)',
                'pan'             => 'ADSSS3456D',
                'gstin'           => '08ADSSS3456D1Z2',
                'address'         => 'Industrial Estate, Near Mandia Road',
                'city'            => 'Pali',
                'state'           => 'Rajasthan',
                'pincode'         => '306401',
                'opening_balance' => 0.00,
                'current_balance' => 0.00,
                'bank_name'       => 'HDFC Bank',
                'bank_account_no' => '50200034567890',
                'bank_ifsc'       => 'HDFC0000456',
                'bank_branch'     => 'Mandia Road Pali',
                'company_id'      => $companyId,
                'status'          => 'inactive',
                'notes'           => 'Mediator for Amla, Shikakai, and Ayurvedic herbs.',
            ],
        ];

        foreach ($brokers as $b) {
            $broker = Broker::withTrashed()->where('code', $b['code'])->first();
            if ($broker) {
                $broker->restore();
                $broker->update($b);
            } else {
                Broker::create($b);
            }
        }
    }
}
