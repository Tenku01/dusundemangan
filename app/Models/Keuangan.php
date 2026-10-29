<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Keuangan extends Model
{
    use HasFactory;

    public const KAS_KARANG_TARUNA = 'Kas Karang Taruna';

    public const KAS_LISTRIK = 'Kas Listrik';

    protected $table = 'keuangan';

    protected $fillable = [
        'kode_transaksi',
        'tanggal',
        'jenis',
        'nominal',
        'kategori_kas',
        'kategori',
        'keterangan',
        'lampiran',
        'dicatat_oleh',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'nominal' => 'decimal:2',
        ];
    }

    public function pencatat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dicatat_oleh');
    }
}
