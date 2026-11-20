<p align="center">
  <img src="public/logo-kartar-dmg-circle.png" width="110" alt="Logo Karang Taruna Dusun Demangan">
</p>

<h1 align="center">Sistem Informasi &amp; Kasir Listrik Dusun Demangan</h1>

<p align="center">
  <strong>Karang Taruna Dusun Demangan, Desa Candimulyo</strong><br>
  Portal layanan publik warga, transparansi kas dusun, inventaris barang, serta aplikasi rekap dan kasir pembayaran listrik berbasis web &amp; PWA.
</p>

<p align="center">
  <a href="https://dusundemangan.vercel.app">Demo Production (Vercel)</a>
</p>

---

## Ringkasan Project

Aplikasi ini dibangun untuk membantu pengurus Karang Taruna Dusun Demangan mengelola administrasi dusun secara transparan, rapi, dan cepat langsung dari perangkat seluler maupun desktop.

### Fitur Utama

#### 1. Portal Publik Warga
- **Katalog & Peminjaman Inventaris**: Warga dapat melihat daftar barang inventaris dusun yang tersedia, mengajukan peminjaman, dan melacak status pengajuan via kode resi.
- **Transparansi Kas Dusun**: Menampilkan saldo kas (Kas Karang Taruna, Kas Listrik, dan total kas) serta riwayat mutasi masuk/keluar yang dapat difilter berdasarkan arus kas, bulan, dan tahun.
- **Cek Tagihan Listrik Warga**: Pencarian tagihan listrik bulanan berdasarkan nama pelanggan/warga atau nomor struk beserta riwayat status lunas.

#### 2. Panel Pengurus (Mobile-First & Kontrol Akses Menu)
- **Rekap Listrik Warga**:
  - Pencatatan cepat berurutan (*sequential entry*) sesuai urutan nomor meter/struk per RT (RT 01, RT 02, RT 03).
  - Pengurutan siklus periode secara kronologis kalender dan duplikasi siklus bulan baru dengan penghitungan otomatis saldo titipan maupun tunggakan bulan sebelumnya.
  - Penggabungan beberapa tagihan ke dalam satu struk kelompok (`GAB-XXXXXX`), ekspor CSV, dan lembar rekap siap cetak ukuran A4.
- **Kasir Pembayaran Listrik**:
  - Pencarian instan tanpa reload, dukungan bayar satuan maupun bayar gabungan, dan pembatalan/pemisahan struk jika diperlukan.
  - Kalkulasi real-time uang diterima, nominal kembalian yang diserahkan, sisa titipan, atau kekurangan bayar, serta pembukuan langsung penerimaan kas Karang Taruna ke Buku Kas Utama.
- **Master Data Warga**: Pengelolaan daftar pelanggan listrik per RT beserta nomor struk dan nama pelanggan PLN.
- **Inventaris & Peminjaman**: Pendataan barang dusun serta alur persetujuan, penolakan, dan pencatatan pengembalian peminjaman.
- **Keuangan & Laporan**: Pencatatan transaksi kas masuk/keluar serta penyusunan laporan arus kas dusun siap cetak.
- **Manajemen Akun Admin**: Pengaturan akun pengurus dan hak akses per menu oleh Superadmin.

#### 3. Progressive Web App (PWA)
- Mendukung instalasi aplikasi ke layar utama perangkat Android, desktop, maupun iPhone/iPad (Safari iOS lengkap dengan panduan visual dan deteksi browser dalam aplikasi).
- Dilengkapi `manifest.webmanifest`, ikon aplikasi multi-ukuran, dan Service Worker (`public/sw.js`).

---

## Teknologi yang Digunakan

| Lapisan | Teknologi |
| --- | --- |
| **Backend** | PHP `^8.3`, Laravel Framework (`^13.17`) |
| **Frontend** | Blade Templates, Tailwind CSS (`^3.1.0`), Alpine.js (`^3.4.2`) |
| **Build Tool** | Vite (`^8.0.0`) + Laravel Vite Plugin |
| **Database** | MySQL / TiDB Cloud Serverless (dengan koneksi SSL) |
| **Deployment** | Vercel Serverless (`vercel-php@0.9.0` via `api/index.php`) |

---

## Struktur Direktori Kunci

```text
├── api/index.php                     # Entrypoint serverless Vercel
├── app/
│   ├── Http/Controllers/Admin/       # Controller Rekap, Kasir, Warga, Inventaris, Kas, Akun
│   └── Models/                       # TagihanListrik, Warga, Barang, Peminjaman, Keuangan, User
├── public/
│   ├── manifest.webmanifest          # Konfigurasi PWA Web App Manifest
│   └── sw.js                         # Service Worker untuk aset statis & fallback offline
├── resources/
│   ├── css/app.css                   # Token tema emas (#CEA042), komponen UI & custom dropdown
│   ├── js/app.js                     # Inisialisasi Alpine.js & custom select dropdown
│   └── views/
│       ├── admin/                    # Tampilan dashboard & modul operasional pengurus
│       ├── layouts/                  # Layout publik, admin, guest & komponen PWA iOS
│       └── publik/                   # Halaman beranda, buku kas, cek listrik & cek status
├── routes/web.php                    # Definisi route publik dan panel admin
└── vercel.json                       # Routing statis CDN & build command Vercel
```

---

## Menjalankan Secara Lokal

### Prasyarat
- PHP 8.3+ dan Composer
- Node.js dan npm
- MySQL (misal via Laragon) atau SQLite untuk pengembangan

### Langkah Instalasi

```bash
# 1. Clone repositori
git clone https://github.com/Tenku01/dusundemangan.git
cd dusundemangan

# 2. Install dependency PHP & JavaScript
composer install
npm ci

# 3. Siapkan file environment & key aplikasi
cp .env.example .env
php artisan key:generate
```

Sesuaikan koneksi database di `.env`, lalu jalankan migrasi dan build aset frontend:

```bash
php artisan migrate
npm run build
php artisan serve
```

> [!IMPORTANT]
> Jika terhubung ke database yang sudah berisi data warga maupun riwayat tagihan aktif, jangan menjalankan `php artisan migrate:fresh` atau `migrate:refresh`.

Untuk pengembangan frontend secara live reload, jalankan `npm run dev` pada terminal terpisah.

---

## Pengujian & Kualitas Kode

```bash
# Menjalankan suite pengujian PHPUnit
php artisan test --compact

# Memeriksa & merapikan format kode PHP menggunakan Laravel Pint
vendor/bin/pint --format agent
```

---

## Deployment Production

Repositori ini terintegrasi dengan CI/CD Vercel: setiap push ke branch `main` akan menjalankan `npm run build` dan men-deploy perubahan ke production di [dusundemangan.vercel.app](https://dusundemangan.vercel.app).

---

<p align="center">
  <sub>&copy; Karang Taruna Dusun Demangan, Desa Candimulyo.</sub>
</p>
