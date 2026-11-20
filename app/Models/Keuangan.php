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

    /**
     * Daftar seluruh kategori kas (bawaan + dinamis dari transaksi tercatat).
     *
     * @return array<int, string>
     */
    public static function getDaftarKategoriKas(): array
    {
        $kategoriKasExisting = static::query()
            ->select('kategori_kas')
            ->distinct()
            ->pluck('kategori_kas')
            ->toArray();

        return array_values(array_unique(array_filter(array_merge(
            [self::KAS_KARANG_TARUNA, self::KAS_LISTRIK],
            $kategoriKasExisting
        ))));
    }

    /**
     * Nama kategori kas dengan fallback default Karang Taruna.
     */
    public function labelKategoriKas(): string
    {
        return $this->kategori_kas ?: self::KAS_KARANG_TARUNA;
    }

    /**
     * Kelas badge Tailwind untuk menampilkan jenis kas secara konsisten.
     */
    public function badgeClassesKategoriKas(): string
    {
        return match ($this->labelKategoriKas()) {
            self::KAS_LISTRIK => 'bg-slate-100 text-slate-800 border border-slate-300',
            self::KAS_KARANG_TARUNA => 'bg-emas-100 text-emas-900 border border-emas-200',
            default => 'bg-sky-50 text-sky-800 border border-sky-200',
        };
    }
}
