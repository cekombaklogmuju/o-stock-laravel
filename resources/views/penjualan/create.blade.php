@extends('layouts.app')

@section('title', 'Kasir & Scan Barcode')

@section('content')
<div class="space-y-6" x-data="posApp()">

    <!-- Header & Scan Trigger Banner -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-black text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-barcode text-emerald-600"></i>
                <span>Kasir Transaksi & Pemindai Barcode</span>
            </h1>
            <p class="text-xs text-slate-500">Pindai barcode produk via alat scanner atau kamera untuk menambahkan ke keranjang</p>
        </div>

        <div class="flex items-center gap-2">
            <button type="button" @click="$dispatch('open-barcode-modal')"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white text-xs font-bold rounded-xl shadow-md transition">
                <i class="fa-solid fa-camera"></i>
                <span>Scan dengan Kamera</span>
            </button>
        </div>
    </div>

    <form action="{{ route('penjualan.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Top Order Info Card -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">No. Invoice</label>
                    <input type="text" name="no_invoice" value="{{ old('no_invoice', $generatedInvoice) }}" required readonly
                           class="w-full px-3 py-2 text-xs bg-slate-100 border border-slate-300 rounded-lg font-mono font-bold text-slate-800">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Cabang</label>
                    <select name="id_cabang" required
                            class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        @foreach($kantors as $k)
                            <option value="{{ $k->id }}" {{ $selectedBranchId == $k->id ? 'selected' : '' }}>
                                {{ $k->nama }} ({{ $k->kode_cabang }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Konsumen / Pelanggan</label>
                    <select name="id_konsumen" required
                            class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        <option value="">Pilih Konsumen</option>
                        @foreach($konsumens as $c)
                            <option value="{{ $c->id }}" {{ old('id_konsumen') == $c->id ? 'selected' : '' }}>{{ $c->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Salesman</label>
                    <select name="id_salesman" required
                            class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        <option value="">Pilih Salesman</option>
                        @foreach($salesmen as $s)
                            <option value="{{ $s->id }}" {{ old('id_salesman') == $s->id ? 'selected' : '' }}>{{ $s->nama }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Tanggal Transaksi</label>
                    <input type="date" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" required
                           class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Catatan Faktur (Opsional)</label>
                    <input type="text" name="catatan" value="{{ old('catatan') }}" placeholder="e.g. Pembayaran tempo 14 hari atau tunai"
                           class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                </div>
            </div>
        </div>

        <!-- Barcode Input Box (Quick scan field) -->
        <div class="bg-gradient-to-r from-emerald-950 to-slate-900 rounded-2xl p-5 text-white shadow-md flex flex-col sm:flex-row items-center gap-4">
            <div class="flex-1 w-full">
                <label class="block text-[11px] font-bold text-emerald-300 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                    <i class="fa-solid fa-barcode"></i>
                    <span>Input Cepat / Target Scanner Barcode:</span>
                </label>
                <div class="relative">
                    <input type="text" x-model="scanInput" @keydown.enter.prevent="lookupBarcode(scanInput)"
                           placeholder="Arahkan scanner ke sini atau ketik barcode lalu tekan Enter..."
                           class="w-full pl-4 pr-24 py-3 bg-slate-900 border-2 border-emerald-500 rounded-xl text-white font-mono text-sm placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                    <button type="button" @click="lookupBarcode(scanInput)"
                            class="absolute right-2 top-2 bottom-2 px-4 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-lg transition">
                        Tambah
                    </button>
                </div>
                <p class="text-[10px] text-slate-400 mt-1.5 flex items-center gap-1">
                    <i class="fa-solid fa-circle-info text-emerald-400"></i>
                    Hardware scanner USB/Bluetooth otomatis menambahkan item tanpa perlu mengklik input ini terlebih dahulu.
                </p>
            </div>
        </div>

        <!-- Cart Items Table Card -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="font-bold text-sm text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-cart-shopping text-indigo-600"></i>
                    <span>Item Penjualan (<span x-text="items.length"></span> Produk)</span>
                </h3>

                <button type="button" @click="addEmptyItem()"
                        class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-lg transition">
                    + Pilih Manual
                </button>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-semibold uppercase">
                        <tr>
                            <th class="px-4 py-3">Produk</th>
                            <th class="px-4 py-3 text-right">Harga Satuan</th>
                            <th class="px-4 py-3 text-center">Jumlah (Qty)</th>
                            <th class="px-4 py-3 text-center">Diskon (%)</th>
                            <th class="px-4 py-3 text-right">Subtotal</th>
                            <th class="px-4 py-3 text-center">Hapus</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <template x-for="(item, index) in items" :key="index">
                            <tr class="hover:bg-slate-50/50">
                                <td class="px-4 py-3">
                                    <input type="hidden" :name="'items[' + index + '][id_produk]'" :value="item.id_produk">
                                    <p class="font-bold text-slate-900" x-text="item.nama"></p>
                                    <p class="font-mono text-slate-400 text-[11px]" x-text="item.kode_produk + (item.barcode ? ' | ' + item.barcode : '')"></p>
                                </td>

                                <td class="px-4 py-3 text-right">
                                    <input type="number" :name="'items[' + index + '][harga]'" x-model.number="item.harga" min="0" required
                                           class="w-28 px-2 py-1.5 text-right text-xs bg-slate-50 border border-slate-300 rounded-lg font-bold text-slate-800">
                                </td>

                                <td class="px-4 py-3 text-center">
                                    <div class="inline-flex items-center border border-slate-300 rounded-lg overflow-hidden">
                                        <button type="button" @click="if(item.jumlah > 1) item.jumlah--" class="px-2.5 py-1 bg-slate-100 text-slate-600 hover:bg-slate-200 font-bold">-</button>
                                        <input type="number" :name="'items[' + index + '][jumlah]'" x-model.number="item.jumlah" min="1" required
                                               class="w-14 text-center text-xs py-1 font-bold border-x border-slate-300">
                                        <button type="button" @click="item.jumlah++" class="px-2.5 py-1 bg-slate-100 text-slate-600 hover:bg-slate-200 font-bold">+</button>
                                    </div>
                                </td>

                                <td class="px-4 py-3 text-center">
                                    <input type="number" :name="'items[' + index + '][discount]'" x-model.number="item.discount" min="0" max="100"
                                           class="w-16 px-2 py-1.5 text-center text-xs bg-slate-50 border border-slate-300 rounded-lg">
                                </td>

                                <td class="px-4 py-3 text-right font-black text-emerald-600 text-sm">
                                    Rp <span x-text="formatNumber(calculateSubtotal(item))"></span>
                                </td>

                                <td class="px-4 py-3 text-center">
                                    <button type="button" @click="removeItem(index)" class="text-slate-400 hover:text-rose-600 p-1 transition">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>
                                </td>
                            </tr>
                        </template>

                        <tr x-show="items.length === 0">
                            <td colspan="6" class="px-4 py-12 text-center text-slate-400">
                                <i class="fa-solid fa-barcode text-4xl mb-2 text-slate-300 block"></i>
                                Keranjang masih kosong. Pindai barcode produk atau klik "+ Pilih Manual".
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Grand Total Summary Banner -->
            <div class="p-5 bg-slate-900 rounded-2xl text-white flex flex-col sm:flex-row items-center justify-between gap-4 mt-6">
                <div>
                    <span class="text-xs text-slate-400 uppercase tracking-wider font-semibold">Total Pembayaran</span>
                    <h2 class="text-3xl font-black text-emerald-400 mt-0.5">
                        Rp <span x-text="formatNumber(calculateGrandTotal())"></span>
                    </h2>
                </div>

                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <a href="{{ route('penjualan.index') }}" class="px-4 py-3 text-xs font-semibold text-slate-400 hover:text-white transition text-center flex-1 sm:flex-none">
                        Batal
                    </a>
                    <button type="submit" :disabled="items.length === 0"
                            class="px-8 py-3 bg-emerald-500 hover:bg-emerald-600 disabled:opacity-50 text-white text-sm font-black rounded-xl shadow-lg shadow-emerald-500/30 transition text-center flex-1 sm:flex-none">
                        <i class="fa-solid fa-check mr-2"></i> Proses & Simpan Invoice
                    </button>
                </div>
            </div>
        </div>
    </form>

    <!-- Barcode Scanner Modal Component -->
    <x-barcode-scanner-modal />

</div>

@push('scripts')
<script src="{{ asset('js/barcode-scanner.js') }}"></script>
<script>
    function posApp() {
        return {
            scanInput: '',
            items: [],
            availableProducts: @json($produks),

            init() {
                // Initialize audio feedback and hardware scanner
                window.scannerAudio = new BarcodeScannerEngine({
                    onScan: (barcode) => {
                        this.lookupBarcode(barcode);
                    }
                });

                // Global function for camera modal callback
                window.handleBarcodeScanned = (barcode) => {
                    this.lookupBarcode(barcode);
                };
            },

            lookupBarcode(code) {
                code = (code || '').trim();
                if (!code) return;

                // 1. Try in-memory first for ultra-fast instant match
                const match = this.availableProducts.find(p => p.barcode === code || p.kode_produk === code);
                if (match) {
                    this.addProductToCart(match);
                    this.scanInput = '';
                    if (window.scannerAudio) window.scannerAudio.playBeep();
                    return;
                }

                // 2. Query lookup API
                fetch(`{{ url('api/v1/produk/lookup') }}?barcode=${encodeURIComponent(code)}`)
                    .then(res => res.json())
                    .then(res => {
                        if (res.success && res.data) {
                            this.addProductToCart(res.data);
                            this.scanInput = '';
                            if (window.scannerAudio) window.scannerAudio.playBeep();
                        } else {
                            alert(`Produk dengan barcode '${code}' tidak ditemukan di sistem.`);
                        }
                    })
                    .catch(err => {
                        alert('Gagal mencari barcode: ' + err);
                    });
            },

            addProductToCart(product) {
                const existing = this.items.find(i => i.id_produk === product.id);
                if (existing) {
                    existing.jumlah += 1;
                } else {
                    this.items.push({
                        id_produk: product.id,
                        nama: product.nama,
                        kode_produk: product.kode_produk,
                        barcode: product.barcode,
                        harga: Number(product.harga_jual),
                        jumlah: 1,
                        discount: 0
                    });
                }
            },

            addEmptyItem() {
                if (this.availableProducts.length > 0) {
                    this.addProductToCart(this.availableProducts[0]);
                }
            },

            removeItem(idx) {
                this.items.splice(idx, 1);
            },

            calculateSubtotal(item) {
                const sub = (item.harga * item.jumlah) * (1 - (item.discount || 0) / 100);
                return Math.max(0, sub);
            },

            calculateGrandTotal() {
                return this.items.reduce((total, item) => total + this.calculateSubtotal(item), 0);
            },

            formatNumber(num) {
                return Number(num || 0).toLocaleString('id-ID');
            }
        };
    }
</script>
@endpush
@endsection
