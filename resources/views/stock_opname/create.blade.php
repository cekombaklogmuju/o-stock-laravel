@extends('layouts.app')

@section('title', 'Form Audit Stock Opname')

@section('content')
<div class="space-y-6" x-data="stockOpnameData()">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('stock-opname.index') }}" class="text-slate-400 hover:text-slate-600 transition">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Pelaksanaan Stock Opname</h1>
            </div>
            <p class="text-sm text-slate-500 mt-1">Audit fisik persediaan gudang. Sistem akan otomatis menyesuaikan saldo dan mencatat selisih.</p>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" @click="loadAllProducts()"
                    class="inline-flex items-center gap-2 px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl border border-slate-200 transition">
                <i class="fa-solid fa-list-check"></i>
                <span>Muat Semua Barang Rak</span>
            </button>
            <button type="button" @click="openScannerModal()"
                    class="inline-flex items-center gap-2 px-3.5 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-semibold text-xs rounded-xl border border-indigo-200 transition">
                <i class="fa-solid fa-camera"></i>
                <span>Scan Barcode Kamera</span>
            </button>
        </div>
    </div>

    <!-- Main Form -->
    <form action="{{ route('stock-opname.store') }}" method="POST" id="stockOpnameForm" @submit="validateSubmit($event)">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left 2 Cols: Item List & Barcode Scanner Area -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Barcode Card -->
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs space-y-4">
                    <h2 class="text-base font-bold text-slate-800 flex items-center gap-2">
                        <i class="fa-solid fa-barcode text-cyan-600"></i>
                        <span>Scan Barcode / Audit per Rak</span>
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                        <div class="sm:col-span-8 relative">
                            <input type="text" x-model="barcodeQuery" @keydown.enter.prevent="lookupBarcode()"
                                   placeholder="Scan barcode barang yang sedang dihitung di rak..."
                                   class="w-full text-sm rounded-xl border-slate-300 focus:border-cyan-500 focus:ring-cyan-500 px-4 py-2.5 border pr-10"
                                   id="barcodeInput" autofocus>
                            <button type="button" @click="lookupBarcode()" class="absolute right-2 top-2.5 text-slate-400 hover:text-cyan-600 px-2" title="Cari">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </button>
                        </div>
                        <div class="sm:col-span-4">
                            <button type="button" @click="openScannerModal()"
                                    class="w-full h-full flex items-center justify-center gap-2 px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-sm font-semibold transition">
                                <i class="fa-solid fa-camera"></i>
                                <span>Buka Kamera</span>
                            </button>
                        </div>
                    </div>

                    <div class="pt-2 border-t border-slate-100 flex items-center gap-2 text-xs">
                        <span class="text-slate-400 shrink-0">Atau pilih manual:</span>
                        <select @change="if($event.target.value) { addProductById($event.target.value); $event.target.value = ''; }"
                                class="text-xs rounded-lg border-slate-200 bg-slate-50 text-slate-700 py-1.5 px-2 focus:ring-cyan-500 focus:border-cyan-500">
                            <option value="">-- Pilih Barang yang Akan Diaudit --</option>
                            @foreach($produks as $p)
                                <option value="{{ $p->id }}">
                                    [{{ $p->lokasi_rak ?: 'Tanpa Rak' }}] {{ $p->kode_produk }} - {{ $p->nama }} (Sistem: {{ $p->stok }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <p x-show="errorMessage" x-text="errorMessage" class="text-xs text-rose-600 font-medium"></p>
                </div>

                <!-- Table of Scanned & Audited Items -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                    <div class="p-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                        <div class="font-bold text-sm text-slate-800 flex items-center gap-2">
                            <i class="fa-solid fa-clipboard-check text-slate-500"></i>
                            <span>Hasil Perhitungan Fisik di Rak</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-cyan-50 text-cyan-700 border border-cyan-200"
                                  x-text="items.length + ' SKU Diaudit'"></span>
                            <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full"
                                  :class="countVariance() > 0 ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200'"
                                  x-text="countVariance() + ' Ada Selisih'"></span>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-slate-50/50 text-slate-600 font-semibold border-b border-slate-200 text-xs uppercase">
                                <tr>
                                    <th class="px-4 py-3">Barang & Rak</th>
                                    <th class="px-3 py-3 text-center w-24">Stok Sistem</th>
                                    <th class="px-3 py-3 text-center w-32">Stok Fisik</th>
                                    <th class="px-3 py-3 text-center w-28">Selisih</th>
                                    <th class="px-3 py-3">Alasan Selisih</th>
                                    <th class="px-3 py-3 text-center w-12">Hapus</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <template x-for="(item, index) in items" :key="item.id_produk">
                                    <tr class="hover:bg-slate-50/50" :class="item.stok_fisik !== item.stok_sistem ? 'bg-amber-50/30' : ''">
                                        <td class="px-4 py-3">
                                            <div class="font-semibold text-slate-900" x-text="item.nama"></div>
                                            <div class="flex items-center gap-2 text-xs text-slate-500 mt-0.5">
                                                <span class="font-mono text-slate-600" x-text="item.kode_produk"></span>
                                                <span>&bull;</span>
                                                <span class="inline-flex items-center gap-1 font-medium text-cyan-700 bg-cyan-50 px-1.5 py-0.5 rounded">
                                                    <i class="fa-solid fa-table-cells"></i>
                                                    <span x-text="item.lokasi_rak || 'Tanpa Rak'"></span>
                                                </span>
                                            </div>
                                            <input type="hidden" :name="'items[' + index + '][id_produk]'" :value="item.id_produk">
                                        </td>
                                        <td class="px-3 py-3 text-center font-bold text-slate-600">
                                            <span x-text="item.stok_sistem"></span>
                                            <span class="text-[10px] text-slate-400 font-normal" x-text="item.satuan || 'Pcs'"></span>
                                        </td>
                                        <td class="px-3 py-3 text-center">
                                            <div class="flex items-center justify-center gap-1">
                                                <button type="button" @click="if(item.stok_fisik > 0) item.stok_fisik--"
                                                        class="w-7 h-7 flex items-center justify-center rounded bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold">-</button>
                                                <input type="number" :name="'items[' + index + '][stok_fisik]'" x-model.number="item.stok_fisik" min="0" required
                                                       class="w-16 text-center text-sm font-semibold rounded-md border-slate-300 py-1 px-1 focus:ring-cyan-500 focus:border-cyan-500 border">
                                                <button type="button" @click="item.stok_fisik++"
                                                        class="w-7 h-7 flex items-center justify-center rounded bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold">+</button>
                                            </div>
                                        </td>
                                        <td class="px-3 py-3 text-center">
                                            <template x-if="item.stok_fisik === item.stok_sistem">
                                                <span class="inline-flex items-center gap-1 text-xs font-semibold px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    <i class="fa-solid fa-check"></i> Cocok
                                                </span>
                                            </template>
                                            <template x-if="item.stok_fisik > item.stok_sistem">
                                                <span class="inline-flex items-center gap-1 text-xs font-bold px-2 py-0.5 rounded bg-blue-50 text-blue-700 border border-blue-200">
                                                    +<span x-text="item.stok_fisik - item.stok_sistem"></span> Surplus
                                                </span>
                                            </template>
                                            <template x-if="item.stok_fisik < item.stok_sistem">
                                                <span class="inline-flex items-center gap-1 text-xs font-bold px-2 py-0.5 rounded bg-rose-50 text-rose-700 border border-rose-200">
                                                    <span x-text="item.stok_fisik - item.stok_sistem"></span> Selisih
                                                </span>
                                            </template>
                                        </td>
                                        <td class="px-3 py-3">
                                            <input type="text" :name="'items[' + index + '][alasan]'" x-model="item.alasan"
                                                   placeholder="Alasan selisih (rusak/salah hitung)"
                                                   class="w-full text-xs rounded-md border-slate-200 py-1 px-2 focus:ring-cyan-500 focus:border-cyan-500 border"
                                                   :class="item.stok_fisik !== item.stok_sistem ? 'border-amber-400 bg-amber-50/50' : ''">
                                        </td>
                                        <td class="px-3 py-3 text-center">
                                            <button type="button" @click="removeItem(index)" class="text-slate-400 hover:text-rose-600 transition" title="Hapus">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                                <tr x-show="items.length === 0">
                                    <td colspan="6" class="px-4 py-12 text-center text-slate-400">
                                        <i class="fa-solid fa-clipboard-list text-3xl mb-2 text-slate-300"></i>
                                        <p class="text-sm font-medium text-slate-500">Belum ada barang di daftar audit</p>
                                        <p class="text-xs text-slate-400">Scan barcode rak atau tekan "Muat Semua Barang Rak" di atas.</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right 1 Col: Document Metadata & Submission -->
            <div class="space-y-6">
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs space-y-4">
                    <h2 class="text-base font-bold text-slate-800 flex items-center gap-2 border-b border-slate-100 pb-3">
                        <i class="fa-solid fa-file-invoice text-cyan-600"></i>
                        <span>Informasi Audit</span>
                    </h2>

                    <!-- No Dokumen -->
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">No. Opname</label>
                        <input type="text" name="no_opname" value="{{ old('no_opname', $generatedNo) }}" required
                               class="w-full text-sm font-semibold rounded-xl bg-slate-50 border-slate-300 px-3 py-2 border font-mono">
                    </div>

                    <!-- Keterangan / Periode Opname -->
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Catatan / Periode Audit</label>
                        <textarea name="keterangan" rows="3" placeholder="Contoh: Audit Fisik Berkala Akhir Bulan - Sektor Rak A & B"
                                  class="w-full text-sm rounded-xl border-slate-300 focus:ring-cyan-500 focus:border-cyan-500 px-3 py-2 border">{{ old('keterangan') }}</textarea>
                    </div>

                    <!-- Warehouse Info Badge -->
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 text-xs text-slate-600 space-y-1">
                        <div class="flex justify-between">
                            <span>Lokasi Gudang:</span>
                            <span class="font-bold text-slate-800">Gudang Utama (Central)</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Auditor:</span>
                            <span class="font-bold text-slate-800">{{ auth()->user()->name }}</span>
                        </div>
                    </div>

                    <!-- Variance Summary Alert -->
                    <div x-show="countVariance() > 0" class="p-3 bg-amber-50 rounded-xl border border-amber-200 text-xs text-amber-900 space-y-1">
                        <p class="font-bold flex items-center gap-1.5">
                            <i class="fa-solid fa-triangle-exclamation text-amber-600"></i>
                            <span>Perhatian Selisih Stok!</span>
                        </p>
                        <p>Ditemukan selisih pada <strong x-text="countVariance()"></strong> barang. Menyimpan form ini akan memperbarui saldo stok produk secara permanen dan mencatat penyesuaian di Kartu Stok.</p>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" :disabled="items.length === 0"
                            class="w-full py-3 px-4 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed text-white font-bold text-sm rounded-xl shadow-md shadow-indigo-600/20 transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>Simpan & Rekonsiliasi Stok</span>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Barcode Scanner Modal Component (Camera) -->
<x-barcode-scanner-modal />

@push('scripts')
<script src="{{ asset('js/barcode-scanner.js') }}"></script>
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('stockOpnameData', () => ({
        barcodeQuery: '',
        errorMessage: '',
        items: [],
        allProducts: @json($produks),

        init() {
            const scanner = new window.BarcodeScannerEngine({
                minBarcodeLength: 3,
                timeThreshold: 60,
                onScan: (scannedCode) => {
                    this.barcodeQuery = scannedCode;
                    this.lookupBarcode();
                }
            });
            scanner.init();

            window.addEventListener('barcode-scanned', (e) => {
                const code = e.detail;
                if (code) {
                    this.barcodeQuery = code;
                    this.lookupBarcode();
                }
            });
        },

        openScannerModal() {
            window.dispatchEvent(new CustomEvent('open-barcode-scanner'));
        },

        countVariance() {
            return this.items.filter(item => item.stok_fisik !== item.stok_sistem).length;
        },

        loadAllProducts() {
            this.items = this.allProducts.map(p => ({
                id_produk: p.id,
                kode_produk: p.kode_produk,
                nama: p.nama,
                lokasi_rak: p.lokasi_rak,
                satuan: p.satuan || 'Pcs',
                stok_sistem: p.stok,
                stok_fisik: p.stok,
                alasan: ''
            }));
        },

        lookupBarcode() {
            const query = this.barcodeQuery.trim();
            if (!query) return;

            this.errorMessage = '';

            const found = this.allProducts.find(p =>
                (p.barcode && p.barcode.toLowerCase() === query.toLowerCase()) ||
                p.kode_produk.toLowerCase() === query.toLowerCase()
            );

            if (found) {
                this.addItem(found);
                this.barcodeQuery = '';
                return;
            }

            fetch(`/api/v1/produk/lookup?barcode=${encodeURIComponent(query)}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.data) {
                        this.addItem(data.data);
                        this.barcodeQuery = '';
                    } else {
                        this.errorMessage = `Barang dengan barcode '${query}' tidak ditemukan di sistem.`;
                    }
                })
                .catch(() => {
                    this.errorMessage = 'Gagal menghubungi server untuk pencarian barcode.';
                });
        },

        addProductById(productId) {
            const found = this.allProducts.find(p => p.id == productId);
            if (found) {
                this.addItem(found);
            }
        },

        addItem(product) {
            const existing = this.items.find(item => item.id_produk == product.id);
            if (existing) {
                // Focus or highlight existing item
                this.errorMessage = `'${product.nama}' sudah ada dalam daftar audit.`;
            } else {
                this.items.push({
                    id_produk: product.id,
                    kode_produk: product.kode_produk,
                    nama: product.nama,
                    lokasi_rak: product.lokasi_rak,
                    satuan: product.satuan || 'Pcs',
                    stok_sistem: product.stok,
                    stok_fisik: product.stok,
                    alasan: ''
                });
            }
        },

        removeItem(index) {
            this.items.splice(index, 1);
        },

        validateSubmit(e) {
            if (this.items.length === 0) {
                e.preventDefault();
                alert('Silakan masukkan minimal 1 barang yang diaudit sebelum menyimpan!');
            }
        }
    }));
});
</script>
@endpush
@endsection
