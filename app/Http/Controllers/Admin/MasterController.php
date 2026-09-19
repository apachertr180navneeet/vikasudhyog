<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MasterController extends Controller
{
    public function company()
    {
        return view('admin.masters.company', [
            'pageTitle' => 'Company Master - VIKAS UDHYOG ERP',
            'pageCode'  => 'master-company'
        ]);
    }

    public function user()
    {
        return view('admin.masters.user', [
            'pageTitle' => 'User Master - VIKAS UDHYOG ERP',
            'pageCode'  => 'master-user'
        ]);
    }

    public function accessLevel()
    {
        $userCounts = \App\Models\User::select('role', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
            ->groupBy('role')
            ->pluck('total', 'role')
            ->toArray();

        return view('admin.masters.access-level', [
            'pageTitle'  => 'Access Level & Role Management - VIKAS UDHYOG ERP',
            'pageCode'   => 'master-access',
            'userCounts' => $userCounts,
        ]);
    }

    public function vendor()
    {
        return view('admin.masters.vendor', [
            'pageTitle' => 'Vendor Master - VIKAS UDHYOG ERP',
            'pageCode'  => 'master-vendor'
        ]);
    }

    public function customer()
    {
        return view('admin.masters.customer', [
            'pageTitle' => 'Customer Master - VIKAS UDHYOG ERP',
            'pageCode'  => 'master-customer'
        ]);
    }

    public function broker()
    {
        return view('admin.masters.broker', [
            'pageTitle' => 'Broker Master - VIKAS UDHYOG ERP',
            'pageCode'  => 'master-broker'
        ]);
    }

    public function item()
    {
        return view('admin.masters.item', [
            'pageTitle' => 'Item Master - VIKAS UDHYOG ERP',
            'pageCode'  => 'master-item'
        ]);
    }

    public function unit()
    {
        return view('admin.masters.unit', [
            'pageTitle' => 'Unit Master - VIKAS UDHYOG ERP',
            'pageCode'  => 'master-unit'
        ]);
    }

    public function account()
    {
        return view('admin.masters.account', [
            'pageTitle' => 'Account Master - VIKAS UDHYOG ERP',
            'pageCode'  => 'master-account'
        ]);
    }
}
