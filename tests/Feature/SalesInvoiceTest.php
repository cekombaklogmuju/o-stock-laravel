<?php

namespace Tests\Feature;

use App\Models\Kantor;
use App\Models\KartuStok;
use App\Models\KategoriProduk;
use App\Models\Konsumen;
use App\Models\Penjualan;
use App\Models\Produk;
use App\Models\Salesman;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SalesInvoiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_process_sale_and_deduct_inventory(): void
    {
        $kantor = Kantor::create([
            'kode_cabang' => 'CBG-POS',
            'tipe' => 'Cabang',
            'nama' => 'Cabang POS',
            'slug' => 'cabang-pos',
        ]);

        $user = User::factory()->create([
            'role' => 'cabang',
            'id_cabang' => $kantor->id,
        ]);

        $kat = KategoriProduk::create(['nama' => 'Aksesoris', 'slug' => 'aksesoris']);
        $sup = Supplier::create(['nama' => 'Supplier POS', 'slug' => 'supplier-pos']);

        $produk = Produk::create([
            'id_kategori' => $kat->id,
            'id_supplier' => $sup->id,
            'kode_produk' => 'PRD-POS-01',
            'barcode' => '8998887771',
            'nama' => 'Headset Bluetooth',
            'harga_jual' => 200000,
            'stok' => 20,
            'status' => 'aktif',
        ]);

        $konsumen = Konsumen::create([
            'id_cabang' => $kantor->id,
            'nama' => 'Pelanggan Retail',
        ]);

        $salesman = Salesman::create([
            'id_cabang' => $kantor->id,
            'nama' => 'Sales POS',
        ]);

        $postData = [
            'no_invoice' => 'INV-TEST-0001',
            'tanggal' => now()->toDateString(),
            'id_cabang' => $kantor->id,
            'id_konsumen' => $konsumen->id,
            'id_salesman' => $salesman->id,
            'items' => [
                [
                    'id_produk' => $produk->id,
                    'harga' => 200000,
                    'jumlah' => 3,
                    'discount' => 10,
                ],
            ],
            'catatan' => 'Test POS checkout',
        ];

        $response = $this->actingAs($user)->post('/penjualan', $postData);

        $penjualan = Penjualan::where('no_invoice', 'INV-TEST-0001')->first();
        $this->assertNotNull($penjualan);
        $response->assertRedirect('/penjualan/' . $penjualan->id);

        // Subtotal: 3 * 200,000 * 0.9 = 540,000
        $this->assertEquals(540000, (float) $penjualan->total_harga);

        // Verify stock deducted: 20 - 3 = 17
        $produk->refresh();
        $this->assertEquals(17, $produk->stok);

        // Verify KartuStok recorded
        $this->assertDatabaseHas('kartu_stoks', [
            'id_produk' => $produk->id,
            'id_cabang' => $kantor->id,
            'no_bukti' => 'INV-TEST-0001',
            'stok_keluar' => 3,
            'saldo_akhir' => 17,
        ]);
    }
}
