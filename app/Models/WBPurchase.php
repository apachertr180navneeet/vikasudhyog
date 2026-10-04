<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WBPurchase extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'wb_purchases';

    protected $fillable = [
        'slip_no',
        'bill_type',
        'invoice_no',
        'entry_date',
        'vendor_id',
        'broker_id',
        'order_type',
        'vehicle_no',
        'payment_terms',
        'driver_name',
        'driver_phone',
        'gross_weight',
        'tare_weight',
        'deduction_weight',
        'net_weight',
        'total_amount',
        'paid_amount',
        'payment_status',
        'payment_mode',
        'company_id',
        'status',
        'notes',
    ];

    protected $casts = [
        'entry_date' => 'date',
        'gross_weight' => 'decimal:3',
        'tare_weight' => 'decimal:3',
        'deduction_weight' => 'decimal:3',
        'net_weight' => 'decimal:3',
        'total_amount' => 'decimal:2',
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
        return $this->hasMany(WBPurchaseItem::class, 'wb_purchase_id');
    }

    public function wbPurchaseItems()
    {
        return $this->hasMany(WBPurchaseItem::class, 'wb_purchase_id');
    }

    public function productItems()
    {
        return $this->belongsToMany(Item::class, 'wb_purchase_items', 'wb_purchase_id', 'item_id')
                    ->withPivot([
                        'batch_no', 'hsn_code', 'unit', 'quantity', 'rate', 'amount', 'notes'
                    ])
                    ->withTimestamps();
    }

    public static function generateNextSlipNo(): string
    {
        $year = date('Y');
        $prefix = "WBP-{$year}-";
        $lastRecord = self::withTrashed()
            ->where('slip_no', 'like', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->first();

        if ($lastRecord && preg_match('/WBP-\d{4}-(\d+)/', $lastRecord->slip_no, $matches)) {
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
