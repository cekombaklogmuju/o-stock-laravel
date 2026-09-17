# O-Stock Warehouse Edition (Serverless Ready for Vercel)

[![Made with Laravel](https://img.shields.io/badge/Made%20with-Laravel%2012-FF2D20.svg?logo=laravel)](https://laravel.com)
[![Vercel Serverless Ready](https://img.shields.io/badge/Deploy-Vercel%20Serverless-black.svg?logo=vercel)](https://vercel.com)
[![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-blue.svg?logo=php)](https://php.net)

A modern, high-efficiency **Warehouse Stock Management System (WMS)** built on **Laravel 12**, **Tailwind CSS**, and optimized for zero-configuration serverless deployment on **Vercel**.

Designed specifically for warehouse operations: **pure physical stock management, rack/bin location tracking, receiving, dispatching, and barcode-driven stock opname audits — without cashier POS or invoice bloat.**

---

## ✨ Fitur & Kapabilitas Utama

### 1. Master Barang & Lokasi Rak (Bin Location Tracking)
* **Lokasi Rak Fisik**: Setiap SKU memiliki penanda letak rak spesifik (contoh: `Rak A-01-A`, `Rak C-03-B`) untuk memudahkan penataan dan pengambilan barang.
* **Satuan & Stok Minimum**: Dukungan unit fleksibel (`Pcs`, `Box`, `Unit`, `Tube`, `Pack`) dan peringatan otomatis jika stok di bawah batas minimum (*Low Stock Alert*).
* **Cetak Stiker Label Barcode & Rak**: Fitur cetak label stiker CODE-128 siap tempel (`/produk/{id}/barcode-print`) lengkap dengan nama barang, SKU, dan lokasi rak untuk kertas stiker atau printer thermal.

### 2. Penerimaan Barang Masuk (Stock-In / Receiving)
* **Pencatatan Dokumen Masuk**: Form penerimaan barang dari supplier atau transfer masuk dengan nomor surat jalan dan keterangan.
* **Dukungan Barcode Scanner**: Scan barcode barang langsung menambah kuantitas barang masuk secara real-time.
* **Update Stok & Audit Otomatis**: Stok produk langsung bertambah secara atomik dan tercatat pada Buku Kartu Stok.
* **Cetak Bukti Penerimaan Barang**: Dokumen penerimaan resmi lengkap dengan tanda tangan petugas gudang dan pengirim.

### 3. Pengeluaran Barang (Stock-Out / Dispatch)
* **Kategori Pengeluaran**: Pemakaian internal/produksi, distribusi/transfer, penjualan grosir B2B, atau pemusnahan barang rusak (*scrap*).
* **Validasi Anti-Minus (Negative Stock Prevention)**: Sistem otomatis memblokir pengeluaran yang melebihi saldo stok yang tersedia baik di sisi browser maupun server.
* **Cetak Surat Pengeluaran Barang**: Dokumen bukti serah terima barang siap cetak.

### 4. Stock Opname & Rekonsiliasi Otomatis (Audit Fisik)
* **Audit per Rak / Batch**: Kemudahan audit fisik dengan tombol *"Muat Semua Barang Rak"* atau pemindaian satu per satu menggunakan barcode scanner.
* **Kalkulasi Selisih Real-Time**: Perbedaan antara *Stok Sistem* dan *Stok Fisik* dihitung seketika:
  * **Cocok (0)**: Hijau
  * **Surplus (+)**: Biru (Stok fisik lebih banyak)
  * **Defisit (-)**: Merah (Stok fisik kurang/hilang/rusak)
* **Penyesuaian Atomik**: Menyimpan audit otomatis memperbarui stok dan mencatat penyesuaian (*adjustment*) ke dalam Kartu Stok.
* **Berita Acara Stock Opname**: Dokumen hasil audit lengkap dengan statistik dan kolom persetujuan kepala gudang.

### 5. Buku Kartu Stok (Inventory Audit Ledger)
* Buku besar riwayat pergerakan barang masuk, keluar, dan audit fisik.
* Filter berdasarkan tanggal, produk, jenis mutasi (masuk/keluar), dan kata kunci pencarian.
* Format cetak laporan kartu stok siap print/PDF.

### 6. Integrasi Pemindai Barcode Ganda
* **Hardware Barcode Scanner (USB / Bluetooth HID)**: Listener keyboard wedge berkecepatan tinggi menangkap input scanner tanpa memerlukan kursor aktif pada text input.
* **In-Browser Camera Scanner**: Mengubah smartphone atau webcam laptop menjadi terminal scan barcode gudang dengan audio feedback (bip POS).

### 7. Arsitektur Serverless Vercel
* Dikonfigurasi dengan runtime `@vercel/php` via `vercel.json` dan `api/index.php`.
* Pengalihan storage, cache, session, dan compiled views ke direktori `/tmp` agar bebas error *read-only filesystem*.
* Kompatibel dengan Cloud DB (Neon PostgreSQL, Supabase, TiDB, PlanetScale, dsb).

---

## 🚀 Panduan Menjalankan (Local Development)

### 1. Kebutuhan Sistem
* PHP 8.2+ (ekstensi `pdo_sqlite` atau `pdo_mysql`)
* Composer 2.x

### 2. Instalasi & Menjalankan Server
```bash
# Masuk ke direktori proyek
cd C:\xampp\htdocs\o-stock-laravel

# Jalankan migrasi dan isi data dummy gudang
php artisan migrate:fresh --seed

# Jalankan automated test suite untuk memastikan semua fungsi hijau (100% pass)
php artisan test

# Jalankan server lokal Laravel
php artisan serve
```

Aplikasi dapat langsung dibuka di browser: **`http://127.0.0.1:8000`**.

---

## 🔑 Akun Demo (Seeder)

| Peran | Username | Password | Deskripsi |
|---|---|---|---|
| **Warehouse Manager** | `admin` | `password` | Kepala Gudang (Akses Penuh Master, Mutasi & Pengaturan) |
| **Warehouse Operator** | `operator` | `password` | Staf Operasional Gudang (Penerimaan, Pengeluaran, Opname) |

*(Tersedia tombol pintas isi otomatis 1-klik di halaman login)*

---

## 📦 Contoh Data Barcode & Rak untuk Pengujian

Gunakan kode barcode dan SKU berikut untuk pengujian pemindaian di form **Barang Masuk**, **Barang Keluar**, atau **Stock Opname**:

| Nama Barang | SKU | Barcode | Lokasi Rak | Stok Awal | Satuan |
|---|---|---|---|---|---|
| Motherboard Intel B760 DDR5 | `BRG-EL-001` | `899100110011` | **Rak A-01-A** | 25 | Pcs |
| RAM DDR5 32GB (2x16GB) | `BRG-EL-002` | `899100110028` | **Rak A-01-B** | 40 | Box |
| Power Supply 750W 80+ Gold | `BRG-EL-003` | `899100110035` | **Rak B-02-A** | 15 | Unit |
| Thermal Paste 4 Gram High Perf | `BRG-MAT-001` | `899100110042` | **Rak C-03-A** | 120 | Tube |
| Kabel Ties 200mm Hitam | `BRG-MAT-002` | `899100110059` | **Rak C-03-B** | 3 *(Low Stock)* | Pack |

---

## ☁️ Deployment ke Vercel

Panduan lengkap deployment serverless ke Vercel tersedia di file **[`VERCEL_DEPLOYMENT_GUIDE.md`](./VERCEL_DEPLOYMENT_GUIDE.md)**.
