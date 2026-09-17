@extends('layouts.app')

@section('title', 'Buku Kartu Stok Gudang')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Buku Kartu Stok (Warehouse Inventory Ledger)</h1>
            <p class="text-sm text-slate-500">Buku besar audit untuk seluruh riwayat barang masuk, keluar, dan penyesuaian fisik opname</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('kartu_stok.print', request()->query()) }}" target="_blank"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold rounded-xl shadow-xs transition">
                <i class="fa-solid fa-print"></i>
                <span>Cetak / Export Buku Stok</span>
            </a>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
        <form action="{{ route('kartu_stok.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
            <div class="lg:col-span-2">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari no. bukti, keterangan, nama barang, rak..."
                       class="w-full px-3 py-2 text-sm bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            </div>

            <div>
                <select name="id_produk" class="w-full px-3 py-2 text-sm bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    <option value="">Semua Barang</option>
                    @foreach($produks as $p)
                        <option value="{{ $p->id }}" {{ $produkId == $p->id ? 'selected' : '' }}>
                            {{ $p->kode_produk }} - {{ $p->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <select name="tipe" class="w-full px-3 py-2 text-sm bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    <option value="">Semua Aliran</option>
                    <option value="masuk" {{ $tipe == 'masuk' ? 'selected' : '' }}>Stok Masuk (+)</option>
                    <option value="keluar" {{ $tipe == 'keluar' ? 'selected' : '' }}>Stok Keluar (-)</option>
                </select>
            </div>

            <div>
                <input type="date" name="start_date" value="{{ $startDate }}" placeholder="Dari Tanggal"
                       class="w-full px-3 py-2 text-sm bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            </div>

            <div class="flex items-center gap-2">
                <input type="date" name="end_date" value="{{ $endDate }}" placeholder="Sampai Tanggal"
                       class="w-full px-3 py-2 text-sm bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                <button type="submit" class="px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-bold transition">
                    <i class="fa-solid fa-filter"></i>
                </button>
                @if($search || $produkId || $tipe || $startDate || $endDate)
                    <a href="{{ route('kartu_stok.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-sm transition" title="Reset filter">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs uppercase tracking-wider">
                    <tr>
                        <th class="px-5 py-3.5">Tanggal</th>
                        <th class="px-5 py-3.5">No. Bukti Transaksi</th>
                        <th class="px-5 py-3.5">Barang & Lokasi Rak</th>
                        <th class="px-5 py-3.5">Keterangan / Alasan Mutasi</th>
                        <th class="px-5 py-3.5 text-center text-emerald-700">Masuk</th>
                        <th class="px-5 py-3.5 text-center text-rose-700">Keluar</th>
                        <th class="px-5 py-3.5 text-center font-black">Saldo Akhir</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($records as $r)
                        <tr class="hover:bg-slate-50/75 transition">
                            <td class="px-5 py-3.5 text-slate-600 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($r->tanggal)->format('d M Y') }}
                            </td>
                            <td class="px-5 py-3.5 font-mono font-bold text-slate-900 whitespace-nowrap">
                                @if(str_starts_with($r->no_bukti, 'BM-'))
                                    <span class="inline-flex items-center gap-1 text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                                        <i class="fa-solid fa-truck-ramp-box"></i> {{ $r->no_bukti }}
                                    </span>
                                @elseif(str_starts_with($r->no_bukti, 'BK-'))
                                    <span class="inline-flex items-center gap-1 text-amber-700 bg-amber-50 px-2 py-0.5 rounded border border-amber-200">
                                        <i class="fa-solid fa-dolly"></i> {{ $r->no_bukti }}
                                    </span>
                                @elseif(str_starts_with($r->no_bukti, 'SO-'))
                                    <span class="inline-flex items-center gap-1 text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded border border-indigo-200">
                                        <i class="fa-solid fa-clipboard-check"></i> {{ $r->no_bukti }}
                                    </span>
                                @else
                                    <span class="text-slate-800">{{ $r->no_bukti }}</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="font-bold text-slate-900">{{ $r->produk->nama ?? 'Barang Telah Dihapus' }}</div>
                                <div class="text-xs text-slate-500 flex items-center gap-2 mt-0.5">
                                    <span class="font-mono text-slate-600">{{ $r->produk->kode_produk ?? '-' }}</span>
                                    <span>&bull;</span>
                                    <span class="text-indigo-600 font-semibold bg-indigo-50 px-1.5 py-0.5 rounded">
                                        <i class="fa-solid fa-table-cells"></i> {{ $r->produk->lokasi_rak ?? 'Tanpa Rak' }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 text-xs text-slate-600 max-w-sm">
                                {{ $r->keterangan }}
                            </td>
                            <td class="px-5 py-3.5 text-center font-bold text-emerald-600 whitespace-nowrap">
                                {{ $r->stok_masuk > 0 ? '+' . $r->stok_masuk : '-' }}
                            </td>
                            <td class="px-5 py-3.5 text-center font-bold text-rose-600 whitespace-nowrap">
                                {{ $r->stok_keluar > 0 ? '-' . $r->stok_keluar : '-' }}
                            </td>
                            <td class="px-5 py-3.5 text-center font-black text-slate-900 bg-slate-50/70 whitespace-nowrap">
                                {{ $r->saldo_akhir }} <span class="font-normal text-xs text-slate-500">{{ $r->produk->satuan ?? 'Pcs' }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center text-slate-400">
                                <i class="fa-solid fa-book-open-reader text-3xl mb-2 text-slate-300"></i>
                                <p class="text-sm font-semibold text-slate-600">Belum ada catatan transaksi di buku kartu stok</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($records->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $records->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
