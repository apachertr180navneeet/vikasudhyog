<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MasterController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AccessLevelController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingController;

// Default redirect
Route::get('/', function () {
    return redirect()->route('admin.login');
});

// Admin Authentication & Operations
Route::prefix('admin')->name('admin.')->group(function () {
    // Guest Login Routes
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');

    // Authenticated Admin Protected Routes
    Route::middleware(['admin.auth'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

        // Root /admin redirect to dashboard
        Route::get('/', function () {
            return redirect()->route('admin.dashboard');
        });

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Masters Routes
        Route::prefix('masters')->name('masters.')->group(function () {
            // Company Master CRUD (Dedicated Pages)
            Route::get('/company', [CompanyController::class, 'index'])->name('company');
            Route::get('/company/create', [CompanyController::class, 'create'])->name('company.create');
            Route::get('/company/generate-code', [CompanyController::class, 'generateCode'])->name('company.generate-code');
            Route::post('/company', [CompanyController::class, 'store'])->name('company.store');
            Route::get('/company/{company}', [CompanyController::class, 'show'])->name('company.show');
            Route::get('/company/{company}/edit', [CompanyController::class, 'edit'])->name('company.edit');
            Route::put('/company/{company}', [CompanyController::class, 'update'])->name('company.update');
            Route::delete('/company/{company}', [CompanyController::class, 'destroy'])->name('company.destroy');
            Route::patch('/company/{company}/toggle-status', [CompanyController::class, 'toggleStatus'])->name('company.toggle-status');
            Route::patch('/company/{company}/set-default', [CompanyController::class, 'setDefault'])->name('company.set-default');

            // User Master CRUD (Dedicated Pages)
            Route::get('/user', [UserController::class, 'index'])->name('user');
            Route::get('/user/create', [UserController::class, 'create'])->name('user.create');
            Route::post('/user', [UserController::class, 'store'])->name('user.store');
            Route::get('/user/{user}', [UserController::class, 'show'])->name('user.show');
            Route::get('/user/{user}/edit', [UserController::class, 'edit'])->name('user.edit');
            Route::put('/user/{user}', [UserController::class, 'update'])->name('user.update');
            Route::delete('/user/{user}', [UserController::class, 'destroy'])->name('user.destroy');
            Route::patch('/user/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('user.toggle-status');

            // Access Level & Dynamic Role Permissions
            Route::get('/access-level', [AccessLevelController::class, 'index'])->name('access-level');
            Route::post('/access-level/roles', [AccessLevelController::class, 'store'])->name('access-level.store');
            Route::put('/access-level/roles/{role}', [AccessLevelController::class, 'update'])->name('access-level.update');
            Route::delete('/access-level/roles/{role}', [AccessLevelController::class, 'destroy'])->name('access-level.destroy');
            Route::patch('/access-level/roles/{role}/toggle-status', [AccessLevelController::class, 'toggleStatus'])->name('access-level.toggle-status');
            Route::post('/access-level/roles/{role}/permissions', [AccessLevelController::class, 'savePermissions'])->name('access-level.save-permissions');
            Route::post('/access-level/roles/{role}/reset-defaults', [AccessLevelController::class, 'resetDefaults'])->name('access-level.reset-defaults');

            Route::get('/vendor', [MasterController::class, 'vendor'])->name('vendor');
            Route::get('/customer', [MasterController::class, 'customer'])->name('customer');
            Route::get('/broker', [MasterController::class, 'broker'])->name('broker');
            Route::get('/item', [MasterController::class, 'item'])->name('item');
            Route::get('/unit', [MasterController::class, 'unit'])->name('unit');
            Route::get('/account', [MasterController::class, 'account'])->name('account');
        });

        // Transaction Routes
        Route::prefix('transactions')->name('transactions.')->group(function () {
            Route::get('/purchase-entry', [TransactionController::class, 'purchaseEntry'])->name('purchase-entry');
            Route::get('/wb-purchase-entry', [TransactionController::class, 'wbPurchaseEntry'])->name('wb-purchase-entry');
            Route::get('/sales-entry', [TransactionController::class, 'salesEntry'])->name('sales-entry');
            Route::get('/wb-sales-entry', [TransactionController::class, 'wbSalesEntry'])->name('wb-sales-entry');
            Route::get('/order-dispatch', [TransactionController::class, 'orderDispatch'])->name('order-dispatch');
            Route::get('/sales-purchase-order', [TransactionController::class, 'salesPurchaseOrder'])->name('sales-purchase-order');
            Route::get('/receipt-voucher', [TransactionController::class, 'receiptVoucher'])->name('receipt-voucher');
            Route::get('/payment-voucher', [TransactionController::class, 'paymentVoucher'])->name('payment-voucher');
        });

        // Inventory Routes
        Route::prefix('inventory')->name('inventory.')->group(function () {
            Route::get('/stock-overview', [InventoryController::class, 'stockOverview'])->name('stock-overview');
            Route::get('/item-ledger', [InventoryController::class, 'itemLedger'])->name('item-ledger');
            Route::get('/stock-adjustment', [InventoryController::class, 'stockAdjustment'])->name('stock-adjustment');
            Route::get('/low-stock-alert', [InventoryController::class, 'lowStockAlert'])->name('low-stock-alert');
        });

        // Report Routes
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/purchase-report', [ReportController::class, 'purchaseReport'])->name('purchase-report');
            Route::get('/sales-report', [ReportController::class, 'salesReport'])->name('sales-report');
            Route::get('/order-report', [ReportController::class, 'orderReport'])->name('order-report');
            Route::get('/cash-bank-register', [ReportController::class, 'cashBankRegister'])->name('cash-bank-register');
        });

        // Setting Routes
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/company', [SettingController::class, 'company'])->name('company');
            Route::get('/whatsapp', [SettingController::class, 'whatsapp'])->name('whatsapp');
            Route::get('/backup-restore', [SettingController::class, 'backupRestore'])->name('backup-restore');
        });
    });
});
