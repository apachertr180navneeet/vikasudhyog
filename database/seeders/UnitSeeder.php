<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Unit;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Primary Base Units
        $meter = Unit::updateOrCreate(
            ['code' => 'M'],
            [
                'name'              => 'Meter',
                'uqc_code'          => 'MTR',
                'decimal_places'    => 2,
                'is_base_unit'      => true,
                'base_unit_id'      => null,
                'conversion_factor' => 1.0000,
                'operator'          => '/',
                'description'       => 'Base linear measurement unit for rolls and packaging fabrics',
                'status'            => 'active',
            ]
        );

        $kg = Unit::updateOrCreate(
            ['code' => 'KG'],
            [
                'name'              => 'Kilogram',
                'uqc_code'          => 'KGS',
                'decimal_places'    => 2,
                'is_base_unit'      => true,
                'base_unit_id'      => null,
                'conversion_factor' => 1.0000,
                'operator'          => '/',
                'description'       => 'Primary mass weight unit for raw henna leaves and powder',
                'status'            => 'active',
            ]
        );

        $box = Unit::updateOrCreate(
            ['code' => 'BOX'],
            [
                'name'              => 'Box',
                'uqc_code'          => 'BOX',
                'decimal_places'    => 0,
                'is_base_unit'      => true,
                'base_unit_id'      => null,
                'conversion_factor' => 1.0000,
                'operator'          => '/',
                'description'       => 'Master corrugated outer packing boxes',
                'status'            => 'active',
            ]
        );

        $pkt = Unit::updateOrCreate(
            ['code' => 'PKT'],
            [
                'name'              => 'Packet',
                'uqc_code'          => 'PAC',
                'decimal_places'    => 0,
                'is_base_unit'      => true,
                'base_unit_id'      => null,
                'conversion_factor' => 1.0000,
                'operator'          => '/',
                'description'       => 'Pre-packaged retail pouch or consumer pack',
                'status'            => 'active',
            ]
        );

        // 2. Sub-Units / Derived Units with Explicit Relations (e.g. 100 cm = 1 m)
        Unit::updateOrCreate(
            ['code' => 'CM'],
            [
                'name'              => 'Centimeter',
                'uqc_code'          => 'CMS',
                'decimal_places'    => 2,
                'is_base_unit'      => false,
                'base_unit_id'      => $meter->id,
                'conversion_factor' => 100.0000,
                'operator'          => '/', // 100 CM = 1 M
                'description'       => 'Derived unit: 100 cm = 1 m. Used for pouch dimensions and roll widths',
                'status'            => 'active',
            ]
        );

        Unit::updateOrCreate(
            ['code' => 'GM'],
            [
                'name'              => 'Gram',
                'uqc_code'          => 'GMS',
                'decimal_places'    => 2,
                'is_base_unit'      => false,
                'base_unit_id'      => $kg->id,
                'conversion_factor' => 1000.0000,
                'operator'          => '/', // 1000 GM = 1 KG
                'description'       => 'Derived unit: 1000 gm = 1 kg. Used for retail henna cones and herbal samples',
                'status'            => 'active',
            ]
        );

        Unit::updateOrCreate(
            ['code' => 'BAG'],
            [
                'name'              => 'Bag (20 KG)',
                'uqc_code'          => 'BGS',
                'decimal_places'    => 0,
                'is_base_unit'      => false,
                'base_unit_id'      => $kg->id,
                'conversion_factor' => 20.0000,
                'operator'          => '*', // 1 BAG = 20 KG
                'description'       => 'Standard agricultural henna wholesale bag (20 kg net weight)',
                'status'            => 'active',
            ]
        );

        Unit::updateOrCreate(
            ['code' => 'QTL'],
            [
                'name'              => 'Quintal',
                'uqc_code'          => 'QTL',
                'decimal_places'    => 2,
                'is_base_unit'      => false,
                'base_unit_id'      => $kg->id,
                'conversion_factor' => 100.0000,
                'operator'          => '*', // 1 QTL = 100 KG
                'description'       => 'Mandi procurement wholesale lot unit (100 kg)',
                'status'            => 'active',
            ]
        );
    }
}
