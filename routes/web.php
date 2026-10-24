<?php

use App\Http\Controllers\AuditController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\B2bController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LocationSwitchController;
use App\Http\Controllers\Master\CategoryController;
use App\Http\Controllers\Master\LocationMasterController;
use App\Http\Controllers\Master\MemberController;
use App\Http\Controllers\Master\ProductController;
use App\Http\Controllers\Master\SupplierController;
use App\Http\Controllers\Master\UomController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\Warehouse\GoodsReceiptController;
use App\Http\Controllers\Warehouse\PurchaseOrderController;
use App\Http\Controllers\Warehouse\StockLedgerController;
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

    // 0. Dashboard Shell (Dasbor Utama & Kesehatan Finansial)
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // 1. Operasional Gudang Pusat & Rantai Pasok (Fase 3 Modular Monolith)
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

        // Kartu Stok & Audit Ledger
        Route::get('/ledger', [StockLedgerController::class, 'index'])->name('ledger.index');
    });

    // 2. Operasional Toko & Kasir POS (Modular Monolith)
    Route::get('/store', [StoreController::class, 'index'])->name('store.index');

    // 3. Kemitraan B2B & Sales (Modular Monolith)
    Route::get('/b2b', [B2bController::class, 'index'])->name('b2b.index');

    // 4. Feed Pergerakan 5-Warna & Anomali (Modular Monolith)
    Route::get('/audit', [AuditController::class, 'index'])->name('audit.index');

    // Location Scope Switcher
    Route::post('/location/switch', [LocationSwitchController::class, 'switch'])->name('location.switch');

    // Master Data Routes (Guarded by inventory.view for viewing, inventory.manage for mutative actions)
    Route::prefix('master')->name('master.')->group(function () {
        // Real-time fuzzy duplicate check API
        Route::get('/products/check-duplicate', [ProductController::class, 'checkDuplicate'])->name('products.check_duplicate');
        
        // Products CRUD
        Route::resource('products', ProductController::class);

        // Categories, UOMs, Suppliers, Members, Locations
        Route::resource('categories', CategoryController::class)->except(['create', 'show', 'edit']);
        Route::resource('uoms', UomController::class)->except(['create', 'show', 'edit']);
        Route::resource('suppliers', SupplierController::class)->except(['create', 'show', 'edit']);
        
        // Member QR Card & Member CRUD
        Route::get('/members/{member}/card', [MemberController::class, 'card'])->name('members.card');
        Route::resource('members', MemberController::class)->except(['create', 'show', 'edit']);
        
        // Locations (Warehouses, Stores, Transit)
        Route::resource('locations', LocationMasterController::class)->except(['create', 'show', 'edit']);
    });
});

