@extends('layouts.app')

@section('title', 'Alokasi Stok')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-black text-slate-900">Alokasi Stok Barang</h1>
            <p class="text-xs text-slate-500">Pencatatan distribusi dan penerimaan stok produk ke cabang</p>
        </div>
        <a href="{{ route('alokasi.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-xs transition">
            <i class="fa-solid fa-truck-ramp-box"></i>
            <span>Buat Alokasi Baru</span>
        </a>
    </div>

    <!-- Search Card -->
    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs flex items-center justify-between">
        <form action="{{ route('alokasi.index') }}" method="GET" class="flex-1 max-w-md">
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text" name="search" value="{{ $search }}"
                       placeholder="Cari nomor alokasi..."
                       class="w-full pl-9 pr-3 py-2 text-xs bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none font-mono">
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 font-semibold uppercase tracking-wider">
                    <tr>
                        <th class="px-5 py-3.5">No. Alokasi</th>
                        <th class="px-5 py-3.5">Tanggal</th>
                        <th class="px-5 py-3.5">Cabang Penerima</th>
                        <th class="px-5 py-3.5">Jumlah Item</th>
                        <th class="px-5 py-3.5">Keterangan</th>
                        <th class="px-5 py-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($alokasis as $a)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-3.5 font-bold font-mono text-slate-900">{{ $a->no_alokasi }}</td>
                            <td class="px-5 py-3.5 text-slate-600">{{ date('d M Y', strtotime($a->tanggal)) }}</td>
                            <td class="px-5 py-3.5 font-semibold text-slate-800">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-indigo-50 text-indigo-700">
                                    {{ $a->cabang->nama ?? '-' }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-slate-600 font-semibold">
                                {{ $a->items->sum('jumlah') }} unit ({{ $a->items->count() }} jenis)
                            </td>
                            <td class="px-5 py-3.5 text-slate-500 max-w-xs truncate">{{ $a->keterangan ?? '-' }}</td>
                            <td class="px-5 py-3.5 text-center">
                                <a href="{{ route('alokasi.show', $a->id) }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-1 bg-slate-100 hover:bg-indigo-50 hover:text-indigo-600 text-slate-700 font-semibold rounded-lg text-xs transition">
                                    <i class="fa-solid fa-eye"></i>
                                    <span>Detail</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-slate-400">
                                Belum ada riwayat alokasi stok.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($alokasis->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $alokasis->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
