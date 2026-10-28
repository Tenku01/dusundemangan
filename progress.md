# Laporan Progres Pengembangan & Status Sistem (Progress Log)
**Sistem Monitoring Dusun & Kasir Listrik Warga Karang Taruna Demangan**

Dokumen ini mencatat seluruh riwayat pengembangan, arsitektur serverless, konfigurasi deployment, penyelesaian kendala teknis (gotchas & fixes), serta status fitur yang sudah selesai maupun roadmap mendatang.

---

## 1. Ringkasan Eksekutif & Status Sistem

| Item | Status / Nilai | Catatan |
|---|---|---|
| **Status Produksi** | 🟢 **LIVE & AKTIF** | HTTP 200 OK di semua endpoint publik & admin |
| **URL Produksi** | [https://dusundemangan.vercel.app](https://dusundemangan.vercel.app) | Domain resmi Vercel (clean alias tanpa `-rho`) |
| **Repositori GitHub** | [github.com/Tenku01/dusundemangan](https://github.com/Tenku01/dusundemangan) | Branch: `main` (CI/CD Otomatis aktif ke Vercel) |
| **Framework** | Laravel 12.x + TailwindCSS + Alpine.js | PHP 8.3 (Lokal) / PHP 8.5 (Vercel Serverless) |
| **Hosting Frontend & Serverless** | **Vercel** (Global Edge CDN + AWS Lambda) | Runtime `vercel-php@0.9.0` (100% Gratis) |
| **Database Cloud** | **TiDB Cloud Serverless** (AWS ap-southeast-1) | MySQL 8.0 compatible, 5GB storage free tier |
| **Integritas Data** | **47 Data Warga Riil** (RT 1 & RT 2) | ⚠️ **DILARANG WIPE / FRESH DATABASE** |

---

## 2. Kredensial & Konfigurasi Lingkungan

### A. Akun Login Sistem (Produksi & Lokal)
- **Superadmin**: `superadmin@demangan.id` | Kata Sandi: `password`
*(Akun admin pengurus tambahan dapat dibuat secara dinamis oleh Superadmin melalui menu Kelola Pengguna).*

### B. Database Cloud TiDB Serverless
- **Host**: `gateway01.ap-southeast-1.prod.aws.tidbcloud.com`
- **Port**: `4000`
- **Database**: `demangan`
- **Username**: `cX5UUTnVXyNCtpZ.root`
- **Password**: `b8r6GTltOTZHRERe`
- **SSL / CA**: Wajib aktif via opsi PDO MySQL (`MYSQL_ATTR_SSL_CA` / fallback integer `1009`)

### C. Daftar Environment Variables (Vercel Production)
Daftar variabel yang telah dikonfigurasi di Vercel Dashboard / CLI:
```ini
APP_NAME="Dusun Demangan"
APP_ENV=production
APP_KEY=base64:7K50i6c8e3l4l5K0v1t3V2x3l4l5K0v1t3V2x3l4l5K=
APP_DEBUG=false
APP_URL=https://dusundemangan.vercel.app
ASSET_URL=https://dusundemangan.vercel.app
DB_CONNECTION=mysql
DB_HOST=gateway01.ap-southeast-1.prod.aws.tidbcloud.com
DB_PORT=4000
DB_DATABASE=demangan
DB_USERNAME=cX5UUTnVXyNCtpZ.root
DB_PASSWORD=b8r6GTltOTZHRERe
DB_SSL_CA=true
SESSION_DRIVER=database
CACHE_STORE=database
LOG_CHANNEL=stderr
```

### D. Vercel System Environment Variables
*(Sesuai dokumentasi resmi [Vercel System Environment Variables](https://vercel.com/docs/environment-variables/system-environment-variables))*
- `VERCEL`: Bernilai `1` otomatis saat berada di serverless environment. Digunakan pada `AppServiceProvider.php` dan `api/index.php`.
- `VERCEL_ENV`: Environment aktif (`production`, `preview`, atau `development`).
- `VERCEL_URL`: Hostname deployment unik Vercel (misal: `dusundemangan-xxx.vercel.app`).
- `VERCEL_PROJECT_PRODUCTION_URL`: Domain canonical produksi (`dusundemangan.vercel.app`).
- `VERCEL_REGION`: Wilayah serverless execution (misal: `iad1` untuk US East, `sin1` untuk Singapore).
- `VERCEL_GIT_PROVIDER`: `github`.
- `VERCEL_GIT_REPO_SLUG`: `dusundemangan`.
- `VERCEL_GIT_REPO_OWNER`: `Tenku01`.
- `VERCEL_GIT_COMMIT_REF`: `main`.

---

## 3. Riwayat Pencapaian & Fitur Selesai (Milestones)

### Milestone 1: Modul Rekap Struk Listrik Cepat & Berurutan (Sequential Entry)
- **Tujuan**: Mempercepat proses input data rekap tagihan bulanan dari struk fisik PLN oleh pengurus.
- **Implementasi**:
  1. **Urutan Numerik**: Mengurutkan warga berdasarkan nomor struk secara numerik (`CAST(nomor_meter AS UNSIGNED) ASC`).
  2. **Sequential Prompting**: Modal memandu pengurus bertahap: *"Warga [Nama] - Struk #[Nomor] tagihan awalnya berapa?"*.
  3. **Auto-Fill & Next Step**: Setelah mengisi nominal tagihan awal, sistem otomatis menyimpan via asynchronous JSON request dan langsung beralih ke struk nomor berikutnya secara instan tanpa reload halaman penuh.
  4. **Penyederhanaan Form**:
     - Menghapus input "Atas Nama PLN" dan "Nama Penghuni" dari rekap struk bulanan.
     - Memindahkan "Nama Pembayar" (opsional) ke kasir pembayaran.
- **Berkas Terkait**:
  - `app/Http/Controllers/Admin/RekapListrikController.php`
  - `resources/views/admin/rekap-listrik/index.blade.php`

### Milestone 2: Modul Kasir Pembayaran Listrik (Mobile-First POS)
- **Tujuan**: Memfasilitasi pembayaran tagihan warga di posko atau saat keliling langsung via smartphone.
- **Implementasi**:
  1. **Input Group Rupiah Bersih**: Mengganti absolute floating `Rp` dengan input-group addon flex (`border-r border-gray-200 bg-gray-100/90 text-gray-500 font-black`) sehingga angka `0` tidak pernah tumpang tindih.
  2. **Format Ribuan Real-Time**: Input uang diterima langsung berformat titik otomatis (`20000` $\rightarrow$ `20.000`).
  3. **Logika Kembalian vs Saldo Titipan**:
     - Jika uang diterima > total wajib: Tampil status **KEMBALIAN** (hijau), uang kembalian diserahkan ke warga sehingga tidak ada saldo titipan (`selisih_bayar = 0`).
     - Opsi Darurat: Checkbox *"Tidak ada kembalian? Simpan sebagai saldo titipan bulan depan"* (`selisih_bayar > 0`).
     - Jika uang diterima < total wajib: Tampil status **KURANG / NINGGAL** (merah, `selisih_bayar < 0`).
  4. **Nama Pembayar**: Kolom nama pembayar tersedia di modal kasir pembayaran (opsional, jika dibayarkan oleh anak/kerabat).
  5. **Pembayaran Gabungan (Multi-Struk)**: Fitur centang beberapa struk dalam 1 keluarga untuk dibayar sekaligus dalam 1 nota transaksi (`GAB-XXXXXX`).
  6. **Integrasi Buku Kas**: Tombol *"Bukukan Kas ke Modul Keuangan"* mencatat otomatis pendapatan jasa kas Karang Taruna ke buku kas dusun.
- **Berkas Terkait**:
  - `app/Http/Controllers/Admin/PembayaranListrikController.php`
  - `resources/views/admin/pembayaran-listrik/index.blade.php`
  - `database/migrations/2026_10_26_085027_add_nama_pembayar_to_tagihan_listrik_table.php`

### Milestone 3: Master Data Warga & Sanitasi Tipografi
- **Tujuan**: Menghilangkan tampilan kaku dan menyelaraskan istilah dengan struk lapangan.
- **Implementasi**:
  - Mengubah label "No. IDPEL / Meter PLN" menjadi **Nomor Struk [ # ]**.
  - Merapikan tipografi dari UPPERCASE menjadi Title Case yang elegan.
  - Memperbarui layout modal tambah/edit warga dengan touch target mobile-friendly.
- **Berkas Terkait**:
  - `resources/views/admin/warga/index.blade.php`
  - `app/Http/Controllers/Admin/WargaController.php`

### Milestone 4: Migrasi Cloud Database (TiDB Cloud Serverless)
- **Tujuan**: Menyediakan database cloud gratis tanpa kartu kredit dengan kompatibilitas penuh MySQL 8.0.
- **Implementasi**:
  - Membuat instance TiDB Serverless di region AWS Singapore (`gateway01.ap-southeast-1.prod.aws.tidbcloud.com:4000`).
  - Menjalankan 13 migrasi Laravel dan seeder data lengkap (47 warga riil RT 1, RT 2, akun pengurus, dan data awal).
  - Mengonfigurasi `config/database.php` agar mendukung koneksi SSL TLS tanpa warning deprecation di PHP 8.5+.

### Milestone 5: Serverless Deployment Vercel
- **Tujuan**: Hosting aplikasi secara gratis dengan CI/CD otomatis dari GitHub, kecepatan CDN global, dan zero-downtime deployment.
- **Implementasi**:
  1. **Konfigurasi `vercel.json`**:
     - Routing aset statis `/build/(.*)` dan file media langsung dilayani Edge CDN Vercel.
     - Routing dinamis Laravel diarahkan ke runtime `api/index.php` (`vercel-php@0.9.0`).
     - Output build disesuaikan ke direktori `public` (`"outputDirectory": "public"`, `"buildCommand": "npm run build"`).
  2. **Serverless Entrypoint `api/index.php`**:
     - Adaptor read-only filesystem AWS Lambda: mengarahkan storage, compiled views, dan cache packages/services ke direktori writable `/tmp/`.
     - Pembersihan sanitasi string UTF-8 BOM pada variabel lingkungan.
     - Suppress error deprecation runtime PHP 8.5.
  3. **Paksa HTTPS & Trust Proxies**:
     - Menambahkan `$middleware->trustProxies(at: '*')` di `bootstrap/app.php`.
     - `URL::forceScheme('https')` di `AppServiceProvider.php` untuk mencegah Mixed Content.
     - CSP tag `<meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">` pada layout Blade.
  4. **Pencegahan Kontaminasi Cache Lokal**:
     - Menambahkan `bootstrap/cache/*.php` ke `.vercelignore` agar cache local development (`laravel/boost`, `laravel/pail`) tidak terbawa ke server produksi.

### Milestone 6: Sanitasi Produksi (Production Ready) & Pembersihan Data Dummy
- **Tujuan**: Menyiapkan sistem untuk penggunaan riil, mengamankan antarmuka publik, dan membersihkan data pengujian.
- **Implementasi**:
  1. **Sanitasi Halaman Login**: Menghapus kotak petunjuk demo kredensial (*Akun Bawaan Sistem*) dari `resources/views/auth/login.blade.php` agar kredensial tidak terekspos ke publik.
  2. **Pembersihan Database Cloud TiDB**:
     - Mengosongkan data transaksi dummy: `tagihan_listrik` (0), `barang` (0), `keuangan` (0), `peminjaman` (0), dan `sessions` (0).
     - Menghapus akun admin demo (`admin@demangan.id`), menyisakan hanya **1 akun Superadmin** (`superadmin@demangan.id`).
     - **Mempertahankan utuh 47 Master Data Warga** (RT 01 & RT 02).
  3. **Penyempurnaan Seeder**:
     - Membuat `MasterWargaSeeder.php` yang khusus menginisialisasi 47 master warga tanpa membuat tagihan dummy.
     - Menyederhanakan `DatabaseSeeder.php` untuk hanya mendaftarkan akun Superadmin dan memanggil `MasterWargaSeeder.php`.

---

## 4. Masalah yang Terjadi & Solusi Teknis Permanen (Troubleshooting Log)

| Masalah yang Terjadi | Penyebab Utama | Solusi Teknis yang Diterapkan |
|---|---|---|
| **Render.com meminta kartu kredit** | Kebijakan baru Render mewajibkan verifikasi CC $1 USD untuk semua instance. | Beralih ke arsitektur **Vercel Serverless (PHP) + TiDB Cloud Serverless** (100% gratis tanpa CC). |
| **Teks input bentrok dengan prefix "Rp"** | Prefix menggunakan class `absolute` dengan padding `pl-11`/`pl-13` yang tidak pas dengan input. | Diubah menjadi flex input-group addon bersatu: `span` Rp di kiri dengan border pemisah dan input nominal di kanan. |
| **`[STATIC_BUILD_NO_OUT_DIR]` saat build Vercel** | Vercel mengira proyek adalah frontend murni dan mencari folder `dist`. | Menambahkan `"outputDirectory": "public"` dan `"buildCommand": "npm run build"` di `vercel.json`. |
| **Tampilan plain HTML / Source PHP tampil polos di browser** | Konfigurasi `"handle": "filesystem"` di `vercel.json` menyajikan `public/index.php` sebagai file teks statis. | Menghapus `"handle": "filesystem"`, membuat rute eksplisit untuk aset statis (`/build/*`, gambar, css), dan me-route sisanya ke `api/index.php`. |
| **Mixed Content: Asset CSS/JS diblokir browser** | Vercel berada di balik reverse proxy HTTPS, namun Laravel menganggap request masuk via HTTP internal. | Konfigurasi `trustProxies(at: '*')` di `bootstrap/app.php` dan `URL::forceScheme('https')` di `AppServiceProvider.php`. |
| **`Constant PDO::MYSQL_ATTR_SSL_CA is deprecated since 8.5`** | PHP 8.5 di Vercel mendeprekasi konstanta global PDO MySQL lama. | Menggunakan conditional constant `Pdo\Mysql::ATTR_SSL_CA` dengan fallback integer universal `1009` & `1014` di `config/database.php`. |
| **`Driver [﻿database] not supported`** | Pipe Windows PowerShell 5.1 (`| vercel env add`) menyisipkan karakter tersembunyi UTF-8 BOM (`\xEF\xBB\xBF`). | Dibuat loop pembersihan byte BOM di `api/index.php` dan semua env di-input ulang secara bersih menggunakan Node.js Buffer UTF-8. |
| **`The /var/task/user/bootstrap/cache directory must be present and writable`** | Vercel filesystem `/var/task` bersifat read-only sehingga Laravel gagal menulis package manifest cache. | Mengarahkan `APP_PACKAGES_CACHE` dan `APP_SERVICES_CACHE` ke `/tmp/bootstrap/cache/` di `api/index.php`. |
| **`Class "Laravel\Boost\BoostServiceProvider" not found`** | File `bootstrap/cache/packages.php` lokal berisi package `require-dev` terunggah ke Vercel. | Menambahkan `bootstrap/cache/*.php` ke `.vercelignore` dan menghapus cache lokal sebelum deployment. |
| **Domain Vercel memiliki suffix random (`-rho`)** | Vercel menghasilkan default deployment domain otomatis dengan suffix branch. | Mengatur canonical domain alias langsung ke `https://dusundemangan.vercel.app`. |

---

## 5. Checklist Pengujian & Verifikasi

- [x] **Koneksi TiDB Cloud**: Berhasil terkoneksi via SSL TLS tanpa error deprecation.
- [x] **Migrasi & Seed Data**: 47 warga, akun superadmin, inventaris, dan buku kas lengkap.
- [x] **Halaman Publik**:
  - Beranda (`/`): 200 OK.
  - Katalog Inventaris (`/katalog-inventaris`): 200 OK.
  - Transparansi Kas (`/transparansi-kas`): 200 OK (data dinamis kas terbaca dari TiDB).
- [x] **Halaman Login**: `/login` merender form dengan aman (CSRF & Session via TiDB Database).
- [x] **Dashboard Admin**: Berhasil login dengan `admin@demangan.id` / `password`.
- [x] **Modul Rekap Listrik**:
  - Modal sequential entry berfungsi nomor per nomor.
  - Input "Atas Nama PLN" & "Nama Penghuni" sudah dieliminasi.
- [x] **Modul Kasir Pembayaran**:
  - Addon `Rp` rapi dan tidak tumpang tindih.
  - Format ribuan real-time bekerja.
  - Nama pembayar tersimpan dengan benar.
  - Status kembalian vs saldo titipan akurat.
- [x] **Automated Feature Tests**: 11 feature test di `tests/Feature/TagihanListrikTest.php` lulus 100%.

---

## 6. Prosedur Operasional Standar (SOP) untuk Sesi Selanjutnya

### A. Larangan Keras
1. **Dilarang Menjalankan Migration Fresh**:
   Jangan pernah menggunakan `php artisan migrate:fresh` atau `migrate:refresh` karena akan menghapus 47 data warga asli Dusun Demangan.
2. **Dilarang Menghapus `.vercelignore`**:
   Pastikan file `bootstrap/cache/*.php` dan `.env*` selalu di-ignore agar cache lokal tidak merusak runtime Lambda di Vercel.

### B. Prosedur Deploy Perubahan Baru
Setiap kali ada perubahan kode atau fitur baru:
1. Jalankan pengujian feature test:
   ```bash
   php artisan test --compact tests/Feature/TagihanListrikTest.php
   ```
2. Pastikan format kode rapi sesuai Laravel Pint:
   ```bash
   vendor/bin/pint --format agent
   ```
3. Commit dan push ke branch `main`:
   ```bash
   git add .
   git commit -m "feat: deskripsi perubahan"
   git push origin main
   ```
   *Vercel akan otomatis mendeteksi push GitHub dan melakukan build & deploy dalam waktu ~30 detik.*
4. Atau deploy manual langsung dari terminal jika diperlukan:
   ```bash
   npx vercel --prod --yes
   ```

---

## 7. Roadmap & Rencana Pengembangan Selanjutnya (Backlog)
1. **Fitur Ekspor / Cetak Struk Bukti Bayar PDF**:
   - Menyediakan tombol cetak struk mini ukuran printer thermal Bluetooth (58mm/80mm) untuk warga yang membutuhkan bukti fisik pembayaran.
2. **Notifikasi WhatsApp Konfirmasi Bayar**:
   - Mengirim ringkasan nota pembayaran otomatis ke nomor WhatsApp warga jika nomor HP terdaftar.
3. **Rekap Tahunan & Grafik Analisis Pengeluaran Listrik Dusun**:
   - Widget visualisasi tren kenaikan atau penurunan konsumsi tagihan listrik warga per RT dari bulan ke bulan.
