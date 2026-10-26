<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TagihanListrik;
use App\Models\Warga;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TagihanListrikController extends Controller
{
    /**
     * Menampilkan daftar rekapitulasi tagihan listrik warga.
     */
    public function index(Request $request): View
    {
        $periode = $request->query('periode');
        $rt = $request->query('rt');
        $statusBayar = $request->query('status_bayar');
        $tab = $request->query('tab', 'semua'); // 'semua', 'belum_bayar', 'lunas'
        $cari = $request->query('cari');

        $query = TagihanListrik::with('warga');

        if ($periode) {
            $query->where('periode', $periode);
        }

        if ($rt) {
            $query->where('rt', (int) $rt);
        }

        if ($tab === 'belum_bayar') {
            $query->where('is_lunas', false);
        } elseif ($tab === 'lunas') {
            $query->where('is_lunas', true);
        }

        if ($statusBayar === 'lunas') {
            $query->where('is_lunas', true);
        } elseif ($statusBayar === 'belum_lunas') {
            $query->where('is_lunas', false);
        } elseif ($statusBayar === 'lebih') {
            $query->where('selisih_bayar', '>', 0);
        } elseif ($statusBayar === 'kurang') {
            $query->where('selisih_bayar', '<', 0);
        } elseif ($statusBayar === 'pas') {
            $query->where('selisih_bayar', 0);
        }

        if ($cari) {
            $query->where(function ($q) use ($cari) {
                $q->where('nama_pelanggan', 'like', "%{$cari}%")
                    ->orWhere('nama_warga', 'like', "%{$cari}%")
                    ->orWhere('nomor_struk', 'like', "%{$cari}%")
                    ->orWhere('kode_gabungan', 'like', "%{$cari}%");
            });
        }

        // Data rekapitulasi untuk KPI
        $summaryQuery = clone $query;
        $totalData = $summaryQuery->count();
        $totalTagihanAwal = (int) $summaryQuery->sum('tagihan_awal');
        $grandTotalTagihan = (int) $summaryQuery->sum('total_tagihan');

        // Kas Karang Taruna (masuk dari tagihan yang lunas)
        $totalKasKarangTaruna = (int) (clone $summaryQuery)->where('is_lunas', true)->sum('kas_karang_taruna');
        $totalKasDusun = (int) (clone $summaryQuery)->where('is_lunas', true)->sum('kas_dusun');

        $totalLunas = (int) (clone $summaryQuery)->where('is_lunas', true)->count();
        $totalBelumLunas = $totalData - $totalLunas;
        $totalLebih = (int) (clone $summaryQuery)->where('selisih_bayar', '>', 0)->sum('selisih_bayar');
        $totalKurang = (int) (clone $summaryQuery)->where('selisih_bayar', '<', 0)->sum('selisih_bayar');

        $daftarTagihan = $query->orderBy('is_lunas', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        // Daftar periode dan RT untuk filter dropdown
        $daftarPeriode = TagihanListrik::select('periode')->distinct()->orderBy('periode', 'desc')->pluck('periode');
        $daftarRt = Warga::select('rt')->distinct()->orderBy('rt', 'asc')->pluck('rt');

        // Master Warga untuk modal quick add jika diperlukan
        $daftarWarga = Warga::where('is_aktif', true)->orderBy('rt', 'asc')->orderBy('nama', 'asc')->get();

        return view('admin.tagihan-listrik.index', compact(
            'daftarTagihan',
            'daftarPeriode',
            'daftarRt',
            'daftarWarga',
            'periode',
            'rt',
            'statusBayar',
            'tab',
            'cari',
            'totalData',
            'totalTagihanAwal',
            'totalKasKarangTaruna',
            'totalKasDusun',
            'grandTotalTagihan',
            'totalLunas',
            'totalBelumLunas',
            'totalLebih',
            'totalKurang'
        ));
    }

    /**
     * Menampilkan form rekap tagihan listrik baru.
     */
    public function create(Request $request): View
    {
        $kodeGabungan = $request->query('kode_gabungan');
        $periodeDefault = $request->query('periode', TagihanListrik::latest()->value('periode') ?? now()->translatedFormat('F Y'));
        $rtDefault = $request->query('rt', TagihanListrik::latest()->value('rt') ?? 1);

        $daftarWarga = Warga::where('is_aktif', true)->orderBy('rt', 'asc')->orderBy('nama', 'asc')->get();

        $anggotaGabungan = [];
        if ($kodeGabungan) {
            $anggotaGabungan = TagihanListrik::where('kode_gabungan', $kodeGabungan)->get();
        }

        return view('admin.tagihan-listrik.create', compact('kodeGabungan', 'periodeDefault', 'rtDefault', 'daftarWarga', 'anggotaGabungan'));
    }

    /**
     * Menyimpan data rekap tagihan listrik ke database.
     */
    public function store(Request $request): RedirectResponse
    {
        // Mendukung input multi-item jika form dikirim sebagai kumpulan tagihan gabungan
        if ($request->has('items') && is_array($request->input('items'))) {
            $validated = $request->validate([
                'periode' => ['required', 'string', 'max:50'],
                'rt' => ['required', 'integer', 'min:1', 'max:50'],
                'kode_gabungan' => ['nullable', 'string', 'max:100'],
                'kategori_kas' => ['nullable', 'string', 'max:50'],
                'items' => ['required', 'array', 'min:1'],
                'items.*.warga_id' => ['nullable', 'integer'],
                'items.*.nama_warga' => ['nullable', 'string', 'max:255'],
                'items.*.nama_pelanggan' => ['required', 'string', 'max:255'],
                'items.*.nomor_struk' => ['required', 'string', 'max:100'],
                'items.*.tagihan_awal' => ['required', 'integer', 'min:0'],
                'items.*.uang_dibayar' => ['nullable', 'integer', 'min:0'],
                'items.*.is_lunas' => ['nullable', 'boolean'],
            ]);

            $kodeGabungan = ($validated['kode_gabungan'] ?? null) ?: (count($validated['items']) > 1 ? 'GAB-'.Str::upper(Str::random(8)) : null);
            $kategoriKas = $validated['kategori_kas'] ?? 'karang_taruna';

            foreach ($validated['items'] as $item) {
                $tagihanAwal = (int) $item['tagihan_awal'];
                $uangDibayar = (int) ($item['uang_dibayar'] ?? 0);
                $kasKarangTaruna = 3000;
                $kasDusun = 0;
                $totalTagihan = $tagihanAwal + $kasKarangTaruna + $kasDusun;
                $selisihBayar = $uangDibayar > 0 ? ($uangDibayar - $totalTagihan) : 0;
                $isLunas = ! empty($item['is_lunas']) || ($uangDibayar >= $totalTagihan && $uangDibayar > 0);

                // Jika warga_id ada, sinkronkan nama warga
                $warga = ! empty($item['warga_id']) ? Warga::find($item['warga_id']) : null;
                $namaWarga = $item['nama_warga'] ?? ($warga ? $warga->nama : null);

                TagihanListrik::create([
                    'warga_id' => $warga ? $warga->id : null,
                    'periode' => $validated['periode'],
                    'rt' => (int) $validated['rt'],
                    'nama_warga' => $namaWarga,
                    'nama_pelanggan' => $item['nama_pelanggan'],
                    'nomor_struk' => $item['nomor_struk'],
                    'tagihan_awal' => $tagihanAwal,
                    'kas_karang_taruna' => $kasKarangTaruna,
                    'kas_dusun' => $kasDusun,
                    'kategori_kas' => $kategoriKas,
                    'total_tagihan' => $totalTagihan,
                    'selisih_bayar' => $selisihBayar,
                    'is_lunas' => $isLunas,
                    'tanggal_bayar' => $isLunas ? now() : null,
                    'kode_gabungan' => $kodeGabungan,
                ]);
            }

            return redirect()->route('admin.tagihan-listrik.index')
                ->with('sukses', count($validated['items']).' data rekap tagihan listrik berhasil dicatat.');
        }

        // Form tunggal standar
        $validated = $request->validate([
            'warga_id' => ['nullable', 'integer'],
            'periode' => ['required', 'string', 'max:50'],
            'rt' => ['required', 'integer', 'min:1', 'max:50'],
            'nama_warga' => ['nullable', 'string', 'max:255'],
            'nama_pelanggan' => ['required', 'string', 'max:255'],
            'nomor_struk' => ['required', 'string', 'max:100'],
            'tagihan_awal' => ['required', 'integer', 'min:0'],
            'uang_dibayar' => ['nullable', 'integer', 'min:0'],
            'is_lunas' => ['nullable', 'boolean'],
            'kode_gabungan' => ['nullable', 'string', 'max:100'],
            'kategori_kas' => ['nullable', 'string', 'max:50'],
            'aksi_selanjutnya' => ['nullable', 'string'],
        ]);

        $tagihanAwal = (int) $validated['tagihan_awal'];
        $uangDibayar = (int) ($validated['uang_dibayar'] ?? 0);
        $kasKarangTaruna = 3000;
        $kasDusun = 0;
        $totalTagihan = $tagihanAwal + $kasKarangTaruna + $kasDusun;
        $selisihBayar = $uangDibayar > 0 ? ($uangDibayar - $totalTagihan) : 0;
        $isLunas = $request->has('is_lunas') ? $request->boolean('is_lunas') : ($uangDibayar >= $totalTagihan && $uangDibayar > 0);

        $warga = ! empty($validated['warga_id']) ? Warga::find($validated['warga_id']) : null;
        $namaWarga = $validated['nama_warga'] ?? ($warga ? $warga->nama : null);

        $tagihan = TagihanListrik::create([
            'warga_id' => $warga ? $warga->id : null,
            'periode' => $validated['periode'],
            'rt' => (int) $validated['rt'],
            'nama_warga' => $namaWarga,
            'nama_pelanggan' => $validated['nama_pelanggan'],
            'nomor_struk' => $validated['nomor_struk'],
            'tagihan_awal' => $tagihanAwal,
            'kas_karang_taruna' => $kasKarangTaruna,
            'kas_dusun' => $kasDusun,
            'kategori_kas' => $validated['kategori_kas'] ?? 'karang_taruna',
            'total_tagihan' => $totalTagihan,
            'selisih_bayar' => $selisihBayar,
            'is_lunas' => $isLunas,
            'tanggal_bayar' => $isLunas ? now() : null,
            'kode_gabungan' => ! empty($validated['kode_gabungan']) ? $validated['kode_gabungan'] : null,
        ]);

        // Jika admin memilih simpan dan lanjutkan input struk gabungan
        if ($request->input('aksi_selanjutnya') === 'lanjut_gabungan') {
            $kodeGabungan = $tagihan->kode_gabungan ?: 'GAB-'.Str::upper(Str::random(8));

            if (! $tagihan->kode_gabungan) {
                $tagihan->update(['kode_gabungan' => $kodeGabungan]);
            }

            return redirect()->route('admin.tagihan-listrik.create', [
                'kode_gabungan' => $kodeGabungan,
                'periode' => $tagihan->periode,
                'rt' => $tagihan->rt,
            ])->with('sukses', "Tagihan {$tagihan->nama_pelanggan} tersimpan. Silakan lanjutkan input nama berikutnya dalam struk gabungan ini.");
        }

        return redirect()->route('admin.tagihan-listrik.index')
            ->with('sukses', "Data rekap tagihan atas nama {$tagihan->nama_pelanggan} berhasil dicatat.");
    }

    /**
     * Menampilkan form edit tagihan.
     */
    public function edit(TagihanListrik $tagihanListrik): View
    {
        $uangDibayar = $tagihanListrik->total_tagihan + $tagihanListrik->selisih_bayar;
        $daftarWarga = Warga::where('is_aktif', true)->orderBy('rt', 'asc')->orderBy('nama', 'asc')->get();

        $anggotaGabungan = [];
        if ($tagihanListrik->kode_gabungan) {
            $anggotaGabungan = TagihanListrik::where('kode_gabungan', $tagihanListrik->kode_gabungan)
                ->where('id', '!=', $tagihanListrik->id)
                ->get();
        }

        return view('admin.tagihan-listrik.edit', compact('tagihanListrik', 'uangDibayar', 'daftarWarga', 'anggotaGabungan'));
    }

    /**
     * Memperbarui data rekap tagihan listrik.
     */
    public function update(Request $request, TagihanListrik $tagihanListrik): RedirectResponse
    {
        $validated = $request->validate([
            'warga_id' => ['nullable', 'integer'],
            'periode' => ['required', 'string', 'max:50'],
            'rt' => ['required', 'integer', 'min:1', 'max:50'],
            'nama_warga' => ['nullable', 'string', 'max:255'],
            'nama_pelanggan' => ['required', 'string', 'max:255'],
            'nomor_struk' => ['required', 'string', 'max:100'],
            'tagihan_awal' => ['required', 'integer', 'min:0'],
            'uang_dibayar' => ['nullable', 'integer', 'min:0'],
            'is_lunas' => ['nullable', 'boolean'],
            'kode_gabungan' => ['nullable', 'string', 'max:100'],
        ]);

        $tagihanAwal = (int) $validated['tagihan_awal'];
        $uangDibayar = (int) ($validated['uang_dibayar'] ?? 0);
        $kasKarangTaruna = 3000;
        $kasDusun = 0;
        $totalTagihan = $tagihanAwal + $kasKarangTaruna + $kasDusun;
        $selisihBayar = $uangDibayar > 0 ? ($uangDibayar - $totalTagihan) : $tagihanListrik->selisih_bayar;
        $isLunas = $request->has('is_lunas') ? $request->boolean('is_lunas') : ($uangDibayar >= $totalTagihan && $uangDibayar > 0);

        $warga = ! empty($validated['warga_id']) ? Warga::find($validated['warga_id']) : null;
        $namaWarga = $validated['nama_warga'] ?? ($warga ? $warga->nama : $tagihanListrik->nama_warga);

        $tagihanListrik->update([
            'warga_id' => $warga ? $warga->id : $tagihanListrik->warga_id,
            'periode' => $validated['periode'],
            'rt' => (int) $validated['rt'],
            'nama_warga' => $namaWarga,
            'nama_pelanggan' => $validated['nama_pelanggan'],
            'nomor_struk' => $validated['nomor_struk'],
            'tagihan_awal' => $tagihanAwal,
            'kas_karang_taruna' => $kasKarangTaruna,
            'kas_dusun' => $kasDusun,
            'total_tagihan' => $totalTagihan,
            'selisih_bayar' => $selisihBayar,
            'is_lunas' => $isLunas,
            'tanggal_bayar' => $isLunas ? ($tagihanListrik->tanggal_bayar ?? now()) : null,
            'kode_gabungan' => ! empty($validated['kode_gabungan']) ? $validated['kode_gabungan'] : null,
        ]);

        return redirect()->route('admin.tagihan-listrik.index')
            ->with('sukses', "Perubahan data tagihan {$tagihanListrik->nama_pelanggan} berhasil disimpan.");
    }

    /**
     * Memproses transaksi pembayaran (Kasir Pembayaran).
     * Menerima uang_dibayar, menghitung selisih_bayar, menandai lunas,
     * serta mendukung pembayaran gabungan beberapa warga dalam 1 struk.
     */
    public function prosesBayar(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tagihan_ids' => ['required', 'array', 'min:1'],
            'tagihan_ids.*' => ['required', 'integer', 'exists:tagihan_listrik,id'],
            'uang_dibayar' => ['required', 'integer', 'min:0'],
            'tanggal_bayar' => ['nullable', 'date'],
            'kode_gabungan' => ['nullable', 'string', 'max:100'],
        ]);

        $tagihanList = TagihanListrik::whereIn('id', $validated['tagihan_ids'])->get();
        $totalHarusDibayar = (int) $tagihanList->sum('total_tagihan');
        $uangDibayar = (int) $validated['uang_dibayar'];
        $selisihTotal = $uangDibayar - $totalHarusDibayar;
        $tanggalBayar = $validated['tanggal_bayar'] ?? now()->toDateString();

        $isMulti = count($tagihanList) > 1;
        $kodeGabungan = ! empty($validated['kode_gabungan'])
            ? $validated['kode_gabungan']
            : ($isMulti ? 'GAB-'.Str::upper(Str::random(8)) : null);

        // Jika single item, selisih disimpan di item tersebut
        if (! $isMulti) {
            $tagihan = $tagihanList->first();
            $tagihan->update([
                'is_lunas' => true,
                'tanggal_bayar' => $tanggalBayar,
                'selisih_bayar' => $selisihTotal,
                'kode_gabungan' => $kodeGabungan ?: $tagihan->kode_gabungan,
            ]);

            return back()->with('sukses', "Pembayaran tagihan {$tagihan->nama_pelanggan} berhasil dicatat lunas.");
        }

        // Jika multi item (struk gabungan), selisih total dialokasikan ke baris pertama struk gabungan
        foreach ($tagihanList as $idx => $tagihan) {
            $tagihan->update([
                'is_lunas' => true,
                'tanggal_bayar' => $tanggalBayar,
                'selisih_bayar' => $idx === 0 ? $selisihTotal : 0,
                'kode_gabungan' => $kodeGabungan,
            ]);
        }

        return back()->with('sukses', count($tagihanList)." tagihan berhasil dibayarkan secara gabungan (Kode Struk: {$kodeGabungan}).");
    }

    /**
     * Menghapus catatan tagihan listrik.
     */
    public function destroy(TagihanListrik $tagihanListrik): RedirectResponse
    {
        $nama = $tagihanListrik->nama_pelanggan;
        $tagihanListrik->delete();

        return redirect()->route('admin.tagihan-listrik.index')
            ->with('sukses', "Catatan tagihan listrik atas nama {$nama} telah dihapus.");
    }

    /**
     * Toggle status lunas tagihan listrik dengan cepat.
     */
    public function toggleLunas(TagihanListrik $tagihanListrik): RedirectResponse
    {
        $tagihanListrik->update([
            'is_lunas' => ! $tagihanListrik->is_lunas,
            'tanggal_bayar' => ! $tagihanListrik->is_lunas ? now() : null,
        ]);

        $statusTeks = $tagihanListrik->is_lunas ? 'Lunas' : 'Belum Lunas';

        return back()->with('sukses', "Status pembayaran {$tagihanListrik->nama_pelanggan} diperbarui menjadi: {$statusTeks}.");
    }
}
