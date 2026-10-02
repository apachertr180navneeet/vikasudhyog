<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\User;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultCompany = Company::updateOrCreate(
            ['code' => 'VU-SOJAT'],
            [
                'name'           => 'Vikas Udhyog',
                'code'           => 'VU-SOJAT',
                'gstin'          => '08AAAAA0000A1Z5',
                'pan'            => 'AAAAA0000A',
                'phone'          => '+91 98290 12345',
                'email'          => 'info@vikasudhyog.com',
                'website'        => 'https://vikasudhyog.com',
                'address'        => 'Industrial Area, Khasra No. 452, Bypass Road',
                'city'           => 'Sojat City',
                'state'          => 'Rajasthan',
                'pincode'        => '306104',
                'financial_year' => '2026-2027',
                'bank_name'      => 'State Bank of India',
                'bank_account_no'=> '389201928374',
                'bank_ifsc'      => 'SBIN0001234',
                'bank_branch'    => 'Sojat City Branch',
                'tagline'        => 'Pure Herbal Excellence Since 1995',
                'status'         => 'active',
                'is_default'     => true,
            ]
        );

        $secondCompany = Company::updateOrCreate(
            ['code' => 'VAH-PALI'],
            [
                'name'           => 'Vikas Agro Herbs',
                'code'           => 'VAH-PALI',
                'gstin'          => '08BBBBB1111B1Z2',
                'pan'            => 'BBBBB1111B',
                'phone'          => '+91 98291 54321',
                'email'          => 'agro@vikasudhyog.com',
                'website'        => 'https://vikasudhyog.com',
                'address'        => 'Phase II, RIICO Industrial Area',
                'city'           => 'Pali',
                'state'          => 'Rajasthan',
                'pincode'        => '306401',
                'financial_year' => '2026-2027',
                'bank_name'      => 'HDFC Bank',
                'bank_account_no'=> '50100234567890',
                'bank_ifsc'      => 'HDFC0002345',
                'bank_branch'    => 'Pali Main Branch',
                'tagline'        => 'Organic Processing & Export Division',
                'status'         => 'active',
                'is_default'     => false,
            ]
        );

        // Assign default company to admin user if not already assigned
        User::where('username', 'admin')
            ->whereNull('company_id')
            ->update(['company_id' => $defaultCompany->id]);

        $this->command->info('Company profiles seeded successfully.');
    }
}
