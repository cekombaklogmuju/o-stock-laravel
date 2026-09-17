<?php

use App\Http\Controllers\API\BarcodeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return response()->json(['status' => 'ok', 'timestamp' => now()]);
});

Route::prefix('v1')->group(function () {
    Route::get('/produk/lookup', [BarcodeController::class, 'lookup'])->name('api.produk.lookup');
    Route::get('/produk/search', [BarcodeController::class, 'search'])->name('api.produk.search');
});
