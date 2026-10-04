<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MasterController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VendorController;
use App\Http\Controllers\Admin\BrokerController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\ItemController;
use App\Http\Controllers\Admin\UnitController;
use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\AccessLevelController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\PurchaseController;
use App\Http\Controllers\Admin\WBPurchaseController;
use App\Http\Controllers\Admin\SaleController;
use App\Http\Controllers\Admin\WBSaleController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingController;

// Default redirect
Route::get('/', function () {
    return redirect()->route('admin.login');
});

// Server Database Migration & Schema Update Route (for server deployment / cPanel / terminal-less hosting)
Route::get('/run-migration', [SettingController::class, 'runMigration'])->name('run-migration');

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
        Route::post('/switch-company', [CompanyController::class, 'switchActive'])->name('switch-company');

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
            Route::post('/company/switch-active', [CompanyController::class, 'switchActive'])->name('company.switch-active');

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

            // Vendor Master CRUD (Dedicated Pages)
            Route::get('/vendor', [VendorController::class, 'index'])->name('vendor');
            Route::get('/vendor/create', [VendorController::class, 'create'])->name('vendor.create');
            Route::get('/vendor/generate-code', [VendorController::class, 'generateCode'])->name('vendor.generate-code');
            Route::post('/vendor', [VendorController::class, 'store'])->name('vendor.store');
            Route::get('/vendor/{vendor}', [VendorController::class, 'show'])->name('vendor.show');
            Route::get('/vendor/{vendor}/edit', [VendorController::class, 'edit'])->name('vendor.edit');
            Route::put('/vendor/{vendor}', [VendorController::class, 'update'])->name('vendor.update');
            Route::delete('/vendor/{vendor}', [VendorController::class, 'destroy'])->name('vendor.destroy');
            Route::patch('/vendor/{vendor}/toggle-status', [VendorController::class, 'toggleStatus'])->name('vendor.toggle-status');
            // Broker Master CRUD (Dedicated Pages)
            Route::get('/broker', [BrokerController::class, 'index'])->name('broker');
            Route::get('/broker/create', [BrokerController::class, 'create'])->name('broker.create');
            Route::get('/broker/generate-code', [BrokerController::class, 'generateCode'])->name('broker.generate-code');
            Route::post('/broker', [BrokerController::class, 'store'])->name('broker.store');
            Route::get('/broker/{broker}', [BrokerController::class, 'show'])->name('broker.show');
            Route::get('/broker/{broker}/edit', [BrokerController::class, 'edit'])->name('broker.edit');
            Route::put('/broker/{broker}', [BrokerController::class, 'update'])->name('broker.update');
            Route::delete('/broker/{broker}', [BrokerController::class, 'destroy'])->name('broker.destroy');
            Route::patch('/broker/{broker}/toggle-status', [BrokerController::class, 'toggleStatus'])->name('broker.toggle-status');

            // Customer Master CRUD (Dedicated Pages)
            Route::get('/customer', [CustomerController::class, 'index'])->name('customer');
            Route::get('/customer/create', [CustomerController::class, 'create'])->name('customer.create');
            Route::get('/customer/generate-code', [CustomerController::class, 'generateCode'])->name('customer.generate-code');
            Route::post('/customer', [CustomerController::class, 'store'])->name('customer.store');
            Route::get('/customer/{customer}', [CustomerController::class, 'show'])->name('customer.show');
            Route::get('/customer/{customer}/edit', [CustomerController::class, 'edit'])->name('customer.edit');
            Route::put('/customer/{customer}', [CustomerController::class, 'update'])->name('customer.update');
            Route::delete('/customer/{customer}', [CustomerController::class, 'destroy'])->name('customer.destroy');
            Route::patch('/customer/{customer}/toggle-status', [CustomerController::class, 'toggleStatus'])->name('customer.toggle-status');
            // Item Master CRUD (Dedicated Pages)
            Route::get('/item', [ItemController::class, 'index'])->name('item');
            Route::get('/item/create', [ItemController::class, 'create'])->name('item.create');
            Route::get('/item/generate-code', [ItemController::class, 'generateCode'])->name('item.generate-code');
            Route::post('/item', [ItemController::class, 'store'])->name('item.store');
            Route::get('/item/{item}', [ItemController::class, 'show'])->name('item.show');
            Route::get('/item/{item}/edit', [ItemController::class, 'edit'])->name('item.edit');
            Route::put('/item/{item}', [ItemController::class, 'update'])->name('item.update');
            Route::delete('/item/{item}', [ItemController::class, 'destroy'])->name('item.destroy');
            Route::patch('/item/{item}/toggle-status', [ItemController::class, 'toggleStatus'])->name('item.toggle-status');
            // Unit Master CRUD (Dedicated Pages)
            Route::get('/unit', [UnitController::class, 'index'])->name('unit');
            Route::get('/unit/create', [UnitController::class, 'create'])->name('unit.create');
            Route::post('/unit', [UnitController::class, 'store'])->name('unit.store');
            Route::get('/unit/{unit}', [UnitController::class, 'show'])->name('unit.show');
            Route::get('/unit/{unit}/edit', [UnitController::class, 'edit'])->name('unit.edit');
            Route::put('/unit/{unit}', [UnitController::class, 'update'])->name('unit.update');
            Route::delete('/unit/{unit}', [UnitController::class, 'destroy'])->name('unit.destroy');
            Route::patch('/unit/{unit}/toggle-status', [UnitController::class, 'toggleStatus'])->name('unit.toggle-status');
            // Account Master CRUD (Dedicated Pages)
            Route::get('/account', [AccountController::class, 'index'])->name('account');
            Route::get('/account/create', [AccountController::class, 'create'])->name('account.create');
            Route::get('/account/generate-code', [AccountController::class, 'generateCode'])->name('account.generate-code');
            Route::post('/account', [AccountController::class, 'store'])->name('account.store');
            Route::get('/account/{account}', [AccountController::class, 'show'])->name('account.show');
            Route::get('/account/{account}/edit', [AccountController::class, 'edit'])->name('account.edit');
            Route::put('/account/{account}', [AccountController::class, 'update'])->name('account.update');
            Route::delete('/account/{account}', [AccountController::class, 'destroy'])->name('account.destroy');
            Route::patch('/account/{account}/toggle-status', [AccountController::class, 'toggleStatus'])->name('account.toggle-status');
        });

        // Transaction Routes
        Route::prefix('transactions')->name('transactions.')->group(function () {
            // Purchase Entry CRUD (Dedicated Pages)
            Route::get('/purchase-entry', [PurchaseController::class, 'index'])->name('purchase-entry');
            Route::get('/purchase-entry/create', [PurchaseController::class, 'create'])->name('purchase-entry.create');
            Route::get('/purchase-entry/generate-code', [PurchaseController::class, 'generateCode'])->name('purchase-entry.generate-code');
            Route::post('/purchase-entry', [PurchaseController::class, 'store'])->name('purchase-entry.store');
            Route::get('/purchase-entry/{purchase}', [PurchaseController::class, 'show'])->name('purchase-entry.show');
            Route::get('/purchase-entry/{purchase}/edit', [PurchaseController::class, 'edit'])->name('purchase-entry.edit');
            Route::put('/purchase-entry/{purchase}', [PurchaseController::class, 'update'])->name('purchase-entry.update');
            Route::delete('/purchase-entry/{purchase}', [PurchaseController::class, 'destroy'])->name('purchase-entry.destroy');
            Route::patch('/purchase-entry/{purchase}/toggle-status', [PurchaseController::class, 'toggleStatus'])->name('purchase-entry.toggle-status');

            // WB Purchase Entry (Without Bill) CRUD (Dedicated Pages)
            Route::get('/wb-purchase-entry', [WBPurchaseController::class, 'index'])->name('wb-purchase-entry');
            Route::get('/wb-purchase-entry/create', [WBPurchaseController::class, 'create'])->name('wb-purchase-entry.create');
            Route::get('/wb-purchase-entry/generate-code', [WBPurchaseController::class, 'generateCode'])->name('wb-purchase-entry.generate-code');
            Route::post('/wb-purchase-entry', [WBPurchaseController::class, 'store'])->name('wb-purchase-entry.store');
            Route::get('/wb-purchase-entry/{wbPurchase}', [WBPurchaseController::class, 'show'])->name('wb-purchase-entry.show');
            Route::get('/wb-purchase-entry/{wbPurchase}/edit', [WBPurchaseController::class, 'edit'])->name('wb-purchase-entry.edit');
            Route::put('/wb-purchase-entry/{wbPurchase}', [WBPurchaseController::class, 'update'])->name('wb-purchase-entry.update');
            Route::delete('/wb-purchase-entry/{wbPurchase}', [WBPurchaseController::class, 'destroy'])->name('wb-purchase-entry.destroy');
            Route::patch('/wb-purchase-entry/{wbPurchase}/toggle-status', [WBPurchaseController::class, 'toggleStatus'])->name('wb-purchase-entry.toggle-status');
            // Sales Entry (With Bill) CRUD
            Route::get('/sales-entry', [SaleController::class, 'index'])->name('sales-entry');
            Route::get('/sales-entry/create', [SaleController::class, 'create'])->name('sales-entry.create');
            Route::get('/sales-entry/generate-code', [SaleController::class, 'generateCode'])->name('sales-entry.generate-code');
            Route::post('/sales-entry', [SaleController::class, 'store'])->name('sales-entry.store');
            Route::get('/sales-entry/{sale}', [SaleController::class, 'show'])->name('sales-entry.show');
            Route::get('/sales-entry/{sale}/edit', [SaleController::class, 'edit'])->name('sales-entry.edit');
            Route::put('/sales-entry/{sale}', [SaleController::class, 'update'])->name('sales-entry.update');
            Route::delete('/sales-entry/{sale}', [SaleController::class, 'destroy'])->name('sales-entry.destroy');
            Route::patch('/sales-entry/{sale}/toggle-status', [SaleController::class, 'toggleStatus'])->name('sales-entry.toggle-status');

            // WB Sales Entry (Without Bill) CRUD
            Route::get('/wb-sales-entry', [WBSaleController::class, 'index'])->name('wb-sales-entry');
            Route::get('/wb-sales-entry/create', [WBSaleController::class, 'create'])->name('wb-sales-entry.create');
            Route::get('/wb-sales-entry/generate-code', [WBSaleController::class, 'generateCode'])->name('wb-sales-entry.generate-code');
            Route::post('/wb-sales-entry', [WBSaleController::class, 'store'])->name('wb-sales-entry.store');
            Route::get('/wb-sales-entry/{wbSale}', [WBSaleController::class, 'show'])->name('wb-sales-entry.show');
            Route::get('/wb-sales-entry/{wbSale}/edit', [WBSaleController::class, 'edit'])->name('wb-sales-entry.edit');
            Route::put('/wb-sales-entry/{wbSale}', [WBSaleController::class, 'update'])->name('wb-sales-entry.update');
            Route::delete('/wb-sales-entry/{wbSale}', [WBSaleController::class, 'destroy'])->name('wb-sales-entry.destroy');
            Route::patch('/wb-sales-entry/{wbSale}/toggle-status', [WBSaleController::class, 'toggleStatus'])->name('wb-sales-entry.toggle-status');
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
            Route::get('/run-migration', [SettingController::class, 'runMigration'])->name('run-migration');
        });
        Route::get('/run-migration', [SettingController::class, 'runMigration'])->name('run-migration');
    });
});
