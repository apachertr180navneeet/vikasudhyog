<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Account extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'account_group',
        'opening_balance',
        'current_balance',
        'balance_type',
        'company_id',
        'bank_name',
        'account_number',
        'ifsc_code',
        'branch_name',
        'upi_id',
        'notes',
        'status',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
    ];

    /**
     * Relationship with Company.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Scope for active accounts.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for bank and cash accounts.
     */
    public function scopeBankAndCash($query)
    {
        return $query->whereIn('account_group', ['Bank Accounts', 'Cash in Hand']);
    }

    /**
     * Scope for expense ledgers.
     */
    public function scopeExpenses($query)
    {
        return $query->whereIn('account_group', ['Direct Expenses', 'Indirect Expenses']);
    }

    /**
     * Scope for income / sales ledgers.
     */
    public function scopeIncomes($query)
    {
        return $query->whereIn('account_group', ['Direct Incomes', 'Indirect Incomes']);
    }

    /**
     * Check if account is a bank account.
     */
    public function getIsBankAttribute(): bool
    {
        return $this->account_group === 'Bank Accounts';
    }

    /**
     * Check if account is cash.
     */
    public function getIsCashAttribute(): bool
    {
        return $this->account_group === 'Cash in Hand';
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
     * Generate an intelligent sequential unique account code (e.g. BNK-01, CSH-01, EXP-01, ACC-01).
     */
    public static function generateUniqueCode(?string $prefix = 'ACC', ?int $excludeId = null): string
    {
        $prefix = strtoupper(trim($prefix ?: 'ACC'));
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

        while (static::withTrashed()->where('code', $candidate)->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))->exists()) {
            $nextNum++;
            $candidate = sprintf('%s-%02d', $prefix, $nextNum);
        }

        return $candidate;
    }
}
