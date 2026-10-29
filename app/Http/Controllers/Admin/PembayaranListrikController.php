<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Keuangan;
use App\Models\TagihanListrik;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PembayaranListrikController extends Controller
{
    /**
     * Menampilkan antarmuka kasir pembayaran listrik warga per periode & RT (1, 2, 3).
     */
    public function index(Request $request): View
    {
        // Ambil daftar periode secara kronologis (bulan terbaru di atas)
        $daftarPeriode = collect(TagihanListrik::getDaftarPeriodeKronologis());

        $periodeAktif = $request->query('periode', $daftarPeriode->first());
        $rtAktif = $request->query('rt', '1'); // 'semua', '1', '2', '3'
        $tab = $request->query('tab', 'belum_bayar'); // default langsung ke yang belum bayar
        $cari = $request->query('cari');

        $query = TagihanListrik::where('periode', $periodeAktif);

        if ($rtAktif !== 'semua' && in_array((int) $rtAktif, [1, 2, 3], true)) {
            $query->where('rt', (int) $rtAktif);
        }

        if ($cari) {
            $query->where(function ($q) use ($cari) {
                $q->where('nama_warga', 'like', "%{$cari}%")
                    ->orWhere('nama_pembayar', 'like', "%{$cari}%")
                    ->orWhere('nama_pelanggan', 'like', "%{$cari}%")
                    ->orWhere('nomor_struk', 'like', "%{$cari}%")
                    ->orWhere('kode_gabungan', 'like', "%{$cari}%");
            });
        }

        // Target & Realisasi Pembayaran (Dihitung dalam 1 Query Terkonsolidasi)
        $stats = (clone $query)->selectRaw('
            COUNT(*) as total_tagihan,
            COALESCE(SUM(tagihan_awal), 0) as target_pln_awal,
            COALESCE(SUM(kas_karang_taruna), 0) as target_kas_kartar,
            COALESCE(SUM(total_tagihan), 0) as target_total_wajib,

            COUNT(CASE WHEN is_lunas = 0 THEN 1 END) as total_belum_lunas,
            COALESCE(SUM(CASE WHEN is_lunas = 0 THEN total_tagihan ELSE 0 END), 0) as nominal_belum_lunas,
            COALESCE(SUM(CASE WHEN is_lunas = 0 THEN tagihan_awal ELSE 0 END), 0) as pln_belum_lunas,
            COALESCE(SUM(CASE WHEN is_lunas = 0 THEN kas_karang_taruna ELSE 0 END), 0) as kas_belum_lunas,

            COUNT(CASE WHEN is_lunas = 1 THEN 1 END) as total_lunas,
            COALESCE(SUM(CASE WHEN is_lunas = 1 THEN total_tagihan ELSE 0 END), 0) as nominal_lunas,
            COALESCE(SUM(CASE WHEN is_lunas = 1 THEN tagihan_awal ELSE 0 END), 0) as pln_terkumpul,
            COALESCE(SUM(CASE WHEN is_lunas = 1 THEN kas_karang_taruna ELSE 0 END), 0) as kas_kartar_terkumpul,

            COALESCE(SUM(CASE WHEN is_lunas = 1 AND selisih_bayar > 0 THEN selisih_bayar ELSE 0 END), 0) as total_titipan,
            COALESCE(SUM(CASE WHEN is_lunas = 1 AND selisih_bayar < 0 THEN selisih_bayar ELSE 0 END), 0) as total_kurang
        ')->first();

        $totalTagihan = (int) ($stats->total_tagihan ?? 0);
        $targetPlnAwal = (int) ($stats->target_pln_awal ?? 0);
        $targetKasKartar = (int) ($stats->target_kas_kartar ?? 0);
        $targetTotalWajib = (int) ($stats->target_total_wajib ?? 0);

        $totalBelumLunas = (int) ($stats->total_belum_lunas ?? 0);
        $nominalBelumLunas = (int) ($stats->nominal_belum_lunas ?? 0);
        $plnBelumLunas = (int) ($stats->pln_belum_lunas ?? 0);
        $kasBelumLunas = (int) ($stats->kas_belum_lunas ?? 0);

        $totalLunas = (int) ($stats->total_lunas ?? 0);
        $nominalLunas = (int) ($stats->nominal_lunas ?? 0);
        $plnTerkumpul = (int) ($stats->pln_terkumpul ?? 0);
        $kasKartarTerkumpul = (int) ($stats->kas_kartar_terkumpul ?? 0);

        $totalTitipan = (int) ($stats->total_titipan ?? 0);
        $totalKurang = (int) ($stats->total_kurang ?? 0);
        $totalUangFisikTerkumpul = $nominalLunas + $totalTitipan;

        $persentaseLunas = $totalTagihan > 0 ? (int) round(($totalLunas / $totalTagihan) * 100) : 0;

        // Cek status pembukuan kas karang taruna di tabel keuangan
        $transaksiKas = Keuangan::where('kategori', 'like', '%Karang Taruna%')
            ->where('keterangan', 'like', "%{$periodeAktif}%")
            ->when($rtAktif !== 'semua', fn ($q) => $q->where('keterangan', 'like', "%RT {$rtAktif}%"))
            ->first();

        // Filter tab
        if ($tab === 'lunas') {
            $query->where('is_lunas', true);
        } else {
            $tab = 'belum_bayar';
            $query->where('is_lunas', false);
        }

        $daftarTagihan = $query->orderByRaw('CAST(nomor_struk AS UNSIGNED) ASC')
            ->orderBy('id', 'asc')
            ->get();

        // Kelompokkan tagihan gabungan agar kasir langsung melihat total gabungan
        $grupTagihan = [];

        foreach ($daftarTagihan as $item) {
            if (! empty($item->kode_gabungan)) {
                $kode = $item->kode_gabungan;
                if (! isset($grupTagihan[$kode])) {
                    $grupTagihan[$kode] = [
                        'is_gabungan' => true,
                        'kode_gabungan' => $kode,
                        'nama_pembayar' => $item->nama_pembayar ?: $item->nama_warga,
                        'items' => collect(),
                        'total_tagihan' => 0,
                        'tagihan_awal' => 0,
                        'kas_karang_taruna' => 0,
                        'saldo_periode_lalu' => 0,
                        'selisih_bayar' => 0,
                        'is_lunas' => (bool) $item->is_lunas,
                        'tanggal_bayar' => $item->tanggal_bayar,
                        'min_struk' => (int) $item->nomor_struk,
                        'rt' => $item->rt,
                    ];
                }
                $grupTagihan[$kode]['items']->push($item);
                $grupTagihan[$kode]['total_tagihan'] += $item->total_tagihan;
                $grupTagihan[$kode]['tagihan_awal'] += $item->tagihan_awal;
                $grupTagihan[$kode]['kas_karang_taruna'] += $item->kas_karang_taruna;
                $grupTagihan[$kode]['saldo_periode_lalu'] += $item->saldo_periode_lalu;
                $grupTagihan[$kode]['selisih_bayar'] += $item->selisih_bayar;
                $grupTagihan[$kode]['min_struk'] = min($grupTagihan[$kode]['min_struk'], (int) $item->nomor_struk);
            } else {
                $grupTagihan['single_'.$item->id] = [
                    'is_gabungan' => false,
                    'kode_gabungan' => null,
                    'nama_pembayar' => $item->nama_pembayar,
                    'items' => collect([$item]),
                    'total_tagihan' => $item->total_tagihan,
                    'tagihan_awal' => $item->tagihan_awal,
                    'kas_karang_taruna' => $item->kas_karang_taruna,
                    'saldo_periode_lalu' => $item->saldo_periode_lalu,
                    'selisih_bayar' => $item->selisih_bayar,
                    'is_lunas' => (bool) $item->is_lunas,
                    'tanggal_bayar' => $item->tanggal_bayar,
                    'min_struk' => (int) $item->nomor_struk,
                    'rt' => $item->rt,
                    'single_item' => $item,
                ];
            }
        }

        $daftarTampilan = collect($grupTagihan)->sortBy('min_struk')->values();

        return view('admin.pembayaran-listrik.index', compact(
            'daftarTagihan',
            'daftarTampilan',
            'daftarPeriode',
            'periodeAktif',
            'rtAktif',
            'tab',
            'cari',
            'totalTagihan',
            'targetPlnAwal',
            'targetKasKartar',
            'targetTotalWajib',
            'totalBelumLunas',
            'nominalBelumLunas',
            'plnBelumLunas',
            'kasBelumLunas',
            'totalLunas',
            'nominalLunas',
            'plnTerkumpul',
            'kasKartarTerkumpul',
            'totalTitipan',
            'totalKurang',
            'totalUangFisikTerkumpul',
            'persentaseLunas',
            'transaksiKas'
        ));
    }

    /**
     * Memproses pembayaran (Kasir). Menerima uang_dibayar, uang_kembalian, dan menghitung sisa kembalian (titip/kurang).
     */
    public function bayar(Request $request): RedirectResponse
    {
        if ($request->has('uang_dibayar') && is_string($request->input('uang_dibayar'))) {
            $request->merge([
                'uang_dibayar' => (int) str_replace(['.', ',', ' '], '', $request->input('uang_dibayar')),
            ]);
        }

        if ($request->has('uang_kembalian') && is_string($request->input('uang_kembalian'))) {
            $request->merge([
                'uang_kembalian' => (int) str_replace(['.', ',', ' '], '', $request->input('uang_kembalian')),
            ]);
        }

        $validated = $request->validate([
            'tagihan_ids' => ['required', 'array', 'min:1'],
            'tagihan_ids.*' => ['required', 'integer', 'exists:tagihan_listrik,id'],
            'nama_pembayar' => ['nullable', 'string', 'max:255'],
            'uang_dibayar' => ['required', 'integer', 'min:0'],
            'uang_kembalian' => ['nullable', 'integer', 'min:0'],
            'jadikan_titipan' => ['nullable', 'boolean'],
            'kode_gabungan' => ['nullable', 'string', 'max:100'],
        ]);

        $tagihanList = TagihanListrik::whereIn('id', $validated['tagihan_ids'])->get();
        $totalWajib = (int) $tagihanList->sum('total_tagihan');
        $uangDibayar = (int) $validated['uang_dibayar'];
        $namaPembayar = ! empty($validated['nama_pembayar']) ? trim($validated['nama_pembayar']) : null;

        $kembalianSemestinya = 0;
        $kembalianDiserahkan = 0;

        // Logika Penentuan Kembalian & Saldo Titipan / Kurang:
        // Misal tagihan 43.000, uang bayar 50.000 -> kembalian semestinya 7.000.
        // 1. Jika kasir input kembalian diserahkan 5.000, maka sisa 2.000 otomatis menjadi saldo titipan bulan depan.
        // 2. Jika kasir input kembalian 0 (tidak ada pecahan), seluruh 7.000 menjadi titipan.
        // 3. Jika kasir menyerahkan penuh 7.000, maka sisa titipan adalah 0 (tanpa titipan).
        // 4. Jika kasir malah punya uang kembalian 10.000 (lebih dari 7.000), maka selisih -3.000 otomatis dicatat sebagai KURANG BAYAR / NINGGAL (tunggakan bulan depan).
        if ($uangDibayar > $totalWajib) {
            $kembalianSemestinya = $uangDibayar - $totalWajib;

            if ($request->filled('uang_kembalian') || ($request->has('uang_kembalian') && $request->input('uang_kembalian') === 0)) {
                $kembalianDiserahkan = max(0, (int) $request->input('uang_kembalian'));
                $selisihTotal = ($uangDibayar - $kembalianDiserahkan) - $totalWajib;
            } else {
                // Fallback kompatibilitas: jika form lama menggunakan checkbox jadikan_titipan
                $jadikanTitipan = $request->boolean('jadikan_titipan');
                $selisihTotal = $jadikanTitipan ? $kembalianSemestinya : 0;
                $kembalianDiserahkan = $kembalianSemestinya - $selisihTotal;
            }
        } elseif ($uangDibayar < $totalWajib) {
            $selisihTotal = $uangDibayar - $totalWajib; // Negatif (Kurang/Ninggal)
            $kembalianSemestinya = 0;
            $kembalianDiserahkan = 0;
        } else {
            $selisihTotal = 0; // Uang Pas
            $kembalianSemestinya = 0;
            $kembalianDiserahkan = 0;
        }

        $isMulti = count($tagihanList) > 1;
        $kodeGabungan = ! empty($validated['kode_gabungan'])
            ? $validated['kode_gabungan']
            : ($isMulti ? 'GAB-'.Str::upper(Str::random(6)) : null);

        if (! $isMulti) {
            $tagihan = $tagihanList->first();
            $tagihan->update([
                'is_lunas' => true,
                'nama_pembayar' => $namaPembayar,
                'tanggal_bayar' => now()->toDateString(),
                'selisih_bayar' => $selisihTotal,
                'kode_gabungan' => $kodeGabungan ?: $tagihan->kode_gabungan,
            ]);

            $nama = $tagihan->nama_warga ?: $tagihan->nama_pelanggan;

            $pesan = "Pembayaran {$nama} (Struk [{$tagihan->nomor_struk}]) berhasil lunas.";
            if ($namaPembayar) {
                $pesan .= " (Dibayar oleh: {$namaPembayar})";
            }
            if ($uangDibayar > $totalWajib) {
                if ($selisihTotal < 0) {
                    $kurang = abs($selisihTotal);
                    $pesan .= ' Kembalian diserahkan Rp '.number_format($kembalianDiserahkan, 0, ',', '.').' (lebih Rp '.number_format($kembalianDiserahkan - $kembalianSemestinya, 0, ',', '.').' dari semestinya). Warga dicatat kurang bayar Rp '.number_format($kurang, 0, ',', '.').' sebagai tunggakan bulan depan.';
                } elseif ($selisihTotal > 0 && $kembalianDiserahkan > 0) {
                    $pesan .= ' Kembalian Rp '.number_format($kembalianDiserahkan, 0, ',', '.').' diserahkan. Sisa kembalian Rp '.number_format($selisihTotal, 0, ',', '.').' disimpan sebagai titipan bulan depan.';
                } elseif ($selisihTotal > 0 && $kembalianDiserahkan === 0) {
                    $pesan .= ' Sisa Rp '.number_format($selisihTotal, 0, ',', '.').' disimpan sebagai titipan bulan depan (karena tidak ada uang kembalian).';
                } else {
                    $pesan .= ' Kembalian Rp '.number_format($kembalianSemestinya, 0, ',', '.').' diserahkan ke warga (tanpa titipan).';
                }
            } elseif ($uangDibayar < $totalWajib) {
                $kurang = $totalWajib - $uangDibayar;
                $pesan .= ' Kurang bayar Rp '.number_format($kurang, 0, ',', '.').' dicatat sebagai tagihan sisa.';
            }

            return back()->with('sukses', $pesan);
        }

        // Struk gabungan: selisih total disimpan di baris pertama
        foreach ($tagihanList as $idx => $tagihan) {
            $tagihan->update([
                'is_lunas' => true,
                'nama_pembayar' => $namaPembayar,
                'tanggal_bayar' => now()->toDateString(),
                'selisih_bayar' => $idx === 0 ? $selisihTotal : 0,
                'kode_gabungan' => $kodeGabungan,
            ]);
        }

        $pesan = count($tagihanList)." warga berhasil dibayarkan dalam struk gabungan ({$kodeGabungan}).";
        if ($namaPembayar) {
            $pesan .= " (Dibayar oleh: {$namaPembayar})";
        }
        if ($uangDibayar > $totalWajib) {
            if ($selisihTotal < 0) {
                $kurang = abs($selisihTotal);
                $pesan .= ' Kembalian diserahkan Rp '.number_format($kembalianDiserahkan, 0, ',', '.').' (lebih Rp '.number_format($kembalianDiserahkan - $kembalianSemestinya, 0, ',', '.').'). Warga dicatat kurang bayar Rp '.number_format($kurang, 0, ',', '.').' sebagai tunggakan bulan depan.';
            } elseif ($selisihTotal > 0 && $kembalianDiserahkan > 0) {
                $pesan .= ' Kembalian Rp '.number_format($kembalianDiserahkan, 0, ',', '.').' diserahkan. Sisa kembalian Rp '.number_format($selisihTotal, 0, ',', '.').' disimpan sebagai titipan bulan depan.';
            } elseif ($selisihTotal > 0 && $kembalianDiserahkan === 0) {
                $pesan .= ' Sisa Rp '.number_format($selisihTotal, 0, ',', '.').' disimpan sebagai titipan bulan depan (karena tidak ada uang kembalian).';
            } else {
                $pesan .= ' Kembalian Rp '.number_format($kembalianSemestinya, 0, ',', '.').' diserahkan (tanpa titipan).';
            }
        } elseif ($uangDibayar < $totalWajib) {
            $kurang = $totalWajib - $uangDibayar;
            $pesan .= ' Kurang bayar Rp '.number_format($kurang, 0, ',', '.').' dicatat sebagai tagihan sisa.';
        }

        return back()->with('sukses', $pesan);
    }

    /**
     * Membatalkan status lunas (jika ada salah klik di HP).
     */
    public function batalBayar(TagihanListrik $tagihanListrik): RedirectResponse
    {
        if ($tagihanListrik->kode_gabungan) {
            $anggota = TagihanListrik::where('kode_gabungan', $tagihanListrik->kode_gabungan)
                ->where('periode', $tagihanListrik->periode)
                ->get();

            foreach ($anggota as $item) {
                $item->update([
                    'is_lunas' => false,
                    'tanggal_bayar' => null,
                    'selisih_bayar' => 0,
                ]);
            }

            return back()->with('sukses', "Status pembayaran struk gabungan {$tagihanListrik->kode_gabungan} (".count($anggota).' warga) dikembalikan ke Belum Lunas.');
        }

        $tagihanListrik->update([
            'is_lunas' => false,
            'tanggal_bayar' => null,
            'selisih_bayar' => 0,
        ]);

        $nama = $tagihanListrik->nama_warga ?: $tagihanListrik->nama_pelanggan;

        return back()->with('sukses', "Status pembayaran {$nama} dikembalikan ke Belum Lunas.");
    }

    /**
     * Menggabungkan beberapa tagihan langsung dari kasir (dinamis jika lupa pada saat rekap).
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
     * Memisahkan tagihan dari kelompok gabungan di kasir.
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

            if ($kodeLama) {
                $sisa = TagihanListrik::where('kode_gabungan', $kodeLama)->get();
                if ($sisa->count() === 1) {
                    $sisa->first()->update([
                        'kode_gabungan' => null,
                        'nama_pembayar' => null,
                    ]);
                }
            }

            $pesan = "Tagihan {$nama} berhasil dipisahkan menjadi tagihan mandiri.";
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

    /**
     * Membukukan kas karang taruna yang telah terkumpul ke modul Keuangan Dusun/Karang Taruna.
     */
    public function bukukanKas(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'periode' => ['required', 'string'],
            'rt' => ['required', 'string'],
            'nominal' => ['required', 'numeric', 'min:1'],
        ]);

        $rtLabel = $validated['rt'] !== 'semua' ? " RT {$validated['rt']}" : ' Seluruh RT';
        $keterangan = "Kas Karang Taruna Rekap Listrik Periode {$validated['periode']}{$rtLabel}";

        $sudahAda = Keuangan::where('kategori', 'like', '%Karang Taruna%')
            ->where('keterangan', $keterangan)
            ->first();

        if ($sudahAda) {
            return back()->with('sukses', "Kas Karang Taruna periode ini sudah pernah dibukukan dengan kode transaksi {$sudahAda->kode_transaksi}.");
        }

        $kodeTransaksi = 'KAS-KARTAR-'.date('Ymd').'-'.Str::upper(Str::random(4));

        Keuangan::create([
            'kode_transaksi' => $kodeTransaksi,
            'tanggal' => now()->toDateString(),
            'jenis' => 'masuk',
            'nominal' => (float) $validated['nominal'],
            'kategori' => 'Iuran Warga',
            'keterangan' => $keterangan,
            'dicatat_oleh' => auth()->id(),
        ]);

        return back()->with('sukses', 'Kas Karang Taruna sebesar Rp '.number_format($validated['nominal'], 0, ',', '.')." berhasil dibukukan ke Kas Dusun (Kode: {$kodeTransaksi})!");
    }
}
