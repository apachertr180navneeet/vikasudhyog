<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vendor extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'contact_person',
        'phone',
        'email',
        'gstin',
        'pan',
        'address',
        'city',
        'state',
        'pincode',
        'opening_balance',
        'current_balance',
        'payment_terms',
        'bank_name',
        'bank_account_no',
        'bank_ifsc',
        'bank_branch',
        'company_id',
        'status',
        'notes',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
    ];

    /**
     * Scope for active vendors.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Relationship with Company plant.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get two-letter initials for avatar badge.
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
     * Generate an intelligent sequential unique vendor code (e.g. VND-01, VND-02).
     */
    public static function generateUniqueCode(?string $prefix = 'VND', ?int $excludeId = null): string
    {
        $prefix = strtoupper(trim($prefix ?: 'VND'));
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
