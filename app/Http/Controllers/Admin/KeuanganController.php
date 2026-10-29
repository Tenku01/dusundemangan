<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Keuangan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class KeuanganController extends Controller
{
    /**
     * Menampilkan buku kas utama pengurus.
     */
    public function index(Request $request): View
    {
        $jenis = $request->query('jenis');
        $kategori = $request->query('kategori');
        $bulan = $request->query('bulan');
        $tahun = $request->query('tahun', date('Y'));
        $cari = $request->query('cari');

        $query = Keuangan::with('pencatat');

        if ($jenis && in_array($jenis, ['masuk', 'keluar'], true)) {
            $query->where('jenis', $jenis);
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
                    ->orWhere('kategori', 'like', "%{$cari}%")
                    ->orWhere('keterangan', 'like', "%{$cari}%");
            });
        }

        $daftarKas = $query->orderBy('tanggal', 'desc')->orderBy('id', 'desc')->paginate(15)->withQueryString();

        // Rekapitulasi Kas (1 Query Konsolidasi)
        $keuanganStats = Keuangan::selectRaw("
            COALESCE(SUM(CASE WHEN jenis = 'masuk' THEN nominal ELSE 0 END), 0) as total_masuk,
            COALESCE(SUM(CASE WHEN jenis = 'keluar' THEN nominal ELSE 0 END), 0) as total_keluar
        ")->first();

        $totalMasuk = (int) ($keuanganStats->total_masuk ?? 0);
        $totalKeluar = (int) ($keuanganStats->total_keluar ?? 0);
        $saldoKasSaatIni = $totalMasuk - $totalKeluar;

        $kategoriList = Keuangan::select('kategori')->distinct()->pluck('kategori');

        return view('admin.keuangan.index', compact(
            'daftarKas',
            'totalMasuk',
            'totalKeluar',
            'saldoKasSaatIni',
            'jenis',
            'kategori',
            'bulan',
            'tahun',
            'cari',
            'kategoriList'
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
            'kategori' => $validated['kategori'],
            'keterangan' => $validated['keterangan'],
            'lampiran' => $lampiranPath,
            'dicatat_oleh' => auth()->id(),
        ]);

        $pesan = $validated['jenis'] === 'masuk'
            ? 'Kas masuk berhasil dicatat. Saldo kas bertambah!'
            : 'Kas keluar berhasil dicatat. Saldo kas berkurang!';

        return redirect()->route('admin.keuangan.index')->with('sukses', $pesan);
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
