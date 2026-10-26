<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Keuangan;
use App\Models\TagihanListrik;
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
                    ->orWhere('nama_pelanggan', 'like', "%{$cari}%")
                    ->orWhere('nomor_struk', 'like', "%{$cari}%")
                    ->orWhere('kode_gabungan', 'like', "%{$cari}%");
            });
        }

        // Target dari Rekapitulasi (Harus 100% Sesuai dengan Hasil Rekap)
        $summaryBase = clone $query;
        $totalTagihan = (clone $summaryBase)->count();
        $targetPlnAwal = (int) (clone $summaryBase)->sum('tagihan_awal');
        $targetKasKartar = (int) (clone $summaryBase)->sum('kas_karang_taruna');
        $targetTotalWajib = (int) (clone $summaryBase)->sum('total_tagihan');

        // Realisasi Pembayaran Kasir
        $totalBelumLunas = (clone $summaryBase)->where('is_lunas', false)->count();
        $nominalBelumLunas = (int) (clone $summaryBase)->where('is_lunas', false)->sum('total_tagihan');
        $plnBelumLunas = (int) (clone $summaryBase)->where('is_lunas', false)->sum('tagihan_awal');
        $kasBelumLunas = (int) (clone $summaryBase)->where('is_lunas', false)->sum('kas_karang_taruna');

        $totalLunas = (clone $summaryBase)->where('is_lunas', true)->count();
        $nominalLunas = (int) (clone $summaryBase)->where('is_lunas', true)->sum('total_tagihan');
        $plnTerkumpul = (int) (clone $summaryBase)->where('is_lunas', true)->sum('tagihan_awal');
        $kasKartarTerkumpul = (int) (clone $summaryBase)->where('is_lunas', true)->sum('kas_karang_taruna');

        $totalTitipan = (int) (clone $summaryBase)->where('is_lunas', true)->where('selisih_bayar', '>', 0)->sum('selisih_bayar');
        $totalKurang = (int) (clone $summaryBase)->where('is_lunas', true)->where('selisih_bayar', '<', 0)->sum('selisih_bayar');
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

        return view('admin.pembayaran-listrik.index', compact(
            'daftarTagihan',
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
     * Memproses pembayaran (Kasir). Menerima uang_dibayar dan menghitung selisih (titip/kurang).
     */
    public function bayar(Request $request): RedirectResponse
    {
        if ($request->has('uang_dibayar') && is_string($request->input('uang_dibayar'))) {
            $request->merge([
                'uang_dibayar' => (int) str_replace(['.', ',', ' '], '', $request->input('uang_dibayar')),
            ]);
        }

        $validated = $request->validate([
            'tagihan_ids' => ['required', 'array', 'min:1'],
            'tagihan_ids.*' => ['required', 'integer', 'exists:tagihan_listrik,id'],
            'nama_pembayar' => ['nullable', 'string', 'max:255'],
            'uang_dibayar' => ['required', 'integer', 'min:0'],
            'jadikan_titipan' => ['nullable', 'boolean'],
            'kode_gabungan' => ['nullable', 'string', 'max:100'],
        ]);

        $tagihanList = TagihanListrik::whereIn('id', $validated['tagihan_ids'])->get();
        $totalWajib = (int) $tagihanList->sum('total_tagihan');
        $uangDibayar = (int) $validated['uang_dibayar'];
        $jadikanTitipan = $request->boolean('jadikan_titipan');
        $namaPembayar = ! empty($validated['nama_pembayar']) ? trim($validated['nama_pembayar']) : null;

        // Aturan: Jika ada kembalian yaudah gaada titipan (kembalian diserahkan ke warga, selisih = 0).
        // Titipan HANYA berfungsi jika tidak ada kembalian (kasir sengaja mencentang opsi jadikan_titipan).
        if ($uangDibayar > $totalWajib) {
            $selisihTotal = $jadikanTitipan ? ($uangDibayar - $totalWajib) : 0;
        } elseif ($uangDibayar < $totalWajib) {
            $selisihTotal = $uangDibayar - $totalWajib; // Negatif (Kurang/Ninggal)
        } else {
            $selisihTotal = 0; // Uang Pas
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
                $kembalian = $uangDibayar - $totalWajib;
                if ($jadikanTitipan) {
                    $pesan .= ' Sisa Rp '.number_format($kembalian, 0, ',', '.').' disimpan sebagai titipan bulan depan (karena tidak ada kembalian).';
                } else {
                    $pesan .= ' Kembalian Rp '.number_format($kembalian, 0, ',', '.').' diserahkan ke warga (tanpa titipan).';
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
        if ($uangDibayar > $totalWajib) {
            $kembalian = $uangDibayar - $totalWajib;
            if ($jadikanTitipan) {
                $pesan .= ' Sisa Rp '.number_format($kembalian, 0, ',', '.').' disimpan sebagai titipan bulan depan.';
            } else {
                $pesan .= ' Kembalian Rp '.number_format($kembalian, 0, ',', '.').' diserahkan (tanpa titipan).';
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
        $tagihanListrik->update([
            'is_lunas' => false,
            'tanggal_bayar' => null,
            'selisih_bayar' => 0,
        ]);

        $nama = $tagihanListrik->nama_warga ?: $tagihanListrik->nama_pelanggan;

        return back()->with('sukses', "Status pembayaran {$nama} dikembalikan ke Belum Lunas.");
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
