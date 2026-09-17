<?php

namespace App\Http\Controllers;

use App\Models\KategoriProduk;
use App\Models\Produk;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProdukController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $kategoriId = $request->input('kategori_id');
        $lowStock = $request->input('low_stock');
        $lokasiRak = $request->input('lokasi_rak');

        $produks = Produk::with(['kategori', 'supplier'])
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('nama', 'like', "%{$search}%")
                        ->orWhere('kode_produk', 'like', "%{$search}%")
                        ->orWhere('barcode', 'like', "%{$search}%")
                        ->orWhere('lokasi_rak', 'like', "%{$search}%");
                });
            })
            ->when($kategoriId, function ($q) use ($kategoriId) {
                $q->where('id_kategori', $kategoriId);
            })
            ->when($lokasiRak, function ($q) use ($lokasiRak) {
                $q->where('lokasi_rak', 'like', "%{$lokasiRak}%");
            })
            ->when($lowStock, function ($q) {
                $q->whereColumn('stok', '<=', 'stok_minimum');
            })
            ->orderBy('lokasi_rak', 'asc')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $kategoris = KategoriProduk::orderBy('nama')->get();

        return view('produk.index', compact('produks', 'kategoris', 'search', 'kategoriId', 'lowStock', 'lokasiRak'));
    }

    public function create()
    {
        $kategoris = KategoriProduk::orderBy('nama')->get();
        $suppliers = Supplier::orderBy('nama')->get();
        $generatedCode = 'BRG-' . strtoupper(Str::random(5));
        $generatedBarcode = '899' . mt_rand(100000000, 999999999);

        return view('produk.create', compact('kategoris', 'suppliers', 'generatedCode', 'generatedBarcode'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_produk' => 'required|string|max:100|unique:produks,kode_produk',
            'barcode' => 'nullable|string|max:100',
            'nama' => 'required|string|max:255',
            'id_kategori' => 'required|exists:kategori_produks,id',
            'id_supplier' => 'required|exists:suppliers,id',
            'lokasi_rak' => 'nullable|string|max:100',
            'satuan' => 'nullable|string|max:50',
            'harga_jual' => 'required|numeric|min:0',
            'stok' => 'nullable|integer|min:0',
            'stok_minimum' => 'nullable|integer|min:0',
            'spesifikasi' => 'nullable|string',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        if (empty($validated['barcode'])) {
            $validated['barcode'] = '899' . mt_rand(100000000, 999999999);
        }

        $validated['slug'] = Str::slug($validated['nama']) . '-' . Str::lower($validated['kode_produk']);
        $validated['stok'] = $validated['stok'] ?? 0;
        $validated['stok_minimum'] = $validated['stok_minimum'] ?? 5;
        $validated['satuan'] = $validated['satuan'] ?? 'Pcs';
        $validated['lokasi_rak'] = $validated['lokasi_rak'] ?? 'Gudang Utama';

        $produk = Produk::create($validated);

        return redirect()->route('produk.index')->with('success', "Barang {$produk->nama} berhasil didaftarkan di {$produk->lokasi_rak}.");
    }

    public function edit(Produk $produk)
    {
        $kategoris = KategoriProduk::orderBy('nama')->get();
        $suppliers = Supplier::orderBy('nama')->get();

        return view('produk.edit', compact('produk', 'kategoris', 'suppliers'));
    }

    public function update(Request $request, Produk $produk)
    {
        $validated = $request->validate([
            'kode_produk' => 'required|string|max:100|unique:produks,kode_produk,' . $produk->id,
            'barcode' => 'nullable|string|max:100',
            'nama' => 'required|string|max:255',
            'id_kategori' => 'required|exists:kategori_produks,id',
            'id_supplier' => 'required|exists:suppliers,id',
            'lokasi_rak' => 'nullable|string|max:100',
            'satuan' => 'nullable|string|max:50',
            'harga_jual' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'stok_minimum' => 'nullable|integer|min:0',
            'spesifikasi' => 'nullable|string',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        if (empty($validated['barcode'])) {
            $validated['barcode'] = $produk->barcode ?: '899' . mt_rand(100000000, 999999999);
        }

        $validated['slug'] = Str::slug($validated['nama']) . '-' . Str::lower($validated['kode_produk']);
        $validated['stok_minimum'] = $validated['stok_minimum'] ?? 5;
        $validated['satuan'] = $validated['satuan'] ?? 'Pcs';
        $validated['lokasi_rak'] = $validated['lokasi_rak'] ?? 'Gudang Utama';

        $produk->update($validated);

        return redirect()->route('produk.index')->with('success', "Data barang {$produk->nama} berhasil diperbarui.");
    }

    public function destroy(Produk $produk)
    {
        $produk->delete();
        return redirect()->route('produk.index')->with('success', 'Data barang berhasil dihapus dari inventori.');
    }

    public function printBarcode(Produk $produk)
    {
        return view('produk.barcode-print', compact('produk'));
    }
}
