@extends('layouts.app')

@section('title', 'Daftar Stock Opname (Audit Fisik)')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Stock Opname & Rekonsiliasi Gudang</h1>
            <p class="text-sm text-slate-500">Pemeriksaan fisik stok di rak secara berkala dan penyesuaian selisih otomatis</p>
        </div>
        <div>
            <a href="{{ route('stock-opname.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-xl shadow-sm shadow-indigo-600/20 transition">
                <i class="fa-solid fa-clipboard-check"></i>
                <span>Mulai Stock Opname Baru</span>
            </a>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
        <form action="{{ route('stock-opname.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div class="lg:col-span-2">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari no. opname, catatan audit..."
                       class="w-full text-sm rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2 border">
            </div>
            <div>
                <input type="date" name="start_date" value="{{ $startDate }}"
                       class="w-full text-sm rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2 border">
            </div>
            <div class="flex items-center gap-2">
                <input type="date" name="end_date" value="{{ $endDate }}"
                       class="w-full text-sm rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2 border">
                <button type="submit" class="px-3 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-lg text-sm transition">
                    <i class="fa-solid fa-filter"></i>
                </button>
                @if($search || $startDate || $endDate)
                    <a href="{{ route('stock-opname.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-sm transition" title="Reset filter">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-slate-700 font-semibold border-b border-slate-200 text-xs uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-3.5">No. Opname</th>
                        <th class="px-6 py-3.5">Tanggal Audit</th>
                        <th class="px-6 py-3.5 text-center">Jumlah SKU Diperiksa</th>
                        <th class="px-6 py-3.5 text-center">Barang Selisih</th>
                        <th class="px-6 py-3.5">Auditor / Petugas</th>
                        <th class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($stockOpnames as $so)
                        @php
                            $varianceItems = $so->items->where('selisih', '!=', 0);
                        @endphp
                        <tr class="hover:bg-slate-50/75 transition">
                            <td class="px-6 py-4 font-semibold text-slate-900">
                                <a href="{{ route('stock-opname.show', $so->id) }}" class="text-indigo-600 hover:underline font-mono">
                                    {{ $so->no_opname }}
                                </a>
                            </td>
                            <td class="px-6 py-4">{{ \Carbon\Carbon::parse($so->tanggal)->format('d M Y') }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="font-semibold text-slate-800">{{ $so->items->count() }} SKU</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($varianceItems->count() > 0)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                        <i class="fa-solid fa-triangle-exclamation"></i>
                                        {{ $varianceItems->count() }} Ada Selisih
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <i class="fa-solid fa-check"></i>
                                        Cocok 100%
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-slate-500 text-xs">
                                {{ $so->creator ? $so->creator->name : '-' }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('stock-opname.show', $so->id) }}"
                                   class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 transition">
                                    <i class="fa-solid fa-eye"></i> Hasil Audit
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                <i class="fa-solid fa-clipboard-check text-4xl mb-3 text-slate-300"></i>
                                <p class="text-base font-semibold text-slate-600">Belum ada audit stock opname</p>
                                <p class="text-sm text-slate-400 mt-1">Lakukan audit fisik berkala dengan menekan tombol "Mulai Stock Opname Baru".</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($stockOpnames->hasPages())
            <div class="px-6 py-4 border-t border-slate-200">
                {{ $stockOpnames->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
