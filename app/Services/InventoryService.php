<?php

namespace App\Services;

use App\Models\BarangKeluar;
use App\Models\BarangKeluarItem;
use App\Models\BarangMasuk;
use App\Models\BarangMasukItem;
use App\Models\KartuStok;
use App\Models\Produk;
use App\Models\StockOpname;
use App\Models\StockOpnameItem;
use Exception;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    /**
     * Process incoming goods (Barang Masuk) to the warehouse.
     */
    public function processStockIn(array $items, string $noMasuk, ?int $supplierId, ?string $suratJalan, ?string $keterangan, ?int $userId): BarangMasuk
    {
        return DB::transaction(function () use ($items, $noMasuk, $supplierId, $suratJalan, $keterangan, $userId) {
            $barangMasuk = BarangMasuk::create([
                'no_masuk' => $noMasuk,
                'tanggal' => now()->toDateString(),
                'id_supplier' => $supplierId,
                'no_surat_jalan' => $suratJalan,
                'keterangan' => $keterangan,
                'created_by' => $userId,
            ]);

            foreach ($items as $item) {
                $produk = Produk::lockForUpdate()->findOrFail($item['id_produk']);
                $qty = (int) $item['jumlah'];

                $newSaldo = $produk->stok + $qty;
                $produk->stok = $newSaldo;
                $produk->save();

                BarangMasukItem::create([
                    'id_barang_masuk' => $barangMasuk->id,
                    'id_produk' => $produk->id,
                    'jumlah' => $qty,
                    'catatan' => $item['catatan'] ?? null,
                ]);

                KartuStok::create([
                    'id_produk' => $produk->id,
                    'id_cabang' => 1, // Single central warehouse
                    'tanggal' => now()->toDateString(),
                    'no_bukti' => $noMasuk,
                    'keterangan' => 'Penerimaan barang: ' . ($suratJalan ? "SJ #{$suratJalan} " : '') . ($keterangan ?: 'Stok Masuk Gudang'),
                    'stok_masuk' => $qty,
                    'stok_keluar' => 0,
                    'saldo_akhir' => $newSaldo,
                ]);
            }

            return $barangMasuk;
        });
    }

    /**
     * Process outgoing goods (Barang Keluar) from the warehouse.
     */
    public function processStockOut(array $items, string $noKeluar, string $tujuan, ?string $penerima, ?string $keterangan, ?int $userId): BarangKeluar
    {
        return DB::transaction(function () use ($items, $noKeluar, $tujuan, $penerima, $keterangan, $userId) {
            $barangKeluar = BarangKeluar::create([
                'no_keluar' => $noKeluar,
                'tanggal' => now()->toDateString(),
                'tujuan' => $tujuan,
                'penerima' => $penerima,
                'keterangan' => $keterangan,
                'created_by' => $userId,
            ]);

            foreach ($items as $item) {
                $produk = Produk::lockForUpdate()->findOrFail($item['id_produk']);
                $qty = (int) $item['jumlah'];

                if ($produk->stok < $qty) {
                    throw new Exception("Stok untuk '{$produk->nama}' tidak mencukupi! Sisa stok: {$produk->stok}, dibutuhkan: {$qty}.");
                }

                $newSaldo = $produk->stok - $qty;
                $produk->stok = $newSaldo;
                $produk->save();

                BarangKeluarItem::create([
                    'id_barang_keluar' => $barangKeluar->id,
                    'id_produk' => $produk->id,
                    'jumlah' => $qty,
                    'catatan' => $item['catatan'] ?? null,
                ]);

                KartuStok::create([
                    'id_produk' => $produk->id,
                    'id_cabang' => 1, // Single central warehouse
                    'tanggal' => now()->toDateString(),
                    'no_bukti' => $noKeluar,
                    'keterangan' => "Pengeluaran [{$tujuan}]" . ($penerima ? " ke {$penerima}" : '') . ($keterangan ? ": {$keterangan}" : ''),
                    'stok_masuk' => 0,
                    'stok_keluar' => $qty,
                    'saldo_akhir' => $newSaldo,
                ]);
            }

            return $barangKeluar;
        });
    }

    /**
     * Process physical inventory count audit (Stock Opname) and reconcile variances.
     */
    public function processStockOpname(array $items, string $noOpname, ?string $keterangan, ?int $userId): StockOpname
    {
        return DB::transaction(function () use ($items, $noOpname, $keterangan, $userId) {
            $opname = StockOpname::create([
                'no_opname' => $noOpname,
                'tanggal' => now()->toDateString(),
                'keterangan' => $keterangan,
                'created_by' => $userId,
            ]);

            foreach ($items as $item) {
                $produk = Produk::lockForUpdate()->findOrFail($item['id_produk']);
                $stokSistem = $produk->stok;
                $stokFisik = (int) $item['stok_fisik'];
                $selisih = $stokFisik - $stokSistem;

                $produk->stok = $stokFisik;
                $produk->save();

                StockOpnameItem::create([
                    'id_stock_opname' => $opname->id,
                    'id_produk' => $produk->id,
                    'stok_sistem' => $stokSistem,
                    'stok_fisik' => $stokFisik,
                    'selisih' => $selisih,
                    'alasan' => $item['alasan'] ?? null,
                ]);

                $stokMasuk = $selisih > 0 ? $selisih : 0;
                $stokKeluar = $selisih < 0 ? abs($selisih) : 0;

                KartuStok::create([
                    'id_produk' => $produk->id,
                    'id_cabang' => 1,
                    'tanggal' => now()->toDateString(),
                    'no_bukti' => $noOpname,
                    'keterangan' => "Stock Opname Audit (Sistem: {$stokSistem}, Fisik: {$stokFisik}, Selisih: " . ($selisih >= 0 ? "+{$selisih}" : $selisih) . ")" . ($keterangan ? " - {$keterangan}" : ''),
                    'stok_masuk' => $stokMasuk,
                    'stok_keluar' => $stokKeluar,
                    'saldo_akhir' => $stokFisik,
                ]);
            }

            return $opname;
        });
    }

    /**
     * Atomically adjust product stock directly (for backward compatibility).
     */
    public function recordStockMovement($produk, int $cabangId, string $type, int $qty, string $noBukti, string $keterangan = ''): KartuStok
    {
        return DB::transaction(function () use ($produk, $cabangId, $type, $qty, $noBukti, $keterangan) {
            $productModel = $produk instanceof Produk ? $produk : Produk::lockForUpdate()->findOrFail($produk);

            if ($type === 'keluar') {
                if ($productModel->stok < $qty) {
                    throw new Exception("Stok untuk produk '{$productModel->nama}' tidak mencukupi. Sisa stok: {$productModel->stok}, dibutuhkan: {$qty}.");
                }
                $newSaldo = $productModel->stok - $qty;
                $stokMasuk = 0;
                $stokKeluar = $qty;
            } elseif ($type === 'masuk') {
                $newSaldo = $productModel->stok + $qty;
                $stokMasuk = $qty;
                $stokKeluar = 0;
            } else {
                throw new Exception("Tipe pergerakan stok tidak valid: '{$type}'");
            }

            $productModel->stok = $newSaldo;
            $productModel->save();

            return KartuStok::create([
                'id_produk' => $productModel->id,
                'id_cabang' => $cabangId,
                'tanggal' => now()->toDateString(),
                'no_bukti' => $noBukti,
                'keterangan' => $keterangan,
                'stok_masuk' => $stokMasuk,
                'stok_keluar' => $stokKeluar,
                'saldo_akhir' => $newSaldo,
            ]);
        });
    }
}
