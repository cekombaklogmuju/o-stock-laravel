<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Label Barcode & Rak - {{ $produk->nama }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; margin: 0; background: white; }
            .label-card { page-break-inside: avoid; border: 1px dashed #bbb !important; }
        }
    </style>
</head>
<body class="bg-slate-100 p-6 min-h-screen">

    <!-- Top Action Bar -->
    <div class="max-w-4xl mx-auto mb-6 flex items-center justify-between no-print bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
        <div class="flex items-center gap-3">
            <a href="{{ route('produk.index') }}" class="text-sm text-slate-600 hover:text-slate-900 font-medium">
                &larr; Kembali ke Inventori
            </a>
            <span class="text-slate-300">|</span>
            <span class="text-sm font-bold text-slate-800">{{ $produk->nama }}</span>
            <span class="text-xs bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded font-semibold">{{ $produk->lokasi_rak }}</span>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-lg shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-print"></i>
                <span>Cetak Label Stiker</span>
            </button>
        </div>
    </div>

    <!-- Printable Grid of Barcodes (12 stickers per sheet) -->
    <div class="max-w-4xl mx-auto bg-white p-8 rounded-2xl border border-slate-200 shadow-sm">
        <div class="text-center mb-6 no-print">
            <h2 class="text-lg font-bold text-slate-900">Preview Label Stiker Rak & Barcode (12 Label)</h2>
            <p class="text-xs text-slate-500 mt-1">Format stiker gudang dengan identitas Lokasi Rak, SKU, dan Barcode CODE128</p>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
            @for($i = 0; $i < 12; $i++)
                <div class="label-card border border-slate-300 rounded-lg p-3 text-center bg-white flex flex-col items-center justify-between shadow-2xs">
                    <div class="w-full flex items-center justify-between border-b border-slate-200 pb-1 mb-1">
                        <span class="text-[9px] font-black uppercase text-indigo-700 bg-indigo-50 px-1 rounded">
                            <i class="fa-solid fa-table-cells text-[8px]"></i> {{ $produk->lokasi_rak ?: 'GUDANG' }}
                        </span>
                        <span class="text-[9px] font-bold text-slate-500">{{ $produk->satuan ?: 'Pcs' }}</span>
                    </div>

                    <p class="text-[10px] font-bold text-slate-900 leading-tight line-clamp-2 max-w-[200px]">
                        {{ $produk->nama }}
                    </p>

                    <svg class="barcode-svg my-1" jsbarcode-value="{{ $produk->barcode ?? $produk->kode_produk }}"></svg>

                    <div class="w-full flex items-center justify-between text-[9px] font-mono text-slate-600 px-1 pt-0.5 border-t border-slate-100">
                        <span class="font-bold">{{ $produk->kode_produk }}</span>
                        <span class="text-slate-400 font-sans">O-Stock WH</span>
                    </div>
                </div>
            @endfor
        </div>
    </div>

    <script>
        JsBarcode(".barcode-svg").init({
            format: "CODE128",
            width: 1.3,
            height: 36,
            fontSize: 10,
            displayValue: true
        });
    </script>
</body>
</html>
