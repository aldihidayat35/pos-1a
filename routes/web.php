<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LocationSwitchController;
use App\Http\Controllers\Master\CategoryController;
use App\Http\Controllers\Master\LocationMasterController;
use App\Http\Controllers\Master\MemberController;
use App\Http\Controllers\Master\ProductController;
use App\Http\Controllers\Master\SupplierController;
use App\Http\Controllers\Master\UomController;
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

    // Dashboard Shell (Accessible to all authenticated roles, content filtered by permissions)
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index']);

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

