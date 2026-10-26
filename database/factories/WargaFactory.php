<?php

namespace Database\Factories;

use App\Models\Warga;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Warga>
 */
class WargaFactory extends Factory
{
    protected $model = Warga::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => fake()->name(),
            'rt' => fake()->numberBetween(1, 3),
            'nomor_meter' => fake()->numerify('52##########'),
            'no_telepon' => fake()->phoneNumber(),
            'alamat' => 'Demangan RT '.fake()->numberBetween(1, 3),
            'is_aktif' => true,
        ];
    }
}
