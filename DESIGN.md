# Pedoman Desain (DESIGN.md)
## Sistem Manajemen Inventaris & Keuangan Dusun Demangan

> Dokumen ini merupakan panduan identitas visual, kepribadian, tipografi, dan palet warna yang diekstrak langsung dari PRD Karang Taruna Demangan.

---

### 1. Dial Liveliness (Antislop Dials)
- **ENERGY**: 1 (Tenang, terstruktur, berbasis layanan publik dan transparansi kas warga)
- **RHYTHM**: 2 (Seimbang dan fungsional antara katalog barang, buku kas, formulir interaktif, dan tabel mutasi)
- **MOTION**: 1 (Transisi fungsional dan mikro-interaksi tombol/modal; tidak ada animasi dekoratif berlebihan)

### 2. Palet Warna (Color Palette)
- **Clean White (`#FFFFFF`)**: Warna latar belakang utama halaman, kontainer kartu, dan modal.
- **Primary Gold (`#CEA042`)**: Warna aksen identitas Karang Taruna Demangan untuk navbar header badge, tombol aksi utama (CTA), dan garis penekanan fokus.
- **Neutral Gray (`#F3F4F6` / `#E5E7EB`)**: Warna latar pendukung untuk tabel, zebra striping, dan border halus.
- **Dark Text (`#111827`)**: Teks utama dengan tingkat keterbacaan tinggi (lolos uji kontras WCAG AA).
- **Muted Text (`#4B5563`)**: Teks sekunder untuk label keterangan, waktu mutasi, dan helper text.
- **Status Colors**:
  - Sukses / Masuk: Emerald (`#059669` / `#D1FAE5`)
  - Peringatan / Pending: Amber (`#D97706` / `#FEF3C7`)
  - Bahaya / Keluar / Tolak: Rose (`#E11D48` / `#FFE4E6`)

### 3. Tipografi
- **Font Utama**: Sans-serif modern berdaya baca tinggi (`Inter`, `Plus Jakarta Sans`, atau fallback sistem `system-ui, -apple-system, sans-serif`).
- **Skala Ukuran**:
  - H1 Judul Halaman: `text-2xl` / `text-3xl font-bold tracking-tight text-gray-900`
  - H2 Subjudul Bagian: `text-xl font-semibold text-gray-800`
  - Body Text: `text-base` / `text-sm text-gray-900`
  - Helper & Meta Text: `text-xs text-gray-500`

### 4. Komposisi & Komponen UI
- **Tanpa Elemen Semu (Anti-Slop)**:
  - Tidak ada em dash (`—`) pada teks UI.
  - Tidak ada angka atau testimoni palsu. Data kas dan inventaris berbasis data riil dari database.
  - Tidak ada tombol mati; semua tombol (Pinjam, Setujui, Tolak, Tambah, Filter) memiliki aksi backend atau modal formulir nyata.
  - Setiap data memiliki 3 status UI lengkap: *Empty State* (saat belum ada barang/mutasi), *Loading/Feedback State*, dan *Error State* (validasi formulir).
- **Aksesibilitas (WCAG AA & Keyboard Friendly)**:
  - Seluruh tombol dan input memiliki `focus-visible:ring-2 focus-visible:ring-[#CEA042]`.
  - Navigasi tabel dan form dapat diakses dengan keyboard (`Tab`, `Enter`, `Escape`).
  - Target sentuh mobile minimal 44px dan responsif tanpa overflow horizontal.
