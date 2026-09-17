<?php

namespace Tests\Feature;

use App\Models\Kantor;
use App\Models\KartuStok;
use App\Models\KategoriProduk;
use App\Models\Produk;
use App\Models\Supplier;
use App\Models\User;
use App\Services\InventoryService;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryStockTest extends TestCase
{
    use RefreshDatabase;

    protected function createSampleData()
    {
        $kantor = Kantor::create([
            'kode_cabang' => 'CBG-TEST',
            'tipe' => 'Cabang',
            'nama' => 'Cabang Uji',
            'slug' => 'cabang-uji',
        ]);

        $kat = KategoriProduk::create(['nama' => 'Komputer', 'slug' => 'komputer']);
        $sup = Supplier::create(['nama' => 'Supplier X', 'slug' => 'supplier-x']);

        $produk = Produk::create([
            'id_kategori' => $kat->id,
            'id_supplier' => $sup->id,
            'kode_produk' => 'PRD-TEST-INV',
            'barcode' => '8999990001',
            'nama' => 'Flashdisk 64GB',
            'harga_jual' => 85000,
            'stok' => 50,
            'status' => 'aktif',
        ]);

        return [$kantor, $produk];
    }

    public function test_inventory_service_records_incoming_stock_correctly(): void
    {
        [$kantor, $produk] = $this->createSampleData();

        $service = new InventoryService();
        $ledger = $service->recordStockMovement($produk, $kantor->id, 'masuk', 20, 'DOC-IN-01', 'Penambahan stok');

        $produk->refresh();
        $this->assertEquals(70, $produk->stok);
        $this->assertEquals(70, $ledger->saldo_akhir);
        $this->assertEquals(20, $ledger->stok_masuk);
        $this->assertEquals(0, $ledger->stok_keluar);
    }

    public function test_inventory_service_prevents_negative_stock(): void
    {
        [$kantor, $produk] = $this->createSampleData();

        $service = new InventoryService();

        $this->expectException(Exception::class);
        $service->recordStockMovement($produk, $kantor->id, 'keluar', 100, 'DOC-OUT-ERR', 'Pencegahan stok minus');
    }
}
