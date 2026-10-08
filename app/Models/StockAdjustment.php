<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class StockAdjustment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'adjustment_no',
        'adjustment_date',
        'item_id',
        'type',
        'quantity',
        'previous_stock',
        'new_stock',
        'unit',
        'rate',
        'total_value',
        'reason',
        'notes',
        'audited_by',
        'user_id',
        'company_id',
        'status',
    ];

    protected $casts = [
        'adjustment_date' => 'date',
        'quantity'        => 'decimal:2',
        'previous_stock'  => 'decimal:2',
        'new_stock'       => 'decimal:2',
        'rate'            => 'decimal:2',
        'total_value'     => 'decimal:2',
    ];

    /**
     * Relationship with Item.
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Relationship with User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship with Company.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Generate next unique adjustment number (e.g. ADJ-202610-0001).
     */
    public static function generateAdjustmentNo(): string
    {
        $prefix = 'ADJ-' . date('Ym') . '-';
        $latest = static::withTrashed()
            ->where('adjustment_no', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if ($latest) {
            $lastNum = (int) substr($latest->adjustment_no, strlen($prefix));
            $nextNum = str_pad($lastNum + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNum = '0001';
        }

        return $prefix . $nextNum;
    }

    /**
     * Scope for filtering by keyword search.
     */
    public function scopeSearch($query, $search)
    {
        if (empty($search)) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('adjustment_no', 'like', "%{$search}%")
              ->orWhere('reason', 'like', "%{$search}%")
              ->orWhere('notes', 'like', "%{$search}%")
              ->orWhere('audited_by', 'like', "%{$search}%")
              ->orWhereHas('item', function ($iq) use ($search) {
                  $iq->where('name', 'like', "%{$search}%")
                     ->orWhere('code', 'like', "%{$search}%")
                     ->orWhere('batch_no', 'like', "%{$search}%");
              });
        });
    }

    /**
     * Scope for filtering by type.
     */
    public function scopeByType($query, $type)
    {
        if (empty($type) || $type === 'all') {
            return $query;
        }

        return $query->where('type', $type);
    }

    /**
     * Scope for filtering by item.
     */
    public function scopeByItem($query, $itemId)
    {
        if (empty($itemId) || $itemId === 'all') {
            return $query;
        }

        return $query->where('item_id', $itemId);
    }
}
