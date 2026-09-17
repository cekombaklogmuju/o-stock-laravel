@extends('layouts.app')

@section('title', 'Pengeluaran Barang (Stock-Out)')

@section('content')
<div class="space-y-6" x-data="stockOutData()">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('barang-keluar.index') }}" class="text-slate-400 hover:text-slate-600 transition">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Form Pengeluaran Barang</h1>
            </div>
            <p class="text-sm text-slate-500 mt-1">Scan barcode atau pilih barang untuk mutasi keluar dengan validasi stok otomatis</p>
        </div>
        <div class="flex items-center gap-2">
            <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 bg-slate-100 border border-slate-200 rounded-lg text-xs font-medium text-slate-600">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>USB/BT Scanner Siap</span>
            </div>
            <button type="button" @click="openScannerModal()"
                    class="inline-flex items-center gap-2 px-3.5 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-semibold text-xs rounded-xl border border-indigo-200 transition">
                <i class="fa-solid fa-camera"></i>
                <span>Scan Kamera HP/Webcam</span>
            </button>
        </div>
    </div>

    <!-- Main Form -->
    <form action="{{ route('barang-keluar.store') }}" method="POST" id="stockOutForm" @submit="validateSubmit($event)">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left 2 Cols: Item List & Barcode Scanner Area -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Barcode Card -->
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs space-y-4">
                    <h2 class="text-base font-bold text-slate-800 flex items-center gap-2">
                        <i class="fa-solid fa-barcode text-amber-600"></i>
                        <span>Scan Barcode / Ambil Barang dari Rak</span>
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                        <div class="sm:col-span-8 relative">
                            <input type="text" x-model="barcodeQuery" @keydown.enter.prevent="lookupBarcode()"
                                   placeholder="Scan barcode barang di rak atau ketik kode..."
                                   class="w-full text-sm rounded-xl border-slate-300 focus:border-amber-500 focus:ring-amber-500 px-4 py-2.5 border pr-10"
                                   id="barcodeInput" autofocus>
                            <button type="button" @click="lookupBarcode()" class="absolute right-2 top-2.5 text-slate-400 hover:text-amber-600 px-2" title="Cari">
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

                    <!-- Quick manual picker fallback -->
                    <div class="pt-2 border-t border-slate-100 flex items-center gap-2 text-xs">
                        <span class="text-slate-400 shrink-0">Atau pilih manual:</span>
                        <select @change="if($event.target.value) { addProductById($event.target.value); $event.target.value = ''; }"
                                class="text-xs rounded-lg border-slate-200 bg-slate-50 text-slate-700 py-1.5 px-2 focus:ring-amber-500 focus:border-amber-500">
                            <option value="">-- Pilih dari Stok Tersedia --</option>
                            @foreach($produks as $p)
                                <option value="{{ $p->id }}" {{ $p->stok <= 0 ? 'disabled' : '' }}>
                                    {{ $p->kode_produk }} - {{ $p->nama }} [Rak: {{ $p->lokasi_rak ?: '-' }}] (Sisa Stok: {{ $p->stok }} {{ $p->satuan ?: 'Pcs' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <p x-show="errorMessage" x-text="errorMessage" class="text-xs text-rose-600 font-medium"></p>
                </div>

                <!-- Table of Scanned Items -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                    <div class="p-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                        <div class="font-bold text-sm text-slate-800 flex items-center gap-2">
                            <i class="fa-solid fa-list-check text-slate-500"></i>
                            <span>Daftar Barang yang Dikeluarkan</span>
                        </div>
                        <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-700 border border-amber-200"
                              x-text="items.length + ' Barang / ' + totalQty() + ' Unit'"></span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-slate-50/50 text-slate-600 font-semibold border-b border-slate-200 text-xs uppercase">
                                <tr>
                                    <th class="px-4 py-3">Barang & Lokasi Rak</th>
                                    <th class="px-3 py-3 text-center">Sisa Stok</th>
                                    <th class="px-3 py-3 text-center w-32">Qty Keluar</th>
                                    <th class="px-3 py-3">Catatan</th>
                                    <th class="px-3 py-3 text-center w-12">Hapus</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <template x-for="(item, index) in items" :key="item.id_produk">
                                    <tr class="hover:bg-slate-50/50">
                                        <td class="px-4 py-3">
                                            <div class="font-semibold text-slate-900" x-text="item.nama"></div>
                                            <div class="flex items-center gap-2 text-xs text-slate-500 mt-0.5">
                                                <span class="font-mono text-slate-600" x-text="item.kode_produk"></span>
                                                <span>&bull;</span>
                                                <span class="inline-flex items-center gap-1 font-medium text-amber-700 bg-amber-50 px-1.5 py-0.5 rounded">
                                                    <i class="fa-solid fa-table-cells"></i>
                                                    <span x-text="item.lokasi_rak || 'Tanpa Rak'"></span>
                                                </span>
                                            </div>
                                            <input type="hidden" :name="'items[' + index + '][id_produk]'" :value="item.id_produk">
                                        </td>
                                        <td class="px-3 py-3 text-center">
                                            <span class="text-xs font-bold"
                                                  :class="item.stok < item.jumlah ? 'text-rose-600 font-black' : 'text-slate-700'"
                                                  x-text="item.stok + ' ' + (item.satuan || 'Pcs')"></span>
                                        </td>
                                        <td class="px-3 py-3 text-center">
                                            <div class="flex items-center justify-center gap-1">
                                                <button type="button" @click="if(item.jumlah > 1) item.jumlah--"
                                                        class="w-7 h-7 flex items-center justify-center rounded bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold">-</button>
                                                <input type="number" :name="'items[' + index + '][jumlah]'" x-model.number="item.jumlah" min="1" :max="item.stok"
                                                       class="w-16 text-center text-sm font-semibold rounded-md border-slate-300 py-1 px-1 focus:ring-amber-500 focus:border-amber-500 border"
                                                       :class="item.jumlah > item.stok ? 'border-rose-500 bg-rose-50 text-rose-700' : ''">
                                                <button type="button" @click="if(item.jumlah < item.stok) item.jumlah++"
                                                        class="w-7 h-7 flex items-center justify-center rounded bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold"
                                                        :disabled="item.jumlah >= item.stok">+</button>
                                            </div>
                                            <p x-show="item.jumlah > item.stok" class="text-[10px] text-rose-600 font-semibold mt-0.5">Melebihi stok!</p>
                                        </td>
                                        <td class="px-3 py-3">
                                            <input type="text" :name="'items[' + index + '][catatan]'" x-model="item.catatan"
                                                   placeholder="Keperluan / batch"
                                                   class="w-full text-xs rounded-md border-slate-200 py-1 px-2 focus:ring-amber-500 focus:border-amber-500 border">
                                        </td>
                                        <td class="px-3 py-3 text-center">
                                            <button type="button" @click="removeItem(index)" class="text-slate-400 hover:text-rose-600 transition" title="Hapus">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                                <tr x-show="items.length === 0">
                                    <td colspan="5" class="px-4 py-12 text-center text-slate-400">
                                        <i class="fa-solid fa-boxes-packing text-3xl mb-2 text-slate-300"></i>
                                        <p class="text-sm font-medium text-slate-500">Belum ada barang di daftar pengeluaran</p>
                                        <p class="text-xs text-slate-400">Scan barcode rak atau pilih barang manual di atas untuk menambahkan.</p>
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
                        <i class="fa-solid fa-file-invoice text-amber-600"></i>
                        <span>Informasi Pengeluaran</span>
                    </h2>

                    <!-- No Dokumen -->
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">No. Pengeluaran</label>
                        <input type="text" name="no_keluar" value="{{ old('no_keluar', $generatedNo) }}" required
                               class="w-full text-sm font-semibold rounded-xl bg-slate-50 border-slate-300 px-3 py-2 border font-mono">
                    </div>

                    <!-- Tujuan Pengeluaran -->
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Tujuan Pengeluaran</label>
                        <select name="tujuan" required class="w-full text-sm rounded-xl border-slate-300 focus:ring-amber-500 focus:border-amber-500 px-3 py-2 border">
                            <option value="pemakaian_internal" {{ old('tujuan') == 'pemakaian_internal' ? 'selected' : '' }}>Pemakaian Internal / Produksi</option>
                            <option value="distribusi" {{ old('tujuan') == 'distribusi' ? 'selected' : '' }}>Distribusi / Transfer Gudang</option>
                            <option value="penjualan_grosir" {{ old('tujuan') == 'penjualan_grosir' ? 'selected' : '' }}>Penjualan Grosir / B2B</option>
                            <option value="scrap_rusak" {{ old('tujuan') == 'scrap_rusak' ? 'selected' : '' }}>Scrap / Rusak / Expired</option>
                            <option value="lainnya" {{ old('tujuan') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                    </div>

                    <!-- Penerima / Divisi Pemohon -->
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Penerima / Divisi Pemohon</label>
                        <input type="text" name="penerima" value="{{ old('penerima') }}" placeholder="Contoh: Tim Teknisi / Bapak Budi"
                               class="w-full text-sm rounded-xl border-slate-300 focus:ring-amber-500 focus:border-amber-500 px-3 py-2 border">
                    </div>

                    <!-- Keterangan -->
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Catatan / Alasan Pengeluaran</label>
                        <textarea name="keterangan" rows="3" placeholder="Contoh: Pengambilan komponen perakitan workstation #04"
                                  class="w-full text-sm rounded-xl border-slate-300 focus:ring-amber-500 focus:border-amber-500 px-3 py-2 border">{{ old('keterangan') }}</textarea>
                    </div>

                    <!-- Warehouse Info Badge -->
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 text-xs text-slate-600 space-y-1">
                        <div class="flex justify-between">
                            <span>Gudang Asal:</span>
                            <span class="font-bold text-slate-800">Gudang Utama (Central)</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Petugas Logistik:</span>
                            <span class="font-bold text-slate-800">{{ auth()->user()->name }}</span>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" :disabled="items.length === 0 || hasOverStock()"
                            class="w-full py-3 px-4 bg-amber-600 hover:bg-amber-700 disabled:opacity-50 disabled:cursor-not-allowed text-white font-bold text-sm rounded-xl shadow-md shadow-amber-600/20 transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>Proses Pengeluaran Barang</span>
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
    Alpine.data('stockOutData', () => ({
        barcodeQuery: '',
        errorMessage: '',
        items: [],
        allProducts: @json($produks),

        init() {
            // Hardware barcode scanner
            const scanner = new window.BarcodeScannerEngine({
                minBarcodeLength: 3,
                timeThreshold: 60,
                onScan: (scannedCode) => {
                    this.barcodeQuery = scannedCode;
                    this.lookupBarcode();
                }
            });
            scanner.init();

            // Camera scanner event
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

        totalQty() {
            return this.items.reduce((sum, item) => sum + (parseInt(item.jumlah) || 0), 0);
        },

        hasOverStock() {
            return this.items.some(item => item.jumlah > item.stok);
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
            if (product.stok <= 0) {
                this.errorMessage = `Stok untuk '${product.nama}' saat ini KOSONG (0 unit) di gudang!`;
                return;
            }

            const existing = this.items.find(item => item.id_produk == product.id);
            if (existing) {
                if (existing.jumlah < product.stok) {
                    existing.jumlah++;
                } else {
                    this.errorMessage = `Jumlah pengeluaran untuk '${product.nama}' sudah mencapai batas stok maksimum (${product.stok} unit).`;
                }
            } else {
                this.items.push({
                    id_produk: product.id,
                    kode_produk: product.kode_produk,
                    nama: product.nama,
                    lokasi_rak: product.lokasi_rak,
                    satuan: product.satuan || 'Pcs',
                    stok: product.stok,
                    jumlah: 1,
                    catatan: ''
                });
            }
        },

        removeItem(index) {
            this.items.splice(index, 1);
        },

        validateSubmit(e) {
            if (this.items.length === 0) {
                e.preventDefault();
                alert('Silakan scan atau masukkan minimal 1 barang sebelum memproses pengeluaran!');
                return;
            }

            if (this.hasOverStock()) {
                e.preventDefault();
                alert('Terdapat barang dengan jumlah keluar melebihi stok yang tersedia!');
            }
        }
    }));
});
</script>
@endpush
@endsection
