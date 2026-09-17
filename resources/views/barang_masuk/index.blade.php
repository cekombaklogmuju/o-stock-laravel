@extends('layouts.app')

@section('title', 'Daftar Barang Masuk (Receiving)')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Penerimaan Barang Masuk (Stock-In)</h1>
            <p class="text-sm text-slate-500">Catatan penerimaan stok barang dari supplier ke dalam gudang</p>
        </div>
        <div>
            <a href="{{ route('barang-masuk.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm rounded-xl shadow-sm shadow-emerald-600/20 transition">
                <i class="fa-solid fa-plus"></i>
                <span>Catat Barang Masuk</span>
            </a>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
        <form action="{{ route('barang-masuk.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div class="lg:col-span-2">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari no. dokumen, no. surat jalan, keterangan..."
                       class="w-full text-sm rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2 border">
            </div>
            <div>
                <select name="supplier_id" class="w-full text-sm rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2 border">
                    <option value="">Semua Supplier</option>
                    @foreach($suppliers as $sup)
                        <option value="{{ $sup->id }}" {{ $supplierId == $sup->id ? 'selected' : '' }}>{{ $sup->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <input type="date" name="start_date" value="{{ $startDate }}" placeholder="Dari Tanggal"
                       class="w-full text-sm rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2 border">
            </div>
            <div class="flex items-center gap-2">
                <input type="date" name="end_date" value="{{ $endDate }}" placeholder="Sampai Tanggal"
                       class="w-full text-sm rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2 border">
                <button type="submit" class="px-3 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-lg text-sm transition">
                    <i class="fa-solid fa-filter"></i>
                </button>
                @if($search || $supplierId || $startDate || $endDate)
                    <a href="{{ route('barang-masuk.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-sm transition" title="Reset filter">
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
                        <th class="px-6 py-3.5">No. Dokumen</th>
                        <th class="px-6 py-3.5">Tanggal</th>
                        <th class="px-6 py-3.5">Supplier</th>
                        <th class="px-6 py-3.5">No. Surat Jalan</th>
                        <th class="px-6 py-3.5 text-center">Total Item</th>
                        <th class="px-6 py-3.5">Pencatat</th>
                        <th class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($barangMasuks as $bm)
                        <tr class="hover:bg-slate-50/75 transition">
                            <td class="px-6 py-4 font-semibold text-slate-900">
                                <a href="{{ route('barang-masuk.show', $bm->id) }}" class="text-indigo-600 hover:underline">
                                    {{ $bm->no_masuk }}
                                </a>
                            </td>
                            <td class="px-6 py-4">{{ \Carbon\Carbon::parse($bm->tanggal)->format('d M Y') }}</td>
                            <td class="px-6 py-4">
                                @if($bm->supplier)
                                    <span class="font-medium text-slate-800">{{ $bm->supplier->nama }}</span>
                                @else
                                    <span class="text-slate-400 italic">Non-Supplier / Internal</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                {{ $bm->no_surat_jalan ?: '-' }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    {{ $bm->items->sum('jumlah') }} unit ({{ $bm->items->count() }} SKU)
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-500 text-xs">
                                {{ $bm->creator ? $bm->creator->name : '-' }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('barang-masuk.show', $bm->id) }}"
                                   class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 transition">
                                    <i class="fa-solid fa-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                <i class="fa-solid fa-truck-ramp-box text-4xl mb-3 text-slate-300"></i>
                                <p class="text-base font-semibold text-slate-600">Belum ada transaksi barang masuk</p>
                                <p class="text-sm text-slate-400 mt-1">Klik tombol "Catat Barang Masuk" untuk mulai menerima stok.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($barangMasuks->hasPages())
            <div class="px-6 py-4 border-t border-slate-200">
                {{ $barangMasuks->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
