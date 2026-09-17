@extends('layouts.app')

@section('title', 'Tambah Salesman')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-black text-slate-900">Tambah Salesman Baru</h1>
            <p class="text-xs text-slate-500">Daftarkan staf sales lapangan</p>
        </div>
        <a href="{{ route('salesman.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900">
            &larr; Kembali
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-xs">
        <form action="{{ route('salesman.store') }}" method="POST" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Cabang Penempatan</label>
                    <select name="id_cabang" required
                            class="w-full px-3.5 py-2 text-xs bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        @foreach($kantors as $ktr)
                            <option value="{{ $ktr->id }}" {{ old('id_cabang') == $ktr->id ? 'selected' : '' }}>
                                {{ $ktr->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nama Salesman</label>
                    <input type="text" name="nama" value="{{ old('nama') }}" required placeholder="e.g. Ahmad Fauzi"
                           class="w-full px-3.5 py-2 text-xs bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">No. Handphone / WhatsApp</label>
                <input type="text" name="no_telp" value="{{ old('no_telp') }}" placeholder="e.g. 0812-3456-7890"
                       class="w-full px-3.5 py-2 text-xs bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none font-mono">
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                <a href="{{ route('salesman.index') }}" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-800">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-lg shadow-sm">
                    Simpan Salesman
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
