<?php

namespace App\Http\Controllers;

use App\Models\KategoriProduk;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class KategoriProdukController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $kategoris = KategoriProduk::withCount('produks')
            ->when($search, function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%");
            })->latest()->paginate(10);

        return view('kategori.index', compact('kategoris', 'search'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
        ]);

        $validated['slug'] = Str::slug($validated['nama']) . '-' . time();
        KategoriProduk::create($validated);

        return redirect()->route('kategori.index')->with('success', 'Kategori produk berhasil ditambahkan.');
    }

    public function update(Request $request, KategoriProduk $kategori)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
        ]);

        $validated['slug'] = Str::slug($validated['nama']) . '-' . $kategori->id;
        $kategori->update($validated);

        return redirect()->route('kategori.index')->with('success', 'Kategori produk berhasil diperbarui.');
    }

    public function destroy(KategoriProduk $kategori)
    {
        $kategori->delete();
        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
