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
use App\Http\Controllers\Admin\ReceiptVoucherController;
use App\Http\Controllers\Admin\PaymentVoucherController;
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
            Route::patch('/sales-entry/{sale}/update-status', [SaleController::class, 'updateStatus'])->name('sales-entry.update-status');

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
            Route::patch('/wb-sales-entry/{wbSale}/update-status', [WBSaleController::class, 'updateStatus'])->name('wb-sales-entry.update-status');
            Route::get('/order-dispatch', [TransactionController::class, 'orderDispatch'])->name('order-dispatch');
            Route::get('/sales-purchase-order', [TransactionController::class, 'salesPurchaseOrder'])->name('sales-purchase-order');
            // Receipt Voucher CRUD (Dedicated Pages)
            Route::get('/receipt-voucher', [ReceiptVoucherController::class, 'index'])->name('receipt-voucher');
            Route::get('/receipt-voucher/create', [ReceiptVoucherController::class, 'create'])->name('receipt-voucher.create');
            Route::get('/receipt-voucher/generate-code', [ReceiptVoucherController::class, 'generateCode'])->name('receipt-voucher.generate-code');
            Route::post('/receipt-voucher', [ReceiptVoucherController::class, 'store'])->name('receipt-voucher.store');
            Route::get('/receipt-voucher/{receiptVoucher}', [ReceiptVoucherController::class, 'show'])->name('receipt-voucher.show');
            Route::get('/receipt-voucher/{receiptVoucher}/edit', [ReceiptVoucherController::class, 'edit'])->name('receipt-voucher.edit');
            Route::put('/receipt-voucher/{receiptVoucher}', [ReceiptVoucherController::class, 'update'])->name('receipt-voucher.update');
            Route::delete('/receipt-voucher/{receiptVoucher}', [ReceiptVoucherController::class, 'destroy'])->name('receipt-voucher.destroy');
            Route::patch('/receipt-voucher/{receiptVoucher}/toggle-status', [ReceiptVoucherController::class, 'toggleStatus'])->name('receipt-voucher.toggle-status');

            // Payment Voucher CRUD (Dedicated Pages)
            Route::get('/payment-voucher', [PaymentVoucherController::class, 'index'])->name('payment-voucher');
            Route::get('/payment-voucher/create', [PaymentVoucherController::class, 'create'])->name('payment-voucher.create');
            Route::get('/payment-voucher/generate-code', [PaymentVoucherController::class, 'generateCode'])->name('payment-voucher.generate-code');
            Route::post('/payment-voucher', [PaymentVoucherController::class, 'store'])->name('payment-voucher.store');
            Route::get('/payment-voucher/{paymentVoucher}', [PaymentVoucherController::class, 'show'])->name('payment-voucher.show');
            Route::get('/payment-voucher/{paymentVoucher}/edit', [PaymentVoucherController::class, 'edit'])->name('payment-voucher.edit');
            Route::put('/payment-voucher/{paymentVoucher}', [PaymentVoucherController::class, 'update'])->name('payment-voucher.update');
            Route::delete('/payment-voucher/{paymentVoucher}', [PaymentVoucherController::class, 'destroy'])->name('payment-voucher.destroy');
            Route::patch('/payment-voucher/{paymentVoucher}/toggle-status', [PaymentVoucherController::class, 'toggleStatus'])->name('payment-voucher.toggle-status');
        });

        // Inventory Routes
        Route::prefix('inventory')->name('inventory.')->group(function () {
            Route::get('/stock-overview', [InventoryController::class, 'stockOverview'])->name('stock-overview');
            Route::get('/item-ledger', [InventoryController::class, 'itemLedger'])->name('item-ledger');
            Route::get('/stock-adjustment', [InventoryController::class, 'stockAdjustment'])->name('stock-adjustment');
            Route::get('/stock-adjustment/create', [InventoryController::class, 'createStockAdjustment'])->name('stock-adjustment.create');
            Route::post('/stock-adjustment', [InventoryController::class, 'storeStockAdjustment'])->name('stock-adjustment.store');
            Route::get('/stock-adjustment/{stockAdjustment}', [InventoryController::class, 'showStockAdjustment'])->name('stock-adjustment.show');
            Route::get('/stock-adjustment/{stockAdjustment}/edit', [InventoryController::class, 'editStockAdjustment'])->name('stock-adjustment.edit');
            Route::put('/stock-adjustment/{stockAdjustment}', [InventoryController::class, 'updateStockAdjustment'])->name('stock-adjustment.update');
            Route::delete('/stock-adjustment/{stockAdjustment}', [InventoryController::class, 'destroyStockAdjustment'])->name('stock-adjustment.destroy');
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
            Route::put('/company', [SettingController::class, 'updateCompany'])->name('company.update');
            Route::get('/whatsapp', [SettingController::class, 'whatsapp'])->name('whatsapp');
            Route::put('/whatsapp', [SettingController::class, 'updateWhatsapp'])->name('whatsapp.update');
            Route::post('/whatsapp/test', [SettingController::class, 'sendTestWhatsapp'])->name('whatsapp.test');
            Route::post('/whatsapp/ping', [SettingController::class, 'pingWhatsapp'])->name('whatsapp.ping');
            Route::post('/whatsapp/clear-logs', [SettingController::class, 'clearWhatsappLogs'])->name('whatsapp.clear-logs');
            Route::match(['get', 'post'], '/whatsapp/webhook', [SettingController::class, 'webhook'])->name('whatsapp.webhook');
            Route::get('/backup-restore', [SettingController::class, 'backupRestore'])->name('backup-restore');
            Route::post('/backup-restore/create', [SettingController::class, 'createBackup'])->name('backup-restore.create');
            Route::get('/backup-restore/download/{filename}', [SettingController::class, 'downloadBackup'])->name('backup-restore.download');
            Route::post('/backup-restore/restore', [SettingController::class, 'restoreBackup'])->name('backup-restore.restore');
            Route::delete('/backup-restore/delete/{filename}', [SettingController::class, 'deleteBackup'])->name('backup-restore.delete');
            Route::get('/run-migration', [SettingController::class, 'runMigration'])->name('run-migration');
        });
        Route::get('/run-migration', [SettingController::class, 'runMigration'])->name('run-migration');
    });
});
