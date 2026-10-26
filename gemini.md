# Konteks Proyek & Rekam Jejak Pengembangan (Antigravity / Gemini)

Dokumen ini adalah ringkasan konteks teknis, status sistem, arsitektur, dan riwayat progres aplikasi untuk melanjutkan sesi kerja di kemudian hari tanpa kehilangan alur pengembangan.

---

## 1. Identitas & Lingkungan Sistem
- **Nama Aplikasi**: Sistem Monitoring Dusun & Kasir Listrik Warga Karang Taruna Demangan
- **Framework**: Laravel 12 (PHP 8.3 lokal / PHP 8.5 di Vercel) + Blade + TailwindCSS + Alpine.js
- **GitHub Repository**: `https://github.com/Tenku01/dusundemangan.git` (Branch `main`)
- **Cloud Hosting & Serverless**: **Vercel** (`https://dusundemangan.vercel.app`)
- **Database Produksi**: **TiDB Cloud Serverless** (MySQL 8.0 kompatibel, AWS Singapura)
  - Host: `gateway01.ap-southeast-1.prod.aws.tidbcloud.com` (Port 4000)
  - Database: `demangan` (SSL wajib aktif via `MYSQL_ATTR_SSL_CA`)
- **Database Lokal**: MySQL Laragon (`monitoring_dusun`)
- **Akun Bawaan Sistem (Lokal & Produksi)**:
  - **Admin Pengurus**: `admin@demangan.id` | Kata Sandi: `password`
  - **Superadmin**: `superadmin@demangan.id` | Kata Sandi: `password`

---

## 2. ATURAN KRUSIAL & LARANGAN MUTLAK
> [!CAUTION]
> **DILARANG KERAS MENGHAPUS / WIPE DATABASE!**
> - Database saat ini berisi **47 data warga riil** Dusun Demangan (RT 1, RT 2, dan RT 3) serta puluhan riwayat tagihan listrik, inventaris, dan buku kas.
> - **JANGAN PERNAH** menjalankan `php artisan migrate:fresh`, `migrate:refresh`, atau menggunakan trait `RefreshDatabase` di PHPUnit yang menghapus database.
> - Di feature test, gunakan pencatatan manual ID uji (`$createdTagihanIds[]`, `$createdWargaIds[]`) yang dibersihkan di method `tearDown()`.

---

## 3. Arsitektur Deployment Vercel & Serverless
Aplikasi berjalan sebagai serverless function di Vercel dengan integrasi CI/CD dari GitHub:
1. **Entrypoint Serverless (`api/index.php`)**:
   - Menangani sanitasi byte UTF-8 BOM pada variabel lingkungan (`$_ENV` & `$_SERVER`).
   - Mengarahkan `APP_PACKAGES_CACHE` dan `APP_SERVICES_CACHE` ke direktori writable `/tmp/bootstrap/cache/`.
   - Mengarahkan view engine Blade ke `/tmp/views`.
   - Mengaktifkan `$app->useStoragePath('/tmp/storage')`.
   - Mengabaikan warning deprecation runtime PHP 8.5 dan mematikan `display_errors` agar stream HTTP tidak rusak.
2. **Konfigurasi Routing (`vercel.json`)**:
   - Aset statis terkompilasi (`/build/*`), favicon, gambar (`/logo-*`), dan file statis lainnya disajikan langsung lewat CDN global Vercel.
   - Seluruh rute dinamis Laravel diarahkan ke runtime `api/index.php` (`vercel-php@0.9.0`).
   - Tidak menggunakan block `"env"` di `vercel.json` agar tidak menimpa variabel rahasia di dashboard Vercel.
3. **Konfigurasi SSL Database (`config/database.php`)**:
   - Menggunakan nilai numerik universal `1009` (`MYSQL_ATTR_SSL_CA`) dan `1014` (`MYSQL_ATTR_SSL_VERIFY_SERVER_CERT`) untuk kompatibilitas tanpa warning deprecation di PHP 8.5+.
4. **Vercel System & Project Environment Variables**:
   - **Environment Variables Proyek (Vercel Project Settings)**:
     - `APP_NAME`: `Dusun Demangan`
     - `APP_ENV`: `production`
     - `APP_KEY`: Application encryption key Laravel (Base64)
     - `APP_DEBUG`: `false`
     - `APP_URL`: `https://dusundemangan.vercel.app`
     - `ASSET_URL`: `https://dusundemangan.vercel.app`
     - `DB_CONNECTION`: `mysql`
     - `DB_HOST`: `gateway01.ap-southeast-1.prod.aws.tidbcloud.com`
     - `DB_PORT`: `4000`
     - `DB_DATABASE`: `demangan`
     - `DB_USERNAME`: `cX5UUTnVXyNCtpZ.root`
     - `DB_PASSWORD`: `b8r6GTltOTZHRERe`
     - `DB_SSL_CA`: `true`
     - `SESSION_DRIVER`: `database` (disimpan di tabel `sessions` TiDB Cloud karena filesystem Vercel read-only)
     - `CACHE_STORE`: `database` (disimpan di tabel `cache` TiDB Cloud)
     - `LOG_CHANNEL`: `stderr` (ditangkap langsung oleh Vercel Runtime Logs)
   - **Vercel System Environment Variables (Otomatis disediakan oleh Vercel)**:
     *(Dokumentasi resmi: https://vercel.com/docs/environment-variables/system-environment-variables)*
     - `VERCEL`: Bernilai `1` jika berjalan di infrastruktur Vercel. Digunakan oleh `AppServiceProvider.php` dan `api/index.php` untuk mendeteksi runtime serverless.
     - `VERCEL_ENV`: Mengindikasikan environment deployment: `production`, `preview`, atau `development`.
     - `VERCEL_URL`: Hostname deployment unik tanpa protokol (misal: `dusundemangan-xxx.vercel.app`).
     - `VERCEL_PROJECT_PRODUCTION_URL`: Domain canonical produksi proyek (`dusundemangan.vercel.app`).
     - `VERCEL_REGION`: Kode region serverless execution (misal: `iad1` untuk Washington D.C., `sin1` untuk Singapura).
     - `VERCEL_GIT_PROVIDER`: Provider Git (misal: `github`).
     - `VERCEL_GIT_REPO_SLUG`: Slug repositori (misal: `dusundemangan`).
     - `VERCEL_GIT_REPO_OWNER`: Pemilik repositori (`Tenku01`).
     - `VERCEL_GIT_COMMIT_REF`: Branch target deployment (`main`).
     - `VERCEL_GIT_COMMIT_SHA`: Hash commit Git yang sedang di-build.
   - **Catatan Sanitasi String PowerShell**:
     Ketika menambahkan env via CLI dari Windows PowerShell 5.1, pipe `| vercel env add` dapat menyisipkan byte UTF-8 BOM (`\xEF\xBB\xBF`). Di `api/index.php`, seluruh `$_ENV` dan `$_SERVER` otomatis dibersihkan dari byte BOM ini saat booting.

---

## 4. Brand Identity & Standar Tampilan (UI/UX)
- **Warna Utama (Brand CTA)**: Emas Karang Taruna Demangan:
  - Background: `bg-[#CEA042]`
  - Hover: `hover:bg-[#BA8832]`
  - Aksen / Border: `border-amber-300`, `text-[#CEA042]`
- **Mobile-First UX**:
  - Semua proses pencatatan rekap dan kasir pembayaran dioperasikan langsung dari HP oleh pengurus di lapangan.
  - Modal menggunakan pola *Bottom Sheet* di HP (`rounded-t-3xl`), dengan ukuran touch target tombol minimal 44px (`min-h-[44px]`).
  - Search real-time otomatis menyaring data tanpa perlu menekan tombol submit/CTA.
  - Input addon grup nominal `Rp` menyatu rapi tanpa overlap teks `0`.

---

## 5. Logika Bisnis & Fitur yang Telah Selesai

### A. Modul Rekap Listrik (`RekapListrikController`)
1. **Pemisahan Modul**: Menu Rekap Listrik mandiri terpisah dari Menu Pembayaran Listrik.
2. **Alur Catat Cepat Berurutan (Sequential Entry)**:
   - Input rekap struk dipandu langsung nomor per nomor (*"Warga [x] - Struk #[y] tagihan awalnya berapa?"*).
   - Diurutkan berdasarkan `nomor_meter` secara numerik (`CAST(nomor_meter AS UNSIGNED) ASC`).
   - Nama warga dan nomor struk langsung terisi otomatis.
3. **Penyederhanaan Form Rekap**:
   - Menghapus input "Atas Nama PLN" dan "Nama Penghuni" dari modal rekap bulanan.
   - Memindahkan input opsional "Nama Pembayar" ke modal kasir pembayaran.
4. **Pengurutan Siklus Periode Kronologis**:
   - Siklus diurutkan berdasarkan kalender riil (Januari – Desember) via `TagihanListrik::parsePeriodeToKey()` dan `TagihanListrik::getDaftarPeriodeKronologis()`.
5. **Duplikasi Siklus & Saldo Titipan Otomatis**:
   - Membuka bulan baru otomatis membawa kelebihan bayar (`selisih_bayar > 0`) dari bulan sebelumnya sebagai **potongan saldo titipan**, atau kekurangan bayar (`selisih_bayar < 0`) sebagai **tunggakan**.

### B. Modul Kasir Pembayaran Listrik (`PembayaranListrikController`)
1. **Format Penulisan Rupiah Real-Time**:
   - Angka yang diketik langsung terformat titik ribuan secara live (`20000` $\rightarrow$ `20.000`).
2. **Logika Kembalian vs Titipan**:
   - **Jika Uang Diterima > Total Wajib**: Status **KEMBALIAN** langsung tampil jelas (hijau). Uang kembalian diserahkan ke warga sehingga **tidak ada titipan** (`selisih_bayar = 0`).
   - **Opsi Darurat**: Checkbox titipan jika uang pas tidak ada: *"Tidak ada kembalian? Simpan sebagai saldo titipan bulan depan"*.
   - **Jika Uang Diterima < Total Wajib**: Status **KURANG / NINGGAL** (merah, `selisih_bayar < 0`).
   - **Jika Uang Pas**: Status **Uang Pas (Lunas)**.
3. **Pembayaran Gabungan (Multi-Struk)**:
   - Kasir dapat mencentang beberapa warga dalam satu keluarga untuk dibayarkan sekaligus dengan satu nomor transaksi gabungan (`GAB-XXXXXX`).
4. **Input Nama Pembayar**:
   - Tersedia di modal kasir pembayaran (opsional, jika dibayarkan oleh anak/kerabat).

### C. Master Data Warga (`WargaController`)
1. **Label Kolom**: Diubah menjadi **Nomor Struk [ # ]** (menggantikan "No. IDPEL / Meter PLN").
2. **Tipografi**: Menggunakan Title Case bersih, menghilangkan huruf kapital berlebih dan font monospace yang kaku.

---

## 6. Struktur Berkas Kunci
- **Model**:
  - `app/Models/TagihanListrik.php` (logika kalkulasi tagihan, parse kronologis bulan, mutasi saldo)
  - `app/Models/Warga.php` (master warga dusun, relasi per RT)
  - `app/Models/Keuangan.php` (buku kas dusun)
- **Controller**:
  - `app/Http/Controllers/Admin/RekapListrikController.php` (rekap, duplikasi siklus, input berurutan)
  - `app/Http/Controllers/Admin/PembayaranListrikController.php` (kasir, bayar, simpan nama pembayar, batal, bukukan kas)
  - `app/Http/Controllers/Admin/KeuanganController.php` (catat kas, laporan keuangan)
  - `app/Http/Controllers/Admin/WargaController.php` (master data warga)
- **Views**:
  - `resources/views/admin/rekap-listrik/index.blade.php` (rekap, modal catat berurutan, modal siklus)
  - `resources/views/admin/pembayaran-listrik/index.blade.php` (kasir HP, floating action gabungan, modal bayar + nama pembayar)
  - `resources/views/admin/warga/index.blade.php` (master warga, nomor struk)
  - `resources/views/layouts/admin.blade.php` (layout admin, bottom nav)
  - `resources/views/layouts/publik.blade.php` (katalog publik, transparansi kas)
- **Konfigurasi Serverless & CI/CD**:
  - `vercel.json` (rute statis CDN, function PHP, build command)
  - `api/index.php` (serverless bootstrapping, path sanitization)
  - `config/view.php` (compiled view path `/tmp/views`)
  - `config/database.php` (koneksi TiDB Cloud & MySQL dengan SSL)

---

## 7. Perintah Verifikasi & Deployment yang Valid
- Menjalankan feature test:
  ```bash
  php artisan test --compact tests/Feature/TagihanListrikTest.php
  ```
- Format kode standar Laravel Pint:
  ```bash
  vendor/bin/pint --format agent
  ```
- Deploy manual via Vercel CLI:
  ```bash
  npx vercel --prod --yes
  ```
- Deploy otomatis via Git CI/CD:
  ```bash
  git add .
  git commit -m "pesan commit"
  git push origin main
  ```
