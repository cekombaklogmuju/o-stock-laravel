<?php

use App\Http\Controllers\AlokasiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BranchRequestController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KantorController;
use App\Http\Controllers\KartuStokController;
use App\Http\Controllers\KategoriProdukController;
use App\Http\Controllers\KonsumenController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\SalesmanController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;

// Guest Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// Authenticated Application Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/switch-branch', [AuthController::class, 'switchBranch'])->name('switch.branch');

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('home');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Master Data (Kantor restricted to Admin)
    Route::middleware('role:admin')->group(function () {
        Route::resource('kantor', KantorController::class);
    });

    Route::resource('kategori', KategoriProdukController::class)->except(['create', 'show', 'edit']);
    Route::resource('supplier', SupplierController::class)->except(['create', 'show', 'edit']);

    Route::get('/produk/{produk}/barcode-print', [ProdukController::class, 'printBarcode'])->name('produk.barcode.print');
    Route::resource('produk', ProdukController::class);

    Route::resource('konsumen', KonsumenController::class);
    Route::resource('salesman', SalesmanController::class);

    // Inventori Workflows
    Route::resource('alokasi', AlokasiController::class)->only(['index', 'create', 'store', 'show']);

    Route::resource('branch_request', BranchRequestController::class)->only(['index', 'create', 'store', 'show']);
    Route::post('/branch_request/{branchRequest}/process', [BranchRequestController::class, 'process'])->name('branch_request.process');

    Route::get('/kartu-stok', [KartuStokController::class, 'index'])->name('kartu_stok.index');
    Route::get('/kartu-stok/print', [KartuStokController::class, 'print'])->name('kartu_stok.print');

    // Penjualan & Kasir POS
    Route::resource('penjualan', PenjualanController::class)->only(['index', 'create', 'store', 'show']);
});
