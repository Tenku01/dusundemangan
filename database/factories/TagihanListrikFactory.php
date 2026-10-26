<?php

namespace Database\Factories;

use App\Models\TagihanListrik;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TagihanListrik>
 */
class TagihanListrikFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $tagihanAwal = fake()->numberBetween(35000, 250000);
        $kasDusun = 3000;
        $totalTagihan = $tagihanAwal + $kasDusun;
        $uangDibayar = fake()->randomElement([
            $totalTagihan,
            $totalTagihan + 2000,
            $totalTagihan - 5000,
            $totalTagihan + 5000,
        ]);
        $selisih = $uangDibayar - $totalTagihan;

        return [
            'periode' => 'September 2026',
            'rt' => fake()->numberBetween(1, 5),
            'nama_pelanggan' => fake()->name(),
            'nomor_struk' => 'STR-'.fake()->unique()->numerify('#####'),
            'tagihan_awal' => $tagihanAwal,
            'kas_dusun' => $kasDusun,
            'total_tagihan' => $totalTagihan,
            'selisih_bayar' => $selisih,
            'is_lunas' => $selisih >= 0,
            'kode_gabungan' => null,
        ];
    }
}
