<?php

namespace Database\Seeders;

use App\Models\Warga;
use Illuminate\Database\Seeder;

class MasterWargaSeeder extends Seeder
{
    /**
     * Seed 47 Master Data Warga Dusun Demangan (RT 01 & RT 02).
     */
    public function run(): void
    {
        // 1. Master Data Warga RT 01 (23 Warga)
        $wargaRt1 = [
            ['nama' => 'RIBUT', 'pln' => 'RIBUT', 'struk' => '2'],
            ['nama' => 'HARSONO', 'pln' => 'HARSONO', 'struk' => '3'],
            ['nama' => 'YASNO ISTIONO', 'pln' => 'YASNO ISTIONO', 'struk' => '4'],
            ['nama' => 'SUGIYANTO (P. TEBEK)', 'pln' => 'SUGIYANTO', 'struk' => '28'],
            ['nama' => 'DARMOREJO', 'pln' => 'DARMOREJO', 'struk' => '17'],
            ['nama' => 'SUKARLAN', 'pln' => 'SUKARLAN', 'struk' => '20'],
            ['nama' => 'SUTOPO', 'pln' => 'SUTOPO', 'struk' => '61'],
            ['nama' => 'PARJONO', 'pln' => 'PARJONO', 'struk' => '41'],
            ['nama' => 'YAHONI', 'pln' => 'YAHONI', 'struk' => '44'],
            ['nama' => 'SARNO', 'pln' => 'SARNO', 'struk' => '45'],
            ['nama' => 'SARIONO', 'pln' => 'SARIONO', 'struk' => '46'],
            ['nama' => 'WIRYO SASMITO', 'pln' => 'WIRYO SASMITO', 'struk' => '24'],
            ['nama' => 'SUDIMAN', 'pln' => 'SUDIMAN', 'struk' => '40'],
            ['nama' => 'SUGITO', 'pln' => 'SUGITO', 'struk' => '48'],
            ['nama' => 'RIYANTO', 'pln' => 'RIYANTO', 'struk' => '49'],
            ['nama' => 'SUMARTONO', 'pln' => 'SUMARTONO', 'struk' => '51'],
            ['nama' => "AM'AD NGASRI", 'pln' => "AM'AD NGASRI", 'struk' => '21'],
            ['nama' => 'ISMAWATI', 'pln' => 'ISMAWATI', 'struk' => '50'],
            ['nama' => 'ROFIATUN', 'pln' => 'ROFIATUN', 'struk' => '55'],
            ['nama' => 'MUSHOLA', 'pln' => 'MUSHOLA RT 01', 'struk' => '56'],
            ['nama' => 'MINARTI', 'pln' => 'MINARTI', 'struk' => '58'],
            ['nama' => 'PAWIT', 'pln' => 'ZAENAL MUSTAKIM', 'struk' => '60'],
            ['nama' => 'MUJIYONO TAMBENG', 'pln' => 'MUJIYONO TAMBENG', 'struk' => '47'],
        ];

        foreach ($wargaRt1 as $item) {
            Warga::firstOrCreate(
                [
                    'rt' => 1,
                    'nama' => $item['nama'],
                ],
                [
                    'nama_pelanggan_pln' => $item['pln'],
                    'nomor_meter' => $item['struk'],
                    'alamat' => 'Dusun Demangan RT 01',
                    'is_aktif' => true,
                ]
            );
        }

        // 2. Master Data Warga RT 02 (24 Warga)
        $wargaRt2 = [
            ['nama' => 'SOEGENO', 'pln' => 'SOEGENO', 'struk' => '1'],
            ['nama' => 'DWI SANTOSO (Pak Par)', 'pln' => 'DWI SANTOSO', 'struk' => '7'],
            ['nama' => 'MITRO DIHARJO', 'pln' => 'MITRO DIHARJO', 'struk' => '8'],
            ['nama' => 'MARLIYAN (Setiyanto)', 'pln' => 'MARLIYAN', 'struk' => '11'],
            ['nama' => 'NGATMIN', 'pln' => 'NGATMIN', 'struk' => '12'],
            ['nama' => 'COMZAH', 'pln' => 'COMZAH', 'struk' => '13'],
            ['nama' => 'HARDJO TARUNO (Pk Yono)', 'pln' => 'HARDJO TARUNO', 'struk' => '16'],
            ['nama' => 'PARDI', 'pln' => 'PARDI', 'struk' => '18'],
            ['nama' => 'DWI SANTOSO (Bu Ika)', 'pln' => 'DWI SANTOSO', 'struk' => '23'],
            ['nama' => 'HARDJONO', 'pln' => 'HARDJONO', 'struk' => '27'],
            ['nama' => 'Drs SURYANTO', 'pln' => 'Drs SURYANTO', 'struk' => '30'],
            ['nama' => 'SUKAMTO', 'pln' => 'SUKAMTO', 'struk' => '32'],
            ['nama' => 'TUGIYONO', 'pln' => 'TUGIYONO', 'struk' => '33'],
            ['nama' => 'SARLAN', 'pln' => 'SARLAN', 'struk' => '38'],
            ['nama' => 'DAIRUS', 'pln' => 'DAIRUS', 'struk' => '22'],
            ['nama' => 'PURWODIHARJO', 'pln' => 'PURWODIHARJO', 'struk' => '26'],
            ['nama' => 'UNTUNG SUPONO', 'pln' => 'UNTUNG SUPONO', 'struk' => '34'],
            ['nama' => 'TEGUH YUWONO', 'pln' => 'TEGUH YUWONO', 'struk' => '37'],
            ['nama' => 'DUWI ASTUTI', 'pln' => 'DUWI ASTUTI', 'struk' => '53'],
            ['nama' => 'SUYADI', 'pln' => 'SUYADI', 'struk' => '54'],
            ['nama' => 'SUPRAPTI', 'pln' => 'SUPRAPTI', 'struk' => '62'],
            ['nama' => 'SRI WAHYUNI', 'pln' => 'SRI WAHYUNI', 'struk' => '57'],
            ['nama' => 'GRIYA MULYA ASRI', 'pln' => 'GRIYA MULYA ASRI', 'struk' => '35'],
            ['nama' => 'HARJO DIWIRYO', 'pln' => 'HARJO DIWIRYO', 'struk' => '5'],
        ];

        foreach ($wargaRt2 as $item) {
            Warga::firstOrCreate(
                [
                    'rt' => 2,
                    'nama' => $item['nama'],
                ],
                [
                    'nama_pelanggan_pln' => $item['pln'],
                    'nomor_meter' => $item['struk'],
                    'alamat' => 'Dusun Demangan RT 02 (Struk #'.$item['struk'].')',
                    'is_aktif' => true,
                ]
            );
        }
    }
}
