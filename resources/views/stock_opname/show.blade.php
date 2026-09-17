@extends('layouts.app')

@section('title', 'Hasil Stock Opname - ' . $stockOpname->no_opname)

@section('content')
<div class="space-y-6">
    <!-- Header with Action Buttons -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 no-print">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('stock-opname.index') }}" class="text-slate-400 hover:text-slate-600 transition">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Berita Acara Stock Opname</h1>
            </div>
            <p class="text-sm text-slate-500 font-mono mt-0.5">{{ $stockOpname->no_opname }}</p>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" onclick="window.print()"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-semibold text-xs rounded-xl shadow-xs transition">
                <i class="fa-solid fa-print"></i>
                <span>Cetak Berita Acara</span>
            </button>
            <a href="{{ route('stock-opname.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs rounded-xl shadow-sm transition">
                <i class="fa-solid fa-plus"></i>
                <span>Opname Baru</span>
            </a>
        </div>
    </div>

    <!-- Printable Audit Report Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 sm:p-8 space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center border-b border-slate-200 pb-6 gap-4">
            <div>
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white flex items-center justify-center font-bold">
                        <i class="fa-solid fa-clipboard-check"></i>
                    </div>
                    <span class="text-xl font-black text-slate-900 tracking-wider uppercase">BERITA ACARA AUDIT STOCK OPNAME</span>
                </div>
                <p class="text-xs text-slate-500 mt-1">Gudang Utama &bull; Kawasan Industri Pulogadung Blok C No. 12, Jakarta</p>
            </div>
            <div class="text-left sm:text-right">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800">
                    STATUS: REKONSILIASI SELESAI
                </span>
                <p class="text-xs text-slate-500 mt-1">Dicetak pada: {{ now()->format('d/m/Y H:i') }}</p>
            </div>
        </div>

        <!-- Meta Information Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-4 bg-slate-50 rounded-xl text-sm">
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase">No. Opname</p>
                <p class="font-bold text-slate-900 font-mono text-base">{{ $stockOpname->no_opname }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase">Tanggal Pelaksanaan</p>
                <p class="font-bold text-slate-900">{{ \Carbon\Carbon::parse($stockOpname->tanggal)->format('d F Y') }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase">Auditor / Penanggung Jawab</p>
                <p class="font-bold text-slate-900">{{ $stockOpname->creator ? $stockOpname->creator->name : '-' }}</p>
            </div>
        </div>

        @if($stockOpname->keterangan)
            <div class="p-3 bg-cyan-50/50 border border-cyan-200/60 rounded-lg text-xs text-cyan-900">
                <span class="font-bold">Catatan Audit:</span> {{ $stockOpname->keterangan }}
            </div>
        @endif

        <!-- Audit Variance Statistics -->
        @php
            $totalSku = $stockOpname->items->count();
            $cocokCount = $stockOpname->items->where('selisih', 0)->count();
            $surplusCount = $stockOpname->items->where('selisih', '>', 0)->count();
            $defisitCount = $stockOpname->items->where('selisih', '<', 0)->count();
        @endphp
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 no-print">
            <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl text-center">
                <span class="text-xs text-slate-500">Total SKU Diperiksa</span>
                <p class="text-lg font-bold text-slate-900">{{ $totalSku }}</p>
            </div>
            <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl text-center">
                <span class="text-xs text-emerald-700">Stok Sesuai (100%)</span>
                <p class="text-lg font-bold text-emerald-800">{{ $cocokCount }}</p>
            </div>
            <div class="p-3 bg-blue-50 border border-blue-200 rounded-xl text-center">
                <span class="text-xs text-blue-700">Surplus (Lebih)</span>
                <p class="text-lg font-bold text-blue-800">{{ $surplusCount }}</p>
            </div>
            <div class="p-3 bg-rose-50 border border-rose-200 rounded-xl text-center">
                <span class="text-xs text-rose-700">Defisit (Kurang)</span>
                <p class="text-lg font-bold text-rose-800">{{ $defisitCount }}</p>
            </div>
        </div>

        <!-- Items Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-100 text-slate-700 font-bold border-b border-slate-200 text-xs uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-3 text-center w-12">#</th>
                        <th class="px-4 py-3">Kode & Barcode</th>
                        <th class="px-4 py-3">Nama Barang</th>
                        <th class="px-4 py-3">Lokasi Rak</th>
                        <th class="px-4 py-3 text-center">Stok Sistem</th>
                        <th class="px-4 py-3 text-center">Stok Fisik</th>
                        <th class="px-4 py-3 text-center">Selisih</th>
                        <th class="px-4 py-3">Alasan / Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($stockOpname->items as $index => $item)
                        <tr class="hover:bg-slate-50/50 {{ $item->selisih != 0 ? 'bg-amber-50/20' : '' }}">
                            <td class="px-4 py-3.5 text-center text-slate-400 font-semibold text-xs">{{ $index + 1 }}</td>
                            <td class="px-4 py-3.5 font-mono text-xs">
                                <div class="font-bold text-slate-900">{{ $item->produk->kode_produk }}</div>
                                <div class="text-slate-400">{{ $item->produk->barcode ?: '-' }}</div>
                            </td>
                            <td class="px-4 py-3.5 font-semibold text-slate-800">
                                {{ $item->produk->nama }}
                                <div class="text-xs font-normal text-slate-500">{{ $item->produk->kategori->nama ?? '' }}</div>
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="inline-flex items-center gap-1 text-xs font-semibold px-2 py-0.5 rounded bg-indigo-50 text-indigo-700 border border-indigo-100">
                                    <i class="fa-solid fa-table-cells"></i>
                                    {{ $item->produk->lokasi_rak ?: 'Tanpa Rak' }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-center font-semibold text-slate-600">
                                {{ $item->stok_sistem }} {{ $item->produk->satuan ?? 'Pcs' }}
                            </td>
                            <td class="px-4 py-3.5 text-center font-bold text-slate-900 text-base">
                                {{ $item->stok_fisik }} {{ $item->produk->satuan ?? 'Pcs' }}
                            </td>
                            <td class="px-4 py-3.5 text-center font-bold text-xs">
                                @if($item->selisih == 0)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <i class="fa-solid fa-check"></i> 0 (Cocok)
                                    </span>
                                @elseif($item->selisih > 0)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-blue-50 text-blue-700 border border-blue-200">
                                        +{{ $item->selisih }} (Surplus)
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-rose-50 text-rose-700 border border-rose-200">
                                        {{ $item->selisih }} (Kurang)
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-xs text-slate-500">
                                {{ $item->alasan ?: '-' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Signatures for Audit Voucher -->
        <div class="pt-8 grid grid-cols-2 sm:grid-cols-3 gap-8 text-center text-xs text-slate-600">
            <div>
                <p class="font-semibold text-slate-800">Petugas Pemeriksa Fisik,</p>
                <div class="h-20"></div>
                <p class="border-t border-slate-300 pt-1 font-semibold text-slate-900">
                    ( {{ $stockOpname->creator ? $stockOpname->creator->name : 'Staff Warehouse' }} )
                </p>
            </div>
            <div>
                <p class="font-semibold text-slate-800">Supervisor / Saksi Audit,</p>
                <div class="h-20"></div>
                <p class="border-t border-slate-300 pt-1 font-medium">( ........................................ )</p>
            </div>
            <div class="hidden sm:block">
                <p class="font-semibold text-slate-800">Kepala Gudang (Menyetujui Penyesuaian),</p>
                <div class="h-20"></div>
                <p class="border-t border-slate-300 pt-1 font-medium">( ........................................ )</p>
            </div>
        </div>
    </div>
</div>
@endsection
