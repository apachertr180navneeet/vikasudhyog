<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReceiptVoucher extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'receipt_vouchers';

    protected $fillable = [
        'voucher_no',
        'voucher_date',
        'receipt_type',
        'customer_id',
        'income_source',
        'account_id',
        'amount',
        'payment_mode',
        'reference_no',
        'reference_date',
        'against_invoice',
        'notes',
        'status',
        'created_by',
    ];

    protected $casts = [
        'voucher_date'   => 'date',
        'reference_date' => 'date',
        'amount'         => 'decimal:2',
    ];

    /**
     * Relationship with Customer.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Relationship with Receiving Account (Bank / Cash).
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Relationship with creator user.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope for active vouchers.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for customer receipts.
     */
    public function scopeCustomerReceipts($query)
    {
        return $query->where('receipt_type', 'Customer');
    }

    /**
     * Scope for direct income receipts.
     */
    public function scopeDirectIncomes($query)
    {
        return $query->where('receipt_type', 'Income');
    }

    /**
     * Get Party Name (Customer or Income Source).
     */
    public function getPartyNameAttribute(): string
    {
        if ($this->receipt_type === 'Customer' && $this->customer) {
            return $this->customer->name;
        }

        return $this->income_source ?: 'Direct Income';
    }

    /**
     * Get 2-letter initials for avatar badge.
     */
    public function getInitialsAttribute(): string
    {
        $name = $this->party_name;
        $words = preg_split('/\s+/', trim($name));
        if (count($words) >= 2 && !empty($words[0]) && !empty($words[1])) {
            return strtoupper(mb_substr($words[0], 0, 1) . mb_substr($words[1], 0, 1));
        }
        return strtoupper(mb_substr($name, 0, 2));
    }

    /**
     * Generate sequential unique voucher number (e.g., RCP-2026-0001).
     */
    public static function generateNextVoucherNo(?int $excludeId = null): string
    {
        $year = date('Y');
        $prefix = "RCP-{$year}";
        $maxNum = 0;

        $existing = static::withTrashed()
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->where('voucher_no', 'like', "{$prefix}-%")
            ->pluck('voucher_no');

        foreach ($existing as $no) {
            if (preg_match('/' . preg_quote($prefix, '/') . '-(\d+)/', $no, $matches)) {
                $num = (int)$matches[1];
                if ($num > $maxNum) {
                    $maxNum = $num;
                }
            }
        }

        $nextNum = $maxNum + 1;
        $candidate = sprintf('%s-%04d', $prefix, $nextNum);

        while (static::withTrashed()->where('voucher_no', $candidate)->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))->exists()) {
            $nextNum++;
            $candidate = sprintf('%s-%04d', $prefix, $nextNum);
        }

        return $candidate;
    }
}
