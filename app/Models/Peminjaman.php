<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Peminjaman extends Model
{
    use HasFactory;

    protected $table = 'peminjaman';

    protected $fillable = [
        'kode_pinjam',
        'barang_id',
        'user_id',
        'nama_peminjam',
        'kontak_peminjam',
        'alamat_rt_rw',
        'jumlah_pinjam',
        'tanggal_pinjam',
        'rencana_tanggal_kembali',
        'tanggal_dikembalikan',
        'tujuan_peminjaman',
        'status',
        'catatan_admin',
        'disetujui_oleh',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_pinjam' => 'date',
            'rencana_tanggal_kembali' => 'date',
            'tanggal_dikembalikan' => 'date',
            'jumlah_pinjam' => 'integer',
        ];
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function penyetuju(): BelongsTo
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }
}
