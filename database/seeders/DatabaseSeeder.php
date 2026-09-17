<?php

namespace Database\Seeders;

use App\Models\Alokasi;
use App\Models\AlokasiItem;
use App\Models\Kantor;
use App\Models\KartuStok;
use App\Models\KategoriProduk;
use App\Models\Konsumen;
use App\Models\Produk;
use App\Models\Salesman;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Branches / Kantors
        $pusat = Kantor::create([
            'kode_cabang' => 'KTR-001',
            'tipe' => 'Pusat',
            'nama' => 'Kantor Pusat',
            'slug' => 'kantor-pusat',
            'alamat' => 'Jl. Jenderal Sudirman No. 1, Jakarta Pusat',
        ]);

        $jkt = Kantor::create([
            'kode_cabang' => 'CBG-JKT',
            'tipe' => 'Cabang',
            'nama' => 'Cabang Jakarta Barat',
            'slug' => 'cabang-jakarta-barat',
            'alamat' => 'Jl. Panjang No. 45, Kebon Jeruk, Jakarta Barat',
        ]);

        $sby = Kantor::create([
            'kode_cabang' => 'CBG-SBY',
            'tipe' => 'Cabang',
            'nama' => 'Cabang Surabaya',
            'slug' => 'cabang-surabaya',
            'alamat' => 'Jl. Pemuda No. 18, Genteng, Surabaya',
        ]);

        // 2. Categories
        $katElektronik = KategoriProduk::create([
            'nama' => 'Elektronik & Gadget',
            'slug' => 'elektronik-gadget',
        ]);

        $katAtk = KategoriProduk::create([
            'nama' => 'Alat Tulis Kantor',
            'slug' => 'alat-tulis-kantor',
        ]);

        // 3. Suppliers
        $supMaju = Supplier::create([
            'nama' => 'PT Maju Bersama Logistik',
            'slug' => 'pt-maju-bersama-logistik',
        ]);

        $supSumber = Supplier::create([
            'nama' => 'CV Sumber Makmur Abadi',
            'slug' => 'cv-sumber-makmur-abadi',
        ]);

        // 4. Products with Barcodes
        $p1 = Produk::create([
            'id_kategori' => $katElektronik->id,
            'id_supplier' => $supMaju->id,
            'kode_produk' => 'PRD-EL-001',
            'barcode' => '899100110011',
            'nama' => 'Laptop Business Core i5 16GB',
            'slug' => 'laptop-business-core-i5-16gb',
            'harga_jual' => 8500000,
            'stok' => 20,
            'status' => 'aktif',
        ]);

        $p2 = Produk::create([
            'id_kategori' => $katElektronik->id,
            'id_supplier' => $supMaju->id,
            'kode_produk' => 'PRD-EL-002',
            'barcode' => '899100110028',
            'nama' => 'Wireless Optical Mouse 2.4GHz',
            'slug' => 'wireless-optical-mouse-2-4ghz',
            'harga_jual' => 125000,
            'stok' => 100,
            'status' => 'aktif',
        ]);

        $p3 = Produk::create([
            'id_kategori' => $katElektronik->id,
            'id_supplier' => $supMaju->id,
            'kode_produk' => 'PRD-EL-003',
            'barcode' => '899100110035',
            'nama' => 'Mechanical Keyboard RGB Backlit',
            'slug' => 'mechanical-keyboard-rgb-backlit',
            'harga_jual' => 450000,
            'stok' => 50,
            'status' => 'aktif',
        ]);

        $p4 = Produk::create([
            'id_kategori' => $katAtk->id,
            'id_supplier' => $supSumber->id,
            'kode_produk' => 'PRD-ATK-001',
            'barcode' => '899100110042',
            'nama' => 'Kertas HVS A4 80 Gram (1 Rim)',
            'slug' => 'kertas-hvs-a4-80-gram-1-rim',
            'harga_jual' => 58000,
            'stok' => 150,
            'status' => 'aktif',
        ]);

        // 5. Salesmen
        $sales1 = Salesman::create([
            'id_cabang' => $jkt->id,
            'nama' => 'Budi Santoso',
            'slug' => 'budi-santoso',
            'no_telp' => '081234567890',
        ]);

        $sales2 = Salesman::create([
            'id_cabang' => $sby->id,
            'nama' => 'Siti Rahmawati',
            'slug' => 'siti-rahmawati',
            'no_telp' => '081987654321',
        ]);

        // 6. Customers / Konsumen
        Konsumen::create([
            'id_cabang' => $jkt->id,
            'nama' => 'Toko Komputer Sejahtera',
            'slug' => 'toko-komputer-sejahtera',
            'alamat' => 'Mangga Dua Mall Lt. 3 No. 42',
            'kota' => 'Jakarta Barat',
            'no_telp' => '021-6123456',
        ]);

        Konsumen::create([
            'id_cabang' => $sby->id,
            'nama' => 'CV Barokah Stationery',
            'slug' => 'cv-barokah-stationery',
            'alamat' => 'Jl. Kertajaya Indah No. 88',
            'kota' => 'Surabaya',
            'no_telp' => '031-5987654',
        ]);

        // 7. Users with Roles
        User::create([
            'name' => 'Super Administrator',
            'username' => 'admin',
            'email' => 'admin@ostock.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'id_cabang' => $pusat->id,
            'status' => 'aktif',
        ]);

        User::create([
            'name' => 'Operator Jakarta',
            'username' => 'cabang_jkt',
            'email' => 'jkt@ostock.com',
            'password' => Hash::make('password'),
            'role' => 'cabang',
            'id_cabang' => $jkt->id,
            'status' => 'aktif',
        ]);

        User::create([
            'name' => 'Operator Surabaya',
            'username' => 'cabang_sby',
            'email' => 'sby@ostock.com',
            'password' => Hash::make('password'),
            'role' => 'cabang',
            'id_cabang' => $sby->id,
            'status' => 'aktif',
        ]);

        User::create([
            'name' => 'Budi Salesman',
            'username' => 'sales_budi',
            'email' => 'budi@ostock.com',
            'password' => Hash::make('password'),
            'role' => 'salesman',
            'id_cabang' => $jkt->id,
            'id_salesman' => $sales1->id,
            'status' => 'aktif',
        ]);

        // 8. Initial KartuStok entries
        foreach ([$p1, $p2, $p3, $p4] as $prod) {
            KartuStok::create([
                'id_produk' => $prod->id,
                'id_cabang' => $pusat->id,
                'tanggal' => date('Y-m-d'),
                'no_bukti' => 'INIT-STOCK',
                'keterangan' => 'Saldo awal pembukaan sistem',
                'stok_masuk' => $prod->stok,
                'stok_keluar' => 0,
                'saldo_akhir' => $prod->stok,
            ]);
        }
    }
}
