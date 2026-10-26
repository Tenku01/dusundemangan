# Konteks Proyek & Rekam Jejak Pengembangan (Antigravity / Gemini)

Dokumen ini adalah ringkasan konteks teknis, status sistem, arsitektur, dan riwayat progres aplikasi untuk melanjutkan sesi kerja di kemudian hari tanpa kehilangan alur pengembangan.

---

## 1. Identitas & Lingkungan Sistem
- **Nama Aplikasi**: Sistem Monitoring Dusun & Kasir Listrik Warga Karang Taruna Demangan
- **Framework**: Laravel 12 (PHP 8.3) + Blade + TailwindCSS + Alpine.js
- **Database**: MySQL (`monitoring_dusun`)
- **Server Lokal**:
  - `php artisan serve` (port 8000)
  - `npm run dev` (Vite)
- **Kredensial Superadmin**:
  - Email: `superadmin@kartar-demangan.id`
  - Password: `password`

---

## 2. ATURAN KRUSIAL & LARANGAN MUTLAK
> [!CAUTION]
> **DILARANG KERAS MENGHAPUS / WIPE DATABASE!**
> - Database saat ini berisi **47 data warga riil** Dusun Demangan (tersebar di RT 1, RT 2, dan RT 3) serta puluhan riwayat tagihan listrik siklus September, Oktober, dan November 2026.
> - **JANGAN PERNAH** menjalankan `php artisan migrate:fresh`, `migrate:refresh`, atau menggunakan trait `RefreshDatabase` di PHPUnit yang menghapus database.
> - Di feature test, gunakan pencatatan manual ID uji (`$createdTagihanIds[]`, `$createdWargaIds[]`) yang dibersihkan di method `tearDown()`.

---

## 3. Brand Identity & Standar Tampilan (UI/UX)
- **Warna Utama (Brand CTA)**: Emas Karang Taruna Demangan:
  - Background: `bg-[#CEA042]`
  - Hover: `hover:bg-[#BA8832]`
  - Aksen / Border: `border-amber-300`, `text-[#CEA042]`
- **Mobile-First UX**:
  - Semua proses pencatatan rekap dan kasir pembayaran dioperasikan langsung dari HP oleh pengurus di lapangan.
  - Modal menggunakan pola *Bottom Sheet* di HP (`rounded-t-3xl`), dengan ukuran touch target tombol minimal 44px (`min-h-[44px]`).
  - Search real-time otomatis menyaring data tanpa perlu menekan tombol submit/CTA.

---

## 4. Logika Bisnis & Fitur yang Telah Selesai (Progress Terkini)

### A. Modul Rekap Listrik (`RekapListrikController`)
1. **Pemisahan Modul**: Menu Rekap Listrik mandiri terpisah dari Menu Pembayaran Listrik.
2. **Pengurutan Siklus Periode Kronologis**:
   - Siklus diurutkan berdasarkan kalender riil (Januari – Desember) via `TagihanListrik::parsePeriodeToKey()` dan `TagihanListrik::getDaftarPeriodeKronologis()`.
   - Dropdown pemilih periode menggunakan Custom Floating Alpine.js dengan badge indikator `Aktif` dan `Terbaru`.
3. **Duplikasi Siklus & Saldo Titipan Otomatis**:
   - Membuka bulan baru otomatis membawa kelebihan bayar (`selisih_bayar > 0`) dari bulan sebelumnya sebagai **potongan saldo titipan**, atau kekurangan bayar (`selisih_bayar < 0`) sebagai **tunggakan**.
4. **Scan & Impor Dokumen Pintar (Banyak Warga Sekaligus)**:
   - **Format Input**: Foto kamera HP langsung (`capture="environment"`), foto galeri, file dokumen PDF, maupun spreadsheet Excel/CSV (`.xlsx`, `.csv`).
   - **Membaca Banyak Warga Sekaligus**: Dalam 1 lembar foto kertas rekap fisik atau 1 file dokumen, sistem membaca seluruh nama warga dan nominal tagihan PLN yang ada di dalamnya.
   - **Layar Cross-Check & Verifikasi (User Review)**:
     - Preview dokumen asli ditampilkan berdampingan (bisa di-zoom).
     - Tabel review interaktif memungkinkan koreksi nomor struk, pergantian nama warga dari master data RT, edit nominal rupiah, penambahan baris manual, atau penghapusan baris.
   - **Penyimpanan Batch**: Dikirim ke endpoint `POST /admin/rekap-listrik/batch-store` yang memproses tagihan banyak warga sekaligus.

### B. Modul Kasir Pembayaran Listrik (`PembayaranListrikController`)
1. **Format Penulisan Rupiah Real-Time**:
   - Pada semua input nominal uang (kasir bayar, rekap struk, keuangan), angka yang diketik langsung terformat titik ribuan secara live (`20000` $\rightarrow$ `20.000`, `200000` $\rightarrow$ `200.000`).
2. **Logika Kembalian vs Titipan (Sesuai Arahan Pengguna)**:
   - **Jika Uang Diterima > Total Wajib**:
     - Status **KEMBALIAN** langsung tampil jelas (hijau). Uang kembalian diserahkan ke warga sehingga **tidak ada titipan** (`selisih_bayar = 0`).
     - **Opsi Darurat**: Titipan hanya berfungsi jika tidak ada kembalian (ada checkbox: *"Tidak ada kembalian? Simpan sebagai saldo titipan bulan depan"*). Jika dicentang, barulah sisa uang dicatat sebagai saldo titipan (`selisih_bayar > 0`).
   - **Jika Uang Diterima < Total Wajib**:
     - Status **KURANG / NINGGAL** langsung tampil (merah) dan tercatat sebagai tagihan sisa (`selisih_bayar < 0`).
   - **Jika Uang Pas**:
     - Status **Uang Pas (Lunas)**.
3. **Pembayaran Gabungan (Multi-Struk)**:
   - Kasir dapat mencentang beberapa warga dalam satu keluarga untuk dibayarkan sekaligus dengan satu nomor transaksi gabungan (`GAB-XXXXXX`).
4. **Rekonsiliasi Saldo Kasir vs Rekap**:
   - Kartu metrik: *Total Tagihan PLN Awal*, *Kas Karang Taruna Terkumpul*, *Total Uang Fisik Kasir*, dan *Status Klop Rekonsiliasi 100%*.
   - Tombol **"Bukukan Kas ke Modul Keuangan"** untuk mencatat otomatis iuran kas Karang Taruna ke buku kas dusun.

---

## 5. Struktur Berkas Kunci
- **Model**:
  - `app/Models/TagihanListrik.php` (logika kalkulasi tagihan, parse kronologis bulan, mutasi saldo)
  - `app/Models/Warga.php` (master warga dusun, relasi per RT)
  - `app/Models/Keuangan.php` (buku kas dusun)
- **Controller**:
  - `app/Http/Controllers/Admin/RekapListrikController.php` (rekap, duplikasi siklus, batch store OCR/Excel)
  - `app/Http/Controllers/Admin/PembayaranListrikController.php` (kasir, bayar, batal, bukukan kas)
  - `app/Http/Controllers/Admin/KeuanganController.php` (catat kas, laporan keuangan)
- **Views**:
  - `resources/views/admin/rekap-listrik/index.blade.php` (daftar rekap, modal catat, modal siklus, modal scan banyak warga)
  - `resources/views/admin/pembayaran-listrik/index.blade.php` (kasir HP, floating action gabungan, modal fast POS)
  - `resources/views/layouts/admin.blade.php` (layout utama, format rupiah global, bottom navigation)
- **Tests**:
  - `tests/Feature/TagihanListrikTest.php` (11 feature test, 100% lulus)

---

## 6. Perintah Verifikasi yang Valid
- Menjalankan feature test:
  ```bash
  php artisan test --compact tests/Feature/TagihanListrikTest.php
  ```
- Format kode standar Laravel:
  ```bash
  vendor/bin/pint --format agent
  ```
- Clear cache view:
  ```bash
  php artisan view:clear
  ```
