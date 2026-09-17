@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">

    <!-- Welcome Header & Banner -->
    <div class="bg-gradient-to-r from-indigo-900 via-indigo-800 to-slate-900 rounded-2xl p-6 sm:p-8 text-white shadow-lg relative overflow-hidden">
        <div class="relative z-10 max-w-2xl">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-indigo-500/30 text-indigo-200 border border-indigo-400/30 mb-3">
                <i class="fa-solid fa-server"></i> Serverless Ready on Vercel
            </span>
            <h1 class="text-2xl sm:text-3xl font-black tracking-tight">Sistem Inventori Multi-Cabang O-Stock</h1>
            <p class="text-indigo-200 text-sm mt-2 leading-relaxed">
                Kelola stok, alokasi gudang, mutasi antar cabang, dan transaksi penjualan kasir dengan pemindaian barcode real-time.
            </p>

            <div class="mt-6 flex flex-wrap gap-3">
                <a href="{{ route('penjualan.create') }}"
                   class="inline-flex items-center gap-2 bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-sm px-4 py-2.5 rounded-xl shadow-md transition">
                    <i class="fa-solid fa-barcode"></i>
                    <span>Buka Kasir & Barcode Scan</span>
                </a>
                <a href="{{ route('kartu_stok.index') }}"
                   class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 text-white font-semibold text-sm px-4 py-2.5 rounded-xl transition border border-white/20">
                    <i class="fa-solid fa-clipboard-list"></i>
                    <span>Lihat Kartu Stok</span>
                </a>
            </div>
        </div>

        <div class="absolute -right-8 -bottom-10 opacity-10 text-white text-[160px] pointer-events-none">
            <i class="fa-solid fa-boxes-stacked"></i>
        </div>
    </div>

    <!-- KPI Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <!-- Total Produk -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Produk</p>
                <h3 class="text-2xl font-black text-slate-800 mt-1">{{ number_format($totalProduk) }}</h3>
                <a href="{{ route('produk.index') }}" class="text-xs text-indigo-600 font-semibold hover:underline mt-1 inline-block">
                    Kelola Katalog &rarr;
                </a>
            </div>
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-box"></i>
            </div>
        </div>

        <!-- Stok Menipis Alert -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Stok Menipis (≤10)</p>
                <h3 class="text-2xl font-black {{ $lowStockCount > 0 ? 'text-amber-600' : 'text-slate-800' }} mt-1">
                    {{ $lowStockCount }}
                </h3>
                <span class="text-xs text-slate-500 mt-1 inline-block">Perlu diisi ulang</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
        </div>

        <!-- Penjualan Hari Ini -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Penjualan Hari Ini</p>
                <h3 class="text-xl font-black text-emerald-600 mt-1">Rp {{ number_format($todaySalesTotal, 0, ',', '.') }}</h3>
                <span class="text-xs text-slate-500 mt-1 inline-block">{{ $todaySalesCount }} Transaksi</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-cash-register"></i>
            </div>
        </div>

        <!-- Permintaan Pending -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Permintaan Cabang</p>
                <h3 class="text-2xl font-black {{ $pendingRequestsCount > 0 ? 'text-blue-600' : 'text-slate-800' }} mt-1">
                    {{ $pendingRequestsCount }}
                </h3>
                <a href="{{ route('branch_request.index') }}" class="text-xs text-indigo-600 font-semibold hover:underline mt-1 inline-block">
                    Tinjau Permintaan &rarr;
                </a>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-code-pull-request"></i>
            </div>
        </div>
    </div>

    <!-- Data Tables Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Recent Invoices Table (2 cols) -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-900">Transaksi Penjualan Terakhir</h3>
                    <p class="text-xs text-slate-500">Invoice dan transaksi penjualan terkini</p>
                </div>
                <a href="{{ route('penjualan.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-700">
                    Lihat Semua &rarr;
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 font-semibold uppercase tracking-wider">
                        <tr>
                            <th class="px-5 py-3">No. Invoice</th>
                            <th class="px-5 py-3">Tanggal</th>
                            <th class="px-5 py-3">Pelanggan</th>
                            <th class="px-5 py-3">Cabang</th>
                            <th class="px-5 py-3 text-right">Total</th>
                            <th class="px-5 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentSales as $sale)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-5 py-3.5 font-bold text-slate-900">{{ $sale->no_invoice }}</td>
                                <td class="px-5 py-3.5 text-slate-600">{{ date('d M Y', strtotime($sale->tanggal)) }}</td>
                                <td class="px-5 py-3.5 text-slate-800 font-medium">{{ $sale->konsumen->nama ?? '-' }}</td>
                                <td class="px-5 py-3.5 text-slate-600">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-slate-100 text-slate-700">
                                        {{ $sale->cabang->nama ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 font-bold text-right text-emerald-600">
                                    Rp {{ number_format($sale->total_harga, 0, ',', '.') }}
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    <a href="{{ route('penjualan.show', $sale->id) }}"
                                       class="inline-flex items-center gap-1 text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                                        <i class="fa-solid fa-print"></i>
                                        <span>Invoice</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-8 text-center text-slate-400">
                                    <i class="fa-solid fa-receipt text-3xl mb-2 block"></i>
                                    Belum ada transaksi penjualan tercatat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Low Stock Items Warning (1 col) -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs flex flex-col overflow-hidden">
            <div class="p-5 border-b border-slate-100">
                <h3 class="font-bold text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-circle-exclamation text-amber-500"></i>
                    <span>Perhatian Stok Produk</span>
                </h3>
                <p class="text-xs text-slate-500">Produk dengan persediaan terbatas</p>
            </div>

            <div class="flex-1 divide-y divide-slate-100">
                @forelse($lowStockItems as $item)
                    <div class="p-4 flex items-center justify-between hover:bg-slate-50/80 transition">
                        <div>
                            <p class="text-xs font-bold text-slate-800">{{ $item->nama }}</p>
                            <p class="text-[11px] text-slate-400 font-mono">{{ $item->kode_produk }}</p>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold {{ $item->stok <= 5 ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700' }}">
                                Sisa: {{ $item->stok }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-400">
                        <i class="fa-solid fa-circle-check text-3xl text-emerald-500 mb-2 block"></i>
                        Semua stok produk dalam kondisi aman.
                    </div>
                @endforelse
            </div>

            <div class="p-4 bg-slate-50 border-t border-slate-100">
                <a href="{{ route('alokasi.create') }}"
                   class="block w-full py-2 px-3 text-center text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-xs transition">
                    + Buat Alokasi Stok Baru
                </a>
            </div>
        </div>

    </div>

</div>
@endsection
