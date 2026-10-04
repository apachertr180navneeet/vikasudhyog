<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WBSaleItem extends Model
{
    use HasFactory;

    protected $table = 'wb_sale_items';

    protected $fillable = [
        'wb_sale_id',
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

    public function wbSale()
    {
        return $this->belongsTo(WBSale::class, 'wb_sale_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
