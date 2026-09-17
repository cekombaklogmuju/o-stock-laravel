<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarangKeluarController;
use App\Http\Controllers\BarangMasukController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KantorController;
use App\Http\Controllers\KartuStokController;
use App\Http\Controllers\KategoriProdukController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\StockOpnameController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;

// Guest Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// Authenticated Warehouse Management Application Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('home');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Warehouse Operations
    Route::resource('barang-masuk', BarangMasukController::class)->only(['index', 'create', 'store', 'show']);
    Route::resource('barang-keluar', BarangKeluarController::class)->only(['index', 'create', 'store', 'show']);
    Route::resource('stock-opname', StockOpnameController::class)->only(['index', 'create', 'store', 'show']);

    // Audit Ledger & Card
    Route::get('/kartu-stok', [KartuStokController::class, 'index'])->name('kartu_stok.index');
    Route::get('/kartu-stok/print', [KartuStokController::class, 'print'])->name('kartu_stok.print');

    // Master Barang & Lokasi Rak
    Route::get('/produk/{produk}/barcode-print', [ProdukController::class, 'printBarcode'])->name('produk.barcode.print');
    Route::resource('produk', ProdukController::class);

    // Kategori & Supplier
    Route::resource('kategori', KategoriProdukController::class)->except(['create', 'show', 'edit']);
    Route::resource('supplier', SupplierController::class)->except(['create', 'show', 'edit']);

    // Central Warehouse / Lokasi Gudang (Admin only)
    Route::middleware('role:admin')->group(function () {
        Route::resource('kantor', KantorController::class);
    });
});
