<?php

namespace App\Http\Controllers;

use App\Models\BarangKeluar;
use App\Models\Produk;
use App\Services\InventoryService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BarangKeluarController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $tujuan = $request->input('tujuan');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = BarangKeluar::with(['creator', 'items.produk'])
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('no_keluar', 'like', "%{$search}%")
                        ->orWhere('penerima', 'like', "%{$search}%")
                        ->orWhere('keterangan', 'like', "%{$search}%");
                });
            })
            ->when($tujuan, function ($q) use ($tujuan) {
                $q->where('tujuan', $tujuan);
            })
            ->when($startDate, function ($q) use ($startDate) {
                $q->whereDate('tanggal', '>=', $startDate);
            })
            ->when($endDate, function ($q) use ($endDate) {
                $q->whereDate('tanggal', '<=', $endDate);
            })
            ->latest('tanggal')
            ->latest('id');

        $barangKeluars = $query->paginate(15)->withQueryString();

        return view('barang_keluar.index', compact('barangKeluars', 'search', 'tujuan', 'startDate', 'endDate'));
    }

    public function create()
    {
        $todayPrefix = 'BK-' . date('Ymd') . '-';
        $todayCount = BarangKeluar::whereDate('created_at', today())->count() + 1;
        $generatedNo = $todayPrefix . str_pad($todayCount, 4, '0', STR_PAD_LEFT);

        $produks = Produk::where('status', 'aktif')->orderBy('nama')->get();

        return view('barang_keluar.create', compact('generatedNo', 'produks'));
    }

    public function store(Request $request, InventoryService $inventoryService)
    {
        $request->validate([
            'no_keluar' => 'required|string|max:100|unique:barang_keluars,no_keluar',
            'tujuan' => 'required|string|in:pemakaian_internal,distribusi,penjualan_grosir,scrap_rusak,lainnya',
            'penerima' => 'nullable|string|max:100',
            'keterangan' => 'nullable|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.id_produk' => 'required|exists:produks,id',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.catatan' => 'nullable|string|max:255',
        ], [
            'items.required' => 'Minimal harus ada 1 barang yang dikeluarkan.',
            'items.*.jumlah.min' => 'Jumlah barang keluar minimal 1.',
        ]);

        try {
            $barangKeluar = $inventoryService->processStockOut(
                $request->input('items'),
                $request->input('no_keluar'),
                $request->input('tujuan'),
                $request->input('penerima'),
                $request->input('keterangan'),
                Auth::id()
            );

            return redirect()->route('barang-keluar.show', $barangKeluar->id)
                ->with('success', "Barang Keluar [{$barangKeluar->no_keluar}] berhasil diproses dan stok gudang dikurangi.");
        } catch (Exception $e) {
            return back()->withInput()->withErrors(['error' => 'Gagal memproses barang keluar: ' . $e->getMessage()]);
        }
    }

    public function show(BarangKeluar $barangKeluar)
    {
        $barangKeluar->load(['creator', 'items.produk.kategori']);
        return view('barang_keluar.show', compact('barangKeluar'));
    }
}
