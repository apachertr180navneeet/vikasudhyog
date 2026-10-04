<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Unit;

class UnitSeeder extends Seeder
{
    private function upsertUnit(array $attributes, array $values): Unit
    {
        $unit = Unit::withTrashed()->where('code', $attributes['code'])->first();
        if ($unit) {
            $unit->restore();
            $unit->update($values);
            return $unit;
        }
        return Unit::create(array_merge($attributes, $values));
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Primary Base Units
        $meter = $this->upsertUnit(
            ['code' => 'M'],
            [
                'name'              => 'Meter',
                'synonyms'          => 'MTR,METRE,METERS',
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

        $kg = $this->upsertUnit(
            ['code' => 'KG'],
            [
                'name'              => 'Kilogram',
                'synonyms'          => 'KGS,KILO,KILOGRAM',
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

        $box = $this->upsertUnit(
            ['code' => 'BOX'],
            [
                'name'              => 'Box',
                'synonyms'          => 'BOXES,BX,CARTON',
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

        $pkt = $this->upsertUnit(
            ['code' => 'PKT'],
            [
                'name'              => 'Packet',
                'synonyms'          => 'PACKET,PACK,POUCH,PCS',
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
        $this->upsertUnit(
            ['code' => 'CM'],
            [
                'name'              => 'Centimeter',
                'synonyms'          => 'CMS,CENTIMETRE',
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

        $this->upsertUnit(
            ['code' => 'GM'],
            [
                'name'              => 'Gram',
                'synonyms'          => 'GMS,GRAMS,GR',
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

        $this->upsertUnit(
            ['code' => 'BAG'],
            [
                'name'              => 'Bag (20 KG)',
                'synonyms'          => 'BGS,BAGS,BORI',
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

        $this->upsertUnit(
            ['code' => 'QTL'],
            [
                'name'              => 'Quintal',
                'synonyms'          => 'QUINTAL,QUINTLE,QNT',
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
