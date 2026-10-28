<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database for production.
     */
    public function run(): void
    {
        // 1. Akun Superadmin Tunggal
        User::firstOrCreate(
            ['email' => 'superadmin@demangan.id'],
            [
                'name' => 'Superadmin Dusun Demangan',
                'password' => Hash::make('password'),
                'peran' => 'superadmin',
                'no_hp' => '081234567890',
                'status' => 'aktif',
            ]
        );

        // 2. 47 Master Data Warga RT 01 & RT 02
        $this->call(MasterWargaSeeder::class);
    }
}
