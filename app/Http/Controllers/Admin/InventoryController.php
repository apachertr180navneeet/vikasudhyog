<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function stockOverview()
    {
        return view('admin.inventory.stock-overview', [
            'pageTitle' => 'Stock Overview - VIKAS UDHYOG ERP',
            'pageCode'  => 'inv-overview'
        ]);
    }

    public function itemLedger()
    {
        return view('admin.inventory.item-ledger', [
            'pageTitle' => 'Item Ledger - VIKAS UDHYOG ERP',
            'pageCode'  => 'inv-ledger'
        ]);
    }

    public function stockAdjustment()
    {
        return view('admin.inventory.stock-adjustment', [
            'pageTitle' => 'Stock Adjustment - VIKAS UDHYOG ERP',
            'pageCode'  => 'inv-adjustment'
        ]);
    }

    public function lowStockAlert()
    {
        return view('admin.inventory.low-stock-alert', [
            'pageTitle' => 'Low Stock Alert - VIKAS UDHYOG ERP',
            'pageCode'  => 'inv-low-stock'
        ]);
    }
}
