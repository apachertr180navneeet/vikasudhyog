<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_id',
        'item_id',
        'batch_no',
        'hsn_code',
        'unit',
        'quantity',
        'actual_rate',
        'bill_rate',
        'ub_rate',
        'gst_percent',
        'tax_amount',
        'bill_amount',
        'under_amount',
        'total_amount',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'actual_rate' => 'decimal:2',
        'bill_rate' => 'decimal:2',
        'ub_rate' => 'decimal:2',
        'gst_percent' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'bill_amount' => 'decimal:2',
        'under_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
