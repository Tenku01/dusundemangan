<?php

namespace Database\Seeders;

use App\Models\TagihanListrik;
use App\Models\Warga;
use Illuminate\Database\Seeder;

class RekapListrikRt1September2026Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $periode = 'September 2026';
        $rt = 1;

        $daftarData = [
            [
                'no_urut' => 1,
                'nama_warga' => 'RIBUT',
                'nama_pln' => 'RIBUT',
                'no_struk' => '2',
                'tagihan_awal' => 66000,
                'kas_kartar' => 3000,
                'total_tagihan' => 69000,
                'selisih' => 40000, // Titip +40.000
                'is_lunas' => true,
            ],
            [
                'no_urut' => 2,
                'nama_warga' => 'HARSONO',
                'nama_pln' => 'HARSONO',
                'no_struk' => '3',
                'tagihan_awal' => 86000,
                'kas_kartar' => 3000,
                'total_tagihan' => 89000,
                'selisih' => 0,
                'is_lunas' => false,
            ],
            [
                'no_urut' => 3,
                'nama_warga' => 'YASNO ISTIONO',
                'nama_pln' => 'YASNO ISTIONO',
                'no_struk' => '4',
                'tagihan_awal' => 0,
                'kas_kartar' => 0,
                'total_tagihan' => 0,
                'selisih' => 0,
                'is_lunas' => true,
            ],
            [
                'no_urut' => 4,
                'nama_warga' => 'SUGIYANTO (P. TEBEK)',
                'nama_pln' => 'SUGIYANTO',
                'no_struk' => '28',
                'tagihan_awal' => 20000,
                'kas_kartar' => 3000,
                'total_tagihan' => 23000,
                'selisih' => 0,
                'is_lunas' => true,
            ],
            [
                'no_urut' => 5,
                'nama_warga' => 'DARMOREJO',
                'nama_pln' => 'DARMOREJO',
                'no_struk' => '17',
                'tagihan_awal' => 10000,
                'kas_kartar' => 3000,
                'total_tagihan' => 13000,
                'selisih' => 0,
                'is_lunas' => true,
            ],
            [
                'no_urut' => 6,
                'nama_warga' => 'SUKARLAN',
                'nama_pln' => 'SUKARLAN',
                'no_struk' => '20',
                'tagihan_awal' => 35000,
                'kas_kartar' => 3000,
                'total_tagihan' => 38000,
                'selisih' => 0,
                'is_lunas' => true,
            ],
            [
                'no_urut' => 7,
                'nama_warga' => 'SUTOPO',
                'nama_pln' => 'SUTOPO',
                'no_struk' => '61',
                'tagihan_awal' => 63000,
                'kas_kartar' => 3000,
                'total_tagihan' => 66000,
                'selisih' => 0,
                'is_lunas' => true,
            ],
            [
                'no_urut' => 8,
                'nama_warga' => 'PARJONO',
                'nama_pln' => 'PARJONO',
                'no_struk' => '41',
                'tagihan_awal' => 69000,
                'kas_kartar' => 3000,
                'total_tagihan' => 72000,
                'selisih' => 0,
                'is_lunas' => true,
            ],
            [
                'no_urut' => 9,
                'nama_warga' => 'YAHONI',
                'nama_pln' => 'YAHONI',
                'no_struk' => '44',
                'tagihan_awal' => 40000,
                'kas_kartar' => 3000,
                'total_tagihan' => 43000,
                'selisih' => 2000, // Titip +2.000
                'is_lunas' => true,
            ],
            [
                'no_urut' => 10,
                'nama_warga' => 'SARNO',
                'nama_pln' => 'SARNO',
                'no_struk' => '45',
                'tagihan_awal' => 0,
                'kas_kartar' => 0,
                'total_tagihan' => 0,
                'selisih' => 0,
                'is_lunas' => false,
            ],
            [
                'no_urut' => 11,
                'nama_warga' => 'SARIONO',
                'nama_pln' => 'SARIONO',
                'no_struk' => '46',
                'tagihan_awal' => 170000,
                'kas_kartar' => 3000,
                'total_tagihan' => 173000,
                'selisih' => 0,
                'is_lunas' => true,
            ],
            [
                'no_urut' => 12,
                'nama_warga' => 'WIRYO SASMITO',
                'nama_pln' => 'WIRYO SASMITO',
                'no_struk' => '24',
                'tagihan_awal' => 16000,
                'kas_kartar' => 3000,
                'total_tagihan' => 19000,
                'selisih' => 1000, // Titip +1.000
                'is_lunas' => true,
            ],
            [
                'no_urut' => 13,
                'nama_warga' => 'SUDIMAN',
                'nama_pln' => 'SUDIMAN',
                'no_struk' => '40',
                'tagihan_awal' => 14000,
                'kas_kartar' => 3000,
                'total_tagihan' => 17000,
                'selisih' => 0,
                'is_lunas' => true,
            ],
            [
                'no_urut' => 14,
                'nama_warga' => 'SUGITO',
                'nama_pln' => 'SUGITO',
                'no_struk' => '48',
                'tagihan_awal' => 152000,
                'kas_kartar' => 3000,
                'total_tagihan' => 155000,
                'selisih' => 0,
                'is_lunas' => true,
            ],
            [
                'no_urut' => 15,
                'nama_warga' => 'RIYANTO',
                'nama_pln' => 'RIYANTO',
                'no_struk' => '49',
                'tagihan_awal' => 45000,
                'kas_kartar' => 3000,
                'total_tagihan' => 48000,
                'selisih' => 0,
                'is_lunas' => true,
            ],
            [
                'no_urut' => 16,
                'nama_warga' => 'SUMARTONO',
                'nama_pln' => 'SUMARTONO',
                'no_struk' => '51',
                'tagihan_awal' => 137000,
                'kas_kartar' => 3000,
                'total_tagihan' => 140000,
                'selisih' => 0,
                'is_lunas' => true,
            ],
            [
                'no_urut' => 17,
                'nama_warga' => "AM'AD NGASRI",
                'nama_pln' => "AM'AD NGASRI",
                'no_struk' => '21',
                'tagihan_awal' => 57000,
                'kas_kartar' => 3000,
                'total_tagihan' => 60000,
                'selisih' => 0,
                'is_lunas' => true,
            ],
            [
                'no_urut' => 18,
                'nama_warga' => 'ISMAWATI',
                'nama_pln' => 'ISMAWATI',
                'no_struk' => '50',
                'tagihan_awal' => 124000,
                'kas_kartar' => 3000,
                'total_tagihan' => 127000,
                'selisih' => 0,
                'is_lunas' => true,
            ],
            [
                'no_urut' => 19,
                'nama_warga' => 'ROFIATUN',
                'nama_pln' => 'ROFIATUN',
                'no_struk' => '55',
                'tagihan_awal' => 27000,
                'kas_kartar' => 3000,
                'total_tagihan' => 30000,
                'selisih' => 0,
                'is_lunas' => true,
            ],
            [
                'no_urut' => 20,
                'nama_warga' => 'MUSHOLA',
                'nama_pln' => 'MUSHOLA RT 01',
                'no_struk' => '56',
                'tagihan_awal' => 35000,
                'kas_kartar' => 3000,
                'total_tagihan' => 38000,
                'selisih' => 0,
                'is_lunas' => true,
            ],
            [
                'no_urut' => 21,
                'nama_warga' => 'MINARTI',
                'nama_pln' => 'MINARTI',
                'no_struk' => '58',
                'tagihan_awal' => 77000,
                'kas_kartar' => 3000,
                'total_tagihan' => 80000,
                'selisih' => 0,
                'is_lunas' => true,
            ],
            [
                'no_urut' => 22,
                'nama_warga' => 'PAWIT',
                'nama_pln' => 'ZAENAL MUSTAKIM',
                'no_struk' => '60',
                'tagihan_awal' => 39000,
                'kas_kartar' => 3000,
                'total_tagihan' => 42000,
                'selisih' => -2000, // Kurang/Ninggal Rp 2.000
                'is_lunas' => true,
            ],
            [
                'no_urut' => 23,
                'nama_warga' => 'MUJIYONO TAMBENG',
                'nama_pln' => 'MUJIYONO TAMBENG',
                'no_struk' => '47',
                'tagihan_awal' => 39000,
                'kas_kartar' => 3000,
                'total_tagihan' => 42000,
                'selisih' => -56000, // Kurang/Ninggal Rp 56.000
                'is_lunas' => false,
            ],
        ];

        foreach ($daftarData as $d) {
            // 1. Simpan ke Master Data Warga RT 1
            $warga = Warga::firstOrCreate(
                [
                    'rt' => $rt,
                    'nama' => $d['nama_warga'],
                ],
                [
                    'nama_pelanggan_pln' => $d['nama_pln'],
                    'nomor_meter' => $d['no_struk'],
                    'alamat' => "Dusun Demangan RT 0{$rt}",
                    'is_aktif' => true,
                ]
            );

            // 2. Simpan Rekap Tagihan Listrik Periode September 2026
            TagihanListrik::updateOrCreate(
                [
                    'periode' => $periode,
                    'rt' => $rt,
                    'nomor_struk' => $d['no_struk'],
                ],
                [
                    'warga_id' => $warga->id,
                    'nama_warga' => $d['nama_warga'],
                    'nama_pelanggan' => $d['nama_pln'],
                    'tagihan_awal' => $d['tagihan_awal'],
                    'kas_karang_taruna' => $d['kas_kartar'],
                    'kas_dusun' => 0,
                    'kategori_kas' => 'karang_taruna',
                    'total_tagihan' => $d['total_tagihan'],
                    'selisih_bayar' => $d['selisih'],
                    'is_lunas' => $d['is_lunas'],
                    'tanggal_bayar' => $d['is_lunas'] ? '2026-09-20' : null,
                ]
            );
        }
    }
}
