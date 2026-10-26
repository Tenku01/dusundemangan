<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TagihanListrik;
use App\Models\Warga;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
                    ->orWhere('nama_pelanggan', 'like', "%{$cari}%")
                    ->orWhere('nomor_struk', 'like', "%{$cari}%");
            });
        }

        // Summary KPI siklus periode ini di RT ini
        $totalWargaTerdata = (clone $query)->count();
        $totalTagihanAwal = (int) (clone $query)->sum('tagihan_awal');
        $grandTotalWajib = (int) (clone $query)->sum('total_tagihan');
        $totalKasKartar = (clone $query)->where('tagihan_awal', '>', 0)->count() * 3000;

        // Ambil data rekap (urutkan berdasarkan nomor struk numerik)
        $daftarRekap = $query->orderByRaw('CAST(nomor_struk AS UNSIGNED) ASC')
            ->orderBy('id', 'asc')
            ->get();

        // Hitung jumlah data per RT di periode ini untuk indikator badge tombol RT
        $countRt1 = TagihanListrik::where('periode', $periodeAktif)->where('rt', 1)->count();
        $countRt2 = TagihanListrik::where('periode', $periodeAktif)->where('rt', 2)->count();
        $countRt3 = TagihanListrik::where('periode', $periodeAktif)->where('rt', 3)->count();

        // Master Warga di RT ini untuk input terarah (urut nomor struk), beserta saldo titipan & status tercatat
        $wargaRtAktif = Warga::where('rt', $rtAktif)
            ->where('is_aktif', true)
            ->orderByRaw('CAST(nomor_meter AS UNSIGNED) ASC')
            ->orderBy('nama', 'asc')
            ->get()
            ->map(function ($w) use ($periodeAktif, $daftarRekap) {
                $w->saldo_lalu = TagihanListrik::getSaldoBulanLalu($w->id, $w->nama, $periodeAktif);
                $w->nomor_struk = (string) ($w->nomor_meter ?: '');

                $rekap = $daftarRekap->first(function ($item) use ($w) {
                    return ($item->warga_id && $item->warga_id == $w->id)
                        || ((string) $item->nomor_struk === (string) $w->nomor_meter);
                });

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

        return view('admin.rekap-listrik.index', compact(
            'daftarRekap',
            'daftarPeriode',
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
            'periode_asal' => ['required', 'string'],
            'periode_baru' => ['required', 'string', 'max:50'],
            'rt' => ['required', 'string'],
        ]);

        $query = TagihanListrik::where('periode', $validated['periode_asal']);
        if ($validated['rt'] !== 'semua' && in_array((int) $validated['rt'], [1, 2, 3], true)) {
            $query->where('rt', (int) $validated['rt']);
        }

        $daftarAsal = $query->get();
        $disalin = 0;

        foreach ($daftarAsal as $asal) {
            $ada = TagihanListrik::where('periode', $validated['periode_baru'])
                ->where('rt', $asal->rt)
                ->where(function ($q) use ($asal) {
                    if ($asal->warga_id) {
                        $q->where('warga_id', $asal->warga_id);
                    } else {
                        $q->where('nama_warga', $asal->nama_warga);
                    }
                })
                ->exists();

            if (! $ada) {
                $saldoLalu = (int) $asal->selisih_bayar;
                TagihanListrik::create([
                    'warga_id' => $asal->warga_id,
                    'periode' => $validated['periode_baru'],
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

        $rtTarget = $validated['rt'] === 'semua' ? 1 : (int) $validated['rt'];

        return redirect()->route('admin.rekap-listrik.index', [
            'periode' => $validated['periode_baru'],
            'rt' => $rtTarget,
        ])->with('sukses', "Siklus {$validated['periode_baru']} berhasil dibuka! {$disalin} warga disiapkan dengan saldo titipan bulan lalu otomatis terbawa.");
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
}
