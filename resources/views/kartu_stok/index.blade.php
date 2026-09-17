@extends('layouts.app')

@section('title', 'Buku Kartu Stok')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-black text-slate-900">Buku Kartu Stok (Ledger Persediaan)</h1>
            <p class="text-xs text-slate-500">Histori pergerakan barang masuk, keluar, mutasi, dan saldo akhir persediaan</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('kartu_stok.print', request()->query()) }}" target="_blank"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold rounded-xl shadow-xs transition">
                <i class="fa-solid fa-print"></i>
                <span>Cetak / Export Laporan</span>
            </a>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
        <form action="{{ route('kartu_stok.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Produk</label>
                <select name="id_produk" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:outline-none">
                    <option value="">Semua Produk</option>
                    @foreach($produks as $p)
                        <option value="{{ $p->id }}" {{ $produkId == $p->id ? 'selected' : '' }}>{{ $p->nama }} [{{ $p->kode_produk }}]</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Cabang</label>
                <select name="id_cabang" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:outline-none">
                    <option value="">Semua Cabang</option>
                    @foreach($kantors as $k)
                        <option value="{{ $k->id }}" {{ $cabangId == $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ $startDate }}"
                       class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:outline-none">
            </div>

            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ $endDate }}"
                       class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:outline-none">
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 px-3 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-lg transition">
                    Terapkan Filter
                </button>
                <a href="{{ route('kartu_stok.index') }}" class="py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold rounded-lg transition" title="Reset">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 font-semibold uppercase tracking-wider">
                    <tr>
                        <th class="px-5 py-3.5">Tanggal</th>
                        <th class="px-5 py-3.5">No. Bukti</th>
                        <th class="px-5 py-3.5">Nama Produk</th>
                        <th class="px-5 py-3.5">Cabang</th>
                        <th class="px-5 py-3.5">Keterangan</th>
                        <th class="px-5 py-3.5 text-center text-emerald-700">Masuk</th>
                        <th class="px-5 py-3.5 text-center text-rose-700">Keluar</th>
                        <th class="px-5 py-3.5 text-center font-black">Saldo Akhir</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($records as $r)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-3.5 text-slate-600">{{ date('d M Y', strtotime($r->tanggal)) }}</td>
                            <td class="px-5 py-3.5 font-mono font-bold text-slate-900">{{ $r->no_bukti }}</td>
                            <td class="px-5 py-3.5 font-bold text-slate-800">{{ $r->produk->nama ?? '-' }}</td>
                            <td class="px-5 py-3.5 text-slate-600">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-slate-100 text-slate-700">
                                    {{ $r->cabang->nama ?? '-' }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-slate-600 max-w-xs truncate">{{ $r->keterangan }}</td>
                            <td class="px-5 py-3.5 text-center font-bold text-emerald-600">
                                {{ $r->stok_masuk > 0 ? '+' . $r->stok_masuk : '-' }}
                            </td>
                            <td class="px-5 py-3.5 text-center font-bold text-rose-600">
                                {{ $r->stok_keluar > 0 ? '-' . $r->stok_keluar : '-' }}
                            </td>
                            <td class="px-5 py-3.5 text-center font-black text-slate-900 bg-slate-50/50">
                                {{ $r->saldo_akhir }} Unit
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-8 text-center text-slate-400">
                                Belum ada catatan histori pergerakan stok.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($records->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $records->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
