<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function purchaseReport()
    {
        return view('admin.reports.purchase-report', [
            'pageTitle' => 'Purchase Report - VIKAS UDHYOG ERP',
            'pageCode'  => 'rpt-purchase'
        ]);
    }

    public function salesReport()
    {
        return view('admin.reports.sales-report', [
            'pageTitle' => 'Sales Report - VIKAS UDHYOG ERP',
            'pageCode'  => 'rpt-sales'
        ]);
    }

    public function orderReport()
    {
        return view('admin.reports.order-report', [
            'pageTitle' => 'Order Report - VIKAS UDHYOG ERP',
            'pageCode'  => 'rpt-order'
        ]);
    }

    public function cashBankRegister()
    {
        return view('admin.reports.cash-bank-register', [
            'pageTitle' => 'Cash & Bank Register - VIKAS UDHYOG ERP',
            'pageCode'  => 'rpt-cash-reg'
        ]);
    }
}
