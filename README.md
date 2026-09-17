# O-Stock Laravel Edition (Serverless Ready for Vercel)

[![Made with Laravel](https://img.shields.io/badge/Made%20with-Laravel%2012-FF2D20.svg?logo=laravel)](https://laravel.com)
[![Vercel Serverless Ready](https://img.shields.io/badge/Deploy-Vercel%20Serverless-black.svg?logo=vercel)](https://vercel.com)
[![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-blue.svg?logo=php)](https://php.net)

A modern, serverless-optimized re-implementation of the **o-stock** multi-branch inventory and sales management system. Built on **Laravel 12**, **Tailwind CSS**, and designed for deployment on **Vercel** serverless functions with zero read-only filesystem issues and integrated **hardware/camera barcode scanning**.

---

## ✨ Features & Capabilities

### 1. Multi-Branch Architecture & Scoping
* **Role-Based Access Control (RBAC)**: `admin`, `cabang`, and `salesman`.
* **Global vs Local Branch Scoping**: Administrators can view global company numbers or toggle into any branch context; branch operators are strictly scoped to their assigned branch.

### 2. Barcode Scanning System
* **Dual-Mode Scanner Support**:
  * **Hardware USB/Bluetooth Scanners (HID Wedge)**: Automatic listener captures rapid keystroke input without requiring focus on an input field.
  * **In-Browser Camera Scanner**: Powered by HTML5 QR/Barcode reader (`html5-qrcode`), turning any smartphone or webcam into a warehouse barcode terminal.
  * **Audio POS Feedback**: Built-in 1800Hz POS beep via Web Audio API.
* **Printable Barcode Sheet**: Generate printable Code-128 barcode stickers (`/produk/{id}/barcode-print`) directly formatted for thermal or A4 label paper.

### 3. Core Inventory & Business Workflows
* **Master Data**: Cabang / Kantor, Kategori Produk, Supplier, Produk (dengan Barcode), Konsumen / Pelanggan, Salesman.
* **Alokasi Stok**: Pengiriman dan distribusi stok dari kantor pusat ke cabang tujuan.
* **Permintaan Mutasi Cabang (Branch Requests)**: Cabang mengajukan permintaan penambahan stok dengan level prioritas (`normal`, `urgent`, `critical`), disetujui atau disesuaikan oleh head office.
* **Buku Kartu Stok (Stock Ledger)**: Pelacakan mutasi masuk, keluar, dan saldo akhir per produk dan cabang dengan filter tanggal dan fitur cetak laporan.
* **Kasir Penjualan & Cetak Invoice**: Kasir real-time dengan pemindaian barcode, kalkulasi diskon, verifikasi stok, dan pencetakan faktur/struk standar.

### 4. Serverless Vercel Adaptation
* **Stateless Lambda Runtime**: Configured via `vercel.json` with community `@vercel/php` / `vercel-php@0.7.x`.
* **Ephemeral `/tmp` Storage Redirection**: Automatically writes compiled Blade views, sessions, cache, and logs to `/tmp` on cold start to prevent read-only filesystem errors.
* **Cloud Database Compatible**: Tested and ready for Neon, Supabase, Railway, or PlanetScale.

---

## 🚀 Quick Start (Local Development)

### 1. Requirements
* PHP 8.2 or later (with `pdo_sqlite` and `pdo_mysql` extensions enabled)
* Composer 2.x
* XAMPP / Local Web Server

### 2. Installation
```bash
# Clone or navigate to the repository
cd C:\xampp\htdocs\o-stock-laravel

# Install composer dependencies (if needed)
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Run database migrations and seed default data
php artisan migrate:fresh --seed

# Start the local development server
php artisan serve
```

Aplikasi sekarang dapat diakses di: **`http://127.0.0.1:8000`** (atau via XAMPP Apache di `http://localhost/o-stock-laravel/public`).

---

## 🔑 Default User Accounts (Seeder)

| Peran (Role) | Username | Password | Akses Cabang |
|---|---|---|---|
| **Super Administrator** | `admin` | `password` | Semua Cabang (Global Switcher) |
| **Operator Cabang** | `cabang_jkt` | `password` | Cabang Jakarta Barat |
| **Operator Cabang** | `cabang_sby` | `password` | Cabang Surabaya |
| **Sales Lapangan** | `sales_budi` | `password` | Cabang Jakarta (Sales) |

*Terdapat tombol pintas (quick-fill) di halaman login untuk mengisi kredensial demo ini dengan 1 klik.*

---

## 📦 Sample Barcode Codes for Testing

Gunakan kode barcode berikut pada fitur **Kasir / Barcode Scan** (`/penjualan/create`):

| Produk | Kode Produk | Barcode | Stok Awal | Harga |
|---|---|---|---|---|
| Laptop Business Core i5 | `PRD-EL-001` | `899100110011` | 20 | Rp 8.500.000 |
| Wireless Optical Mouse | `PRD-EL-002` | `899100110028` | 100 | Rp 125.000 |
| Mechanical Keyboard RGB | `PRD-EL-003` | `899100110035` | 50 | Rp 450.000 |
| Kertas HVS A4 80gr | `PRD-ATK-001` | `899100110042` | 150 | Rp 58.000 |

---

## ☁️ Deployment ke Vercel

Lihat panduan lengkap langkah-demi-langkah di file **[`VERCEL_DEPLOYMENT_GUIDE.md`](./VERCEL_DEPLOYMENT_GUIDE.md)**.
