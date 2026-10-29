<?php

use App\Http\Controllers\AuditController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\B2b\B2bInvoiceController;
use App\Http\Controllers\B2b\B2bOrderController;
use App\Http\Controllers\B2b\B2bPickupController;
use App\Http\Controllers\B2bController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LocationSwitchController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Master\CategoryController;
use App\Http\Controllers\Master\LocationMasterController;
use App\Http\Controllers\Master\MemberController;
use App\Http\Controllers\Master\ProductController;
use App\Http\Controllers\Master\RoleController;
use App\Http\Controllers\Master\SupplierController;
use App\Http\Controllers\Master\UomController;
use App\Http\Controllers\Master\UserController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\Master\PricingController;
use App\Http\Controllers\Store\CashShiftController;
use App\Http\Controllers\Store\EmergencyPurchaseController;
use App\Http\Controllers\Store\PosController;
use App\Http\Controllers\Store\StoreTransferController;
use App\Http\Controllers\Warehouse\GoodsReceiptController;
use App\Http\Controllers\Warehouse\PurchaseOrderController;
use App\Http\Controllers\Warehouse\StockLedgerController;
use App\Http\Controllers\Warehouse\StockTransferController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StockOpnameController;
use App\Http\Controllers\WarehouseController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - PasarHub Sistem Operasional Pasar Terpadu
|--------------------------------------------------------------------------
*/

// Guest Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // 0. Dashboard Shell (Dasbor Utama & Kesehatan Finansial)
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // 1. Operasional Gudang Pusat & Rantai Pasok (Fase 3 & 4 Modular Monolith)
    Route::prefix('warehouse')->name('warehouse.')->group(function () {
        Route::get('/', [WarehouseController::class, 'index'])->name('index');

        // Purchase Orders
        Route::get('/po', [PurchaseOrderController::class, 'index'])->name('po.index');
        Route::get('/po/create', [PurchaseOrderController::class, 'create'])->name('po.create');
        Route::post('/po', [PurchaseOrderController::class, 'store'])->name('po.store');
        Route::get('/po/{purchaseOrder}', [PurchaseOrderController::class, 'show'])->name('po.show');

        // Goods Receipts (Inbound Receiving)
        Route::get('/po/{purchaseOrder}/receive', [GoodsReceiptController::class, 'create'])->name('receipts.create');
        Route::post('/po/{purchaseOrder}/receive', [GoodsReceiptController::class, 'store'])->name('receipts.store');
        Route::get('/receipts/{goodsReceipt}', [GoodsReceiptController::class, 'show'])->name('receipts.show');

        // Stock Transfers (Gudang -> Toko)
        Route::get('/transfers', [StockTransferController::class, 'index'])->name('transfers.index');
        Route::get('/transfers/create', [StockTransferController::class, 'create'])->name('transfers.create');
        Route::post('/transfers', [StockTransferController::class, 'store'])->name('transfers.store');
        Route::get('/transfers/{stockTransfer}', [StockTransferController::class, 'show'])->name('transfers.show');

        // Kartu Stok & Audit Ledger
        Route::get('/ledger', [StockLedgerController::class, 'index'])->name('ledger.index');
    });

    // 2. Operasional Toko & Kasir POS (Fase 4 & 5 Modular Monolith)
    Route::prefix('store')->name('store.')->group(function () {
        Route::get('/', [StoreController::class, 'index'])->name('index');

        // Penerimaan Transfer Masuk Toko
        Route::get('/transfers', [StoreTransferController::class, 'index'])->name('transfers.index');
        Route::get('/transfers/{stockTransfer}', [StoreTransferController::class, 'show'])->name('transfers.show');
        Route::post('/transfers/{stockTransfer}/receive', [StoreTransferController::class, 'receive'])->name('transfers.receive');

        // Pembelian Emergency Toko (+Foto Nota)
        Route::get('/emergency', [EmergencyPurchaseController::class, 'index'])->name('emergency.index');
        Route::get('/emergency/create', [EmergencyPurchaseController::class, 'create'])->name('emergency.create');
        Route::post('/emergency', [EmergencyPurchaseController::class, 'store'])->name('emergency.store');
        Route::get('/emergency/{emergencyPurchase}', [EmergencyPurchaseController::class, 'show'])->name('emergency.show');

        // Terminal POS Kasir & Transaksi Ritel (Fase 5)
        Route::get('/pos', [PosController::class, 'terminal'])->name('pos.terminal');
        Route::get('/pos/search', [PosController::class, 'searchProducts'])->name('pos.search');
        Route::post('/pos/checkout', [PosController::class, 'checkout'])->name('pos.checkout');
        Route::get('/pos/receipt/{sale}', [PosController::class, 'receipt'])->name('pos.receipt');
        Route::get('/pos/history', [PosController::class, 'history'])->name('pos.history');
        Route::post('/pos/{sale}/void', [PosController::class, 'void'])->name('pos.void');

        // Shift Laci Kasir (Fase 5)
        Route::get('/shifts', [CashShiftController::class, 'index'])->name('shifts.index');
        Route::post('/shifts/open', [CashShiftController::class, 'open'])->name('shifts.open');
        Route::post('/shifts/{shift}/close', [CashShiftController::class, 'close'])->name('shifts.close');
        Route::post('/shifts/{shift}/validate', [CashShiftController::class, 'validateShift'])->name('shifts.validate');
    });

    // 3. Kemitraan B2B & Sales (Modular Monolith - Fase 6)
    Route::prefix('b2b')->name('b2b.')->group(function () {
        Route::get('/', [B2bController::class, 'index'])->name('index');
        
        // Orders & Credit Control
        Route::get('/orders', [B2bOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/create', [B2bOrderController::class, 'create'])->name('orders.create');
        Route::post('/orders', [B2bOrderController::class, 'store'])->name('orders.store');
        Route::get('/orders/{order}', [B2bOrderController::class, 'show'])->name('orders.show');
        Route::post('/orders/{order}/cancel', [B2bOrderController::class, 'cancel'])->name('orders.cancel');
        Route::get('/credit-check/{member}', [B2bOrderController::class, 'creditCheck'])->name('credit_check');

        // Fulfillment & One-Time QR Pickup
        Route::get('/pickup/scan', [B2bPickupController::class, 'scan'])->name('pickup.scan');
        Route::post('/pickup/verify', [B2bPickupController::class, 'verify'])->name('pickup.verify');
        Route::post('/pickup/redeem', [B2bPickupController::class, 'redeem'])->name('pickup.redeem');

        // Accounts Receivable & Invoices
        Route::get('/invoices', [B2bInvoiceController::class, 'index'])->name('invoices.index');
        Route::get('/invoices/{invoice}', [B2bInvoiceController::class, 'show'])->name('invoices.show');
        Route::post('/invoices/{invoice}/pay', [B2bInvoiceController::class, 'pay'])->name('invoices.pay');
    });

    // 4. Stok Opname Mandiri & Blind Count (Fase 7)
    Route::prefix('opname')->name('opname.')->group(function () {
        Route::get('/', [StockOpnameController::class, 'index'])->name('index');
        Route::get('/create', [StockOpnameController::class, 'create'])->name('create');
        Route::post('/', [StockOpnameController::class, 'store'])->name('store');
        Route::get('/{opname}', [StockOpnameController::class, 'show'])->name('show');
        Route::get('/{opname}/count', [StockOpnameController::class, 'count'])->name('count');
        Route::post('/{opname}/count', [StockOpnameController::class, 'saveCount'])->name('count.save');
        Route::get('/{opname}/reconcile', [StockOpnameController::class, 'reconcile'])->name('reconcile');
        Route::post('/{opname}/reconcile', [StockOpnameController::class, 'saveReconcile'])->name('reconcile.save');
        Route::post('/{opname}/finalize', [StockOpnameController::class, 'finalize'])->name('finalize');
        Route::post('/{opname}/cancel', [StockOpnameController::class, 'cancel'])->name('cancel');
    });

    // 5. Feed Pergerakan 5-Warna & Anomali (Khusus Super Admin)
    Route::get('/audit', [AuditController::class, 'index'])
        ->name('audit.index')
        ->middleware('permission:dashboard.audit');

    // 6. Laporan Finansial & Dashboard Owner (Fase 8)
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/sales', [ReportController::class, 'sales'])->name('sales');
        Route::get('/inventory', [ReportController::class, 'inventory'])->name('inventory');
        Route::get('/receivables', [ReportController::class, 'receivables'])->name('receivables');
        Route::get('/export/financial', [ReportController::class, 'exportFinancial'])->name('export.financial');
        Route::get('/export/sales', [ReportController::class, 'exportSales'])->name('export.sales');
        Route::get('/export/inventory', [ReportController::class, 'exportInventory'])->name('export.inventory');
    });

    // Location Scope Switcher
    Route::post('/location/switch', [LocationSwitchController::class, 'switch'])->name('location.switch');

    // Master Data Routes (Guarded by inventory.view for viewing, inventory.manage for mutative actions)
    Route::prefix('master')->name('master.')->group(function () {
        // Real-time fuzzy duplicate check API
        Route::get('/products/check-duplicate', [ProductController::class, 'checkDuplicate'])->name('products.check_duplicate');
        
        // Products CRUD
        Route::resource('products', ProductController::class);

        // Categories, UOMs, Suppliers, Members, Locations
        Route::resource('categories', CategoryController::class)->except(['show', 'edit']);
        Route::resource('uoms', UomController::class)->except(['show', 'edit']);
        Route::resource('suppliers', SupplierController::class)->except(['show', 'edit']);
        
        // Member QR Card & Member CRUD
        Route::get('/members/{member}/card', [MemberController::class, 'card'])->name('members.card');
        Route::resource('members', MemberController::class)->except(['show', 'edit']);
        
        // Locations (Warehouses, Stores, Transit)
        Route::resource('locations', LocationMasterController::class)->except(['show', 'edit']);

        // Pricing Policies & Floor-Price Lock Matrix (Fase 4)
        Route::get('/pricing', [PricingController::class, 'index'])->name('pricing.index');
        Route::post('/pricing/check', [PricingController::class, 'check'])->name('pricing.check');
        Route::put('/pricing/policy', [PricingController::class, 'updatePolicy'])->name('pricing.policy.update');

        // User & Role Permission Matrix Management (Fase RBAC Centang-Centang)
        Route::middleware('permission:system.users')->group(function () {
            Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle_status');
            Route::resource('users', UserController::class)->except(['create', 'show', 'edit']);
            Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
            Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
        });
    });
});

