@extends('layouts.app')

@section('title', 'Warehouse Dashboard')

@section('content')
<div class="space-y-6">

    <!-- Welcome Header & Banner -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden border border-slate-800">
        <div class="relative z-10 max-w-3xl">
            <div class="flex items-center gap-2 mb-3">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Central Warehouse Online
                </span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                    <i class="fa-solid fa-server"></i> Vercel Serverless Ready
                </span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black tracking-tight">Pusat Kendali Stok Gudang</h1>
            <p class="text-slate-300 text-sm mt-2 leading-relaxed">
                Manajemen inventori murni tanpa invoice kasir. Pantau kapasitas rak, terima pasokan supplier, catat alur pengeluaran internal/distribusi, dan audit stock opname dengan pemindai barcode.
            </p>

            <div class="mt-6 flex flex-wrap gap-3">
                <a href="{{ route('barang-masuk.create') }}"
                   class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs sm:text-sm px-4 py-2.5 rounded-xl shadow-md shadow-emerald-600/30 transition">
                    <i class="fa-solid fa-truck-ramp-box"></i>
                    <span>Catat Barang Masuk</span>
                </a>
                <a href="{{ route('barang-keluar.create') }}"
                   class="inline-flex items-center gap-2 bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs sm:text-sm px-4 py-2.5 rounded-xl shadow-md shadow-amber-600/30 transition">
                    <i class="fa-solid fa-dolly"></i>
                    <span>Catat Barang Keluar</span>
                </a>
                <a href="{{ route('stock-opname.create') }}"
                   class="inline-flex items-center gap-2 bg-slate-800 hover:bg-slate-700 text-white font-semibold text-xs sm:text-sm px-4 py-2.5 rounded-xl transition border border-slate-700">
                    <i class="fa-solid fa-clipboard-check text-cyan-400"></i>
                    <span>Stock Opname</span>
                </a>
                <a href="{{ route('kartu_stok.index') }}"
                   class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 text-white font-semibold text-xs sm:text-sm px-4 py-2.5 rounded-xl transition border border-white/20">
                    <i class="fa-solid fa-book-open-reader"></i>
                    <span>Buku Kartu Stok</span>
                </a>
            </div>
        </div>

        <div class="absolute -right-8 -bottom-10 opacity-10 text-white text-[170px] pointer-events-none">
            <i class="fa-solid fa-warehouse"></i>
        </div>
    </div>

    <!-- 4 Warehouse KPI Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <!-- Total SKU -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total SKU Master</p>
                <h3 class="text-2xl font-black text-slate-800 mt-1">{{ number_format($totalSku) }} SKU</h3>
                <a href="{{ route('produk.index') }}" class="text-xs text-indigo-600 font-semibold hover:underline mt-1 inline-block">
                    Kelola Katalog & Rak &rarr;
                </a>
            </div>
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-boxes-stacked"></i>
            </div>
        </div>

        <!-- Total Unit Fisik -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Fisik Unit</p>
                <h3 class="text-2xl font-black text-slate-900 mt-1">{{ number_format($totalFisikStok) }} Unit</h3>
                <span class="text-xs text-slate-500 mt-1 inline-block">Tersimpan di rak gudang</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-cyan-50 text-cyan-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-cubes-stacked"></i>
            </div>
        </div>

        <!-- Barang Masuk Hari Ini -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Masuk Hari Ini</p>
                <h3 class="text-2xl font-black text-emerald-600 mt-1">+{{ number_format($barangMasukQtyToday) }} Unit</h3>
                <span class="text-xs text-slate-500 mt-1 inline-block">{{ $barangMasukTodayCount }} Dokumen Penerimaan</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-truck-ramp-box"></i>
            </div>
        </div>

        <!-- Barang Keluar Hari Ini -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Keluar Hari Ini</p>
                <h3 class="text-2xl font-black text-amber-600 mt-1">-{{ number_format($barangKeluarQtyToday) }} Unit</h3>
                <span class="text-xs text-slate-500 mt-1 inline-block">{{ $barangKeluarTodayCount }} Dokumen Pengeluaran</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-dolly"></i>
            </div>
        </div>
    </div>

    <!-- Data Tables Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Recent Stock Movement Ledger (2 cols) -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden flex flex-col">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-book-open-reader text-indigo-600"></i>
                        <span>Log Mutasi Kartu Stok Terakhir</span>
                    </h3>
                    <p class="text-xs text-slate-500">Aliran fisik barang masuk, keluar, dan opname terkini</p>
                </div>
                <a href="{{ route('kartu_stok.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800">
                    Buku Lengkap &rarr;
                </a>
            </div>

            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 font-semibold uppercase tracking-wider">
                        <tr>
                            <th class="px-5 py-3">Tanggal / No. Bukti</th>
                            <th class="px-5 py-3">Barang & Rak</th>
                            <th class="px-5 py-3 text-center">Masuk</th>
                            <th class="px-5 py-3 text-center">Keluar</th>
                            <th class="px-5 py-3 text-center">Sisa Saldo</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentMovements as $mv)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-5 py-3.5">
                                    <div class="font-bold text-slate-900 font-mono">{{ $mv->no_bukti }}</div>
                                    <div class="text-[11px] text-slate-400">{{ \Carbon\Carbon::parse($mv->tanggal)->format('d M Y') }}</div>
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="font-semibold text-slate-800">{{ $mv->produk->nama ?? 'Barang Dihapus' }}</div>
                                    <div class="text-[11px] text-slate-400 flex items-center gap-1.5 mt-0.5">
                                        <span class="font-mono">{{ $mv->produk->kode_produk ?? '-' }}</span>
                                        <span>&bull;</span>
                                        <span class="text-indigo-600 font-medium">Rak: {{ $mv->produk->lokasi_rak ?? '-' }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    @if($mv->stok_masuk > 0)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded font-bold text-emerald-700 bg-emerald-50">
                                            +{{ $mv->stok_masuk }}
                                        </span>
                                    @else
                                        <span class="text-slate-300">-</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    @if($mv->stok_keluar > 0)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded font-bold text-rose-700 bg-rose-50">
                                            -{{ $mv->stok_keluar }}
                                        </span>
                                    @else
                                        <span class="text-slate-300">-</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-center font-bold text-slate-900">
                                    {{ $mv->saldo_akhir }} <span class="font-normal text-[11px] text-slate-400">{{ $mv->produk->satuan ?? 'Pcs' }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-10 text-center text-slate-400">
                                    <i class="fa-solid fa-box-open text-3xl mb-2 block"></i>
                                    Belum ada catatan mutasi stok di kartu stok.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right Col: Low Stock Warning (1 col) -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs flex flex-col overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-triangle-exclamation text-amber-500"></i>
                        <span>Peringatan Stok Menipis</span>
                    </h3>
                    <p class="text-xs text-slate-500">Barang mendekati atau di bawah batas minimum</p>
                </div>
                <span class="text-xs font-bold px-2 py-0.5 rounded-full {{ $lowStockCount > 0 ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-700' }}">
                    {{ $lowStockCount }} SKU
                </span>
            </div>

            <div class="flex-1 divide-y divide-slate-100">
                @forelse($lowStockItems as $item)
                    <div class="p-4 flex items-center justify-between hover:bg-slate-50/80 transition">
                        <div>
                            <p class="text-xs font-bold text-slate-800">{{ $item->nama }}</p>
                            <div class="flex items-center gap-2 text-[11px] text-slate-400 mt-0.5">
                                <span class="font-mono text-slate-500">{{ $item->kode_produk }}</span>
                                <span>&bull;</span>
                                <span class="text-indigo-600 font-semibold">{{ $item->lokasi_rak ?: 'Tanpa Rak' }}</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold {{ $item->stok <= 0 ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800' }}">
                                Sisa: {{ $item->stok }} {{ $item->satuan ?: 'Pcs' }}
                            </span>
                            <p class="text-[10px] text-slate-400 mt-0.5">Min: {{ $item->stok_minimum }}</p>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-400 my-auto">
                        <i class="fa-solid fa-circle-check text-3xl text-emerald-500 mb-2 block"></i>
                        <p class="font-semibold text-slate-700 text-xs">Semua persediaan barang aman!</p>
                        <p class="text-[11px] text-slate-400 mt-0.5">Tidak ada produk di bawah batas minimum.</p>
                    </div>
                @endforelse
            </div>

            <div class="p-4 bg-slate-50 border-t border-slate-100">
                <a href="{{ route('barang-masuk.create') }}"
                   class="block w-full py-2.5 px-3 text-center text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-xs transition">
                    + Restok via Barang Masuk
                </a>
            </div>
        </div>

    </div>

</div>
@endsection
