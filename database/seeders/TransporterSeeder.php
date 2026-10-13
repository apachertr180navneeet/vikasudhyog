<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Transporter;
use Illuminate\Database\Seeder;

class TransporterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $company = Company::first();
        $companyId = $company ? $company->id : null;

        $transporters = [
            [
                'name'                 => 'VRL Logistics Ltd',
                'transporter_id_gstin' => '29AABCV1234F1Z8',
                'phone'                => '0836-2237511',
                'city'                 => 'Hubballi',
                'is_active'            => true,
            ],
            [
                'name'                 => 'TCI Freight (Transport Corp of India)',
                'transporter_id_gstin' => '07AAACT2727Q1ZT',
                'phone'                => '0124-2381603',
                'city'                 => 'Gurugram',
                'is_active'            => true,
            ],
            [
                'name'                 => 'Gati Kintetsu Express',
                'transporter_id_gstin' => '36AABCG7384A1Z7',
                'phone'                => '040-27844284',
                'city'                 => 'Hyderabad',
                'is_active'            => true,
            ],
            [
                'name'                 => 'Safechem Logistics & Cargo',
                'transporter_id_gstin' => '08AABCS9821K1Z2',
                'phone'                => '0141-2334589',
                'city'                 => 'Jaipur',
                'is_active'            => true,
            ],
            [
                'name'                 => 'Local Dedicated Transport',
                'transporter_id_gstin' => null,
                'phone'                => '9829012345',
                'city'                 => 'Local Plant',
                'is_active'            => true,
            ],
        ];

        foreach ($transporters as $data) {
            Transporter::updateOrCreate(
                ['name' => $data['name']],
                array_merge($data, ['company_id' => $companyId])
            );
        }
    }
}
