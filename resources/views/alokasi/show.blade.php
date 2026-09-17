@extends('layouts.app')

@section('title', 'Detail Alokasi ' . $alokasi->no_alokasi)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between no-print">
        <div>
            <h1 class="text-xl font-black text-slate-900">Detail Alokasi Stok</h1>
            <p class="text-xs text-slate-500 font-mono">{{ $alokasi->no_alokasi }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('alokasi.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900">
                &larr; Kembali
            </a>
            <button onclick="window.print()" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs rounded-lg shadow-sm flex items-center gap-1.5">
                <i class="fa-solid fa-print"></i>
                <span>Cetak Dokumen</span>
            </button>
        </div>
    </div>

    <!-- Allocation Document Card -->
    <div class="bg-white rounded-2xl border border-slate-200 p-8 shadow-xs space-y-6">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div>
                <span class="text-xs font-bold text-indigo-600 uppercase tracking-widest">Surat Alokasi Barang</span>
                <h2 class="text-2xl font-black text-slate-900 mt-0.5 font-mono">{{ $alokasi->no_alokasi }}</h2>
            </div>
            <div class="text-right text-xs">
                <p class="text-slate-400">Tanggal Pengiriman:</p>
                <p class="font-bold text-slate-800">{{ date('d F Y', strtotime($alokasi->tanggal)) }}</p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 text-xs">
            <div>
                <p class="text-slate-400 font-medium">Cabang Penerima:</p>
                <p class="font-bold text-slate-800 text-sm mt-0.5">{{ $alokasi->cabang->nama }}</p>
                <p class="text-slate-500 mt-0.5">{{ $alokasi->cabang->alamat ?? '-' }}</p>
            </div>
            <div>
                <p class="text-slate-400 font-medium">Keterangan / Catatan:</p>
                <p class="text-slate-700 mt-0.5">{{ $alokasi->keterangan ?: '-' }}</p>
            </div>
        </div>

        <!-- Table Items -->
        <div class="border border-slate-200 rounded-xl overflow-hidden">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-200 font-semibold text-slate-600 uppercase">
                    <tr>
                        <th class="px-4 py-2.5">No</th>
                        <th class="px-4 py-2.5">Kode Produk</th>
                        <th class="px-4 py-2.5">Nama Produk</th>
                        <th class="px-4 py-2.5 text-center">Jumlah Dialokasikan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($alokasi->items as $idx => $item)
                        <tr>
                            <td class="px-4 py-3 text-slate-400">{{ $idx + 1 }}</td>
                            <td class="px-4 py-3 font-mono font-bold text-slate-800">{{ $item->produk->kode_produk ?? '-' }}</td>
                            <td class="px-4 py-3 font-medium text-slate-800">{{ $item->produk->nama ?? '-' }}</td>
                            <td class="px-4 py-3 text-center font-black text-indigo-700 text-sm">{{ $item->jumlah }} Unit</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
