<?php

namespace App\Http\Controllers;

use App\Models\BarangMasuk;
use App\Models\Produk;
use App\Models\Supplier;
use App\Services\InventoryService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BarangMasukController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $supplierId = $request->input('supplier_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = BarangMasuk::with(['supplier', 'creator', 'items.produk'])
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('no_masuk', 'like', "%{$search}%")
                        ->orWhere('no_surat_jalan', 'like', "%{$search}%")
                        ->orWhere('keterangan', 'like', "%{$search}%");
                });
            })
            ->when($supplierId, function ($q) use ($supplierId) {
                $q->where('id_supplier', $supplierId);
            })
            ->when($startDate, function ($q) use ($startDate) {
                $q->whereDate('tanggal', '>=', $startDate);
            })
            ->when($endDate, function ($q) use ($endDate) {
                $q->whereDate('tanggal', '<=', $endDate);
            })
            ->latest('tanggal')
            ->latest('id');

        $barangMasuks = $query->paginate(15)->withQueryString();
        $suppliers = Supplier::orderBy('nama')->get();

        return view('barang_masuk.index', compact('barangMasuks', 'suppliers', 'search', 'supplierId', 'startDate', 'endDate'));
    }

    public function create()
    {
        $todayPrefix = 'BM-' . date('Ymd') . '-';
        $todayCount = BarangMasuk::whereDate('created_at', today())->count() + 1;
        $generatedNo = $todayPrefix . str_pad($todayCount, 4, '0', STR_PAD_LEFT);

        $suppliers = Supplier::orderBy('nama')->get();
        $produks = Produk::where('status', 'aktif')->orderBy('nama')->get();

        return view('barang_masuk.create', compact('generatedNo', 'suppliers', 'produks'));
    }

    public function store(Request $request, InventoryService $inventoryService)
    {
        $request->validate([
            'no_masuk' => 'required|string|max:100|unique:barang_masuks,no_masuk',
            'id_supplier' => 'nullable|exists:suppliers,id',
            'no_surat_jalan' => 'nullable|string|max:100',
            'keterangan' => 'nullable|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.id_produk' => 'required|exists:produks,id',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.catatan' => 'nullable|string|max:255',
        ], [
            'items.required' => 'Minimal harus ada 1 barang yang dimasukkan ke daftar penerimaan.',
            'items.*.jumlah.min' => 'Jumlah barang masuk minimal 1.',
        ]);

        try {
            $barangMasuk = $inventoryService->processStockIn(
                $request->input('items'),
                $request->input('no_masuk'),
                $request->input('id_supplier') ? (int) $request->input('id_supplier') : null,
                $request->input('no_surat_jalan'),
                $request->input('keterangan'),
                Auth::id()
            );

            return redirect()->route('barang-masuk.show', $barangMasuk->id)
                ->with('success', "Barang Masuk [{$barangMasuk->no_masuk}] berhasil disimpan dan stok gudang bertambah.");
        } catch (Exception $e) {
            return back()->withInput()->withErrors(['error' => 'Gagal memproses barang masuk: ' . $e->getMessage()]);
        }
    }

    public function show(BarangMasuk $barangMasuk)
    {
        $barangMasuk->load(['supplier', 'creator', 'items.produk.kategori']);
        return view('barang_masuk.show', compact('barangMasuk'));
    }
}
