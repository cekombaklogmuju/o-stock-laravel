@extends('layouts.app')

@section('title', 'Data Barang & Lokasi Rak')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Data Barang & Lokasi Rak Gudang</h1>
            <p class="text-sm text-slate-500">Master inventori barang, lokasi bin/rak penyimpanan, barcode label, dan batas minimum</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('produk.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-sm transition">
                <i class="fa-solid fa-plus"></i>
                <span>Tambah Barang Baru</span>
            </a>
        </div>
    </div>

    <!-- Search & Filter Card -->
    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
        <form action="{{ route('produk.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div class="lg:col-span-2 relative">
                <input type="text" name="search" value="{{ $search }}"
                       placeholder="Cari nama barang, kode SKU, barcode, atau rak..."
                       class="w-full pl-3 pr-3 py-2 text-sm bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            </div>

            <div>
                <select name="kategori_id" onchange="this.form.submit()"
                        class="w-full py-2 px-3 text-sm bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    <option value="">Semua Kategori</option>
                    @foreach($kategoris as $k)
                        <option value="{{ $k->id }}" {{ $kategoriId == $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <input type="text" name="lokasi_rak" value="{{ $lokasiRak }}" placeholder="Filter Rak (mis: Rak A)"
                       class="w-full py-2 px-3 text-sm bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            </div>

            <div class="flex items-center gap-2">
                <label class="flex items-center gap-2 text-xs font-semibold text-slate-600 cursor-pointer">
                    <input type="checkbox" name="low_stock" value="1" {{ $lowStock ? 'checked' : '' }} onchange="this.form.submit()"
                           class="rounded border-slate-300 text-rose-600 focus:ring-rose-500">
                    <span>Stok Menipis</span>
                </label>

                <button type="submit" class="px-3 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-lg text-sm transition">
                    <i class="fa-solid fa-filter"></i>
                </button>
                @if($search || $kategoriId || $lokasiRak || $lowStock)
                    <a href="{{ route('produk.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-sm transition" title="Reset filter">
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
                        <th class="px-5 py-3.5">Kode SKU / Barcode</th>
                        <th class="px-5 py-3.5">Nama Barang</th>
                        <th class="px-5 py-3.5">Lokasi Rak</th>
                        <th class="px-5 py-3.5">Kategori</th>
                        <th class="px-5 py-3.5 text-center">Stok Fisik</th>
                        <th class="px-5 py-3.5 text-center">Batas Min</th>
                        <th class="px-5 py-3.5 text-center">Status</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($produks as $p)
                        <tr class="hover:bg-slate-50/75 transition">
                            <td class="px-5 py-3.5">
                                <div class="font-bold font-mono text-slate-900">{{ $p->kode_produk }}</div>
                                <div class="text-[11px] text-slate-400 font-mono flex items-center gap-1 mt-0.5">
                                    <i class="fa-solid fa-barcode text-xs"></i>
                                    <span>{{ $p->barcode ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="font-bold text-slate-900">{{ $p->nama }}</span>
                                <p class="text-xs text-slate-400">{{ $p->supplier->nama ?? '-' }}</p>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 border border-indigo-100">
                                    <i class="fa-solid fa-table-cells text-indigo-500"></i>
                                    {{ $p->lokasi_rak ?: 'Tanpa Rak' }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-slate-600 text-xs">{{ $p->kategori->nama ?? '-' }}</td>
                            <td class="px-5 py-3.5 text-center">
                                @if($p->isLowStock())
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                        <i class="fa-solid fa-triangle-exclamation"></i>
                                        {{ $p->stok }} {{ $p->satuan ?: 'Pcs' }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-800">
                                        {{ $p->stok }} {{ $p->satuan ?: 'Pcs' }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-center text-xs text-slate-500 font-mono">
                                {{ $p->stok_minimum ?? 5 }} {{ $p->satuan ?: 'Pcs' }}
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $p->status === 'aktif' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-700' }}">
                                    {{ $p->status }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <!-- Print Barcode -->
                                    <a href="{{ route('produk.barcode.print', $p->id) }}" target="_blank"
                                       class="p-2 text-slate-500 hover:text-emerald-600 transition" title="Cetak Label Barcode & Rak">
                                        <i class="fa-solid fa-barcode text-sm"></i>
                                    </a>
                                    <!-- Edit -->
                                    <a href="{{ route('produk.edit', $p->id) }}"
                                       class="p-2 text-slate-500 hover:text-indigo-600 transition" title="Edit Barang">
                                        <i class="fa-regular fa-pen-to-square text-sm"></i>
                                    </a>
                                    <!-- Delete -->
                                    <form action="{{ route('produk.destroy', $p->id) }}" method="POST"
                                          onsubmit="return confirm('Hapus barang ini dari inventori?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-slate-500 hover:text-rose-600 transition" title="Hapus">
                                            <i class="fa-regular fa-trash-can text-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-12 text-center text-slate-400">
                                <i class="fa-solid fa-boxes-stacked text-3xl mb-2 text-slate-300"></i>
                                <p class="text-sm font-semibold text-slate-600">Tidak ada barang yang cocok dengan kriteria filter</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($produks->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $produks->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
