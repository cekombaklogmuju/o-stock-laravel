@extends('layouts.app')

@section('title', 'Daftar Barang Keluar (Dispatch)')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Pengeluaran Barang (Stock-Out)</h1>
            <p class="text-sm text-slate-500">Catatan mutasi pengeluaran stok untuk distribusi, pemakaian internal, atau scrap</p>
        </div>
        <div>
            <a href="{{ route('barang-keluar.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-semibold text-sm rounded-xl shadow-sm shadow-amber-600/20 transition">
                <i class="fa-solid fa-minus"></i>
                <span>Catat Barang Keluar</span>
            </a>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
        <form action="{{ route('barang-keluar.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div class="lg:col-span-2">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari no. dokumen, penerima, keterangan..."
                       class="w-full text-sm rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2 border">
            </div>
            <div>
                <select name="tujuan" class="w-full text-sm rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2 border">
                    <option value="">Semua Tujuan</option>
                    <option value="pemakaian_internal" {{ $tujuan == 'pemakaian_internal' ? 'selected' : '' }}>Pemakaian Internal</option>
                    <option value="distribusi" {{ $tujuan == 'distribusi' ? 'selected' : '' }}>Distribusi / Transfer</option>
                    <option value="penjualan_grosir" {{ $tujuan == 'penjualan_grosir' ? 'selected' : '' }}>Penjualan Grosir / Proyek</option>
                    <option value="scrap_rusak" {{ $tujuan == 'scrap_rusak' ? 'selected' : '' }}>Scrap / Barang Rusak</option>
                    <option value="lainnya" {{ $tujuan == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                </select>
            </div>
            <div>
                <input type="date" name="start_date" value="{{ $startDate }}"
                       class="w-full text-sm rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2 border">
            </div>
            <div class="flex items-center gap-2">
                <input type="date" name="end_date" value="{{ $endDate }}"
                       class="w-full text-sm rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2 border">
                <button type="submit" class="px-3 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-lg text-sm transition">
                    <i class="fa-solid fa-filter"></i>
                </button>
                @if($search || $tujuan || $startDate || $endDate)
                    <a href="{{ route('barang-keluar.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-sm transition" title="Reset filter">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-slate-700 font-semibold border-b border-slate-200 text-xs uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-3.5">No. Dokumen</th>
                        <th class="px-6 py-3.5">Tanggal</th>
                        <th class="px-6 py-3.5">Tujuan Mutasi</th>
                        <th class="px-6 py-3.5">Penerima</th>
                        <th class="px-6 py-3.5 text-center">Total Item</th>
                        <th class="px-6 py-3.5">Pencatat</th>
                        <th class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($barangKeluars as $bk)
                        <tr class="hover:bg-slate-50/75 transition">
                            <td class="px-6 py-4 font-semibold text-slate-900">
                                <a href="{{ route('barang-keluar.show', $bk->id) }}" class="text-indigo-600 hover:underline">
                                    {{ $bk->no_keluar }}
                                </a>
                            </td>
                            <td class="px-6 py-4">{{ \Carbon\Carbon::parse($bk->tanggal)->format('d M Y') }}</td>
                            <td class="px-6 py-4">
                                @php
                                    $badgeColor = match($bk->tujuan) {
                                        'pemakaian_internal' => 'bg-blue-50 text-blue-700 border-blue-200',
                                        'distribusi' => 'bg-purple-50 text-purple-700 border-purple-200',
                                        'penjualan_grosir' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'scrap_rusak' => 'bg-rose-50 text-rose-700 border-rose-200',
                                        default => 'bg-slate-50 text-slate-700 border-slate-200',
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold border {{ $badgeColor }}">
                                    {{ ucwords(str_replace('_', ' ', $bk->tujuan)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-medium text-slate-800">
                                {{ $bk->penerima ?: '-' }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                    {{ $bk->items->sum('jumlah') }} unit ({{ $bk->items->count() }} SKU)
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-500 text-xs">
                                {{ $bk->creator ? $bk->creator->name : '-' }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('barang-keluar.show', $bk->id) }}"
                                   class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 transition">
                                    <i class="fa-solid fa-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                <i class="fa-solid fa-dolly text-4xl mb-3 text-slate-300"></i>
                                <p class="text-base font-semibold text-slate-600">Belum ada transaksi pengeluaran barang</p>
                                <p class="text-sm text-slate-400 mt-1">Klik tombol "Catat Barang Keluar" untuk memproses pengeluaran stok.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($barangKeluars->hasPages())
            <div class="px-6 py-4 border-t border-slate-200">
                {{ $barangKeluars->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
