@extends('layouts.app')

@section('title', 'Buat Alokasi Stok Baru')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="{
    items: [
        { id_produk: '', jumlah: 1 }
    ],
    addItem() {
        this.items.push({ id_produk: '', jumlah: 1 });
    },
    removeItem(index) {
        if (this.items.length > 1) {
            this.items.splice(index, 1);
        }
    }
}">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-black text-slate-900">Buat Alokasi Stok Baru</h1>
            <p class="text-xs text-slate-500">Kirim dan distribusikan stok barang dari pusat ke cabang tujuan</p>
        </div>
        <a href="{{ route('alokasi.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900">
            &larr; Kembali
        </a>
    </div>

    <form action="{{ route('alokasi.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Info Header Card -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs space-y-4">
            <h3 class="font-bold text-sm text-slate-800 border-b border-slate-100 pb-2">Informasi Alokasi</h3>
            
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">No. Alokasi</label>
                    <input type="text" name="no_alokasi" value="{{ old('no_alokasi', $generatedNo) }}" required readonly
                           class="w-full px-3.5 py-2 text-xs bg-slate-100 border border-slate-300 rounded-lg font-mono font-bold text-slate-700">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Cabang Penerima</label>
                    <select name="id_cabang" required
                            class="w-full px-3.5 py-2 text-xs bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        <option value="">Pilih Cabang</option>
                        @foreach($kantors as $k)
                            <option value="{{ $k->id }}" {{ old('id_cabang') == $k->id ? 'selected' : '' }}>
                                {{ $k->nama }} ({{ $k->kode_cabang }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Tanggal Alokasi</label>
                    <input type="date" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" required
                           class="w-full px-3.5 py-2 text-xs bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Keterangan / Catatan Pengiriman</label>
                <input type="text" name="keterangan" value="{{ old('keterangan') }}" placeholder="e.g. Distribusi stok rutin awal bulan"
                       class="w-full px-3.5 py-2 text-xs bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            </div>
        </div>

        <!-- Line Items Card -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="font-bold text-sm text-slate-800">Daftar Item Produk yang Dialokasikan</h3>
                <button type="button" @click="addItem()"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-xs rounded-lg transition">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span>Tambah Baris Produk</span>
                </button>
            </div>

            <div class="space-y-3">
                <template x-for="(item, index) in items" :key="index">
                    <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl border border-slate-200">
                        <div class="flex-1">
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Produk</label>
                            <select :name="'items[' + index + '][id_produk]'" x-model="item.id_produk" required
                                    class="w-full px-3 py-2 text-xs bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                                <option value="">-- Pilih Produk --</option>
                                @foreach($produks as $p)
                                    <option value="{{ $p->id }}">{{ $p->nama }} [{{ $p->kode_produk }}] (Stok: {{ $p->stok }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="w-32">
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Jumlah (Qty)</label>
                            <input type="number" :name="'items[' + index + '][jumlah]'" x-model="item.jumlah" min="1" required
                                   class="w-full px-3 py-2 text-xs bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none text-center font-bold">
                        </div>

                        <div class="pt-5">
                            <button type="button" @click="removeItem(index)"
                                    class="p-2 text-slate-400 hover:text-rose-600 transition" title="Hapus Baris">
                                <i class="fa-solid fa-trash-can text-sm"></i>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('alokasi.index') }}" class="px-4 py-2.5 text-xs font-semibold text-slate-600 hover:text-slate-800">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-md transition">
                Simpan & Distribusikan Stok
            </button>
        </div>
    </form>
</div>
@endsection
