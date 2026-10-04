<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchase extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'purchase_no',
        'bill_type',
        'invoice_no',
        'invoice_date',
        'vendor_id',
        'broker_id',
        'order_type',
        'vehicle_no',
        'payment_terms',
        'subtotal',
        'tax_amount',
        'bill_total',
        'under_billing_total',
        'round_off',
        'grand_total',
        'paid_amount',
        'payment_status',
        'company_id',
        'status',
        'notes',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'bill_total' => 'decimal:2',
        'under_billing_total' => 'decimal:2',
        'round_off' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'paid_amount' => 'decimal:2',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function broker()
    {
        return $this->belongsTo(Broker::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function productItems()
    {
        return $this->belongsToMany(Item::class, 'purchase_items', 'purchase_id', 'item_id')
                    ->withPivot([
                        'batch_no', 'hsn_code', 'unit', 'quantity', 'actual_rate',
                        'bill_rate', 'ub_rate', 'gst_percent', 'tax_amount',
                        'bill_amount', 'under_amount', 'total_amount'
                    ])
                    ->withTimestamps();
    }

    public static function generateNextPurchaseNo(): string
    {
        $year = date('Y');
        $prefix = "PUR-{$year}-";
        $lastRecord = self::withTrashed()
            ->where('purchase_no', 'like', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->first();

        if ($lastRecord && preg_match('/PUR-\d{4}-(\d+)/', $lastRecord->purchase_no, $matches)) {
            $nextSeq = intval($matches[1]) + 1;
        } else {
            $nextSeq = 1;
        }

        return sprintf("%s%04d", $prefix, $nextSeq);
    }

    public function scopeActive($query)
    {
        return $query->where('status', '!=', 'cancelled');
    }
}
