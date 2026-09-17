<?php

namespace App\Http\Controllers;

use App\Models\BarangKeluar;
use App\Models\BarangKeluarItem;
use App\Models\BarangMasuk;
use App\Models\BarangMasukItem;
use App\Models\KartuStok;
use App\Models\Produk;
use App\Models\StockOpname;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // 1. Warehouse Overview Metrics
        $totalSku = Produk::count();
        $totalFisikStok = Produk::sum('stok');

        $lowStockQuery = Produk::with(['kategori'])
            ->whereColumn('stok', '<=', 'stok_minimum')
            ->where('status', 'aktif')
            ->orderBy('stok', 'asc');
        
        $lowStockCount = $lowStockQuery->count();
        $lowStockItems = $lowStockQuery->limit(6)->get();

        // 2. Today's Warehouse Activities
        $today = today()->toDateString();

        $barangMasukTodayCount = BarangMasuk::whereDate('tanggal', $today)->count();
        $barangMasukQtyToday = BarangMasukItem::whereHas('barangMasuk', function ($q) use ($today) {
            $q->whereDate('tanggal', $today);
        })->sum('jumlah');

        $barangKeluarTodayCount = BarangKeluar::whereDate('tanggal', $today)->count();
        $barangKeluarQtyToday = BarangKeluarItem::whereHas('barangKeluar', function ($q) use ($today) {
            $q->whereDate('tanggal', $today);
        })->sum('jumlah');

        $totalOpnameCount = StockOpname::count();
        $lastOpname = StockOpname::with('creator')->latest('tanggal')->latest('id')->first();

        // 3. Recent Transactions & Ledger Audits
        $recentMasuk = BarangMasuk::with(['supplier', 'items.produk'])->latest('tanggal')->latest('id')->limit(5)->get();
        $recentKeluar = BarangKeluar::with(['items.produk'])->latest('tanggal')->latest('id')->limit(5)->get();
        $recentMovements = KartuStok::with(['produk'])->latest('tanggal')->latest('id')->limit(8)->get();

        return view('dashboard.index', compact(
            'totalSku',
            'totalFisikStok',
            'lowStockCount',
            'lowStockItems',
            'barangMasukTodayCount',
            'barangMasukQtyToday',
            'barangKeluarTodayCount',
            'barangKeluarQtyToday',
            'totalOpnameCount',
            'lastOpname',
            'recentMasuk',
            'recentKeluar',
            'recentMovements'
        ));
    }
}
