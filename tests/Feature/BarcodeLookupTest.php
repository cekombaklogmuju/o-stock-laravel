<?php

namespace Tests\Feature;

use App\Models\KategoriProduk;
use App\Models\Produk;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BarcodeLookupTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_lookup_product_by_barcode(): void
    {
        $kat = KategoriProduk::create(['nama' => 'Elektronik', 'slug' => 'elektronik']);
        $sup = Supplier::create(['nama' => 'Supplier A', 'slug' => 'supplier-a']);

        $produk = Produk::create([
            'id_kategori' => $kat->id,
            'id_supplier' => $sup->id,
            'kode_produk' => 'PRD-TEST-01',
            'barcode' => '899123456789',
            'nama' => 'Mouse Gaming Optik',
            'harga_jual' => 250000,
            'stok' => 15,
            'status' => 'aktif',
        ]);

        $response = $this->getJson('/api/v1/produk/lookup?barcode=899123456789');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $produk->id,
                    'kode_produk' => 'PRD-TEST-01',
                    'barcode' => '899123456789',
                    'nama' => 'Mouse Gaming Optik',
                    'harga_jual' => 250000,
                    'stok' => 15,
                ],
            ]);
    }

    public function test_lookup_returns_404_when_barcode_not_found(): void
    {
        $response = $this->getJson('/api/v1/produk/lookup?barcode=999999999999');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
            ]);
    }
}
