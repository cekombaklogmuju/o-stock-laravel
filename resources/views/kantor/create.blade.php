@extends('layouts.app')

@section('title', 'Tambah Cabang Baru')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-black text-slate-900">Tambah Cabang Baru</h1>
            <p class="text-xs text-slate-500">Daftarkan kantor cabang baru ke dalam sistem</p>
        </div>
        <a href="{{ route('kantor.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900">
            &larr; Kembali
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-xs">
        <form action="{{ route('kantor.store') }}" method="POST" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Kode Cabang</label>
                    <input type="text" name="kode_cabang" value="{{ old('kode_cabang') }}" required placeholder="e.g. CBG-BDG"
                           class="w-full px-3.5 py-2 text-xs bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Tipe Kantor</label>
                    <select name="tipe" required
                            class="w-full px-3.5 py-2 text-xs bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        <option value="Cabang" selected>Cabang</option>
                        <option value="Pusat">Kantor Pusat</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nama Kantor / Cabang</label>
                <input type="text" name="nama" value="{{ old('nama') }}" required placeholder="e.g. Cabang Bandung Kota"
                       class="w-full px-3.5 py-2 text-xs bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Alamat Lengkap</label>
                <textarea name="alamat" rows="3" placeholder="Alamat jalan, nomor, kota..."
                          class="w-full px-3.5 py-2 text-xs bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">{{ old('alamat') }}</textarea>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                <a href="{{ route('kantor.index') }}" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-800">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-lg shadow-sm">
                    Simpan Cabang
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
