@extends('layouts.app')

@section('title', 'Edit Data Barang')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Edit Data Barang</h1>
            <p class="text-sm text-slate-500">Perbarui spesifikasi, lokasi rak, stok minimum, dan barcode</p>
        </div>
        <a href="{{ route('produk.index') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900">
            &larr; Kembali ke Inventori
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-xs">
        <form action="{{ route('produk.update', $produk->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Identitas Barang -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Kode Barang / SKU</label>
                    <input type="text" name="kode_produk" value="{{ old('kode_produk', $produk->kode_produk) }}" required
                           class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none font-mono font-semibold">
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Barcode Produk</label>
                        <button type="button" onclick="document.getElementById('barcode').value = '899' + Math.floor(100000000 + Math.random() * 900000000);"
                                class="text-xs text-indigo-600 hover:underline font-semibold">
                            Generate Barcode Baru
                        </button>
                    </div>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
                            <i class="fa-solid fa-barcode text-sm"></i>
                        </span>
                        <input type="text" name="barcode" id="barcode" value="{{ old('barcode', $produk->barcode) }}"
                               class="w-full pl-10 pr-3.5 py-2.5 text-sm bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none font-mono font-semibold">
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nama Barang</label>
                <input type="text" name="nama" value="{{ old('nama', $produk->nama) }}" required
                       class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            </div>

            <!-- Lokasi Rak & Satuan -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 bg-indigo-50/50 rounded-xl border border-indigo-100">
                <div>
                    <label class="block text-xs font-bold text-indigo-900 uppercase tracking-wider mb-1.5">
                        <i class="fa-solid fa-table-cells mr-1"></i> Lokasi Rak / Bin Gudang
                    </label>
                    <input type="text" name="lokasi_rak" value="{{ old('lokasi_rak', $produk->lokasi_rak ?? 'Rak A-01-A') }}" required
                           class="w-full px-3.5 py-2.5 text-sm bg-white border border-indigo-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none font-semibold text-slate-800">
                    <p class="text-[11px] text-indigo-700/80 mt-1">Titik penempatan fisik barang di dalam gudang</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-indigo-900 uppercase tracking-wider mb-1.5">
                        <i class="fa-solid fa-scale-balanced mr-1"></i> Satuan Ukuran / Kemasan
                    </label>
                    <input type="text" name="satuan" value="{{ old('satuan', $produk->satuan ?? 'Pcs') }}" required
                           class="w-full px-3.5 py-2.5 text-sm bg-white border border-indigo-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none font-semibold text-slate-800">
                    <p class="text-[11px] text-indigo-700/80 mt-1">Satuan hitung fisik saat masuk/keluar</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Kategori Barang</label>
                    <select name="id_kategori" required
                            class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        @foreach($kategoris as $k)
                            <option value="{{ $k->id }}" {{ old('id_kategori', $produk->id_kategori) == $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Supplier Utama</label>
                    <select name="id_supplier" required
                            class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        @foreach($suppliers as $s)
                            <option value="{{ $s->id }}" {{ old('id_supplier', $produk->id_supplier) == $s->id ? 'selected' : '' }}>{{ $s->nama }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Estimasi Harga (Rp)</label>
                    <input type="number" name="harga_jual" value="{{ old('harga_jual', (int)$produk->harga_jual) }}" min="0" required
                           class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none font-medium">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Stok Saat Ini</label>
                    <input type="number" name="stok" value="{{ old('stok', $produk->stok) }}" min="0" required
                           class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none font-bold">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Batas Stok Min</label>
                    <input type="number" name="stok_minimum" value="{{ old('stok_minimum', $produk->stok_minimum ?? 5) }}" min="0"
                           class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none font-bold text-rose-600">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Status Barang</label>
                    <select name="status" required
                            class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        <option value="aktif" {{ $produk->status === 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ $produk->status === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Spesifikasi / Keterangan Teknis</label>
                <textarea name="spesifikasi" rows="2"
                          class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">{{ old('spesifikasi', $produk->spesifikasi) }}</textarea>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                <a href="{{ route('produk.index') }}" class="px-4 py-2 text-sm font-semibold text-slate-600 hover:text-slate-800">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl shadow-sm">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
