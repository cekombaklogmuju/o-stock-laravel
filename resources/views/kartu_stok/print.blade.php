<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Kartu Stok - O-Stock</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; margin: 0; background: white; }
        }
    </style>
</head>
<body class="bg-slate-100 p-6 min-h-screen">

    <div class="max-w-5xl mx-auto mb-6 flex items-center justify-between no-print bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
        <div class="flex items-center gap-3">
            <button onclick="window.history.back()" class="text-xs text-slate-600 hover:text-slate-900 font-bold">
                &larr; Kembali
            </button>
            <span class="text-slate-300">|</span>
            <span class="text-xs font-bold text-slate-800">Laporan Histori Kartu Stok</span>
        </div>
        <button onclick="window.print()" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-lg shadow-sm flex items-center gap-2">
            <i class="fa-solid fa-print"></i>
            <span>Cetak Dokumen</span>
        </button>
    </div>

    <div class="max-w-5xl mx-auto bg-white p-8 sm:p-10 rounded-2xl border border-slate-200 shadow-sm space-y-6">
        <!-- Header -->
        <div class="text-center border-b border-slate-200 pb-6">
            <h1 class="text-2xl font-black text-slate-900 uppercase tracking-wide">LAPORAN BUKU KARTU STOK</h1>
            <p class="text-xs text-slate-500 mt-1">O-Stock Multi-Branch Inventory Management</p>
            <div class="flex items-center justify-center gap-4 text-xs font-medium text-slate-600 mt-3">
                @if($selectedCabang)
                    <span>Cabang: <strong>{{ $selectedCabang->nama }}</strong></span>
                @endif
                @if($selectedProduk)
                    <span>Produk: <strong>{{ $selectedProduk->nama }} ({{ $selectedProduk->kode_produk }})</strong></span>
                @endif
                @if($startDate || $endDate)
                    <span>Periode: <strong>{{ $startDate ?? 'Awal' }} s/d {{ $endDate ?? 'Sekarang' }}</strong></span>
                @endif
            </div>
        </div>

        <table class="w-full text-left text-xs border-collapse">
            <thead>
                <tr class="border-b-2 border-slate-900 text-slate-800 font-bold uppercase">
                    <th class="py-2.5 px-2">Tanggal</th>
                    <th class="py-2.5 px-2">No. Bukti</th>
                    <th class="py-2.5 px-2">Produk</th>
                    <th class="py-2.5 px-2">Cabang</th>
                    <th class="py-2.5 px-2">Keterangan</th>
                    <th class="py-2.5 px-2 text-center">Masuk</th>
                    <th class="py-2.5 px-2 text-center">Keluar</th>
                    <th class="py-2.5 px-2 text-center">Saldo</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse($records as $r)
                    <tr>
                        <td class="py-2.5 px-2 text-slate-600">{{ date('d/m/Y', strtotime($r->tanggal)) }}</td>
                        <td class="py-2.5 px-2 font-mono font-bold">{{ $r->no_bukti }}</td>
                        <td class="py-2.5 px-2 font-medium">{{ $r->produk->nama ?? '-' }}</td>
                        <td class="py-2.5 px-2 text-slate-600">{{ $r->cabang->nama ?? '-' }}</td>
                        <td class="py-2.5 px-2 text-slate-600">{{ $r->keterangan }}</td>
                        <td class="py-2.5 px-2 text-center font-bold text-emerald-700">{{ $r->stok_masuk > 0 ? $r->stok_masuk : '-' }}</td>
                        <td class="py-2.5 px-2 text-center font-bold text-rose-700">{{ $r->stok_keluar > 0 ? $r->stok_keluar : '-' }}</td>
                        <td class="py-2.5 px-2 text-center font-black text-slate-900">{{ $r->saldo_akhir }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="py-8 text-center text-slate-400">Tidak ada data untuk periode ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</body>
</html>
