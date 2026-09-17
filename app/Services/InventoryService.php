<?php

namespace App\Services;

use App\Models\KartuStok;
use App\Models\Produk;
use Exception;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    /**
     * Atomically adjust product stock and create corresponding KartuStok ledger record.
     *
     * @param Produk|int $produk
     * @param int $cabangId
     * @param string $type 'masuk' or 'keluar'
     * @param int $qty
     * @param string $noBukti
     * @param string $keterangan
     * @return KartuStok
     * @throws Exception
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

    /**
     * Check if a product has enough inventory.
     */
    public function hasSufficientStock(int $produkId, int $qty): bool
    {
        $produk = Produk::find($produkId);
        return $produk && $produk->stok >= $qty;
    }
}
