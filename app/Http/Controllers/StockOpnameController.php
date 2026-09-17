<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\StockOpname;
use App\Services\InventoryService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockOpnameController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = StockOpname::with(['creator', 'items.produk'])
            ->when($search, function ($q) use ($search) {
                $q->where('no_opname', 'like', "%{$search}%")
                    ->orWhere('keterangan', 'like', "%{$search}%");
            })
            ->when($startDate, function ($q) use ($startDate) {
                $q->whereDate('tanggal', '>=', $startDate);
            })
            ->when($endDate, function ($q) use ($endDate) {
                $q->whereDate('tanggal', '<=', $endDate);
            })
            ->latest('tanggal')
            ->latest('id');

        $stockOpnames = $query->paginate(15)->withQueryString();

        return view('stock_opname.index', compact('stockOpnames', 'search', 'startDate', 'endDate'));
    }

    public function create()
    {
        $todayPrefix = 'SO-' . date('Ymd') . '-';
        $todayCount = StockOpname::whereDate('created_at', today())->count() + 1;
        $generatedNo = $todayPrefix . str_pad($todayCount, 4, '0', STR_PAD_LEFT);

        $produks = Produk::where('status', 'aktif')->orderBy('lokasi_rak')->orderBy('nama')->get();

        return view('stock_opname.create', compact('generatedNo', 'produks'));
    }

    public function store(Request $request, InventoryService $inventoryService)
    {
        $request->validate([
            'no_opname' => 'required|string|max:100|unique:stock_opnames,no_opname',
            'keterangan' => 'nullable|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.id_produk' => 'required|exists:produks,id',
            'items.*.stok_fisik' => 'required|integer|min:0',
            'items.*.alasan' => 'nullable|string|max:255',
        ], [
            'items.required' => 'Minimal harus ada 1 barang dalam audit stock opname.',
            'items.*.stok_fisik.min' => 'Hasil hitung stok fisik tidak boleh negatif.',
        ]);

        try {
            $stockOpname = $inventoryService->processStockOpname(
                $request->input('items'),
                $request->input('no_opname'),
                $request->input('keterangan'),
                Auth::id()
            );

            return redirect()->route('stock-opname.show', $stockOpname->id)
                ->with('success', "Stock Opname [{$stockOpname->no_opname}] berhasil dicatat dan penyesuaian stok telah direkonsiliasi.");
        } catch (Exception $e) {
            return back()->withInput()->withErrors(['error' => 'Gagal memproses stock opname: ' . $e->getMessage()]);
        }
    }

    public function show(StockOpname $stockOpname)
    {
        $stockOpname->load(['creator', 'items.produk.kategori']);
        return view('stock_opname.show', compact('stockOpname'));
    }
}
