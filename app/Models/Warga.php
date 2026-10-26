<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Warga extends Model
{
    use HasFactory;

    protected $table = 'warga';

    protected $fillable = [
        'nama',
        'nama_pelanggan_pln',
        'rt',
        'nomor_meter',
        'no_telepon',
        'alamat',
        'is_aktif',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'rt' => 'integer',
            'is_aktif' => 'boolean',
        ];
    }

    /**
     * Relasi ke riwayat tagihan listrik warga.
     */
    public function tagihanListrik(): HasMany
    {
        return $this->hasMany(TagihanListrik::class, 'warga_id');
    }
}
