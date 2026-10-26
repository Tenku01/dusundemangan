<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TagihanListrik extends Model
{
    use HasFactory;

    protected $table = 'tagihan_listrik';

    protected $fillable = [
        'warga_id',
        'periode',
        'rt',
        'nama_warga',
        'nama_pembayar',
        'nama_pelanggan',
        'nomor_struk',
        'tagihan_awal',
        'kas_karang_taruna',
        'kas_dusun',
        'kategori_kas',
        'saldo_periode_lalu',
        'total_tagihan',
        'selisih_bayar',
        'is_lunas',
        'tanggal_bayar',
        'kode_gabungan',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'warga_id' => 'integer',
            'rt' => 'integer',
            'tagihan_awal' => 'integer',
            'kas_karang_taruna' => 'integer',
            'kas_dusun' => 'integer',
            'saldo_periode_lalu' => 'integer',
            'total_tagihan' => 'integer',
            'selisih_bayar' => 'integer',
            'is_lunas' => 'boolean',
            'tanggal_bayar' => 'date',
        ];
    }

    /**
     * Relasi ke data master Warga.
     */
    public function warga(): BelongsTo
    {
        return $this->belongsTo(Warga::class, 'warga_id');
    }

    /**
     * Cek apakah pembayaran berlebih (titipan).
     */
    public function isLebih(): bool
    {
        return $this->selisih_bayar > 0;
    }

    /**
     * Cek apakah pembayaran pas.
     */
    public function isPas(): bool
    {
        return $this->selisih_bayar === 0;
    }

    /**
     * Cek apakah pembayaran kurang (ninggal).
     */
    public function isKurang(): bool
    {
        return $this->selisih_bayar < 0;
    }

    public const DAFTAR_BULAN = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    ];

    /**
     * Konversi string periode (misal: "September 2026") menjadi integer kronologis YYYYMM (misal: 202609).
     */
    public static function parsePeriodeToKey(string $periode): int
    {
        $bulanMap = array_change_key_case(array_flip(self::DAFTAR_BULAN), CASE_LOWER);

        $parts = preg_split('/\s+/', trim($periode));
        $bulanStr = strtolower($parts[0] ?? '');
        $tahunStr = (int) ($parts[1] ?? date('Y'));

        $bulanNum = $bulanMap[$bulanStr] ?? 1;

        return ($tahunStr * 100) + $bulanNum;
    }

    /**
     * Dapatkan nama periode bulan berikutnya (misal: "September 2026" -> "Oktober 2026").
     */
    public static function getBulanBerikutnya(string $periode): string
    {
        $key = self::parsePeriodeToKey($periode);
        $tahun = (int) floor($key / 100);
        $bulan = $key % 100;

        $bulan++;
        if ($bulan > 12) {
            $bulan = 1;
            $tahun++;
        }

        return (self::DAFTAR_BULAN[$bulan] ?? 'Januari').' '.$tahun;
    }

    /**
     * Dapatkan daftar seluruh periode yang ada di database, diurutkan kronologis (terbaru di paling atas).
     *
     * @return array<int, string>
     */
    public static function getDaftarPeriodeKronologis(): array
    {
        $semua = self::select('periode')->distinct()->pluck('periode')->toArray();
        if (empty($semua)) {
            $semua = [self::DAFTAR_BULAN[(int) date('n')].' '.date('Y')];
        }

        // Urutkan kronologis desc: 202611, 202610, 202609
        usort($semua, function ($a, $b) {
            return self::parsePeriodeToKey($b) <=> self::parsePeriodeToKey($a);
        });

        return array_values(array_unique($semua));
    }

    /**
     * Cari saldo titipan (+) atau tunggakan (-) dari periode sebelumnya (berdasarkan urutan bulan kronologis).
     */
    public static function getSaldoBulanLalu(?int $wargaId, ?string $namaWarga, string $periodeSaatIni): int
    {
        $query = self::query();

        if ($wargaId) {
            $query->where('warga_id', $wargaId);
        } elseif ($namaWarga) {
            $query->where('nama_warga', $namaWarga);
        } else {
            return 0;
        }

        $currentKey = self::parsePeriodeToKey($periodeSaatIni);

        // Ambil riwayat warga dan cari bulan terdekat sebelum bulan saat ini
        $riwayatList = $query->get();
        $riwayatLalu = $riwayatList->filter(function ($item) use ($currentKey) {
            return self::parsePeriodeToKey($item->periode) < $currentKey;
        })->sortByDesc(function ($item) {
            return self::parsePeriodeToKey($item->periode);
        })->first();

        // Fallback jika tidak ada periode sebelumnya
        if (! $riwayatLalu) {
            $riwayatLalu = $riwayatList->where('periode', '!=', $periodeSaatIni)->last();
        }

        if ($riwayatLalu && $riwayatLalu->is_lunas) {
            return (int) $riwayatLalu->selisih_bayar;
        }

        return 0;
    }

    /**
     * Hitung total tagihan bersih setelah memperhitungkan titipan bulan lalu.
     */
    public static function hitungTotalTagihan(int $tagihanAwal, int $kasKartar = 3000, int $saldoLalu = 0): int
    {
        if ($tagihanAwal <= 0) {
            return 0;
        }

        $bruto = $tagihanAwal + $kasKartar;
        $netto = $bruto - $saldoLalu;

        return max(0, $netto);
    }
}
