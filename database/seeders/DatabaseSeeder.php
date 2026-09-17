<?php

namespace Database\Seeders;

use App\Models\Kantor;
use App\Models\KartuStok;
use App\Models\KategoriProduk;
use App\Models\Produk;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Central Warehouse
        $warehouse = Kantor::create([
            'kode_cabang' => 'GDG-PST',
            'tipe' => 'Pusat',
            'nama' => 'Gudang Utama (Central Warehouse)',
            'slug' => 'gudang-utama',
            'alamat' => 'Kawasan Industri Pulogadung Blok C No. 12, Jakarta',
        ]);

        // 2. Categories
        $katKomponen = KategoriProduk::create(['nama' => 'Komponen & Suku Cadang', 'slug' => 'komponen-suku-cadang']);
        $katElektronik = KategoriProduk::create(['nama' => 'Elektronik & Perangkat', 'slug' => 'elektronik-perangkat']);
        $katMaterial = KategoriProduk::create(['nama' => 'Bahan Baku & Material', 'slug' => 'bahan-baku-material']);

        // 3. Suppliers
        $supA = Supplier::create(['nama' => 'PT Maju Logistik Utama', 'slug' => 'pt-maju-logistik-utama']);
        $supB = Supplier::create(['nama' => 'CV Sumber Makmur Mandiri', 'slug' => 'cv-sumber-makmur-mandiri']);

        // 4. Products with Rack Locations & Barcodes
        $productsData = [
            [
                'kode_produk' => 'BRG-EL-001',
                'barcode' => '899100110011',
                'nama' => 'Motherboard Intel B760 DDR5',
                'id_kategori' => $katElektronik->id,
                'id_supplier' => $supA->id,
                'lokasi_rak' => 'Rak A-01-A',
                'satuan' => 'Pcs',
                'harga_jual' => 2450000,
                'stok' => 25,
                'stok_minimum' => 5,
                'status' => 'aktif',
            ],
            [
                'kode_produk' => 'BRG-EL-002',
                'barcode' => '899100110028',
                'nama' => 'RAM DDR5 32GB (2x16GB) 6000MHz',
                'id_kategori' => $katKomponen->id,
                'id_supplier' => $supA->id,
                'lokasi_rak' => 'Rak A-01-B',
                'satuan' => 'Box',
                'harga_jual' => 1750000,
                'stok' => 40,
                'stok_minimum' => 10,
                'status' => 'aktif',
            ],
            [
                'kode_produk' => 'BRG-EL-003',
                'barcode' => '899100110035',
                'nama' => 'Power Supply 750W 80+ Gold Modular',
                'id_kategori' => $katKomponen->id,
                'id_supplier' => $supB->id,
                'lokasi_rak' => 'Rak B-02-A',
                'satuan' => 'Unit',
                'harga_jual' => 1350000,
                'stok' => 15,
                'stok_minimum' => 5,
                'status' => 'aktif',
            ],
            [
                'kode_produk' => 'BRG-MAT-001',
                'barcode' => '899100110042',
                'nama' => 'Thermal Paste 4 Gram High Performance',
                'id_kategori' => $katMaterial->id,
                'id_supplier' => $supB->id,
                'lokasi_rak' => 'Rak C-03-A',
                'satuan' => 'Tube',
                'harga_jual' => 75000,
                'stok' => 120,
                'stok_minimum' => 20,
                'status' => 'aktif',
            ],
            [
                'kode_produk' => 'BRG-MAT-002',
                'barcode' => '899100110059',
                'nama' => 'Kabel Ties 200mm Hitam (100 Pcs/Pack)',
                'id_kategori' => $katMaterial->id,
                'id_supplier' => $supB->id,
                'lokasi_rak' => 'Rak C-03-B',
                'satuan' => 'Pack',
                'harga_jual' => 25000,
                'stok' => 3, // Low stock test
                'stok_minimum' => 10,
                'status' => 'aktif',
            ],
        ];

        foreach ($productsData as $data) {
            $p = Produk::create($data);

            // Log initial stock to KartuStok
            KartuStok::create([
                'id_produk' => $p->id,
                'id_cabang' => $warehouse->id,
                'tanggal' => now()->toDateString(),
                'no_bukti' => 'INIT-STOCK',
                'keterangan' => 'Saldo awal stok gudang di ' . $p->lokasi_rak,
                'stok_masuk' => $p->stok,
                'stok_keluar' => 0,
                'saldo_akhir' => $p->stok,
            ]);
        }

        // 5. Users
        User::create([
            'name' => 'Kepala Gudang (Warehouse Manager)',
            'username' => 'admin',
            'email' => 'admin@warehouse.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'id_cabang' => $warehouse->id,
            'status' => 'aktif',
        ]);

        User::create([
            'name' => 'Staf Gudang (Warehouse Operator)',
            'username' => 'operator',
            'email' => 'operator@warehouse.com',
            'password' => Hash::make('password'),
            'role' => 'cabang',
            'id_cabang' => $warehouse->id,
            'status' => 'aktif',
        ]);
    }
}
