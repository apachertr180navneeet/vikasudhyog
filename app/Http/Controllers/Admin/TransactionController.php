<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
        return view('admin.transactions.order-dispatch', [
            'pageTitle' => 'Order Dispatch - VIKAS UDHYOG ERP',
            'pageCode'  => 'txn-order-dispatch'
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
        return view('admin.transactions.receipt-voucher', [
            'pageTitle' => 'Receipt Voucher - VIKAS UDHYOG ERP',
            'pageCode'  => 'txn-receipt'
        ]);
    }

    public function paymentVoucher()
    {
        return view('admin.transactions.payment-voucher', [
            'pageTitle' => 'Payment Voucher - VIKAS UDHYOG ERP',
            'pageCode'  => 'txn-payment'
        ]);
    }
}
