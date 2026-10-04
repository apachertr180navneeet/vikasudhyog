<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Item extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'category',
        'unit',
        'hsn_code',
        'gst_rate',
        'purchase_rate',
        'sale_rate',
        'opening_stock',
        'current_stock',
        'min_stock_alert',
        'batch_no',
        'company_id',
        'status',
        'notes',
    ];

    protected $casts = [
        'gst_rate'        => 'decimal:2',
        'purchase_rate'   => 'decimal:2',
        'sale_rate'       => 'decimal:2',
        'opening_stock'   => 'decimal:2',
        'current_stock'   => 'decimal:2',
        'min_stock_alert' => 'decimal:2',
    ];

    /**
     * Scope for active items.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for low stock alert items.
     */
    public function scopeLowStock($query)
    {
        return $query->whereColumn('current_stock', '<=', 'min_stock_alert');
    }

    /**
     * Relationship with Company plant.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get two-letter initials for circular avatar.
     */
    public function getInitialsAttribute(): string
    {
        $words = preg_split('/\s+/', trim($this->name));
        if (count($words) >= 2 && !empty($words[0]) && !empty($words[1])) {
            return strtoupper(mb_substr($words[0], 0, 1) . mb_substr($words[1], 0, 1));
        }
        return strtoupper(mb_substr($this->name, 0, 2));
    }

    /**
     * Check if product is in low stock state.
     */
    public function getIsLowStockAttribute(): bool
    {
        return (float)$this->current_stock <= (float)$this->min_stock_alert;
    }

    /**
     * Calculate stock valuation (current stock * purchase rate).
     */
    public function getStockValuationAttribute(): float
    {
        return (float)($this->current_stock * $this->purchase_rate);
    }

    /**
     * Generate an intelligent sequential unique item code (e.g. ITM-01, ITM-02).
     */
    public static function generateUniqueCode(?string $prefix = 'ITM', ?int $excludeId = null): string
    {
        $prefix = strtoupper(trim($prefix ?: 'ITM'));
        $maxNum = 0;

        $existingCodes = static::withTrashed()
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->where('code', 'like', "{$prefix}-%")
            ->pluck('code');

        foreach ($existingCodes as $code) {
            if (preg_match('/' . preg_quote($prefix, '/') . '-(\d+)/i', $code, $matches)) {
                $num = (int)$matches[1];
                if ($num > $maxNum) {
                    $maxNum = $num;
                }
            }
        }

        $nextNum = $maxNum + 1;
        $candidate = sprintf('%s-%02d', $prefix, $nextNum);

        // Ensure absolute uniqueness
        while (static::withTrashed()->where('code', $candidate)->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))->exists()) {
            $nextNum++;
            $candidate = sprintf('%s-%02d', $prefix, $nextNum);
        }

        return $candidate;
    }
}
