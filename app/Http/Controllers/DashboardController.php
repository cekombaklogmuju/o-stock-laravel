<?php

namespace App\Http\Controllers;

use App\Models\BranchRequest;
use App\Models\Kantor;
use App\Models\Penjualan;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $activeBranchId = session('active_branch_id');

        $totalProduk = Produk::count();
        $lowStockQuery = Produk::where('stok', '<=', 10)->where('status', 'aktif');
        $lowStockCount = $lowStockQuery->count();
        $lowStockItems = $lowStockQuery->limit(5)->get();

        $totalCabang = Kantor::count();

        // Penjualan query scoped by branch if active
        $penjualanQuery = Penjualan::with(['cabang', 'konsumen', 'salesman'])->latest('tanggal');
        if ($activeBranchId) {
            $penjualanQuery->where('id_cabang', $activeBranchId);
        }

        $todaySalesQuery = Penjualan::whereDate('tanggal', today());
        if ($activeBranchId) {
            $todaySalesQuery->where('id_cabang', $activeBranchId);
        }
        $todaySalesTotal = $todaySalesQuery->sum('total_harga');
        $todaySalesCount = $todaySalesQuery->count();

        // Branch Requests
        $requestQuery = BranchRequest::with(['cabangPeminta', 'userPeminta'])->latest();
        if ($user->role === 'cabang' && $user->id_cabang) {
            $requestQuery->where('id_cabang_peminta', $user->id_cabang);
        }
        $pendingRequestsCount = (clone $requestQuery)->where('status', 'pending')->count();
        $recentRequests = $requestQuery->limit(5)->get();

        $recentSales = $penjualanQuery->limit(5)->get();

        return view('dashboard.index', compact(
            'totalProduk',
            'lowStockCount',
            'lowStockItems',
            'totalCabang',
            'todaySalesTotal',
            'todaySalesCount',
            'pendingRequestsCount',
            'recentRequests',
            'recentSales'
        ));
    }
}
