<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TagihanListrik;
use App\Models\Warga;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class RekapListrikController extends Controller
{
    /**
     * Menampilkan daftar rekapitulasi tagihan listrik per periode & per RT (RT 1, 2, 3).
     */
    public function index(Request $request): View
    {
        // Ambil daftar periode secara kronologis (bulan terbaru di atas)
        $daftarPeriode = collect(TagihanListrik::getDaftarPeriodeKronologis());

        // Periode aktif (default ke bulan terbaru yang tersedia)
        $periodeAktif = $request->query('periode', $daftarPeriode->first());
        $rtAktif = (int) $request->query('rt', 1);
        if (! in_array($rtAktif, [1, 2, 3], true)) {
            $rtAktif = 1;
        }

        $cari = $request->query('cari');

        $query = TagihanListrik::where('periode', $periodeAktif)
            ->where('rt', $rtAktif);

        if ($cari) {
            $query->where(function ($q) use ($cari) {
                $q->where('nama_warga', 'like', "%{$cari}%")
                    ->orWhere('nama_pembayar', 'like', "%{$cari}%")
                    ->orWhere('nama_pelanggan', 'like', "%{$cari}%")
                    ->orWhere('nomor_struk', 'like', "%{$cari}%")
                    ->orWhere('kode_gabungan', 'like', "%{$cari}%");
            });
        }

        // Summary KPI siklus periode ini di RT ini (1 Query Konsolidasi)
        $summary = (clone $query)->selectRaw('
            COUNT(*) as total_warga,
            COALESCE(SUM(tagihan_awal), 0) as total_awal,
            COALESCE(SUM(total_tagihan), 0) as grand_total,
            COUNT(CASE WHEN tagihan_awal > 0 THEN 1 END) as count_kartar
        ')->first();
        $totalWargaTerdata = (int) ($summary->total_warga ?? 0);
        $totalTagihanAwal = (int) ($summary->total_awal ?? 0);
        $grandTotalWajib = (int) ($summary->grand_total ?? 0);
        $totalKasKartar = (int) ($summary->count_kartar ?? 0) * 3000;

        // Ambil data rekap (urutkan berdasarkan nomor struk numerik)
        $daftarRekap = $query->orderByRaw('CAST(nomor_struk AS UNSIGNED) ASC')
            ->orderBy('id', 'asc')
            ->get();

        // Hitung jumlah data per RT di periode ini (1 Query GroupBy)
        $rtCounts = TagihanListrik::where('periode', $periodeAktif)
            ->whereIn('rt', [1, 2, 3])
            ->selectRaw('rt, COUNT(*) as aggregate')
            ->groupBy('rt')
            ->pluck('aggregate', 'rt');

        $countRt1 = (int) ($rtCounts[1] ?? 0);
        $countRt2 = (int) ($rtCounts[2] ?? 0);
        $countRt3 = (int) ($rtCounts[3] ?? 0);

        // Master Warga di RT ini untuk input terarah (urut nomor struk), beserta saldo titipan & status tercatat
        $wargaRtAktif = Warga::where('rt', $rtAktif)
            ->where('is_aktif', true)
            ->orderByRaw('CAST(nomor_meter AS UNSIGNED) ASC')
            ->orderBy('nama', 'asc')
            ->get();

        // Index rekap saat ini untuk lookup instan O(1)
        $rekapByWargaId = $daftarRekap->whereNotNull('warga_id')->keyBy('warga_id');
        $rekapByStruk = $daftarRekap->keyBy(fn ($item) => (string) $item->nomor_struk);

        // Preload riwayat tagihan periode lalu secara bulk (Mencegah N+1 Query)
        $wargaIds = $wargaRtAktif->pluck('id')->filter()->all();
        $wargaNames = $wargaRtAktif->pluck('nama')->filter()->all();
        $currentKey = TagihanListrik::parsePeriodeToKey($periodeAktif);

        $riwayatListAll = TagihanListrik::where(function ($q) use ($wargaIds, $wargaNames) {
            if (! empty($wargaIds)) {
                $q->whereIn('warga_id', $wargaIds);
            }
            if (! empty($wargaNames)) {
                $q->orWhereIn('nama_warga', $wargaNames);
            }
        })->get();

        $riwayatByWargaId = $riwayatListAll->whereNotNull('warga_id')->groupBy('warga_id');
        $riwayatByNama = $riwayatListAll->groupBy('nama_warga');

        $wargaRtAktif->transform(function ($w) use ($periodeAktif, $currentKey, $rekapByWargaId, $rekapByStruk, $riwayatByWargaId, $riwayatByNama) {
            $riwayatWarga = ($w->id && isset($riwayatByWargaId[$w->id]))
                ? $riwayatByWargaId[$w->id]
                : ($riwayatByNama[$w->nama] ?? collect());

            $riwayatLalu = $riwayatWarga->filter(function ($item) use ($currentKey) {
                return TagihanListrik::parsePeriodeToKey($item->periode) < $currentKey;
            })->sortByDesc(function ($item) {
                return TagihanListrik::parsePeriodeToKey($item->periode);
            })->first();

            if (! $riwayatLalu) {
                $riwayatLalu = $riwayatWarga->where('periode', '!=', $periodeAktif)->last();
            }

            $w->saldo_lalu = ($riwayatLalu && $riwayatLalu->is_lunas) ? (int) $riwayatLalu->selisih_bayar : 0;
            $w->nomor_struk = (string) ($w->nomor_meter ?: '');

            $rekap = ($w->id ? $rekapByWargaId->get($w->id) : null) ?? $rekapByStruk->get($w->nomor_struk);

            $w->is_tercatat = ! is_null($rekap);
            $w->tagihan_awal_tercatat = $rekap ? (int) $rekap->tagihan_awal : 0;
            $w->total_tagihan_tercatat = $rekap ? (int) $rekap->total_tagihan : 0;
            $w->rekap_id = $rekap?->id;

            return $w;
        });

        // Struktur data bulan untuk modal pembuka siklus baru
        $bulanBerikutnya = TagihanListrik::getBulanBerikutnya($periodeAktif);
        $partsNext = explode(' ', $bulanBerikutnya);
        $saranBulan = $partsNext[0] ?? 'Oktober';
        $saranTahun = (int) ($partsNext[1] ?? date('Y'));
        $daftarBulan = TagihanListrik::DAFTAR_BULAN;

        // Statistik ringkas per siklus periode untuk fitur Kelola Siklus (CRUD)
        $periodeStats = TagihanListrik::selectRaw('
            periode,
            COUNT(*) as total_tagihan,
            COUNT(CASE WHEN is_lunas = 1 THEN 1 END) as total_lunas,
            COUNT(CASE WHEN is_lunas = 0 THEN 1 END) as total_belum_lunas,
            COALESCE(SUM(total_tagihan), 0) as total_nominal
        ')
            ->groupBy('periode')
            ->get()
            ->keyBy('periode');

        $daftarPeriodeWithStats = $daftarPeriode->map(function ($p) use ($periodeStats) {
            $stat = $periodeStats->get($p);

            return [
                'nama' => $p,
                'total_tagihan' => (int) ($stat->total_tagihan ?? 0),
                'total_lunas' => (int) ($stat->total_lunas ?? 0),
                'total_belum_lunas' => (int) ($stat->total_belum_lunas ?? 0),
                'total_nominal' => (int) ($stat->total_nominal ?? 0),
            ];
        })->values()->all();

        return view('admin.rekap-listrik.index', compact(
            'daftarRekap',
            'daftarPeriode',
            'daftarPeriodeWithStats',
            'periodeAktif',
            'rtAktif',
            'cari',
            'totalWargaTerdata',
            'totalTagihanAwal',
            'grandTotalWajib',
            'totalKasKartar',
            'countRt1',
            'countRt2',
            'countRt3',
            'wargaRtAktif',
            'bulanBerikutnya',
            'saranBulan',
            'saranTahun',
            'daftarBulan'
        ));
    }

    /**
     * Menampilkan form rekap tagihan warga baru.
     */
    public function create(Request $request): View
    {
        $periode = $request->query('periode', 'September 2026');
        $rt = (int) $request->query('rt', 1);
        if (! in_array($rt, [1, 2, 3], true)) {
            $rt = 1;
        }

        $wargaList = Warga::where('rt', $rt)
            ->where('is_aktif', true)
            ->orderByRaw('CAST(nomor_meter AS UNSIGNED) ASC')
            ->orderBy('nama', 'asc')
            ->get()
            ->map(function ($w) use ($periode) {
                $w->saldo_lalu = TagihanListrik::getSaldoBulanLalu($w->id, $w->nama, $periode);

                return $w;
            });

        return view('admin.rekap-listrik.create', compact('periode', 'rt', 'wargaList'));
    }

    /**
     * Menyimpan data rekap tagihan listrik baru / memperbarui tagihan warga terarah.
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        if ($request->has('tagihan_awal') && is_string($request->input('tagihan_awal'))) {
            $request->merge([
                'tagihan_awal' => (int) str_replace(['.', ',', ' '], '', $request->input('tagihan_awal')),
            ]);
        }

        $validated = $request->validate([
            'periode' => ['required', 'string', 'max:50'],
            'rt' => ['required', 'integer', 'in:1,2,3'],
            'warga_id' => ['nullable', 'integer'],
            'nama_warga' => ['nullable', 'string', 'max:255'],
            'nama_pelanggan' => ['nullable', 'string', 'max:255'],
            'nomor_struk' => ['nullable', 'string', 'max:100'],
            'tagihan_awal' => ['required', 'integer', 'min:0'],
        ]);

        $warga = ! empty($validated['warga_id']) ? Warga::find($validated['warga_id']) : null;
        $namaWarga = $validated['nama_warga'] ?? $warga?->nama ?? ('Warga RT '.$validated['rt']);
        $nomorStruk = $validated['nomor_struk'] ?? $warga?->nomor_meter ?? '0';
        $namaPelanggan = $validated['nama_pelanggan'] ?? $warga?->nama_pelanggan_pln ?? $namaWarga;

        $tagihanAwal = (int) $validated['tagihan_awal'];
        $kasKartar = $tagihanAwal > 0 ? 3000 : 0;

        // Ambil saldo titipan (+) atau tunggakan (-) bulan lalu
        $saldoLalu = TagihanListrik::getSaldoBulanLalu($warga?->id, $namaWarga, $validated['periode']);
        $totalTagihan = TagihanListrik::hitungTotalTagihan($tagihanAwal, $kasKartar, $saldoLalu);

        $tagihan = TagihanListrik::updateOrCreate(
            [
                'periode' => $validated['periode'],
                'rt' => (int) $validated['rt'],
                'nomor_struk' => (string) $nomorStruk,
            ],
            [
                'warga_id' => $warga ? $warga->id : null,
                'nama_warga' => $namaWarga,
                'nama_pelanggan' => $namaPelanggan,
                'tagihan_awal' => $tagihanAwal,
                'kas_karang_taruna' => $kasKartar,
                'kas_dusun' => 0,
                'kategori_kas' => 'karang_taruna',
                'saldo_periode_lalu' => $saldoLalu,
                'total_tagihan' => $totalTagihan,
            ]
        );

        $pesan = "Tagihan {$namaWarga} (Struk [{$nomorStruk}]) berhasil dicatat.";
        if ($saldoLalu > 0) {
            $pesan .= ' Otomatis dipotong titipan bulan lalu Rp '.number_format($saldoLalu, 0, ',', '.');
        } elseif ($saldoLalu < 0) {
            $pesan .= ' Ditambahkan tunggakan bulan lalu Rp '.number_format(abs($saldoLalu), 0, ',', '.');
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $pesan,
                'data' => $tagihan,
            ]);
        }

        return redirect()->route('admin.rekap-listrik.index', [
            'periode' => $validated['periode'],
            'rt' => $validated['rt'],
        ])->with('sukses', $pesan);
    }

    /**
     * Menampilkan form edit rekap tagihan.
     */
    public function edit(TagihanListrik $tagihanListrik): View
    {
        $wargaList = Warga::where('rt', $tagihanListrik->rt)->where('is_aktif', true)->orderByRaw('CAST(nomor_meter AS UNSIGNED) ASC')->orderBy('nama', 'asc')->get();

        return view('admin.rekap-listrik.edit', compact('tagihanListrik', 'wargaList'));
    }

    /**
     * Memperbarui data rekap tagihan.
     */
    public function update(Request $request, TagihanListrik $tagihanListrik): RedirectResponse
    {
        if ($request->has('tagihan_awal') && is_string($request->input('tagihan_awal'))) {
            $request->merge([
                'tagihan_awal' => (int) str_replace(['.', ',', ' '], '', $request->input('tagihan_awal')),
            ]);
        }

        $validated = $request->validate([
            'nama_warga' => ['nullable', 'string', 'max:255'],
            'nama_pelanggan' => ['nullable', 'string', 'max:255'],
            'nomor_struk' => ['nullable', 'string', 'max:100'],
            'tagihan_awal' => ['required', 'integer', 'min:0'],
        ]);

        $tagihanAwal = (int) $validated['tagihan_awal'];
        $kasKartar = $tagihanAwal > 0 ? 3000 : 0;
        $totalTagihan = TagihanListrik::hitungTotalTagihan($tagihanAwal, $kasKartar, $tagihanListrik->saldo_periode_lalu);

        $tagihanListrik->update([
            'nama_warga' => $validated['nama_warga'] ?? $tagihanListrik->nama_warga,
            'nama_pelanggan' => $validated['nama_pelanggan'] ?? $tagihanListrik->nama_pelanggan,
            'nomor_struk' => $validated['nomor_struk'] ?? $tagihanListrik->nomor_struk,
            'tagihan_awal' => $tagihanAwal,
            'kas_karang_taruna' => $kasKartar,
            'total_tagihan' => $totalTagihan,
        ]);

        return redirect()->route('admin.rekap-listrik.index', [
            'periode' => $tagihanListrik->periode,
            'rt' => $tagihanListrik->rt,
        ])->with('sukses', "Perubahan rekap tagihan {$tagihanListrik->nama_warga} berhasil disimpan.");
    }

    /**
     * Membuka siklus baru dan secara otomatis membawa saldo titipan/kurang dari siklus sebelumnya.
     */
    public function duplikasiSiklus(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'periode_asal' => ['nullable', 'string'],
            'periode_baru' => ['required', 'string', 'max:50'],
            'rt' => ['required', 'string'],
            'mode' => ['nullable', 'string'], // 'salin_saldo' atau 'kosong'
        ]);

        $periodeBaru = trim($validated['periode_baru']);
        $periodeAsal = trim($validated['periode_asal'] ?? '');
        $mode = $validated['mode'] ?? 'salin_saldo';
        $rtPilihan = $validated['rt'];

        // Cek jika periode baru sudah ada
        if (TagihanListrik::where('periode', $periodeBaru)->exists()) {
            return redirect()->route('admin.rekap-listrik.index', [
                'periode' => $periodeBaru,
                'rt' => $rtPilihan === 'semua' ? 1 : (int) $rtPilihan,
            ])->with('info', "Siklus {$periodeBaru} sudah ada di sistem dan langsung dibuka.");
        }

        $disalin = 0;

        if ($mode === 'kosong') {
            // Inisialisasi dari Master Warga Aktif tanpa membawa saldo periode lalu
            $wargaQuery = Warga::where('is_aktif', true);
            if ($rtPilihan !== 'semua' && in_array((int) $rtPilihan, [1, 2, 3], true)) {
                $wargaQuery->where('rt', (int) $rtPilihan);
            }
            $daftarWarga = $wargaQuery->orderByRaw('CAST(nomor_meter AS UNSIGNED) ASC')->get();

            foreach ($daftarWarga as $w) {
                TagihanListrik::create([
                    'warga_id' => $w->id,
                    'periode' => $periodeBaru,
                    'rt' => $w->rt,
                    'nama_warga' => $w->nama,
                    'nama_pelanggan' => $w->nama_pelanggan_pln ?? $w->nama,
                    'nomor_struk' => (string) ($w->nomor_meter ?: ''),
                    'tagihan_awal' => 0,
                    'kas_karang_taruna' => 0,
                    'kas_dusun' => 0,
                    'kategori_kas' => 'karang_taruna',
                    'saldo_periode_lalu' => 0,
                    'total_tagihan' => 0,
                    'selisih_bayar' => 0,
                    'is_lunas' => false,
                ]);
                $disalin++;
            }
        } else {
            // Mode Salin Saldo: Ambil dari periode asal, bawa selisih_bayar sebagai saldo titipan/tunggakan
            $query = TagihanListrik::where('periode', $periodeAsal);
            if ($rtPilihan !== 'semua' && in_array((int) $rtPilihan, [1, 2, 3], true)) {
                $query->where('rt', (int) $rtPilihan);
            }
            $daftarAsal = $query->get();

            // Jika periode asal belum ada data tagihan sama sekali, fallback ke master warga aktif
            if ($daftarAsal->isEmpty()) {
                $wargaQuery = Warga::where('is_aktif', true);
                if ($rtPilihan !== 'semua' && in_array((int) $rtPilihan, [1, 2, 3], true)) {
                    $wargaQuery->where('rt', (int) $rtPilihan);
                }
                $daftarWarga = $wargaQuery->orderByRaw('CAST(nomor_meter AS UNSIGNED) ASC')->get();

                foreach ($daftarWarga as $w) {
                    $saldoLalu = TagihanListrik::getSaldoBulanLalu($w->id, $w->nama, $periodeBaru);
                    TagihanListrik::create([
                        'warga_id' => $w->id,
                        'periode' => $periodeBaru,
                        'rt' => $w->rt,
                        'nama_warga' => $w->nama,
                        'nama_pelanggan' => $w->nama_pelanggan_pln ?? $w->nama,
                        'nomor_struk' => (string) ($w->nomor_meter ?: ''),
                        'tagihan_awal' => 0,
                        'kas_karang_taruna' => 0,
                        'kas_dusun' => 0,
                        'kategori_kas' => 'karang_taruna',
                        'saldo_periode_lalu' => $saldoLalu,
                        'total_tagihan' => 0,
                        'selisih_bayar' => 0,
                        'is_lunas' => false,
                    ]);
                    $disalin++;
                }
            } else {
                foreach ($daftarAsal as $asal) {
                    $saldoLalu = (int) $asal->selisih_bayar;
                    TagihanListrik::create([
                        'warga_id' => $asal->warga_id,
                        'periode' => $periodeBaru,
                        'rt' => $asal->rt,
                        'nama_warga' => $asal->nama_warga,
                        'nama_pelanggan' => $asal->nama_pelanggan,
                        'nomor_struk' => $asal->nomor_struk,
                        'tagihan_awal' => 0,
                        'kas_karang_taruna' => 0,
                        'kas_dusun' => 0,
                        'kategori_kas' => 'karang_taruna',
                        'saldo_periode_lalu' => $saldoLalu,
                        'total_tagihan' => 0,
                        'selisih_bayar' => 0,
                        'is_lunas' => false,
                    ]);
                    $disalin++;
                }
            }
        }

        $rtTarget = $rtPilihan === 'semua' ? 1 : (int) $rtPilihan;

        return redirect()->route('admin.rekap-listrik.index', [
            'periode' => $periodeBaru,
            'rt' => $rtTarget,
        ])->with('sukses', "Siklus {$periodeBaru} berhasil dibuka! {$disalin} data warga telah disiapkan dengan saldo titipan bulan lalu otomatis terbawa.");
    }

    /**
     * Mengubah nama siklus periode di database (CRUD Siklus - Update/Rename).
     */
    public function renameSiklus(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'periode_lama' => ['required', 'string'],
            'periode_baru' => ['required', 'string', 'max:50'],
        ]);

        $lama = trim($validated['periode_lama']);
        $baru = trim($validated['periode_baru']);

        if ($lama === $baru) {
            return back()->with('info', 'Nama siklus tidak mengalami perubahan.');
        }

        // Cek apakah siklus lama ada
        $existsLama = TagihanListrik::where('periode', $lama)->exists();
        if (! $existsLama) {
            return back()->with('error', "Siklus {$lama} tidak ditemukan di sistem.");
        }

        // Cek apakah siklus baru sudah ada
        $existsBaru = TagihanListrik::where('periode', $baru)->exists();
        if ($existsBaru) {
            return back()->with('error', "Siklus {$baru} sudah ada. Silakan pilih nama bulan atau tahun yang berbeda.");
        }

        $affected = TagihanListrik::where('periode', $lama)->update(['periode' => $baru]);

        return redirect()->route('admin.rekap-listrik.index', [
            'periode' => $baru,
            'rt' => $request->query('rt', 1),
        ])->with('sukses', "Nama siklus {$lama} berhasil diubah menjadi {$baru} ({$affected} data tagihan warga disesuaikan).");
    }

    /**
     * Menghapus seluruh data tagihan pada satu siklus periode (CRUD Siklus - Delete).
     */
    public function hapusSiklus(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'periode' => ['required', 'string'],
        ]);

        $periode = trim($validated['periode']);

        $daftarPeriode = TagihanListrik::getDaftarPeriodeKronologis();
        if (count($daftarPeriode) <= 1) {
            return back()->with('error', "Tidak dapat menghapus siklus {$periode}. Sistem membutuhkan minimal 1 siklus periode aktif.");
        }

        $count = TagihanListrik::where('periode', $periode)->count();
        TagihanListrik::where('periode', $periode)->delete();

        // Cari periode aktif tersisa
        $sisaPeriode = TagihanListrik::getDaftarPeriodeKronologis();
        $targetPeriode = $sisaPeriode[0] ?? null;

        return redirect()->route('admin.rekap-listrik.index', [
            'periode' => $targetPeriode,
            'rt' => 1,
        ])->with('sukses', "Siklus {$periode} beserta {$count} data tagihan di dalamnya berhasil dihapus.");
    }

    /**
     * Menyimpan hasil impor/scan massal banyak warga sekaligus setelah proses cross-check.
     */
    public function batchStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'periode' => ['required', 'string', 'max:50'],
            'rt' => ['required', 'integer', 'in:1,2,3'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.nama_warga' => ['required', 'string', 'max:255'],
            'items.*.nama_pelanggan' => ['nullable', 'string', 'max:255'],
            'items.*.nomor_struk' => ['required', 'string', 'max:100'],
            'items.*.tagihan_awal' => ['required'],
            'items.*.warga_id' => ['nullable'],
        ]);

        $periode = $validated['periode'];
        $rt = (int) $validated['rt'];
        $disimpan = 0;

        foreach ($validated['items'] as $item) {
            $tagihanAwal = (int) str_replace(['.', ',', ' '], '', (string) $item['tagihan_awal']);
            if ($tagihanAwal <= 0) {
                continue;
            }

            $kasKartar = $tagihanAwal > 0 ? 3000 : 0;
            $wargaId = ! empty($item['warga_id']) ? (int) $item['warga_id'] : null;

            $saldoLalu = TagihanListrik::getSaldoBulanLalu($wargaId, $item['nama_warga'], $periode);
            $totalTagihan = TagihanListrik::hitungTotalTagihan($tagihanAwal, $kasKartar, $saldoLalu);

            $namaPelanggan = ! empty($item['nama_pelanggan']) ? $item['nama_pelanggan'] : $item['nama_warga'];

            TagihanListrik::updateOrCreate(
                [
                    'periode' => $periode,
                    'rt' => $rt,
                    'nomor_struk' => (string) $item['nomor_struk'],
                ],
                [
                    'warga_id' => $wargaId,
                    'nama_warga' => $item['nama_warga'],
                    'nama_pelanggan' => $namaPelanggan,
                    'tagihan_awal' => $tagihanAwal,
                    'kas_karang_taruna' => $kasKartar,
                    'kas_dusun' => 0,
                    'saldo_periode_lalu' => $saldoLalu,
                    'total_tagihan' => $totalTagihan,
                    'is_lunas' => false,
                ]
            );
            $disimpan++;
        }

        return redirect()->route('admin.rekap-listrik.index', ['periode' => $periode, 'rt' => $rt])
            ->with('sukses', "Alhamdulillah! Berhasil merekap {$disimpan} data tagihan warga dari dokumen/foto ke RT {$rt}!");
    }

    /**
     * Menghapus baris rekap tagihan.
     */
    public function destroy(TagihanListrik $tagihanListrik): RedirectResponse
    {
        $nama = $tagihanListrik->nama_warga ?: $tagihanListrik->nama_pelanggan;
        $periode = $tagihanListrik->periode;
        $rt = $tagihanListrik->rt;

        $tagihanListrik->delete();

        return redirect()->route('admin.rekap-listrik.index', [
            'periode' => $periode,
            'rt' => $rt,
        ])->with('sukses', "Data rekap {$nama} telah dihapus.");
    }

    /**
     * Menggabungkan beberapa tagihan warga ke dalam satu struk tagihan gabungan pada waktu rekapitulasi.
     */
    public function gabungkan(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'tagihan_ids' => ['required', 'array', 'min:2'],
            'tagihan_ids.*' => ['required', 'integer', 'exists:tagihan_listrik,id'],
            'nama_pembayar' => ['required', 'string', 'max:255'],
            'kode_gabungan' => ['nullable', 'string', 'max:100'],
        ]);

        $tagihanList = TagihanListrik::whereIn('id', $validated['tagihan_ids'])->get();
        $kodeGabungan = ! empty($validated['kode_gabungan'])
            ? trim($validated['kode_gabungan'])
            : 'GAB-'.Str::upper(Str::random(6));

        $namaPembayar = trim($validated['nama_pembayar']);

        foreach ($tagihanList as $tagihan) {
            $tagihan->update([
                'kode_gabungan' => $kodeGabungan,
                'nama_pembayar' => $namaPembayar,
            ]);
        }

        $totalWajib = (int) $tagihanList->sum('total_tagihan');
        $pesan = count($tagihanList)." tagihan berhasil digabungkan atas nama {$namaPembayar} (Kode: {$kodeGabungan}, Total: Rp ".number_format($totalWajib, 0, ',', '.').').';

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $pesan,
                'kode_gabungan' => $kodeGabungan,
                'nama_pembayar' => $namaPembayar,
                'total_tagihan' => $totalWajib,
            ]);
        }

        return back()->with('sukses', $pesan);
    }

    /**
     * Memisahkan tagihan dari kelompok gabungan (mengembalikan menjadi tagihan mandiri).
     */
    public function pisahkan(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'tagihan_id' => ['nullable', 'integer', 'exists:tagihan_listrik,id'],
            'kode_gabungan' => ['nullable', 'string', 'max:100'],
        ]);

        if (! empty($validated['tagihan_id'])) {
            $tagihan = TagihanListrik::findOrFail($validated['tagihan_id']);
            $kodeLama = $tagihan->kode_gabungan;
            $nama = $tagihan->nama_warga ?: $tagihan->nama_pelanggan;

            $tagihan->update([
                'kode_gabungan' => null,
                'nama_pembayar' => null,
            ]);

            // Jika anggota kelompok tersisa hanya 1, lepaskan juga agar tidak ada gabungan tunggal
            if ($kodeLama) {
                $sisa = TagihanListrik::where('kode_gabungan', $kodeLama)->get();
                if ($sisa->count() === 1) {
                    $sisa->first()->update([
                        'kode_gabungan' => null,
                        'nama_pembayar' => null,
                    ]);
                }
            }

            $pesan = "Tagihan {$nama} berhasil dipisahkan dari tagihan gabungan.";
        } elseif (! empty($validated['kode_gabungan'])) {
            $affected = TagihanListrik::where('kode_gabungan', $validated['kode_gabungan'])
                ->update([
                    'kode_gabungan' => null,
                    'nama_pembayar' => null,
                ]);

            $pesan = "Seluruh tagihan dalam kelompok {$validated['kode_gabungan']} ({$affected} tagihan) berhasil dipisahkan menjadi tagihan mandiri.";
        } else {
            return back()->withErrors(['error' => 'Data tagihan atau kode gabungan tidak ditemukan.']);
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $pesan,
            ]);
        }

        return back()->with('sukses', $pesan);
    }
}
