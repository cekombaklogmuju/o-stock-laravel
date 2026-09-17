@extends('layouts.app')

@section('title', 'Detail Penerimaan Barang Masuk - ' . $barangMasuk->no_masuk)

@section('content')
<div class="space-y-6">
    <!-- Header with Action Buttons -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 no-print">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('barang-masuk.index') }}" class="text-slate-400 hover:text-slate-600 transition">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Dokumen Barang Masuk</h1>
            </div>
            <p class="text-sm text-slate-500 font-mono mt-0.5">{{ $barangMasuk->no_masuk }}</p>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" onclick="window.print()"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-semibold text-xs rounded-xl shadow-xs transition">
                <i class="fa-solid fa-print"></i>
                <span>Cetak Bukti Penerimaan</span>
            </button>
            <a href="{{ route('barang-masuk.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs rounded-xl shadow-sm transition">
                <i class="fa-solid fa-plus"></i>
                <span>Catat Masuk Baru</span>
            </a>
        </div>
    </div>

    <!-- Printable Receipt Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 sm:p-8 space-y-6">
        <!-- Receipt Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center border-b border-slate-200 pb-6 gap-4">
            <div>
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-emerald-600 text-white flex items-center justify-center font-bold">
                        <i class="fa-solid fa-truck-ramp-box"></i>
                    </div>
                    <span class="text-xl font-black text-slate-900 tracking-wider uppercase">BUKTI PENERIMAAN BARANG</span>
                </div>
                <p class="text-xs text-slate-500 mt-1">Gudang Utama &bull; Kawasan Industri Pulogadung Blok C No. 12, Jakarta</p>
            </div>
            <div class="text-left sm:text-right">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                    STATUS: SELESAI (STOK DITAMBAH)
                </span>
                <p class="text-xs text-slate-500 mt-1">Dicetak pada: {{ now()->format('d/m/Y H:i') }}</p>
            </div>
        </div>

        <!-- Meta Information Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 p-4 bg-slate-50 rounded-xl text-sm">
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase">No. Dokumen</p>
                <p class="font-bold text-slate-900 font-mono text-base">{{ $barangMasuk->no_masuk }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase">Tanggal Masuk</p>
                <p class="font-bold text-slate-900">{{ \Carbon\Carbon::parse($barangMasuk->tanggal)->format('d F Y') }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase">Supplier / Asal</p>
                <p class="font-bold text-slate-900">{{ $barangMasuk->supplier ? $barangMasuk->supplier->nama : 'Non-Supplier / Internal' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase">No. Surat Jalan</p>
                <p class="font-bold text-slate-900 font-mono">{{ $barangMasuk->no_surat_jalan ?: '-' }}</p>
            </div>
        </div>

        @if($barangMasuk->keterangan)
            <div class="p-3 bg-amber-50/50 border border-amber-200/60 rounded-lg text-xs text-amber-900">
                <span class="font-bold">Keterangan:</span> {{ $barangMasuk->keterangan }}
            </div>
        @endif

        <!-- Items Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-100 text-slate-700 font-bold border-b border-slate-200 text-xs uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-3 text-center w-12">#</th>
                        <th class="px-4 py-3">Kode & Barcode</th>
                        <th class="px-4 py-3">Nama Barang</th>
                        <th class="px-4 py-3">Lokasi Rak</th>
                        <th class="px-4 py-3 text-center">Jumlah Diterima</th>
                        <th class="px-4 py-3">Catatan / Lot</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($barangMasuk->items as $index => $item)
                        <tr class="hover:bg-slate-50/50">
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
                            <td class="px-4 py-3.5 text-center font-bold text-emerald-700 text-base">
                                +{{ $item->jumlah }} <span class="text-xs font-normal text-slate-500">{{ $item->produk->satuan ?? 'Pcs' }}</span>
                            </td>
                            <td class="px-4 py-3.5 text-xs text-slate-500">
                                {{ $item->catatan ?: '-' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-slate-50 border-t border-slate-200 font-bold text-slate-900">
                    <tr>
                        <td colspan="4" class="px-4 py-3 text-right text-xs uppercase">Total Barang Diterima:</td>
                        <td class="px-4 py-3 text-center text-emerald-700 text-base">
                            {{ $barangMasuk->items->sum('jumlah') }} Unit
                        </td>
                        <td class="px-4 py-3 text-xs text-slate-500">
                            ({{ $barangMasuk->items->count() }} Macam Produk)
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Signatures for Receipt Voucher -->
        <div class="pt-8 grid grid-cols-2 sm:grid-cols-3 gap-8 text-center text-xs text-slate-600">
            <div>
                <p class="font-semibold text-slate-800">Yang Menyerahkan (Driver/Supplier),</p>
                <div class="h-20"></div>
                <p class="border-t border-slate-300 pt-1 font-medium">( ........................................ )</p>
            </div>
            <div>
                <p class="font-semibold text-slate-800">Petugas Gudang (Penerima),</p>
                <div class="h-20"></div>
                <p class="border-t border-slate-300 pt-1 font-medium font-semibold text-slate-900">
                    ( {{ $barangMasuk->creator ? $barangMasuk->creator->name : 'Staff Warehouse' }} )
                </p>
            </div>
            <div class="hidden sm:block">
                <p class="font-semibold text-slate-800">Kepala Gudang (Mengetahui),</p>
                <div class="h-20"></div>
                <p class="border-t border-slate-300 pt-1 font-medium">( ........................................ )</p>
            </div>
        </div>
    </div>
</div>
@endsection
