<?php

return [
    'current_version' => 'v2.4.0',

    /*
    |--------------------------------------------------------------------------
    | Riwayat Pembaruan Sistem Web (Permanen & Tidak Dapat Dihapus)
    |--------------------------------------------------------------------------
    */
    'releases' => [
        [
            'version' => 'v2.4.0',
            'tanggal' => '20 November 2026',
            'kategori' => 'Monitoring & Keandalan',
            'judul' => 'Pusat Status Sistem (/status) & Halaman Error Lengkap (3xx, 4xx, 5xx)',
            'ringkasan' => 'Penambahan pemantauan audit perubahan data pengguna, status eksekusi method & pembersihan memori, riwayat rilis permanen, serta halaman error seragam untuk kode 300-an, 400-an, dan 500-an.',
            'poin' => [
                'Log Perubahan Data otomatis mencatat siapa yang menambah, mengubah, atau menghapus data beserta nilai sebelum & sesudah.',
                'Pemantauan Status Method & Memori lengkap dengan tombol pembersihan log dan cache untuk memperluas memori.',
                'Halaman status update web permanen (terkunci dan tidak dapat dihapus).',
                'Halaman error terstandar tema emas Dusun Demangan untuk seluruh kode HTTP 3xx, 4xx, dan 5xx.',
            ],
        ],
        [
            'version' => 'v2.3.0',
            'tanggal' => '20 November 2026',
            'kategori' => 'Dokumen & Transparansi',
            'judul' => 'Laporan Keuangan Surat Resmi Word-Style & Filter Jenis Kas Publik',
            'ringkasan' => 'Pembaruan format cetak PDF laporan keuangan menyerupai surat resmi dokumen Word dengan tanda tangan Ketua Anggun Fatchurrohman, serta filter pilih kas di /transparansi-kas.',
            'poin' => [
                'Format cetak A4 dengan Kop Surat resmi, tabel berulang di tiap halaman, dan satu blok tanda tangan Ketua Karang Taruna.',
                'Filter Pilih Kas (Kas Karang Taruna, Kas Listrik, dan pos kas dinamis) beserta kolom jenis kas di halaman /transparansi-kas.',
                'Penyeragaman istilah menjadi Kas dan identitas organisasi Karang Taruna Dusun Demangan, Desa Candimulyo.',
            ],
        ],
        [
            'version' => 'v2.2.0',
            'tanggal' => '20 November 2026',
            'kategori' => 'UI/UX & PWA',
            'judul' => 'Audit UI Tema Emas WCAG AA, Custom Dropdown Global & Panduan PWA iOS',
            'ringkasan' => 'Pemolesan menyeluruh seluruh tampilan publik dan admin mengikuti Tailwind CSS v3, custom select dropdown bertema emas, serta dukungan instalasi PWA di iPhone/iPad.',
            'poin' => [
                'Custom dropdown bertema emas menggantikan daftar select bawaan OS di seluruh halaman.',
                'Tombol dan panduan visual 3 langkah pemasangan PWA khusus Safari iOS beserta deteksi browser dalam aplikasi.',
                'Optimasi keterbacaan kontras tombol primary #1F1406 di atas emas #CEA042 dan tampilan kartu responsif mobile.',
            ],
        ],
        [
            'version' => 'v2.1.0',
            'tanggal' => '19 November 2026',
            'kategori' => 'Operasional Listrik & PWA',
            'judul' => 'Dukungan PWA, Cetak Rekap A4, Ekspor CSV & Cek Tagihan Warga',
            'ringkasan' => 'Penambahan cetak rekap tagihan listrik ukuran A4, ekspor CSV, halaman publik cek tagihan listrik, serta manifest & service worker PWA.',
            'poin' => [
                'Fitur cetak rekap A4 dan unduh CSV per siklus periode dan RT.',
                'Halaman publik /cek-listrik untuk pengecekan status pembayaran warga.',
                'Drawer navigasi lengkap di perangkat mobile untuk Superadmin.',
            ],
        ],
        [
            'version' => 'v2.0.0',
            'tanggal' => '30 Oktober 2026',
            'kategori' => 'Keuangan & Keamanan',
            'judul' => 'Kategori Kas Dinamis, Setoran Kas Listrik & Lupa Kata Sandi',
            'ringkasan' => 'Pemisahan Kas Karang Taruna dan Kas Listrik warga, alur pembukuan setor ke kas utama, serta fitur reset password via SMTP.',
            'poin' => [
                'Pelacakan terpisah antara saldo Kas Listrik dan Kas Karang Taruna.',
                'Hak akses granular per menu bagi masing-masing akun admin pengurus.',
                'Alur lupa kata sandi dan reset password terenkripsi.',
            ],
        ],
    ],
];
