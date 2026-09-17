@extends('layouts.app')

@section('title', 'Katalog Produk & Barcode')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-black text-slate-900">Katalog Produk & Barcode</h1>
            <p class="text-xs text-slate-500">Daftar produk, status persediaan, harga jual, dan kode barcode</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('produk.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-xs transition">
                <i class="fa-solid fa-plus"></i>
                <span>Tambah Produk</span>
            </a>
        </div>
    </div>

    <!-- Search & Filter Card -->
    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs flex flex-wrap items-center justify-between gap-4">
        <form action="{{ route('produk.index') }}" method="GET" class="flex flex-wrap items-center gap-3 flex-1">
            <div class="relative flex-1 min-w-[200px] max-w-md">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text" name="search" value="{{ $search }}"
                       placeholder="Cari nama, kode, atau barcode..."
                       class="w-full pl-9 pr-3 py-2 text-xs bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            </div>

            <select name="kategori_id" onchange="this.form.submit()"
                    class="py-2 px-3 text-xs bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                <option value="">Semua Kategori</option>
                @foreach($kategoris as $k)
                    <option value="{{ $k->id }}" {{ $kategoriId == $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 font-semibold uppercase tracking-wider">
                    <tr>
                        <th class="px-5 py-3.5">Kode / Barcode</th>
                        <th class="px-5 py-3.5">Nama Produk</th>
                        <th class="px-5 py-3.5">Kategori</th>
                        <th class="px-5 py-3.5">Supplier</th>
                        <th class="px-5 py-3.5 text-right">Harga Jual</th>
                        <th class="px-5 py-3.5 text-center">Stok</th>
                        <th class="px-5 py-3.5 text-center">Status</th>
                        <th class="px-5 py-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($produks as $p)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-3.5">
                                <div class="font-bold font-mono text-slate-900">{{ $p->kode_produk }}</div>
                                <div class="text-[11px] text-slate-400 font-mono flex items-center gap-1 mt-0.5">
                                    <i class="fa-solid fa-barcode text-xs"></i>
                                    <span>{{ $p->barcode ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 font-bold text-slate-800">{{ $p->nama }}</td>
                            <td class="px-5 py-3.5 text-slate-600">{{ $p->kategori->nama ?? '-' }}</td>
                            <td class="px-5 py-3.5 text-slate-600">{{ $p->supplier->nama ?? '-' }}</td>
                            <td class="px-5 py-3.5 text-right font-bold text-emerald-600">
                                Rp {{ number_format($p->harga_jual, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold {{ $p->stok <= 10 ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-700' }}">
                                    {{ $p->stok }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $p->status === 'aktif' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-700' }}">
                                    {{ $p->status }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <!-- Print Barcode -->
                                    <a href="{{ route('produk.barcode.print', $p->id) }}" target="_blank"
                                       class="p-1.5 text-slate-500 hover:text-emerald-600 transition" title="Cetak Barcode">
                                        <i class="fa-solid fa-barcode"></i>
                                    </a>
                                    <!-- Edit -->
                                    <a href="{{ route('produk.edit', $p->id) }}"
                                       class="p-1.5 text-slate-500 hover:text-indigo-600 transition" title="Edit">
                                        <i class="fa-regular fa-pen-to-square"></i>
                                    </a>
                                    <!-- Delete -->
                                    <form action="{{ route('produk.destroy', $p->id) }}" method="POST"
                                          onsubmit="return confirm('Hapus produk ini?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-500 hover:text-rose-600 transition" title="Hapus">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-8 text-center text-slate-400">
                                Tidak ada produk yang sesuai dengan pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($produks->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $produks->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
