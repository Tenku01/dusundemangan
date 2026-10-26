<?php

namespace Database\Seeders;

use App\Models\TagihanListrik;
use App\Models\Warga;
use Illuminate\Database\Seeder;

class WargaRt2Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $periode = 'September 2026';
        $rt = 2;

        $daftarWargaRt2 = [
            ['no' => 1, 'nama' => 'SOEGENO', 'pln' => 'SOEGENO', 'struk' => '1'],
            ['no' => 2, 'nama' => 'DWI SANTOSO (Pak Par)', 'pln' => 'DWI SANTOSO', 'struk' => '7'],
            ['no' => 3, 'nama' => 'MITRO DIHARJO', 'pln' => 'MITRO DIHARJO', 'struk' => '8'],
            ['no' => 4, 'nama' => 'MARLIYAN (Setiyanto)', 'pln' => 'MARLIYAN', 'struk' => '11'],
            ['no' => 5, 'nama' => 'NGATMIN', 'pln' => 'NGATMIN', 'struk' => '12'],
            ['no' => 6, 'nama' => 'COMZAH', 'pln' => 'COMZAH', 'struk' => '13'],
            ['no' => 7, 'nama' => 'HARDJO TARUNO (Pk Yono)', 'pln' => 'HARDJO TARUNO', 'struk' => '16'],
            ['no' => 8, 'nama' => 'PARDI', 'pln' => 'PARDI', 'struk' => '18'],
            ['no' => 9, 'nama' => 'DWI SANTOSO (Bu Ika)', 'pln' => 'DWI SANTOSO', 'struk' => '23'],
            ['no' => 10, 'nama' => 'HARDJONO', 'pln' => 'HARDJONO', 'struk' => '27'],
            ['no' => 11, 'nama' => 'Drs SURYANTO', 'pln' => 'Drs SURYANTO', 'struk' => '30'],
            ['no' => 12, 'nama' => 'SUKAMTO', 'pln' => 'SUKAMTO', 'struk' => '32'],
            ['no' => 13, 'nama' => 'TUGIYONO', 'pln' => 'TUGIYONO', 'struk' => '33'],
            ['no' => 14, 'nama' => 'SARLAN', 'pln' => 'SARLAN', 'struk' => '38'],
            ['no' => 15, 'nama' => 'DAIRUS', 'pln' => 'DAIRUS', 'struk' => '22'],
            ['no' => 16, 'nama' => 'PURWODIHARJO', 'pln' => 'PURWODIHARJO', 'struk' => '26'],
            ['no' => 17, 'nama' => 'UNTUNG SUPONO', 'pln' => 'UNTUNG SUPONO', 'struk' => '34'],
            ['no' => 18, 'nama' => 'TEGUH YUWONO', 'pln' => 'TEGUH YUWONO', 'struk' => '37'],
            ['no' => 19, 'nama' => 'DUWI ASTUTI', 'pln' => 'DUWI ASTUTI', 'struk' => '53'],
            ['no' => 20, 'nama' => 'SUYADI', 'pln' => 'SUYADI', 'struk' => '54'],
            ['no' => 21, 'nama' => 'SUPRAPTI', 'pln' => 'SUPRAPTI', 'struk' => '62'],
            ['no' => 22, 'nama' => 'SRI WAHYUNI', 'pln' => 'SRI WAHYUNI', 'struk' => '57'],
            ['no' => 23, 'nama' => 'GRIYA MULYA ASRI', 'pln' => 'GRIYA MULYA ASRI', 'struk' => '35'],
            ['no' => 24, 'nama' => 'HARJO DIWIRYO', 'pln' => 'HARJO DIWIRYO', 'struk' => '5'],
        ];

        foreach ($daftarWargaRt2 as $item) {
            $warga = Warga::updateOrCreate(
                [
                    'nama' => $item['nama'],
                    'rt' => $rt,
                ],
                [
                    'nama_pelanggan_pln' => $item['pln'],
                    'nomor_meter' => $item['struk'],
                    'alamat' => 'Demangan RT 02 (Struk #'.$item['struk'].')',
                    'is_aktif' => true,
                ]
            );

            TagihanListrik::updateOrCreate(
                [
                    'periode' => $periode,
                    'rt' => $rt,
                    'nomor_struk' => $item['struk'],
                ],
                [
                    'warga_id' => $warga->id,
                    'nama_warga' => $item['nama'],
                    'nama_pelanggan' => $item['pln'],
                    'tagihan_awal' => 0,
                    'kas_karang_taruna' => 0,
                    'kas_dusun' => 0,
                    'kategori_kas' => 'karang_taruna',
                    'total_tagihan' => 0,
                    'selisih_bayar' => 0,
                    'is_lunas' => false,
                ]
            );
        }
    }
}
