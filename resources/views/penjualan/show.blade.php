@extends('layouts.app')

@section('title', 'Invoice ' . $penjualan->no_invoice)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between no-print">
        <div>
            <h1 class="text-xl font-black text-slate-900">Invoice Penjualan</h1>
            <p class="text-xs text-slate-500 font-mono">{{ $penjualan->no_invoice }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('penjualan.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900">
                &larr; Kembali
            </a>
            <button onclick="window.print()" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-lg shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-print"></i>
                <span>Cetak Invoice / Struk</span>
            </button>
        </div>
    </div>

    <!-- Printable Invoice Card -->
    <div class="bg-white rounded-2xl border border-slate-200 p-8 sm:p-10 shadow-xs space-y-6">
        
        <!-- Header -->
        <div class="flex items-start justify-between border-b border-slate-200 pb-6">
            <div>
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white flex items-center justify-center font-bold text-sm">
                        <i class="fa-solid fa-boxes-stacked"></i>
                    </div>
                    <span class="font-black text-lg text-slate-900">O-STOCK STORE</span>
                </div>
                <p class="text-xs font-bold text-slate-700 mt-2">{{ $penjualan->cabang->nama ?? 'Kantor Pusat' }}</p>
                <p class="text-[11px] text-slate-500 max-w-xs">{{ $penjualan->cabang->alamat ?? 'Alamat Operasional' }}</p>
            </div>

            <div class="text-right">
                <span class="text-xs font-bold text-emerald-600 uppercase tracking-widest">FAKTUR PENJUALAN</span>
                <h2 class="text-2xl font-black font-mono text-slate-900 mt-0.5">{{ $penjualan->no_invoice }}</h2>
                <p class="text-xs text-slate-500 mt-1">Tanggal: <strong>{{ date('d F Y', strtotime($penjualan->tanggal)) }}</strong></p>
            </div>
        </div>

        <!-- Customer & Salesman Info -->
        <div class="grid grid-cols-2 gap-6 text-xs">
            <div>
                <p class="text-slate-400 font-semibold uppercase tracking-wider text-[10px]">Ditujukan Kepada Pelanggan:</p>
                <h3 class="font-bold text-slate-900 text-sm mt-1">{{ $penjualan->konsumen->nama ?? '-' }}</h3>
                <p class="text-slate-600 mt-0.5">{{ $penjualan->konsumen->alamat ?? '-' }}</p>
                <p class="text-slate-500 mt-0.5">{{ $penjualan->konsumen->kota ?? '' }} ({{ $penjualan->konsumen->no_telp ?? '-' }})</p>
            </div>

            <div class="text-right">
                <p class="text-slate-400 font-semibold uppercase tracking-wider text-[10px]">Staf Penjual / Salesman:</p>
                <h3 class="font-bold text-slate-900 text-sm mt-1">{{ $penjualan->salesman->nama ?? '-' }}</h3>
                <p class="text-slate-600 mt-0.5">{{ $penjualan->salesman->no_telp ?? '-' }}</p>
            </div>
        </div>

        <!-- Line Items Table -->
        <div class="border border-slate-200 rounded-xl overflow-hidden">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-200 font-semibold text-slate-600 uppercase">
                    <tr>
                        <th class="px-4 py-2.5">No</th>
                        <th class="px-4 py-2.5">Produk</th>
                        <th class="px-4 py-2.5 text-right">Harga Satuan</th>
                        <th class="px-4 py-2.5 text-center">Qty</th>
                        <th class="px-4 py-2.5 text-center">Diskon</th>
                        <th class="px-4 py-2.5 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($penjualan->items as $idx => $item)
                        <tr>
                            <td class="px-4 py-3 text-slate-400">{{ $idx + 1 }}</td>
                            <td class="px-4 py-3">
                                <p class="font-bold text-slate-900">{{ $item->produk->nama ?? '-' }}</p>
                                <p class="text-[10px] font-mono text-slate-400">{{ $item->produk->kode_produk ?? '' }}</p>
                            </td>
                            <td class="px-4 py-3 text-right text-slate-700">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-center font-bold text-slate-900">{{ $item->jumlah }}</td>
                            <td class="px-4 py-3 text-center text-slate-500">{{ $item->discount > 0 ? $item->discount . '%' : '-' }}</td>
                            <td class="px-4 py-3 text-right font-bold text-slate-900">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Total Breakdown -->
        <div class="flex justify-between items-start pt-4 border-t border-slate-100">
            <div class="text-xs text-slate-500 max-w-sm">
                @if($penjualan->catatan)
                    <p class="font-semibold text-slate-700">Catatan:</p>
                    <p class="italic mt-0.5">{{ $penjualan->catatan }}</p>
                @endif
            </div>

            <div class="w-64 space-y-2 text-xs">
                <div class="flex justify-between text-slate-600">
                    <span>Total Subtotal:</span>
                    <span class="font-medium">Rp {{ number_format($penjualan->total_harga, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-sm font-black text-slate-900 border-t border-slate-200 pt-2">
                    <span>Grand Total:</span>
                    <span class="text-emerald-600">Rp {{ number_format($penjualan->total_harga, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Signature Lines for Print -->
        <div class="hidden print-only grid grid-cols-2 pt-12 text-center text-xs">
            <div>
                <p class="font-semibold text-slate-700">Tanda Terima Pelanggan,</p>
                <div class="h-16"></div>
                <p class="font-bold text-slate-900">( {{ $penjualan->konsumen->nama ?? '..........................' }} )</p>
            </div>
            <div>
                <p class="font-semibold text-slate-700">Hormat Kami,</p>
                <div class="h-16"></div>
                <p class="font-bold text-slate-900">( {{ $penjualan->salesman->nama ?? 'Kasir Toko' }} )</p>
            </div>
        </div>
    </div>
</div>
@endsection
