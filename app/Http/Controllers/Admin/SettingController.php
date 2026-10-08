<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\WhatsAppLog;
use App\Models\WhatsAppSetting;
use App\Services\BackupService;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class SettingController extends Controller
{
    /**
     * Display the Company Profile & Operating Settings page.
     */
    public function company(Request $request)
    {
        $companyId = $request->query('company_id');
        if ($companyId) {
            $company = Company::find($companyId);
        } else {
            $company = Company::getActiveCompany() ?? Company::first();
        }

        if (!$company) {
            $company = Company::create([
                'name'           => 'Vikas Udhyog',
                'code'           => 'VU-SOJAT',
                'tagline'        => 'Pure Herbal & Henna Manufacturing',
                'gstin'          => '08ABCDE1234F1Z5',
                'pan'            => 'ABCDE1234F',
                'phone'          => '+91 98290 12345',
                'email'          => 'info@vikasudhyog.com',
                'website'        => 'https://vikasudhyog.com',
                'address'        => 'Industrial Area, Mandi Road',
                'city'           => 'Sojat City',
                'state'          => 'Rajasthan',
                'pincode'        => '306104',
                'financial_year' => '2026-2027',
                'bank_name'      => 'State Bank of India',
                'bank_account_no'=> '39201928472',
                'bank_ifsc'      => 'SBIN0031245',
                'bank_branch'    => 'Sojat Main Branch',
                'status'         => 'active',
                'is_default'     => true,
            ]);
        }

        $allCompanies = Company::orderBy('is_default', 'desc')->orderBy('name')->get();

        return view('admin.settings.company', [
            'pageTitle'    => 'Company Information & Settings - VIKAS UDHYOG ERP',
            'pageCode'     => 'set-company',
            'company'      => $company,
            'allCompanies' => $allCompanies,
        ]);
    }

    /**
     * Update the Company Profile and Operating Settings.
     */
    public function updateCompany(Request $request)
    {
        $companyId = $request->input('company_id');
        $company = Company::findOrFail($companyId);

        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'code'            => 'nullable|string|max:50',
            'tagline'         => 'nullable|string|max:255',
            'gstin'           => 'nullable|string|max:20',
            'pan'             => 'nullable|string|max:20',
            'phone'           => 'nullable|string|max:50',
            'email'           => 'nullable|email|max:100',
            'website'         => 'nullable|string|max:150',
            'address'         => 'nullable|string|max:500',
            'city'            => 'nullable|string|max:100',
            'state'           => 'nullable|string|max:100',
            'pincode'         => 'nullable|string|max:20',
            'financial_year'  => 'nullable|string|max:20',
            'bank_name'       => 'nullable|string|max:100',
            'bank_account_no' => 'nullable|string|max:50',
            'bank_ifsc'       => 'nullable|string|max:30',
            'bank_branch'     => 'nullable|string|max:100',
            'is_default'      => 'nullable|boolean',
        ]);

        if ($request->has('is_default') && $request->boolean('is_default')) {
            $company->makeDefault();
            $validated['is_default'] = true;
        }

        $company->update($validated);

        // Update session if editing active operating company
        if (session('active_company_id') == $company->id || $company->is_default) {
            session([
                'active_company_id'   => $company->id,
                'active_company_name' => $company->name,
                'active_company_city' => $company->city,
            ]);
        }

        return redirect()->route('admin.settings.company', ['company_id' => $company->id])
            ->with('success', "Company settings for '{$company->name}' have been saved successfully!");
    }

    /**
     * Display the WhatsApp Business API Integration page.
     */
    public function whatsapp(Request $request, WhatsAppService $waService)
    {
        $company = Company::getActiveCompany() ?? Company::first();
        $settings = $waService->getSettings($company->id ?? null);

        // Fetch recent outgoing message logs
        $logs = WhatsAppLog::where('company_id', $company->id ?? null)
            ->orWhereNull('company_id')
            ->latest()
            ->take(20)
            ->get();

        // Calculate statistics & quota
        $monthlyQuota = $settings->monthly_quota > 0 ? $settings->monthly_quota : 5000;
        $creditsUsed = $settings->credits_used ?? 0;
        $quotaPercentage = min(100, round(($creditsUsed / $monthlyQuota) * 100, 1));
        $creditsRemaining = max(0, $monthlyQuota - $creditsUsed);

        $totalSent = WhatsAppLog::count();
        $successfulDeliveries = WhatsAppLog::whereIn('status', ['sent', 'delivered', 'read', 'simulated'])->count();
        $deliveryRate = $totalSent > 0 ? round(($successfulDeliveries / $totalSent) * 100, 1) : 100.0;

        $activeTriggers = 0;
        if ($settings->auto_send_invoice) $activeTriggers++;
        if ($settings->auto_send_order) $activeTriggers++;
        if ($settings->auto_send_dispatch) $activeTriggers++;
        if ($settings->auto_send_receipt) $activeTriggers++;
        if ($settings->auto_send_due_reminder) $activeTriggers++;

        $stats = [
            'status'             => $settings->sandbox_mode ? 'Sandbox Active' : ($settings->is_enabled ? 'Connected' : 'Disabled'),
            'status_color'       => $settings->sandbox_mode ? '#F59E0B' : ($settings->is_enabled ? '#10B981' : '#EF4444'),
            'credits_used'       => $creditsUsed,
            'monthly_quota'      => $monthlyQuota,
            'quota_percentage'   => $quotaPercentage,
            'credits_remaining'  => $creditsRemaining,
            'total_sent'         => $totalSent,
            'delivery_rate'      => $deliveryRate,
            'active_triggers'    => $activeTriggers,
        ];

        // Sample substitution variables for live preview
        $sampleVariables = [
            'invoice'      => $waService->getSampleVariables('invoice', $company),
            'order'        => $waService->getSampleVariables('order', $company),
            'dispatch'     => $waService->getSampleVariables('dispatch', $company),
            'receipt'      => $waService->getSampleVariables('receipt', $company),
            'due_reminder' => $waService->getSampleVariables('due_reminder', $company),
        ];

        return view('admin.settings.whatsapp', [
            'pageTitle'        => 'WhatsApp Business API - VIKAS UDHYOG ERP',
            'pageCode'         => 'set-whatsapp',
            'company'          => $company,
            'settings'         => $settings,
            'logs'             => $logs,
            'stats'            => $stats,
            'sampleVariables'  => $sampleVariables,
        ]);
    }

    /**
     * Update WhatsApp Business API credentials & notification templates.
     */
    public function updateWhatsapp(Request $request, WhatsAppService $waService)
    {
        $company = Company::getActiveCompany() ?? Company::first();
        $settings = $waService->getSettings($company->id ?? null);

        $validated = $request->validate([
            'provider'              => 'required|in:meta_cloud_api,twilio,gupshup,sandbox',
            'display_phone_number'  => 'nullable|string|max:30',
            'phone_number_id'       => 'nullable|string|max:100',
            'waba_id'               => 'nullable|string|max:100',
            'access_token'          => 'nullable|string',
            'webhook_verify_token'  => 'nullable|string|max:100',
            'monthly_quota'         => 'nullable|integer|min:100|max:100000',
            'invoice_template'      => 'nullable|string',
            'order_template'        => 'nullable|string',
            'dispatch_template'     => 'nullable|string',
            'receipt_template'      => 'nullable|string',
            'due_reminder_template' => 'nullable|string',
        ]);

        $settings->fill([
            'provider'               => $validated['provider'],
            'display_phone_number'   => $validated['display_phone_number'] ?? $settings->display_phone_number,
            'phone_number_id'        => $validated['phone_number_id'] ?? null,
            'waba_id'                => $validated['waba_id'] ?? null,
            'webhook_verify_token'   => $validated['webhook_verify_token'] ?? 'vu_wa_verify_token_2026',
            'monthly_quota'          => $validated['monthly_quota'] ?? 5000,
            'is_enabled'             => $request->has('is_enabled'),
            'sandbox_mode'           => $request->has('sandbox_mode'),
            'auto_send_invoice'      => $request->has('auto_send_invoice'),
            'auto_send_order'        => $request->has('auto_send_order'),
            'auto_send_dispatch'     => $request->has('auto_send_dispatch'),
            'auto_send_receipt'      => $request->has('auto_send_receipt'),
            'auto_send_due_reminder' => $request->has('auto_send_due_reminder'),
            'invoice_template'       => $validated['invoice_template'] ?? $settings->invoice_template,
            'order_template'         => $validated['order_template'] ?? $settings->order_template,
            'dispatch_template'      => $validated['dispatch_template'] ?? $settings->dispatch_template,
            'receipt_template'       => $validated['receipt_template'] ?? $settings->receipt_template,
            'due_reminder_template'  => $validated['due_reminder_template'] ?? $settings->due_reminder_template,
        ]);

        // Don't overwrite access_token with empty string if not supplied
        if (!empty($validated['access_token'])) {
            $settings->access_token = $validated['access_token'];
        }

        $settings->save();

        return redirect()->route('admin.settings.whatsapp')
            ->with('success', 'WhatsApp Business API configuration and templates updated successfully!');
    }

    /**
     * Dispatch a test WhatsApp notification.
     */
    public function sendTestWhatsapp(Request $request, WhatsAppService $waService)
    {
        $validated = $request->validate([
            'test_phone'    => 'required|string|min:10|max:20',
            'template_type' => 'required|in:invoice,order,dispatch,receipt,due_reminder,custom',
            'custom_text'   => 'nullable|string',
        ]);

        $company = Company::getActiveCompany() ?? Company::first();

        $result = $waService->dispatchMessage(
            phone: $validated['test_phone'],
            templateType: $validated['template_type'],
            variables: [],
            customText: $validated['custom_text'] ?? null,
            companyId: $company->id ?? null,
            recipientName: 'Test Recipient (' . $validated['test_phone'] . ')'
        );

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($result);
        }

        if ($result['success']) {
            return redirect()->route('admin.settings.whatsapp')
                ->with('success', $result['message']);
        }

        return redirect()->route('admin.settings.whatsapp')
            ->with('error', $result['message']);
    }

    /**
     * Test ping connectivity to Meta Graph API.
     */
    public function pingWhatsapp(Request $request, WhatsAppService $waService)
    {
        $company = Company::getActiveCompany() ?? Company::first();
        $result = $waService->pingConnection($company->id ?? null);

        return response()->json($result);
    }

    /**
     * Clear outgoing WhatsApp message logs.
     */
    public function clearWhatsappLogs(Request $request)
    {
        WhatsAppLog::truncate();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'WhatsApp message audit logs cleared.']);
        }

        return redirect()->route('admin.settings.whatsapp')
            ->with('success', 'WhatsApp dispatch logs cleared successfully.');
    }

    /**
     * Meta Cloud API Webhook Verification & Event Handler.
     */
    public function webhook(Request $request)
    {
        // 1. GET Handshake Verification (hub.mode, hub.verify_token, hub.challenge)
        if ($request->isMethod('get')) {
            $mode = $request->query('hub_mode') ?? $request->query('hub.mode');
            $token = $request->query('hub_verify_token') ?? $request->query('hub.verify_token');
            $challenge = $request->query('hub_challenge') ?? $request->query('hub.challenge');

            $setting = WhatsAppSetting::first();
            $expectedToken = $setting->webhook_verify_token ?? 'vu_wa_verify_token_2026';

            if ($mode === 'subscribe' && $token === $expectedToken) {
                return response($challenge, 200)->header('Content-Type', 'text/plain');
            }

            return response('Forbidden', 403);
        }

        // 2. POST Delivery Status / Incoming Message Callbacks
        $payload = $request->all();
        Log::info('Meta WhatsApp Webhook Payload:', $payload);

        // Update log status if message status event
        try {
            if (isset($payload['entry'][0]['changes'][0]['value']['statuses'][0])) {
                $statusData = $payload['entry'][0]['changes'][0]['value']['statuses'][0];
                $wamId = $statusData['id'] ?? null;
                $status = $statusData['status'] ?? null;

                if ($wamId && $status) {
                    WhatsAppLog::where('response_id', $wamId)->update(['status' => $status]);
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Failed to parse WhatsApp status update: ' . $e->getMessage());
        }

        return response()->json(['status' => 'EVENT_RECEIVED']);
    }

    /**
     * Display the Database Backup & Restore interface.
     */
    public function backupRestore(Request $request, BackupService $backupService)
    {
        $stats = $backupService->getDatabaseStats();
        $backups = $backupService->getBackupsList();

        return view('admin.settings.backup-restore', [
            'pageTitle' => 'Database Backup & Restore - VIKAS UDHYOG ERP',
            'pageCode'  => 'set-backup',
            'stats'     => $stats,
            'backups'   => $backups,
        ]);
    }

    /**
     * Create an on-demand SQL or JSON backup snapshot.
     */
    public function createBackup(Request $request, BackupService $backupService)
    {
        $type = $request->input('type', 'sql');
        if (!in_array(strtolower($type), ['sql', 'json'])) {
            $type = 'sql';
        }

        try {
            $result = $backupService->createBackup($type);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json($result);
            }

            return redirect()->route('admin.settings.backup-restore')
                ->with('success', $result['message']);
        } catch (\Throwable $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }

            return redirect()->route('admin.settings.backup-restore')
                ->with('error', 'Backup failed: ' . $e->getMessage());
        }
    }

    /**
     * Download a stored backup file.
     */
    public function downloadBackup(string $filename, BackupService $backupService)
    {
        $safeName = basename($filename);
        $filepath = storage_path('app/backups/' . $safeName);

        if (!File::exists($filepath)) {
            return redirect()->route('admin.settings.backup-restore')
                ->with('error', "Backup file '{$safeName}' was not found.");
        }

        return response()->download($filepath, $safeName, [
            'Content-Type' => 'application/octet-stream',
        ]);
    }

    /**
     * Restore database from an uploaded file or existing snapshot.
     */
    public function restoreBackup(Request $request, BackupService $backupService)
    {
        $tempFile = null;

        try {
            // Case 1: Uploaded backup file
            if ($request->hasFile('backup_file')) {
                $file = $request->file('backup_file');
                $extension = strtolower($file->getClientOriginalExtension());

                if (!in_array($extension, ['sql', 'json'])) {
                    return redirect()->route('admin.settings.backup-restore')
                        ->with('error', 'Invalid file type. Only .sql and .json database snapshots are supported.');
                }

                $filename = 'restore_temp_' . time() . '.' . $extension;
                $file->move(storage_path('app/backups'), $filename);
                $filepath = storage_path('app/backups/' . $filename);
                $tempFile = $filepath;

                $result = $backupService->restoreFromFile($filepath);
            }
            // Case 2: Selected existing backup snapshot
            elseif ($request->filled('existing_filename')) {
                $safeName = basename($request->input('existing_filename'));
                $filepath = storage_path('app/backups/' . $safeName);

                if (!File::exists($filepath)) {
                    return redirect()->route('admin.settings.backup-restore')
                        ->with('error', "Snapshot file '{$safeName}' not found.");
                }

                $result = $backupService->restoreFromFile($filepath);
            } else {
                return redirect()->route('admin.settings.backup-restore')
                    ->with('error', 'Please upload a backup file or choose an existing snapshot to restore.');
            }

            // Cleanup temp file if created
            if ($tempFile && File::exists($tempFile)) {
                File::delete($tempFile);
            }

            if ($result['success']) {
                return redirect()->route('admin.settings.backup-restore')
                    ->with('success', $result['message']);
            }

            return redirect()->route('admin.settings.backup-restore')
                ->with('error', $result['message']);

        } catch (\Throwable $e) {
            if ($tempFile && File::exists($tempFile)) {
                File::delete($tempFile);
            }

            return redirect()->route('admin.settings.backup-restore')
                ->with('error', 'Database restore failed: ' . $e->getMessage());
        }
    }

    /**
     * Delete an existing backup snapshot.
     */
    public function deleteBackup(string $filename, BackupService $backupService)
    {
        $safeName = basename($filename);
        $deleted = $backupService->deleteBackup($safeName);

        if ($deleted) {
            return redirect()->route('admin.settings.backup-restore')
                ->with('success', "Backup file '{$safeName}' has been permanently deleted.");
        }

        return redirect()->route('admin.settings.backup-restore')
            ->with('error', "Could not delete '{$safeName}' or file not found.");
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
