<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Unit;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UnitModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_matches_value_direct_code_and_name(): void
    {
        $unit = Unit::create([
            'name'           => 'Kilogram',
            'code'           => 'KG',
            'synonyms'       => 'KGS,KILO,KILOGRAM',
            'uqc_code'       => 'KGS',
            'decimal_places' => 2,
            'is_base_unit'   => true,
            'status'         => 'active',
        ]);

        $this->assertTrue($unit->matchesValue('KG'));
        $this->assertTrue($unit->matchesValue('kg'));
        $this->assertTrue($unit->matchesValue('Kilogram'));
        $this->assertTrue($unit->matchesValue('KILOGRAM'));
        $this->assertTrue($unit->matchesValue('kgs'));
        $this->assertTrue($unit->matchesValue('KILO'));
        $this->assertFalse($unit->matchesValue('GRAM'));
        $this->assertFalse($unit->matchesValue('BAG'));
    }

    public function test_matches_value_custom_unit_with_synonyms(): void
    {
        $unit = Unit::create([
            'name'           => 'Litre',
            'code'           => 'LTR',
            'synonyms'       => 'LITER,LITRES,L,LT',
            'uqc_code'       => 'LTR',
            'decimal_places' => 2,
            'is_base_unit'   => true,
            'status'         => 'active',
        ]);

        $this->assertTrue($unit->matchesValue('LTR'));
        $this->assertTrue($unit->matchesValue('ltr'));
        $this->assertTrue($unit->matchesValue('LITER'));
        $this->assertTrue($unit->matchesValue('LITRES'));
        $this->assertTrue($unit->matchesValue('lt'));
        $this->assertFalse($unit->matchesValue('KG'));
    }

    public function test_unit_initials_attribute(): void
    {
        $unit = Unit::create([
            'name'           => 'Metric Ton',
            'code'           => 'MT',
            'is_base_unit'   => true,
            'status'         => 'active',
        ]);

        $this->assertEquals('MT', $unit->initials);
    }

    public function test_unit_to_base_and_from_base_conversion(): void
    {
        $baseKg = Unit::create([
            'name'           => 'Kilogram',
            'code'           => 'KG',
            'is_base_unit'   => true,
            'status'         => 'active',
        ]);

        // 1 BAG = 20 KG
        $bag = Unit::create([
            'name'              => 'Bag (20 KG)',
            'code'              => 'BAG',
            'is_base_unit'      => false,
            'base_unit_id'      => $baseKg->id,
            'conversion_factor' => 20.0000,
            'operator'          => '*',
            'status'            => 'active',
        ]);

        // 5 BAG * 20 = 100 KG
        $this->assertEquals(100.0, $bag->toBase(5));
        // 100 KG / 20 = 5 BAG
        $this->assertEquals(5.0, $bag->fromBase(100));
    }
}
