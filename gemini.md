# Konteks Proyek & Rekam Jejak Pengembangan (Antigravity / Gemini)

Dokumen ini adalah ringkasan konteks teknis, status sistem, arsitektur, dan riwayat progres aplikasi untuk melanjutkan sesi kerja di kemudian hari tanpa kehilangan alur pengembangan.

---

## 1. Identitas & Lingkungan Sistem
- **Nama Aplikasi**: Sistem Monitoring Dusun & Kasir Listrik Warga Karang Taruna Demangan
- **Organisasi**: Karang Taruna Dusun Demangan, Desa Candimulyo (Ketua: **Anggun Fatchurrohman**)
- **Framework**: Laravel 13 (`^13.17`, PHP 8.3 lokal / PHP 8.5 di Vercel) + Blade + Tailwind CSS `3.4.19` + Alpine.js + Vite `8`
- **GitHub Repository**: `https://github.com/Tenku01/dusundemangan.git` (Branch `main`)
- **Cloud Hosting & Serverless**: **Vercel** (`https://dusundemangan.vercel.app`)
- **Database Produksi**: **TiDB Cloud Serverless** (MySQL 8.0 kompatibel, AWS Singapura)
  - Host: `gateway01.ap-southeast-1.prod.aws.tidbcloud.com` (Port 4000)
  - Database: `demangan` (SSL wajib aktif via `MYSQL_ATTR_SSL_CA`)
- **Database Lokal**: MySQL Laragon (`monitoring_dusun`)
- **Akun Utama Sistem (Produksi & Lokal)**:
  - **Superadmin**: `superadmin@demangan.id` | Kata Sandi: `password` (Akun admin pengurus dapat ditambahkan langsung oleh superadmin melalui dashboard)

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
6. **Penggabungan Tagihan pada Waktu Rekapitulasi**:
   - Pengurus mengisi nominal tagihan tiap struk/warga terlebih dahulu.
   - Setelah tagihan tercatat, pengurus dapat memilih/mencentang beberapa tagihan warga dan menentukan **Atas Nama Siapa** (misal: "Keluarga Pak Budi") untuk digabungkan menjadi satu struk kelompok (`GAB-XXXXXX`).
   - Tersedia tombol cepat untuk memisahkan struk jika salah gabung.

### B. Modul Kasir Pembayaran Listrik (`PembayaranListrikController`)
1. **Tampilan Kartu Tagihan Gabungan (Bundled Card)**:
   - Tagihan yang sudah digabungkan saat rekap langsung tampil sebagai **satu kartu paket gabungan** yang memperlihatkan **Total Wajib Bayar Gabungan**, daftar nomor struk, dan accordion rincian tiap meter.
   - Kasir cukup menekan satu tombol **"Bayar Gabungan"** untuk langsung memproses lunas seluruh struk dalam kelompok tersebut tanpa perlu mencentang manual satu per satu.
2. **Fleksibilitas & Dinamis di Kasir**:
   - Apabila lupa menggabungkan saat rekap, kasir tetap dapat mencentang beberapa tagihan mandiri dan memilih **"Bayar Gabungan"** atau **"Gabung Dulu"**.
   - Kasir juga dapat memisahkan (*ungroup*) struk gabungan kembali menjadi tagihan mandiri langsung dari kasir jika warga ingin membayar terpisah.
3. **Format Penulisan Rupiah Real-Time**:
   - Angka yang diketik langsung terformat titik ribuan secara live (`20000` $\rightarrow$ `20.000`).
4. **Logika Kembalian vs Titipan**:
   - **Jika Uang Diterima > Total Wajib**: Status **KEMBALIAN** langsung tampil jelas (hijau). Uang kembalian diserahkan ke warga sehingga **tidak ada titipan** (`selisih_bayar = 0`).
   - **Opsi Darurat**: Checkbox titipan jika uang pas tidak ada: *"Tidak ada kembalian? Simpan sebagai saldo titipan bulan depan"*.
   - **Jika Uang Diterima < Total Wajib**: Status **KURANG / NINGGAL** (merah, `selisih_bayar < 0`).
   - **Jika Uang Pas**: Status **Uang Pas (Lunas)**.

### C. Master Data Warga (`WargaController`)
1. **Label Kolom**: Diubah menjadi **Nomor Struk [ # ]** (menggantikan "No. IDPEL / Meter PLN").
2. **Tipografi**: Menggunakan Title Case bersih, menghilangkan huruf kapital berlebih dan font monospace yang kaku.

### D. Pembaruan UI/UX, PWA iOS, Transparansi Kas & Laporan Resmi (Sesi 20 Nov 2026)
1. **Audit Menyeluruh UI/UX & Tema Emas WCAG AA (`resources/css/app.css`)**:
   - Membersihkan utilitas Tailwind v4 yang tidak aktif pada Tailwind v3 (`shadow-xs`, `shadow-2xs`, `backdrop-blur-xs`, `bg-linear-to`), menghapus warna off-theme (`indigo-*`, `purple-*`) dan emoji dekoratif, menggantinya dengan token terpusat `.btn-primary` (teks espresso solid `#1F1406` di atas `#CEA042` / hover `#BA8832`), `.btn-secondary`, `.btn-subtle`, `.btn-gold-soft`, `.input-field`, `.select-field`, `.card-surface`, `.modal-backdrop`, `.modal-panel`, dan `.tabular-money`.
   - Menambahkan tampilan kartu responsif mobile pada `publik/buku_kas.blade.php` dan `admin/warga/index.blade.php`, aksesibilitas modal (`Escape`, klik backdrop, `aria-label`), serta menyamakan penamaan resmi `Karang Taruna Dusun Demangan, Desa Candimulyo`.
2. **Custom Select Dropdown Global (`resources/js/app.js` & `resources/css/app.css`)**:
   - Menambahkan `setupCustomSelects()` untuk menggantikan seluruh popup `<select>` native (biru OS) dengan dropdown mengambang bertema emas (border `#EAD199`, hover `#FAF4E4` + `#674620`, opsi aktif `#CEA042` + `#1F1406` dengan centang), lengkap dengan navigasi keyboard dan sinkronisasi `x-model` / Alpine `x-for`.
   - Menambahkan `'./resources/**/*.js'` di `tailwind.config.js` dan meletakkan class `.custom-select-*` di luar `@layer components` agar selalu ikut terkompilasi.
3. **Panduan & Tombol Instalasi PWA Khusus iPhone / iOS (`resources/views/components/pwa-install-ios.blade.php`)**:
   - Mengatasi keterbatasan iOS Safari yang tidak memicu `beforeinstallprompt` dengan deteksi perangkat iOS non-standalone untuk langsung menampilkan tombol **Pasang App** / **Instal Aplikasi di HP**, banner ringkas yang dapat ditutup (tersimpan 7 hari di `localStorage`), dan modal bottom-sheet 3 langkah Safari Share (*Tambahkan ke Layar Utama*) + tombol salin link bila dibuka dari in-app browser.
   - Memperbarui `<link rel="apple-touch-icon" sizes="180x180">` di seluruh layout utama dan bump cache Service Worker `public/sw.js` ke `demangan-pwa-v2`.
4. **Transparansi Kas & Penyeragaman Istilah "Kas" (`KatalogController@bukuKas` & `publik/buku_kas.blade.php`)**:
   - Mengubah istilah "Kas Dusun" menjadi "Kas" di halaman publik, admin, dan test terkait.
   - Menambahkan filter dropdown **Pilih Kas** (`kategori_kas`), kolom/badge **Kas** di tabel desktop & kartu mobile `/transparansi-kas` serta beranda, dan helper terpusat `Keuangan::getDaftarKategoriKas()`, `labelKategoriKas()`, dan `badgeClassesKategoriKas()`.
5. **Laporan PDF Resmi Format Surat & Tabel Dokumen Word (`admin/keuangan/laporan.blade.php` & `layouts/admin.blade.php`)**:
   - Menambahkan `print:hidden` pada header, sidebar, bottom navigation bar admin, dan banner PWA agar hasil cetak/PDF bebas dari elemen UI web.
   - Mendesain ulang `admin/keuangan/laporan.blade.php` sebagai terbitan surat resmi A4 (`Times New Roman` / `Georgia`, Kop Surat resmi bergaris ganda, tabel identitas surat tanpa garis, paragraf pembuka & penutup formal, tabel akuntansi bergaya dokumen Word dengan header berulang di tiap halaman dan anti-potong baris `break-inside: avoid`), filter jenis kas + bulan berbahasa Indonesia, serta **satu blok tanda tangan Ketua Karang Taruna Dusun Demangan (`Anggun Fatchurrohman`)** tanpa tanda tangan superadmin.
6. **Dokumentasi Repositori (`README.md`)**:
   - Mengganti `README.md` bawaan Laravel dengan ringkasan project, daftar fitur publik/pengurus/PWA, stack teknologi aktual, struktur berkas, panduan lokal, dan deployment Vercel.
7. **Pusat Status Sistem `/status` (`StatusController`, `AuditModelObserver`, `MonitorMethodStatus`)**:
   - Menambahkan dua tabel log non-destruktif via migrasi (`data_change_logs` & `method_status_logs`) dengan penjagaan `Schema::hasTable` agar aman sebelum migrasi.
   - **Tab 1 (Prioritas) — Log Perubahan Data**: Mencatat otomatis operasi `created`/`updated`/`deleted` pada model `User`, `Warga`, `TagihanListrik`, `Keuangan`, `Barang`, dan `Peminjaman` beserta identitas pengubah, IP, dan diff nilai (dengan penyensoran `password`/`remember_token`), serta tombol **Bersihkan Log Perubahan**.
   - **Tab 2 — Status Method & Memori**: Middleware `MonitorMethodStatus` mencatat performa & status HTTP tiap request beserta tombol **Bersihkan Log Method** dan **Bersihkan Memori & Cache** (`Cache::flush()`, `view:clear`, `gc_collect_cycles()`).
   - **Tab 3 — Status Update Web**: Rilis aplikasi tersimpan permanen di `config/web_updates.php` (tanpa tombol maupun rute hapus).
8. **Halaman Error Lengkap (`resources/views/errors/`)**:
   - Membangun `errors/layout.blade.php` mandiri bertema emas Dusun Demangan (tanpa query DB agar tetap tampil saat gangguan database) untuk seluruh kode HTTP `3xx` (`300`, `301`, `302`, `304`, `307`, `308`, `3xx`), `4xx` (`400`, `401`, `402`, `403`, `404`, `405`, `408`, `419`, `422`, `429`, `4xx`), dan `5xx` (`500`, `502`, `503`, `504`, `5xx`).

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
