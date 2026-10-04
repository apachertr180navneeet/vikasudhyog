<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unit extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'synonyms',
        'uqc_code',
        'decimal_places',
        'is_base_unit',
        'base_unit_id',
        'conversion_factor',
        'operator',
        'description',
        'status',
    ];

    protected $casts = [
        'decimal_places'    => 'integer',
        'is_base_unit'      => 'boolean',
        'conversion_factor' => 'decimal:4',
    ];

    /**
     * Scope for active units.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for primary base units.
     */
    public function scopeBaseUnits($query)
    {
        return $query->where(function ($q) {
            $q->where('is_base_unit', true)
              ->orWhereNull('base_unit_id');
        });
    }

    /**
     * Parent base unit relationship.
     */
    public function baseUnit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'base_unit_id');
    }

    /**
     * Derived sub-units relationship.
     */
    public function subUnits(): HasMany
    {
        return $this->hasMany(Unit::class, 'base_unit_id');
    }

    /**
     * Items assigned to this unit of measure.
     */
    public function items(): HasMany
    {
        return $this->hasMany(Item::class, 'unit_id');
    }

    /**
     * Check if a given value matches this unit's code, name, or any synonym.
     * Fully dynamic — no hardcoded synonym arrays needed.
     *
     * @param string $value  The unit value to match (e.g. 'KG', 'Kilogram', 'PKT')
     * @return bool
     */
    public function matchesValue(string $value): bool
    {
        $needle = strtoupper(trim($value));
        if ($needle === '') {
            return false;
        }

        // Direct match on code or name
        if ($needle === strtoupper(trim($this->code ?? '')) ||
            $needle === strtoupper(trim($this->name ?? ''))) {
            return true;
        }

        // Match against comma-separated synonyms
        if (!empty($this->synonyms)) {
            $synonymList = array_map(function ($s) {
                return strtoupper(trim($s));
            }, explode(',', $this->synonyms));

            if (in_array($needle, $synonymList, true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get two-letter initials for circular avatar.
     */
    public function getInitialsAttribute(): string
    {
        $code = trim($this->code);
        if (strlen($code) >= 2) {
            return strtoupper(substr($code, 0, 2));
        }
        $nameWords = preg_split('/\s+/', trim($this->name));
        if (count($nameWords) >= 2 && !empty($nameWords[0]) && !empty($nameWords[1])) {
            return strtoupper(mb_substr($nameWords[0], 0, 1) . mb_substr($nameWords[1], 0, 1));
        }
        return strtoupper(mb_substr($this->name, 0, 2));
    }

    /**
     * Human-readable unit relation formula.
     * e.g. "100 CM = 1 M" or "1 BAG = 20 KG" or "Primary Base Unit".
     */
    public function getRelationFormulaAttribute(): string
    {
        if ($this->is_base_unit || empty($this->base_unit_id) || !$this->baseUnit) {
            return 'Primary Base Unit';
        }

        $factor = (float)$this->conversion_factor;
        $factorStr = rtrim(rtrim(number_format($factor, 4, '.', ''), '0'), '.');
        $baseCode = $this->baseUnit->code;

        if ($this->operator === '/') {
            // e.g. 100 CM = 1 M
            return "{$factorStr} {$this->code} = 1 {$baseCode}";
        } else {
            // e.g. 1 BAG = 20 KG
            return "1 {$this->code} = {$factorStr} {$baseCode}";
        }
    }

    /**
     * Secondary unit equivalent formula (e.g. 1 CM = 0.01 M).
     */
    public function getEquivalentFormulaAttribute(): ?string
    {
        if ($this->is_base_unit || empty($this->base_unit_id) || !$this->baseUnit) {
            return null;
        }

        $factor = (float)$this->conversion_factor;
        $baseCode = $this->baseUnit->code;

        if ($factor <= 0) {
            return null;
        }

        if ($this->operator === '/') {
            $equiv = 1 / $factor;
            $equivStr = rtrim(rtrim(number_format($equiv, 6, '.', ''), '0'), '.');
            return "1 {$this->code} = {$equivStr} {$baseCode}";
        } else {
            $equiv = 1 / $factor;
            $equivStr = rtrim(rtrim(number_format($equiv, 6, '.', ''), '0'), '.');
            return "1 {$baseCode} = {$equivStr} {$this->code}";
        }
    }

    /**
     * Convert an amount from this unit to base unit.
     */
    public function toBase(float $qty): float
    {
        if ($this->is_base_unit || empty($this->base_unit_id)) {
            return $qty;
        }

        $factor = (float)$this->conversion_factor;
        if ($factor <= 0) {
            return $qty;
        }

        if ($this->operator === '/') {
            // 100 CM = 1 M -> 250 CM / 100 = 2.5 M
            return $qty / $factor;
        } else {
            // 1 BAG = 20 KG -> 5 BAG * 20 = 100 KG
            return $qty * $factor;
        }
    }

    /**
     * Convert an amount from base unit to this unit.
     */
    public function fromBase(float $baseQty): float
    {
        if ($this->is_base_unit || empty($this->base_unit_id)) {
            return $baseQty;
        }

        $factor = (float)$this->conversion_factor;
        if ($factor <= 0) {
            return $baseQty;
        }

        if ($this->operator === '/') {
            // 2.5 M * 100 = 250 CM
            return $baseQty * $factor;
        } else {
            // 100 KG / 20 = 5 BAG
            return $baseQty / $factor;
        }
    }
}
