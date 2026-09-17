<?php

namespace Tests\Feature;

use App\Models\BarangKeluar;
use App\Models\BarangMasuk;
use App\Models\Kantor;
use App\Models\KartuStok;
use App\Models\KategoriProduk;
use App\Models\Produk;
use App\Models\StockOpname;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WarehouseMovementTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $warehouse;
    protected $produk;
    protected $supplier;

    protected function setUp(): void
    {
        parent::setUp();

        $this->warehouse = Kantor::create([
            'kode_cabang' => 'GDG-PST',
            'tipe' => 'Pusat',
            'nama' => 'Gudang Utama',
            'slug' => 'gudang-utama',
        ]);

        $this->user = User::factory()->create([
            'role' => 'admin',
            'id_cabang' => $this->warehouse->id,
        ]);

        $kategori = KategoriProduk::create(['nama' => 'Komponen', 'slug' => 'komponen']);
        $this->supplier = Supplier::create(['nama' => 'PT Supplier Utama', 'slug' => 'pt-supplier-utama']);

        $this->produk = Produk::create([
            'id_kategori' => $kategori->id,
            'id_supplier' => $this->supplier->id,
            'kode_produk' => 'BRG-TST-01',
            'barcode' => '899123456001',
            'nama' => 'SSD NVMe 1TB High Speed',
            'lokasi_rak' => 'Rak A-02-B',
            'satuan' => 'Unit',
            'harga_jual' => 1200000,
            'stok' => 20,
            'stok_minimum' => 5,
            'status' => 'aktif',
        ]);
    }

    public function test_can_access_warehouse_dashboard(): void
    {
        $response = $this->actingAs($this->user)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Pusat Kendali Stok Gudang');
        $response->assertSee('Total SKU Master');
        $response->assertSee('Total Fisik Unit');
    }

    public function test_can_access_all_warehouse_management_pages(): void
    {
        $pages = [
            '/barang-masuk',
            '/barang-masuk/create',
            '/barang-keluar',
            '/barang-keluar/create',
            '/stock-opname',
            '/stock-opname/create',
            '/kartu-stok',
            '/kartu-stok/print',
            '/produk',
            '/produk/create',
            "/produk/{$this->produk->id}/barcode-print",
            '/kategori',
            '/supplier',
        ];

        foreach ($pages as $page) {
            $res = $this->actingAs($this->user)->get($page);
            $res->assertStatus(200);
        }
    }

    public function test_can_process_barang_masuk_and_increase_stock(): void
    {
        $payload = [
            'no_masuk' => 'BM-20260917-0001',
            'id_supplier' => $this->supplier->id,
            'no_surat_jalan' => 'SJ-SUP-9988',
            'keterangan' => 'Pasokan batch baru dari distributor',
            'items' => [
                [
                    'id_produk' => $this->produk->id,
                    'jumlah' => 15,
                    'catatan' => 'Kondisi segel baik',
                ],
            ],
        ];

        $response = $this->actingAs($this->user)->post('/barang-masuk', $payload);

        $bm = BarangMasuk::where('no_masuk', 'BM-20260917-0001')->first();
        $this->assertNotNull($bm);
        $response->assertRedirect('/barang-masuk/' . $bm->id);

        // Verify stock increased from 20 to 35
        $this->produk->refresh();
        $this->assertEquals(35, $this->produk->stok);

        // Verify KartuStok entry
        $this->assertDatabaseHas('kartu_stoks', [
            'id_produk' => $this->produk->id,
            'no_bukti' => 'BM-20260917-0001',
            'stok_masuk' => 15,
            'stok_keluar' => 0,
            'saldo_akhir' => 35,
        ]);
    }

    public function test_can_process_barang_keluar_and_decrease_stock(): void
    {
        $payload = [
            'no_keluar' => 'BK-20260917-0001',
            'tujuan' => 'pemakaian_internal',
            'penerima' => 'Divisi IT Perakitan',
            'keterangan' => 'Pengambilan unit untuk server internal',
            'items' => [
                [
                    'id_produk' => $this->produk->id,
                    'jumlah' => 4,
                    'catatan' => 'Unit rack A-02',
                ],
            ],
        ];

        $response = $this->actingAs($this->user)->post('/barang-keluar', $payload);

        $bk = BarangKeluar::where('no_keluar', 'BK-20260917-0001')->first();
        $this->assertNotNull($bk);
        $response->assertRedirect('/barang-keluar/' . $bk->id);

        // Verify stock decreased from 20 to 16
        $this->produk->refresh();
        $this->assertEquals(16, $this->produk->stok);

        // Verify KartuStok entry
        $this->assertDatabaseHas('kartu_stoks', [
            'id_produk' => $this->produk->id,
            'no_bukti' => 'BK-20260917-0001',
            'stok_masuk' => 0,
            'stok_keluar' => 4,
            'saldo_akhir' => 16,
        ]);
    }

    public function test_cannot_process_barang_keluar_exceeding_stock(): void
    {
        $payload = [
            'no_keluar' => 'BK-ERR-0001',
            'tujuan' => 'distribusi',
            'items' => [
                [
                    'id_produk' => $this->produk->id,
                    'jumlah' => 50, // Available is only 20
                ],
            ],
        ];

        $response = $this->actingAs($this->user)->post('/barang-keluar', $payload);
        $response->assertSessionHasErrors('error');

        // Stock must remain unchanged
        $this->produk->refresh();
        $this->assertEquals(20, $this->produk->stok);
    }

    public function test_can_process_stock_opname_and_reconcile_variance(): void
    {
        // System stock is 20, physical count is 18 (variance -2)
        $payload = [
            'no_opname' => 'SO-20260917-0001',
            'keterangan' => 'Audit fisik mingguan Rak Sektor A',
            'items' => [
                [
                    'id_produk' => $this->produk->id,
                    'stok_fisik' => 18,
                    'alasan' => '2 unit rusak dalam packaging',
                ],
            ],
        ];

        $response = $this->actingAs($this->user)->post('/stock-opname', $payload);

        $so = StockOpname::where('no_opname', 'SO-20260917-0001')->first();
        $this->assertNotNull($so);
        $response->assertRedirect('/stock-opname/' . $so->id);

        // Verify stock updated to physical count (18)
        $this->produk->refresh();
        $this->assertEquals(18, $this->produk->stok);

        // Verify StockOpnameItem recorded variance -2
        $this->assertDatabaseHas('stock_opname_items', [
            'id_stock_opname' => $so->id,
            'id_produk' => $this->produk->id,
            'stok_sistem' => 20,
            'stok_fisik' => 18,
            'selisih' => -2,
        ]);

        // Verify KartuStok recorded variance adjustment
        $this->assertDatabaseHas('kartu_stoks', [
            'id_produk' => $this->produk->id,
            'no_bukti' => 'SO-20260917-0001',
            'stok_keluar' => 2,
            'saldo_akhir' => 18,
        ]);
    }
}
