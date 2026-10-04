<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sale_no',
        'bill_type',
        'invoice_no',
        'sale_date',
        'customer_id',
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
        'sale_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'bill_total' => 'decimal:2',
        'under_billing_total' => 'decimal:2',
        'round_off' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'paid_amount' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
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
        return $this->hasMany(SaleItem::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function productItems()
    {
        return $this->belongsToMany(Item::class, 'sale_items', 'sale_id', 'item_id')
                    ->withPivot([
                        'batch_no', 'hsn_code', 'unit', 'quantity', 'actual_rate',
                        'bill_rate', 'ub_rate', 'gst_percent', 'tax_amount',
                        'bill_amount', 'under_amount', 'total_amount'
                    ])
                    ->withTimestamps();
    }

    public static function generateNextSaleNo(): string
    {
        $year = date('Y');
        $prefix = "SAL-{$year}-";
        $lastRecord = self::withTrashed()
            ->where('sale_no', 'like', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->first();

        if ($lastRecord && preg_match('/SAL-\d{4}-(\d+)/', $lastRecord->sale_no, $matches)) {
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
