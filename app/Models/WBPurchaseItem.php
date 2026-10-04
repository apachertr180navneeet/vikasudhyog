<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WBPurchaseItem extends Model
{
    use HasFactory;

    protected $table = 'wb_purchase_items';

    protected $fillable = [
        'wb_purchase_id',
        'item_id',
        'batch_no',
        'hsn_code',
        'unit',
        'quantity',
        'rate',
        'amount',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'rate' => 'decimal:2',
        'amount' => 'decimal:2',
    ];

    public function wbPurchase()
    {
        return $this->belongsTo(WBPurchase::class, 'wb_purchase_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function unitRelation()
    {
        return $this->belongsTo(Unit::class, 'unit', 'code');
    }
}
