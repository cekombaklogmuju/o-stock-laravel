@extends('layouts.app')

@section('title', 'Permintaan Stok Cabang')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-black text-slate-900">Permintaan Stok Cabang</h1>
            <p class="text-xs text-slate-500">Pengajuan mutasi persediaan barang antar cabang dan persetujuan pusat</p>
        </div>
        <a href="{{ route('branch_request.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-xs transition">
            <i class="fa-solid fa-plus"></i>
            <span>Ajukan Permintaan Stok</span>
        </a>
    </div>

    <!-- Filter Status Tabs -->
    <div class="flex items-center gap-2 border-b border-slate-200 pb-3 overflow-x-auto text-xs font-bold">
        <a href="{{ route('branch_request.index') }}"
           class="px-3.5 py-1.5 rounded-lg transition {{ empty($status) ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
            Semua Permintaan
        </a>
        <a href="{{ route('branch_request.index', ['status' => 'pending']) }}"
           class="px-3.5 py-1.5 rounded-lg transition {{ $status === 'pending' ? 'bg-amber-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
            Menunggu Persetujuan
        </a>
        <a href="{{ route('branch_request.index', ['status' => 'approved']) }}"
           class="px-3.5 py-1.5 rounded-lg transition {{ $status === 'approved' ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
            Disetujui Penuh
        </a>
        <a href="{{ route('branch_request.index', ['status' => 'partial']) }}"
           class="px-3.5 py-1.5 rounded-lg transition {{ $status === 'partial' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
            Sebagian
        </a>
        <a href="{{ route('branch_request.index', ['status' => 'rejected']) }}"
           class="px-3.5 py-1.5 rounded-lg transition {{ $status === 'rejected' ? 'bg-rose-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
            Ditolak
        </a>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 font-semibold uppercase tracking-wider">
                    <tr>
                        <th class="px-5 py-3.5">No. Permintaan</th>
                        <th class="px-5 py-3.5">Tanggal</th>
                        <th class="px-5 py-3.5">Cabang Peminta</th>
                        <th class="px-5 py-3.5">Diajukan Oleh</th>
                        <th class="px-5 py-3.5 text-center">Prioritas</th>
                        <th class="px-5 py-3.5 text-center">Status</th>
                        <th class="px-5 py-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($requests as $r)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-3.5 font-bold font-mono text-slate-900">{{ $r->no_permintaan }}</td>
                            <td class="px-5 py-3.5 text-slate-600">{{ date('d M Y H:i', strtotime($r->tanggal_permintaan)) }}</td>
                            <td class="px-5 py-3.5 font-semibold text-slate-800">{{ $r->cabangPeminta->nama ?? '-' }}</td>
                            <td class="px-5 py-3.5 text-slate-600">{{ $r->userPeminta->name ?? '-' }}</td>
                            <td class="px-5 py-3.5 text-center">
                                @if($r->prioritas === 'critical')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-extrabold uppercase bg-rose-100 text-rose-800">Kritis</span>
                                @elseif($r->prioritas === 'urgent')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-amber-100 text-amber-800">Mendesak</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium uppercase bg-slate-100 text-slate-700">Normal</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                @if($r->status === 'approved')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">Disetujui</span>
                                @elseif($r->status === 'rejected')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-100 text-rose-800">Ditolak</span>
                                @elseif($r->status === 'partial')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-100 text-blue-800">Sebagian</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800">Menunggu</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <a href="{{ route('branch_request.show', $r->id) }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-1 bg-slate-100 hover:bg-indigo-50 hover:text-indigo-600 text-slate-700 font-semibold rounded-lg text-xs transition">
                                    <i class="fa-solid fa-clipboard-check"></i>
                                    <span>Tinjau</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-8 text-center text-slate-400">
                                Belum ada permohonan stok cabang.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($requests->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $requests->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
