<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Barang extends Model
{
    use HasFactory;

    protected $table = 'barang';

    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'kategori',
        'foto',
        'jumlah_total',
        'jumlah_tersedia',
        'kondisi',
        'lokasi_penyimpanan',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'jumlah_total' => 'integer',
            'jumlah_tersedia' => 'integer',
        ];
    }

    public function peminjaman(): HasMany
    {
        return $this->hasMany(Peminjaman::class, 'barang_id');
    }

    public function isTersedia(): bool
    {
        return $this->jumlah_tersedia > 0;
    }
}
