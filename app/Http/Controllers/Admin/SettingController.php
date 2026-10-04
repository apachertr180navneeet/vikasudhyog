<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Artisan;

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

    /**
     * Run database migrations, seeders, and cache clears on the server via web route.
     */
    public function runMigration(Request $request)
    {
        $action = $request->query('action', 'migrate');
        $outputs = [];
        $status = 'success';
        $message = '';

        try {
            if ($action === 'status') {
                Artisan::call('migrate:status');
                $outputs['migrate:status'] = Artisan::output();
                $message = 'Migration status retrieved.';
            } elseif ($action === 'migrate-only') {
                Artisan::call('migrate', ['--force' => true]);
                $outputs['migrate'] = Artisan::output();
                $message = 'Pending database migrations executed successfully (migrations only).';
            } elseif ($action === 'seed') {
                Artisan::call('db:seed', ['--class' => 'UnitSeeder', '--force' => true]);
                $outputs['db:seed UnitSeeder'] = Artisan::output();
                $message = 'Unit master seeder executed successfully.';
            } elseif ($action === 'clear') {
                Artisan::call('optimize:clear');
                $outputs['optimize:clear'] = Artisan::output();
                $message = 'All system caches cleared.';
            } else {
                // Default action: run pending migrations, sync UnitSeeder, and clear cache
                Artisan::call('migrate', ['--force' => true]);
                $outputs['migrate'] = Artisan::output();

                Artisan::call('db:seed', ['--class' => 'UnitSeeder', '--force' => true]);
                $outputs['db:seed UnitSeeder'] = Artisan::output();

                Artisan::call('optimize:clear');
                $outputs['optimize:clear'] = Artisan::output();

                $message = 'Database migrations executed and caches cleared successfully!';
            }
        } catch (\Throwable $e) {
            $status = 'error';
            $message = 'Migration failed: ' . $e->getMessage();
            $outputs['error'] = $e->getMessage() . "\n\n" . $e->getTraceAsString();
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status'  => $status,
                'message' => $message,
                'outputs' => $outputs,
            ]);
        }

        return view('admin.settings.migration-result', [
            'pageTitle' => 'Server Migration Runner - VIKAS UDHYOG ERP',
            'pageCode'  => 'set-migration',
            'status'    => $status,
            'message'   => $message,
            'outputs'   => $outputs,
            'action'    => $action,
        ]);
    }
}
