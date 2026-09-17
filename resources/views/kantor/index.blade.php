@extends('layouts.app')

@section('title', 'Daftar Cabang & Kantor')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-black text-slate-900">Daftar Cabang & Kantor</h1>
            <p class="text-xs text-slate-500">Kelola kantor pusat dan cabang operasional perusahaan</p>
        </div>
        <a href="{{ route('kantor.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-xs transition">
            <i class="fa-solid fa-plus"></i>
            <span>Tambah Cabang Baru</span>
        </a>
    </div>

    <!-- Search & Filter Card -->
    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs flex items-center justify-between">
        <form action="{{ route('kantor.index') }}" method="GET" class="flex-1 max-w-md">
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text" name="search" value="{{ $search }}"
                       placeholder="Cari nama atau kode cabang..."
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
                        <th class="px-5 py-3.5">Kode Cabang</th>
                        <th class="px-5 py-3.5">Tipe</th>
                        <th class="px-5 py-3.5">Nama Kantor</th>
                        <th class="px-5 py-3.5">Alamat</th>
                        <th class="px-5 py-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($kantors as $k)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-3.5 font-bold font-mono text-slate-800">{{ $k->kode_cabang }}</td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold {{ $k->tipe === 'Pusat' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ $k->tipe }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 font-bold text-slate-900">{{ $k->nama }}</td>
                            <td class="px-5 py-3.5 text-slate-600 max-w-xs truncate">{{ $k->alamat ?? '-' }}</td>
                            <td class="px-5 py-3.5 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('kantor.edit', $k->id) }}"
                                       class="p-1.5 text-slate-500 hover:text-indigo-600 transition" title="Edit">
                                        <i class="fa-regular fa-pen-to-square"></i>
                                    </a>
                                    @if($k->tipe !== 'Pusat')
                                    <form action="{{ route('kantor.destroy', $k->id) }}" method="POST"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus cabang ini?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-500 hover:text-rose-600 transition" title="Hapus">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-8 text-center text-slate-400">
                                Tidak ada data cabang yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($kantors->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $kantors->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
