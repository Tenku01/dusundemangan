<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Keuangan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class KeuanganController extends Controller
{
    /**
     * Menampilkan buku kas utama pengurus.
     */
    public function index(Request $request): View
    {
        $jenis = $request->query('jenis');
        $kategoriKas = $request->query('kategori_kas');
        $kategori = $request->query('kategori');
        $bulan = $request->query('bulan');
        $tahun = $request->query('tahun', date('Y'));
        $cari = $request->query('cari');

        $query = Keuangan::with('pencatat');

        if ($jenis && in_array($jenis, ['masuk', 'keluar'], true)) {
            $query->where('jenis', $jenis);
        }

        if ($kategoriKas) {
            $query->where('kategori_kas', $kategoriKas);
        }

        if ($kategori) {
            $query->where('kategori', $kategori);
        }

        if ($bulan) {
            $query->whereMonth('tanggal', $bulan);
        }

        if ($tahun) {
            $query->whereYear('tanggal', $tahun);
        }

        if ($cari) {
            $query->where(function ($q) use ($cari) {
                $q->where('kode_transaksi', 'like', "%{$cari}%")
                    ->orWhere('kategori_kas', 'like', "%{$cari}%")
                    ->orWhere('kategori', 'like', "%{$cari}%")
                    ->orWhere('keterangan', 'like', "%{$cari}%");
            });
        }

        $daftarKas = $query->orderBy('tanggal', 'desc')->orderBy('id', 'desc')->paginate(15)->withQueryString();

        // Rekapitulasi Kas Keseluruhan
        $keuanganStats = Keuangan::selectRaw("
            COALESCE(SUM(CASE WHEN jenis = 'masuk' THEN nominal ELSE 0 END), 0) as total_masuk,
            COALESCE(SUM(CASE WHEN jenis = 'keluar' THEN nominal ELSE 0 END), 0) as total_keluar
        ")->first();

        $totalMasuk = (int) ($keuanganStats->total_masuk ?? 0);
        $totalKeluar = (int) ($keuanganStats->total_keluar ?? 0);
        $saldoKasSaatIni = $totalMasuk - $totalKeluar;

        // Saldo Terpisah: Kas Karang Taruna
        $statsKartar = Keuangan::where('kategori_kas', Keuangan::KAS_KARANG_TARUNA)->selectRaw("
            COALESCE(SUM(CASE WHEN jenis = 'masuk' THEN nominal ELSE 0 END), 0) as masuk,
            COALESCE(SUM(CASE WHEN jenis = 'keluar' THEN nominal ELSE 0 END), 0) as keluar
        ")->first();
        $saldoKartar = (int) (($statsKartar->masuk ?? 0) - ($statsKartar->keluar ?? 0));

        // Saldo Terpisah: Kas Listrik (yang masih ditahan sebelum disetor ke Kas Karang Taruna)
        $statsListrik = Keuangan::where('kategori_kas', Keuangan::KAS_LISTRIK)->selectRaw("
            COALESCE(SUM(CASE WHEN jenis = 'masuk' THEN nominal ELSE 0 END), 0) as masuk,
            COALESCE(SUM(CASE WHEN jenis = 'keluar' THEN nominal ELSE 0 END), 0) as keluar
        ")->first();
        $saldoListrik = (int) (($statsListrik->masuk ?? 0) - ($statsListrik->keluar ?? 0));

        $kategoriList = Keuangan::select('kategori')->distinct()->pluck('kategori');

        // Daftar Kategori Kas Dinamis (Bisa di-inputkan sendiri oleh pengguna)
        $kategoriKasExisting = Keuangan::select('kategori_kas')->distinct()->pluck('kategori_kas')->toArray();
        $daftarKategoriKas = array_values(array_unique(array_filter(array_merge(
            [Keuangan::KAS_KARANG_TARUNA, Keuangan::KAS_LISTRIK],
            $kategoriKasExisting
        ))));

        return view('admin.keuangan.index', compact(
            'daftarKas',
            'totalMasuk',
            'totalKeluar',
            'saldoKasSaatIni',
            'saldoKartar',
            'saldoListrik',
            'jenis',
            'kategoriKas',
            'kategori',
            'bulan',
            'tahun',
            'cari',
            'kategoriList',
            'daftarKategoriKas'
        ));
    }

    /**
     * Mencatat transaksi kas masuk atau keluar.
     */
    public function store(Request $request): RedirectResponse
    {
        if ($request->has('nominal') && is_string($request->input('nominal'))) {
            $request->merge([
                'nominal' => (float) str_replace(['.', ' '], '', str_replace(',', '.', $request->input('nominal'))),
            ]);
        }

        $validated = $request->validate([
            'tanggal' => 'required|date',
            'jenis' => 'required|in:masuk,keluar',
            'nominal' => 'required|numeric|min:1',
            'kategori_kas' => 'nullable|string|max:100',
            'kategori' => 'required|string|max:100',
            'keterangan' => 'required|string|max:1000',
            'lampiran' => 'nullable|file|mimes:jpeg,png,jpg,pdf,webp|max:3072',
        ], [
            'tanggal.required' => 'Tanggal transaksi wajib diisi.',
            'nominal.min' => 'Nominal transaksi minimal Rp 1.',
            'kategori.required' => 'Kategori atau pos anggaran wajib diisi.',
            'keterangan.required' => 'Uraian atau keterangan transaksi wajib diisi.',
            'lampiran.max' => 'Ukuran file lampiran maksimal 3MB.',
        ]);

        $kategoriKas = ! empty($validated['kategori_kas']) ? trim($validated['kategori_kas']) : Keuangan::KAS_KARANG_TARUNA;

        // Generate Kode Transaksi Otomatis
        $awalan = 'KAS-'.date('ym');
        $nomorUrut = Keuangan::whereDate('created_at', today())->count() + 1;
        $kodeTransaksi = $awalan.'-'.str_pad((string) $nomorUrut, 3, '0', STR_PAD_LEFT);

        while (Keuangan::where('kode_transaksi', $kodeTransaksi)->exists()) {
            $nomorUrut++;
            $kodeTransaksi = $awalan.'-'.str_pad((string) $nomorUrut, 3, '0', STR_PAD_LEFT);
        }

        $lampiranPath = null;
        if ($request->hasFile('lampiran')) {
            $lampiranPath = $request->file('lampiran')->store('lampiran_kas', 'public');
        }

        Keuangan::create([
            'kode_transaksi' => $kodeTransaksi,
            'tanggal' => $validated['tanggal'],
            'jenis' => $validated['jenis'],
            'nominal' => $validated['nominal'],
            'kategori_kas' => $kategoriKas,
            'kategori' => $validated['kategori'],
            'keterangan' => $validated['keterangan'],
            'lampiran' => $lampiranPath,
            'dicatat_oleh' => auth()->id(),
        ]);

        $pesan = $validated['jenis'] === 'masuk'
            ? "Kas masuk berhasil dicatat ke {$kategoriKas}. Saldo bertambah!"
            : "Kas keluar berhasil dicatat dari {$kategoriKas}. Saldo berkurang!";

        return redirect()->route('admin.keuangan.index')->with('sukses', $pesan);
    }

    /**
     * Menyetorkan saldo kas listrik yang ditahan ke Kas Karang Taruna.
     */
    public function setorKeKasKarangTaruna(Request $request): RedirectResponse
    {
        if ($request->has('nominal') && is_string($request->input('nominal'))) {
            $request->merge([
                'nominal' => (float) str_replace(['.', ' '], '', str_replace(',', '.', $request->input('nominal'))),
            ]);
        }

        $validated = $request->validate([
            'nominal' => 'required|numeric|min:1',
            'tanggal' => 'nullable|date',
            'keterangan' => 'nullable|string|max:500',
        ], [
            'nominal.required' => 'Nominal setoran wajib diisi.',
            'nominal.min' => 'Nominal setoran minimal Rp 1.',
        ]);

        // Cek saldo Kas Listrik saat ini
        $statsListrik = Keuangan::where('kategori_kas', Keuangan::KAS_LISTRIK)->selectRaw("
            COALESCE(SUM(CASE WHEN jenis = 'masuk' THEN nominal ELSE 0 END), 0) as masuk,
            COALESCE(SUM(CASE WHEN jenis = 'keluar' THEN nominal ELSE 0 END), 0) as keluar
        ")->first();
        $saldoListrik = (int) (($statsListrik->masuk ?? 0) - ($statsListrik->keluar ?? 0));

        $nominalSetor = (float) $validated['nominal'];
        if ($nominalSetor > $saldoListrik) {
            return back()->withErrors([
                'nominal' => 'Saldo Kas Listrik saat ini hanya Rp '.number_format($saldoListrik, 0, ',', '.').'. Tidak mencukupi untuk disetor sebesar Rp '.number_format($nominalSetor, 0, ',', '.').'.',
            ]);
        }

        $tanggal = $validated['tanggal'] ?? now()->toDateString();
        $catatanTambahan = ! empty($validated['keterangan']) ? ' ('.$validated['keterangan'].')' : '';

        DB::transaction(function () use ($nominalSetor, $tanggal, $catatanTambahan) {
            $randomSuffix = Str::upper(Str::random(4));

            // 1. Catat KAS KELUAR dari Kas Listrik
            $kodeKeluar = 'KAS-LST-OUT-'.date('ymd').'-'.$randomSuffix;
            Keuangan::create([
                'kode_transaksi' => $kodeKeluar,
                'tanggal' => $tanggal,
                'jenis' => 'keluar',
                'nominal' => $nominalSetor,
                'kategori_kas' => Keuangan::KAS_LISTRIK,
                'kategori' => 'Setor ke Kas Karang Taruna',
                'keterangan' => "Penyetoran dana Kas Listrik ke Kas Karang Taruna{$catatanTambahan}",
                'dicatat_oleh' => auth()->id(),
            ]);

            // 2. Catat KAS MASUK ke Kas Karang Taruna
            $kodeMasuk = 'KAS-KARTAR-IN-'.date('ymd').'-'.$randomSuffix;
            Keuangan::create([
                'kode_transaksi' => $kodeMasuk,
                'tanggal' => $tanggal,
                'jenis' => 'masuk',
                'nominal' => $nominalSetor,
                'kategori_kas' => Keuangan::KAS_KARANG_TARUNA,
                'kategori' => 'Penerimaan Kas Listrik',
                'keterangan' => "Penerimaan setoran dana dari Kas Listrik{$catatanTambahan}",
                'dicatat_oleh' => auth()->id(),
            ]);
        });

        return redirect()->route('admin.keuangan.index')->with('sukses', 'Berhasil menyetor Rp '.number_format($nominalSetor, 0, ',', '.').' dari Kas Listrik ke Kas Karang Taruna!');
    }

    /**
     * Menampilkan laporan ringkas keuangan berdasarkan rentang bulan / tahun.
     */
    public function laporan(Request $request): View
    {
        $bulanMulai = $request->query('bulan_mulai', 1);
        $bulanSelesai = $request->query('bulan_selesai', 12);
        $tahun = $request->query('tahun', date('Y'));

        $tanggalAwal = sprintf('%04d-%02d-01', $tahun, $bulanMulai);
        $hariTerakhirBulanSelesai = date('t', strtotime(sprintf('%04d-%02d-01', $tahun, $bulanSelesai)));
        $tanggalAkhir = sprintf('%04d-%02d-%02d', $tahun, $bulanSelesai, $hariTerakhirBulanSelesai);

        $query = Keuangan::whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir]);

        $transaksi = (clone $query)->orderBy('tanggal', 'asc')->get();

        $totalMasuk = (clone $query)->where('jenis', 'masuk')->sum('nominal');
        $totalKeluar = (clone $query)->where('jenis', 'keluar')->sum('nominal');
        $surplusDefisit = $totalMasuk - $totalKeluar;

        // Rincian per kategori
        $rekapKategoriMasuk = (clone $query)->where('jenis', 'masuk')
            ->selectRaw('kategori, sum(nominal) as total')
            ->groupBy('kategori')
            ->get();

        $rekapKategoriKeluar = (clone $query)->where('jenis', 'keluar')
            ->selectRaw('kategori, sum(nominal) as total')
            ->groupBy('kategori')
            ->get();

        return view('admin.keuangan.laporan', compact(
            'transaksi',
            'totalMasuk',
            'totalKeluar',
            'surplusDefisit',
            'rekapKategoriMasuk',
            'rekapKategoriKeluar',
            'bulanMulai',
            'bulanSelesai',
            'tahun',
            'tanggalAwal',
            'tanggalAkhir'
        ));
    }

    /**
     * Menghapus catatan kas (jika terdapat kesalahan input).
     */
    public function destroy(Keuangan $keuangan): RedirectResponse
    {
        if ($keuangan->lampiran) {
            Storage::disk('public')->delete($keuangan->lampiran);
        }

        $kode = $keuangan->kode_transaksi;
        $keuangan->delete();

        return redirect()->route('admin.keuangan.index')->with('sukses', "Catatan kas {$kode} berhasil dihapus.");
    }
}
