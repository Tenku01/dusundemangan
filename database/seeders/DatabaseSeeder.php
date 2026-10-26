<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\Keuangan;
use App\Models\User;
use App\Models\Warga;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Akun Superadmin
        $superadmin = User::firstOrCreate(
            ['email' => 'superadmin@demangan.id'],
            [
                'name' => 'Superadmin Dusun Demangan',
                'password' => Hash::make('password'),
                'peran' => 'superadmin',
                'no_hp' => '081234567890',
                'status' => 'aktif',
            ]
        );

        // 2. Akun Admin (Pengurus Karang Taruna)
        $admin = User::firstOrCreate(
            ['email' => 'admin@demangan.id'],
            [
                'name' => 'Budi Santoso (Ketua Karang Taruna)',
                'password' => Hash::make('password'),
                'peran' => 'admin',
                'no_hp' => '089876543210',
                'status' => 'aktif',
            ]
        );

        // 3. Data Inventaris Barang Awal
        $daftarBarang = [
            [
                'kode_barang' => 'BRG-001',
                'nama_barang' => 'Tenda Terop Besi 4x6 Meter',
                'kategori' => 'Tenda & Terpal',
                'jumlah_total' => 2,
                'jumlah_tersedia' => 2,
                'kondisi' => 'Baik',
                'lokasi_penyimpanan' => 'Gudang Pos RW 02',
                'keterangan' => 'Lengkap dengan besi tiang penopang dan terpal putih penutup samping.',
            ],
            [
                'kode_barang' => 'BRG-002',
                'nama_barang' => 'Kursi Lipat / Plastik Hijau',
                'kategori' => 'Meja & Kursi',
                'jumlah_total' => 100,
                'jumlah_tersedia' => 100,
                'kondisi' => 'Baik',
                'lokasi_penyimpanan' => 'Gudang Balai RT 01',
                'keterangan' => 'Kondisi kokoh, dapat ditumpuk hingga 25 unit per kelompok.',
            ],
            [
                'kode_barang' => 'BRG-003',
                'nama_barang' => 'Meja Prasmanan Lipat Panjang',
                'kategori' => 'Meja & Kursi',
                'jumlah_total' => 8,
                'jumlah_tersedia' => 8,
                'kondisi' => 'Baik',
                'lokasi_penyimpanan' => 'Gudang Pos RW 02',
                'keterangan' => 'Ukuran 180 x 60 cm, rangka besi lipat.',
            ],
            [
                'kode_barang' => 'BRG-004',
                'nama_barang' => 'Sound System Portable + 2 Mic Wireless',
                'kategori' => 'Elektronik & Sound',
                'jumlah_total' => 1,
                'jumlah_tersedia' => 1,
                'kondisi' => 'Baik',
                'lokasi_penyimpanan' => 'Rumah Pengurus (Budi)',
                'keterangan' => 'Speaker aktif 12 inch, baterai tahan 5 jam, termasuk kabel charger.',
            ],
            [
                'kode_barang' => 'BRG-005',
                'nama_barang' => 'Terpal Plastik Biru 6x8 Meter',
                'kategori' => 'Tenda & Terpal',
                'jumlah_total' => 4,
                'jumlah_tersedia' => 4,
                'kondisi' => 'Baik',
                'lokasi_penyimpanan' => 'Gudang Pos RW 02',
                'keterangan' => 'Untuk alas duduk lesehan pengajian atau atap darurat.',
            ],
            [
                'kode_barang' => 'BRG-006',
                'nama_barang' => 'Mesin Potong Rumput Gendong',
                'kategori' => 'Alat Kebersihan & Taman',
                'jumlah_total' => 1,
                'jumlah_tersedia' => 1,
                'kondisi' => 'Baik',
                'lokasi_penyimpanan' => 'Pos Kamling RT 03',
                'keterangan' => 'Mesin 2 tak, harap isi bensin campur sebelum pemakaian.',
            ],
        ];

        foreach ($daftarBarang as $barang) {
            Barang::firstOrCreate(
                ['kode_barang' => $barang['kode_barang']],
                $barang
            );
        }

        // 4. Data Transaksi Kas Awal
        $daftarKas = [
            [
                'kode_transaksi' => 'KAS-202610-001',
                'tanggal' => now()->subDays(15)->format('Y-m-d'),
                'jenis' => 'masuk',
                'nominal' => 2500000,
                'kategori' => 'Iuran Warga',
                'keterangan' => 'Penerimaan iuran sukarela bulanan kas dusun RT 01-04.',
                'dicatat_oleh' => $admin->id,
            ],
            [
                'kode_transaksi' => 'KAS-202610-002',
                'tanggal' => now()->subDays(10)->format('Y-m-d'),
                'jenis' => 'masuk',
                'nominal' => 1000000,
                'kategori' => 'Donasi',
                'keterangan' => 'Donasi warga untuk pemeliharaan sound system dan perlengkapan.',
                'dicatat_oleh' => $admin->id,
            ],
            [
                'kode_transaksi' => 'KAS-202610-003',
                'tanggal' => now()->subDays(5)->format('Y-m-d'),
                'jenis' => 'keluar',
                'nominal' => 350000,
                'kategori' => 'Pengeluaran Fasilitas',
                'keterangan' => 'Pembelian 5 unit bohlam LED penerangan jalan gang RT 02.',
                'dicatat_oleh' => $admin->id,
            ],
            [
                'kode_transaksi' => 'KAS-202610-004',
                'tanggal' => now()->subDays(2)->format('Y-m-d'),
                'jenis' => 'keluar',
                'nominal' => 200000,
                'kategori' => 'Konsumsi & Acara',
                'keterangan' => 'Konsumsi air mineral dan gorengan kerja bakti saluran air.',
                'dicatat_oleh' => $admin->id,
            ],
        ];

        foreach ($daftarKas as $kas) {
            Keuangan::firstOrCreate(
                ['kode_transaksi' => $kas['kode_transaksi']],
                $kas
            );
        }

        // 5. Data Rekap Listrik RT 1 September 2026 (23 Warga)
        $this->call(RekapListrikRt1September2026Seeder::class);

        // 6. Data Rekap Listrik RT 2 September 2026 (24 Warga)
        $this->call(WargaRt2Seeder::class);
    }
}
