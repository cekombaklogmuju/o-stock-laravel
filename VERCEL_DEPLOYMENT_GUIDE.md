# Panduan Lengkap Deployment O-Stock Laravel ke Vercel

Panduan ini memandu Anda dalam melakukan deploy aplikasi **O-Stock Laravel** ke platform **Vercel** serverless.

---

## 1. Persiapan Database Cloud (Wajib)

Vercel menjalankan aplikasi dalam container serverless (AWS Lambda) yang sifatnya **ephemeral (sementara)** dan **read-only**. Oleh karena itu, Anda memerlukan database cloud eksternal:

### Rekomendasi Database Cloud Gratis / Terjangkau:
1. **Neon PostgreSQL** (Sangat disarankan, gratis): [neon.tech](https://neon.tech)
2. **Supabase PostgreSQL** (Gratis): [supabase.com](https://supabase.com)
3. **Railway MySQL / PostgreSQL**: [railway.app](https://railway.app)
4. **Aiven MySQL / PostgreSQL**: [aiven.io](https://aiven.io)

*Catatan: Laravel mendukung PostgreSQL dan MySQL secara transparan.*

---

## 2. Struktur Konfigurasi Serverless yang Disediakan

Aplikasi ini sudah dilengkapi dengan konfigurasi serverless:

1. **`vercel.json`**:
   * Mengatur routing asset statis (`/build`, `/css`, `/js`) langsung ke CDN Vercel.
   * Mengarahkan request dinamis ke handler `api/index.php`.
   * Mengatur variabel lingkungan runtime (`VIEW_COMPILED_PATH=/tmp/views`, `SESSION_DRIVER=cookie`, `CACHE_STORE=array`).
2. **`api/index.php`**:
   * Secara otomatis membuat direktori `/tmp/views`, `/tmp/storage/framework/cache`, `/tmp/storage/framework/sessions`, dan `/tmp/storage/logs` setiap kali lambda cold start terjadi.

---

## 3. Langkah-Langkah Deployment

### Opsi A: Deployment via GitHub & Vercel Dashboard (Paling Mudah)

1. **Push Repository ke GitHub**:
   ```bash
   cd C:\xampp\htdocs\o-stock-laravel
   git remote add origin https://github.com/USERNAME/o-stock-laravel.git
   git branch -M main
   git push -u origin main
   ```

2. **Import ke Vercel**:
   * Buka [vercel.com](https://vercel.com) dan login.
   * Klik **"Add New..." > "Project"**.
   * Pilih repositori GitHub `o-stock-laravel` Anda.

3. **Konfigurasi Environment Variables di Vercel**:
   Sebelum mengklik **Deploy**, buka bagian **Environment Variables** dan tambahkan:

   | Variabel | Contoh Nilai | Keterangan |
   |---|---|---|
   | `APP_NAME` | `O-Stock` | Nama aplikasi |
   | `APP_ENV` | `production` | Environment |
   | `APP_KEY` | *(Salin nilai `APP_KEY` dari file `.env` lokal Anda)* | Kunci enkripsi |
   | `APP_DEBUG` | `false` | Matikan debug di production |
   | `APP_URL` | `https://o-stock-laravel.vercel.app` | URL domain Vercel Anda |
   | `DB_CONNECTION` | `pgsql` *(atau `mysql`)* | Driver DB cloud |
   | `DB_HOST` | `ep-xxxxxx.us-east-2.aws.neon.tech` | Host DB cloud Anda |
   | `DB_PORT` | `5432` *(atau `3306` untuk MySQL)* | Port database |
   | `DB_DATABASE` | `neondb` | Nama database |
   | `DB_USERNAME` | `neondb_owner` | Username DB |
   | `DB_PASSWORD` | `rahasia123` | Password DB |
   | `VIEW_COMPILED_PATH`| `/tmp/views` | Direktori view serverless |
   | `CACHE_STORE` | `array` | Cache store stateless |
   | `SESSION_DRIVER` | `cookie` | Session driver stateless |
   | `LOG_CHANNEL` | `stderr` | Log langsung ke Vercel Console |

4. **Klik "Deploy"**:
   Vercel akan membangun dan mendeploy aplikasi Anda dalam beberapa detik!

---

### Opsi B: Deployment via Vercel CLI

Jika Anda memiliki `vercel` CLI di komputer Anda:

```bash
# 1. Install vercel CLI (jika belum ada)
npm i -g vercel

# 2. Login ke akun Vercel
vercel login

# 3. Deploy langsung dari terminal
cd C:\xampp\htdocs\o-stock-laravel
vercel

# 4. Deploy ke Production
vercel --prod
```

---

## 4. Menjalankan Migrasi & Seeding ke Cloud Database

Untuk mengisi tabel dan akun default ke database cloud Anda untuk pertama kali:

1. Di komputer lokal Anda, buka file `.env` sementara dan arahkan parameter database ke database cloud Anda.
2. Jalankan perintah:
   ```bash
   php artisan migrate --seed --force
   ```
3. Kembalikan `.env` lokal ke konfigurasi SQLite/MySQL lokal Anda.
4. Sekarang database cloud Anda sudah memiliki seluruh tabel, cabang, produk berbarcode, dan pengguna!

---

## 5. Verifikasi Deployment

Setelah deploy selesai:
1. Kunjungi URL Vercel yang diberikan (misal: `https://o-stock-laravel.vercel.app`).
2. Halaman login modern O-Stock akan muncul.
3. Masuk dengan akun administrator:
   * **Username**: `admin`
   * **Password**: `password`
4. Coba lakukan transaksi baru dan scan barcode menggunakan kamera ponsel atau laptop Anda!
