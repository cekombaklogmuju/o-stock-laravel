@extends('layouts.app')

@section('title', 'Daftar Konsumen')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-black text-slate-900">Daftar Konsumen / Pelanggan</h1>
            <p class="text-xs text-slate-500">Kelola data pelanggan dan konsumen toko cabang</p>
        </div>
        <a href="{{ route('konsumen.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-xs transition">
            <i class="fa-solid fa-plus"></i>
            <span>Tambah Konsumen</span>
        </a>
    </div>

    <!-- Search Card -->
    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs flex items-center justify-between">
        <form action="{{ route('konsumen.index') }}" method="GET" class="flex-1 max-w-md">
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text" name="search" value="{{ $search }}"
                       placeholder="Cari nama, kota, atau telepon konsumen..."
                       class="w-full pl-9 pr-3 py-2 text-xs bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 font-semibold uppercase tracking-wider">
                    <tr>
                        <th class="px-5 py-3.5">Nama Konsumen</th>
                        <th class="px-5 py-3.5">Cabang</th>
                        <th class="px-5 py-3.5">Kota</th>
                        <th class="px-5 py-3.5">No. Telepon</th>
                        <th class="px-5 py-3.5">Alamat</th>
                        <th class="px-5 py-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($konsumens as $k)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-3.5 font-bold text-slate-900">{{ $k->nama }}</td>
                            <td class="px-5 py-3.5 text-slate-600 font-medium">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-slate-100 text-slate-700">
                                    {{ $k->cabang->nama ?? '-' }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-slate-600">{{ $k->kota ?? '-' }}</td>
                            <td class="px-5 py-3.5 text-slate-600 font-mono">{{ $k->no_telp ?? '-' }}</td>
                            <td class="px-5 py-3.5 text-slate-500 max-w-xs truncate">{{ $k->alamat ?? '-' }}</td>
                            <td class="px-5 py-3.5 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('konsumen.edit', $k->id) }}"
                                       class="p-1.5 text-slate-500 hover:text-indigo-600 transition" title="Edit">
                                        <i class="fa-regular fa-pen-to-square"></i>
                                    </a>
                                    <form action="{{ route('konsumen.destroy', $k->id) }}" method="POST"
                                          onsubmit="return confirm('Hapus konsumen ini?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-500 hover:text-rose-600 transition" title="Hapus">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-slate-400">
                                Tidak ada data konsumen.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($konsumens->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $konsumens->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
