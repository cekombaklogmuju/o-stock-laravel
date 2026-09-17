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

        $produks = Produk::with(['kategori', 'supplier'])
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('nama', 'like', "%{$search}%")
                        ->orWhere('kode_produk', 'like', "%{$search}%")
                        ->orWhere('barcode', 'like', "%{$search}%");
                });
            })
            ->when($kategoriId, function ($q) use ($kategoriId) {
                $q->where('id_kategori', $kategoriId);
            })
            ->latest()
            ->paginate(15);

        $kategoris = KategoriProduk::all();

        return view('produk.index', compact('produks', 'kategoris', 'search', 'kategoriId'));
    }

    public function create()
    {
        $kategoris = KategoriProduk::all();
        $suppliers = Supplier::all();
        $generatedCode = 'PRD-' . strtoupper(Str::random(6));
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
            'harga_jual' => 'required|numeric|min:0',
            'stok' => 'nullable|integer|min:0',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        if (empty($validated['barcode'])) {
            $validated['barcode'] = '899' . mt_rand(100000000, 999999999);
        }

        $validated['slug'] = Str::slug($validated['nama']) . '-' . Str::lower($validated['kode_produk']);
        $validated['stok'] = $validated['stok'] ?? 0;

        Produk::create($validated);

        return redirect()->route('produk.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Produk $produk)
    {
        $kategoris = KategoriProduk::all();
        $suppliers = Supplier::all();

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
            'harga_jual' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        if (empty($validated['barcode'])) {
            $validated['barcode'] = $produk->barcode ?: '899' . mt_rand(100000000, 999999999);
        }

        $validated['slug'] = Str::slug($validated['nama']) . '-' . Str::lower($validated['kode_produk']);

        $produk->update($validated);

        return redirect()->route('produk.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Produk $produk)
    {
        $produk->delete();
        return redirect()->route('produk.index')->with('success', 'Produk berhasil dihapus.');
    }

    public function printBarcode(Produk $produk)
    {
        return view('produk.barcode-print', compact('produk'));
    }
}
