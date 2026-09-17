@extends('layouts.app')

@section('title', 'Tinjau Permintaan ' . $branchRequest->no_permintaan)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between no-print">
        <div>
            <h1 class="text-xl font-black text-slate-900">Tinjau Permintaan Stok</h1>
            <p class="text-xs text-slate-500 font-mono">{{ $branchRequest->no_permintaan }}</p>
        </div>
        <a href="{{ route('branch_request.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900">
            &larr; Kembali
        </a>
    </div>

    <!-- Header Details Card -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-xs space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-100 pb-4">
            <div>
                <span class="text-xs font-bold text-indigo-600 uppercase tracking-wider">Surat Permintaan Barang</span>
                <h2 class="text-2xl font-black text-slate-900 font-mono mt-0.5">{{ $branchRequest->no_permintaan }}</h2>
            </div>
            <div class="flex items-center gap-2">
                @if($branchRequest->status === 'approved')
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">Disetujui</span>
                @elseif($branchRequest->status === 'rejected')
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800">Ditolak</span>
                @elseif($branchRequest->status === 'partial')
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800">Disetujui Sebagian</span>
                @else
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">Menunggu Persetujuan</span>
                @endif

                @if($branchRequest->prioritas === 'critical')
                    <span class="px-2.5 py-1 rounded-md text-xs font-bold bg-rose-100 text-rose-800 uppercase">Kritis</span>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
            <div>
                <p class="text-slate-400 font-medium">Cabang Pemohon:</p>
                <p class="font-bold text-slate-800 mt-0.5 text-sm">{{ $branchRequest->cabangPeminta->nama ?? '-' }}</p>
                <p class="text-slate-500 font-mono">{{ $branchRequest->cabangPeminta->kode_cabang ?? '' }}</p>
            </div>
            <div>
                <p class="text-slate-400 font-medium">Diajukan Oleh:</p>
                <p class="font-bold text-slate-800 mt-0.5 text-sm">{{ $branchRequest->userPeminta->name ?? '-' }}</p>
                <p class="text-slate-500">{{ date('d M Y H:i', strtotime($branchRequest->tanggal_permintaan)) }}</p>
            </div>
            <div>
                <p class="text-slate-400 font-medium">Keterangan Pemohon:</p>
                <p class="text-slate-700 mt-0.5 italic">{{ $branchRequest->keterangan ?: 'Tidak ada keterangan tambahan.' }}</p>
            </div>
        </div>

        @if($branchRequest->catatan_proses)
            <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-xl text-xs">
                <p class="font-bold text-slate-700">Catatan Proses Admin:</p>
                <p class="text-slate-600 mt-0.5">{{ $branchRequest->catatan_proses }}</p>
                @if($branchRequest->processor)
                    <p class="text-[10px] text-slate-400 mt-1">Diproses oleh: {{ $branchRequest->processor->name }} pada {{ date('d M Y H:i', strtotime($branchRequest->tanggal_diproses)) }}</p>
                @endif
            </div>
        @endif

        <!-- Items Table / Approval Form -->
        <form action="{{ route('branch_request.process', $branchRequest->id) }}" method="POST" class="space-y-6">
            @csrf

            <div class="border border-slate-200 rounded-xl overflow-hidden">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 border-b border-slate-200 font-semibold text-slate-600 uppercase">
                        <tr>
                            <th class="px-4 py-3">No</th>
                            <th class="px-4 py-3">Produk</th>
                            <th class="px-4 py-3 text-center">Stok Gudang</th>
                            <th class="px-4 py-3 text-center">Qty Diminta</th>
                            <th class="px-4 py-3 text-center">Qty Disetujui</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($branchRequest->items as $idx => $item)
                            <tr>
                                <td class="px-4 py-3 text-slate-400">{{ $idx + 1 }}</td>
                                <td class="px-4 py-3">
                                    <p class="font-bold text-slate-900">{{ $item->produk->nama ?? '-' }}</p>
                                    <p class="font-mono text-slate-400 text-[11px]">{{ $item->produk->kode_produk ?? '-' }}</p>
                                </td>
                                <td class="px-4 py-3 text-center font-bold text-slate-600">
                                    {{ $item->produk->stok ?? 0 }} Unit
                                </td>
                                <td class="px-4 py-3 text-center font-bold text-slate-900">
                                    {{ $item->jumlah_diminta }} Unit
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($branchRequest->status === 'pending' && auth()->user()->isAdmin())
                                        <input type="number" name="approved_qty[{{ $item->id }}]"
                                               value="{{ $item->jumlah_diminta }}" min="0" max="{{ $item->produk->stok ?? 9999 }}"
                                               class="w-20 px-2.5 py-1.5 text-center text-xs bg-slate-50 border border-slate-300 rounded-lg font-bold text-emerald-700 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                                    @else
                                        <span class="font-black text-sm {{ $item->jumlah_disetujui > 0 ? 'text-emerald-700' : 'text-slate-400' }}">
                                            {{ $item->jumlah_disetujui }} Unit
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($branchRequest->status === 'pending' && auth()->user()->isAdmin())
                <div class="p-5 bg-indigo-50/50 border border-indigo-100 rounded-xl space-y-4">
                    <h4 class="font-bold text-xs text-indigo-900 uppercase tracking-wider flex items-center gap-2">
                        <i class="fa-solid fa-user-shield"></i>
                        <span>Tindakan Persetujuan Head Office</span>
                    </h4>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Persetujuan / Alasan Penolakan</label>
                        <input type="text" name="catatan_proses" placeholder="e.g. Disetujui sesuai ketersediaan stok pusat"
                               class="w-full px-3.5 py-2 text-xs bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="submit" name="action" value="reject" onclick="return confirm('Tolak permintaan mutasi ini?');"
                                class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-lg transition shadow-xs">
                            <i class="fa-solid fa-xmark mr-1"></i> Tolak Permintaan
                        </button>
                        <button type="submit" name="action" value="approve" onclick="return confirm('Setujui permintaan dan transfer stok?');"
                                class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg transition shadow-xs">
                            <i class="fa-solid fa-check mr-1"></i> Setujui & Transfer Stok
                        </button>
                    </div>
                </div>
            @endif
        </form>
    </div>
</div>
@endsection
