<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function company()
    {
        return view('admin.settings.company', [
            'pageTitle' => 'Company Information - VIKAS UDHYOG ERP',
            'pageCode'  => 'set-company'
        ]);
    }

    public function whatsapp()
    {
        return view('admin.settings.whatsapp', [
            'pageTitle' => 'WhatsApp Business API - VIKAS UDHYOG ERP',
            'pageCode'  => 'set-whatsapp'
        ]);
    }

    public function backupRestore()
    {
        return view('admin.settings.backup-restore', [
            'pageTitle' => 'Backup / Restore - VIKAS UDHYOG ERP',
            'pageCode'  => 'set-backup'
        ]);
    }
}
