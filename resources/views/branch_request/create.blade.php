@extends('layouts.app')

@section('title', 'Ajukan Permintaan Stok')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="{
    items: [
        { id_produk: '', jumlah_diminta: 1 }
    ],
    addItem() {
        this.items.push({ id_produk: '', jumlah_diminta: 1 });
    },
    removeItem(index) {
        if (this.items.length > 1) {
            this.items.splice(index, 1);
        }
    }
}">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-black text-slate-900">Ajukan Permintaan Stok Cabang</h1>
            <p class="text-xs text-slate-500">Kirimkan permintaan penambahan barang atau mutasi ke kantor pusat</p>
        </div>
        <a href="{{ route('branch_request.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900">
            &larr; Kembali
        </a>
    </div>

    <form action="{{ route('branch_request.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs space-y-4">
            <h3 class="font-bold text-sm text-slate-800 border-b border-slate-100 pb-2">Rincian Permintaan</h3>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">No. Permintaan</label>
                    <input type="text" name="no_permintaan" value="{{ old('no_permintaan', $generatedNo) }}" required readonly
                           class="w-full px-3.5 py-2 text-xs bg-slate-100 border border-slate-300 rounded-lg font-mono font-bold text-slate-700">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Cabang Peminta</label>
                    <select name="id_cabang_peminta" required
                            class="w-full px-3.5 py-2 text-xs bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        @foreach($kantors as $k)
                            <option value="{{ $k->id }}" {{ (old('id_cabang_peminta') == $k->id || auth()->user()->id_cabang == $k->id) ? 'selected' : '' }}>
                                {{ $k->nama }} ({{ $k->kode_cabang }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Tingkat Prioritas</label>
                    <select name="prioritas" required
                            class="w-full px-3.5 py-2 text-xs bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        <option value="normal" selected>Normal</option>
                        <option value="urgent">Mendesak (Urgent)</option>
                        <option value="critical">Kritis (Critical)</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Alasan / Catatan Kebutuhan</label>
                <textarea name="keterangan" rows="2" placeholder="Jelaskan kebutuhan pengadaan atau stok menipis..."
                          class="w-full px-3.5 py-2 text-xs bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">{{ old('keterangan') }}</textarea>
            </div>
        </div>

        <!-- Items Table -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="font-bold text-sm text-slate-800">Daftar Produk yang Diminta</h3>
                <button type="button" @click="addItem()"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-xs rounded-lg transition">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span>Tambah Baris</span>
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
                                    <option value="{{ $p->id }}">{{ $p->nama }} [{{ $p->kode_produk }}]</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="w-32">
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Qty Diminta</label>
                            <input type="number" :name="'items[' + index + '][jumlah_diminta]'" x-model="item.jumlah_diminta" min="1" required
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
            <a href="{{ route('branch_request.index') }}" class="px-4 py-2.5 text-xs font-semibold text-slate-600 hover:text-slate-800">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-md transition">
                Kirim Permintaan Stok
            </button>
        </div>
    </form>
</div>
@endsection
