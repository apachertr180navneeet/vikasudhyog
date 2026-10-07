<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Sale;
use App\Models\WBSale;
use App\Models\Customer;

class TransactionController extends Controller
{
    public function purchaseEntry()
    {
        return view('admin.transactions.purchase-entry', [
            'pageTitle' => 'Purchase Entry - VIKAS UDHYOG ERP',
            'pageCode'  => 'txn-purchase'
        ]);
    }

    public function wbPurchaseEntry()
    {
        return view('admin.transactions.wb-purchase-entry', [
            'pageTitle' => 'Weighbridge Purchase Entry - VIKAS UDHYOG ERP',
            'pageCode'  => 'txn-wb-purchase'
        ]);
    }

    public function salesEntry()
    {
        return view('admin.transactions.sales-entry', [
            'pageTitle' => 'Sales Entry - VIKAS UDHYOG ERP',
            'pageCode'  => 'txn-sales-order'
        ]);
    }

    public function wbSalesEntry()
    {
        return view('admin.transactions.wb-sales-entry', [
            'pageTitle' => 'Weighbridge Sales Entry - VIKAS UDHYOG ERP',
            'pageCode'  => 'txn-wb-sales'
        ]);
    }

    public function orderDispatch()
    {
        $sales = Sale::with('customer', 'broker')->latest()->get();
        $wbSales = WBSale::with('customer', 'broker')->latest()->get();
        $customers = Customer::where('status', 'active')->orderBy('name')->get();

        return view('admin.transactions.order-dispatch', [
            'pageTitle' => 'Order Dispatch - VIKAS UDHYOG ERP',
            'pageCode'  => 'txn-order-dispatch',
            'sales'     => $sales,
            'wbSales'   => $wbSales,
            'customers' => $customers,
        ]);
    }

    public function salesPurchaseOrder()
    {
        return view('admin.transactions.sales-purchase-order', [
            'pageTitle' => 'Sales / Purchase Order - VIKAS UDHYOG ERP',
            'pageCode'  => 'txn-sales-invoice'
        ]);
    }

    public function receiptVoucher()
    {
        return redirect()->route('admin.transactions.receipt-voucher');
    }

    public function paymentVoucher()
    {
        return redirect()->route('admin.transactions.payment-voucher');
    }
}
