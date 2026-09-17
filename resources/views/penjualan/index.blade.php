@extends('layouts.app')

@section('title', 'Daftar Invoice Penjualan')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-black text-slate-900">Daftar Invoice Penjualan</h1>
            <p class="text-xs text-slate-500">Histori transaksi kasir, nomor faktur, dan pelanggan</p>
        </div>
        <a href="{{ route('penjualan.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-xs transition">
            <i class="fa-solid fa-barcode"></i>
            <span>Kasir / Scan Barcode Baru</span>
        </a>
    </div>

    <!-- Search Card -->
    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs flex items-center justify-between">
        <form action="{{ route('penjualan.index') }}" method="GET" class="flex-1 max-w-md">
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text" name="search" value="{{ $search }}"
                       placeholder="Cari no invoice atau nama konsumen..."
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
                        <th class="px-5 py-3.5">No. Invoice</th>
                        <th class="px-5 py-3.5">Tanggal</th>
                        <th class="px-5 py-3.5">Konsumen</th>
                        <th class="px-5 py-3.5">Salesman</th>
                        <th class="px-5 py-3.5">Cabang</th>
                        <th class="px-5 py-3.5 text-right">Total Transaksi</th>
                        <th class="px-5 py-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($penjualans as $p)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-3.5 font-bold font-mono text-slate-900">{{ $p->no_invoice }}</td>
                            <td class="px-5 py-3.5 text-slate-600">{{ date('d M Y', strtotime($p->tanggal)) }}</td>
                            <td class="px-5 py-3.5 font-semibold text-slate-800">{{ $p->konsumen->nama ?? '-' }}</td>
                            <td class="px-5 py-3.5 text-slate-600">{{ $p->salesman->nama ?? '-' }}</td>
                            <td class="px-5 py-3.5 text-slate-600">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-slate-100 text-slate-700">
                                    {{ $p->cabang->nama ?? '-' }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-right font-black text-emerald-600">
                                Rp {{ number_format($p->total_harga, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <a href="{{ route('penjualan.show', $p->id) }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-1 bg-slate-100 hover:bg-emerald-50 hover:text-emerald-700 text-slate-700 font-semibold rounded-lg text-xs transition">
                                    <i class="fa-solid fa-receipt"></i>
                                    <span>Cetak Invoice</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-8 text-center text-slate-400">
                                Belum ada riwayat transaksi penjualan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($penjualans->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $penjualans->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
