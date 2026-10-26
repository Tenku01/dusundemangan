<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Keuangan;
use App\Models\Peminjaman;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KatalogController extends Controller
{
    /**
     * Menampilkan beranda publik dengan katalog barang dan transparansi kas.
     */
    public function index(Request $request): View
    {
        $kategoriTerpilih = $request->query('kategori');
        $cari = $request->query('cari');

        $queryBarang = Barang::query();

        if ($kategoriTerpilih && $kategoriTerpilih !== 'Semua') {
            $queryBarang->where('kategori', $kategoriTerpilih);
        }

        if ($cari) {
            $queryBarang->where(function ($q) use ($cari) {
                $q->where('nama_barang', 'like', "%{$cari}%")
                    ->orWhere('kode_barang', 'like', "%{$cari}%")
                    ->orWhere('keterangan', 'like', "%{$cari}%");
            });
        }

        $daftarBarang = $queryBarang->orderBy('nama_barang')->paginate(12)->withQueryString();
        $kategoriList = Barang::select('kategori')->distinct()->pluck('kategori');

        // Ringkasan Keuangan Publik
        $totalMasuk = Keuangan::where('jenis', 'masuk')->sum('nominal');
        $totalKeluar = Keuangan::where('jenis', 'keluar')->sum('nominal');
        $saldoKasSaatIni = $totalMasuk - $totalKeluar;

        // Riwayat Mutasi Kas Publik Terakhir (10 data)
        $mutasiTerakhir = Keuangan::orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->take(10)
            ->get();

        // Statistik Cepat Inventaris
        $totalJenisBarang = Barang::count();
        $totalUnitTersedia = Barang::sum('jumlah_tersedia');

        return view('publik.beranda', compact(
            'daftarBarang',
            'kategoriList',
            'kategoriTerpilih',
            'cari',
            'saldoKasSaatIni',
            'totalMasuk',
            'totalKeluar',
            'mutasiTerakhir',
            'totalJenisBarang',
            'totalUnitTersedia'
        ));
    }

    /**
     * Memproses pengajuan peminjaman barang oleh warga tanpa wajib login.
     */
    public function ajukanPinjam(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'barang_id' => 'required|exists:barang,id',
            'nama_peminjam' => 'required|string|max:150',
            'kontak_peminjam' => 'required|string|max:30',
            'alamat_rt_rw' => 'required|string|max:100',
            'jumlah_pinjam' => 'required|integer|min:1',
            'tanggal_pinjam' => 'required|date|after_or_equal:today',
            'rencana_tanggal_kembali' => 'required|date|after_or_equal:tanggal_pinjam',
            'tujuan_peminjaman' => 'required|string|max:1000',
        ], [
            'barang_id.required' => 'Pilih barang yang ingin dipinjam.',
            'nama_peminjam.required' => 'Nama lengkap peminjam wajib diisi.',
            'kontak_peminjam.required' => 'Nomor WhatsApp atau HP aktif wajib diisi.',
            'alamat_rt_rw.required' => 'Alamat RT/RW tempat tinggal wajib diisi.',
            'jumlah_pinjam.min' => 'Jumlah pinjam minimal 1 unit.',
            'tanggal_pinjam.after_or_equal' => 'Tanggal pinjam tidak boleh sebelum hari ini.',
            'rencana_tanggal_kembali.after_or_equal' => 'Tanggal kembali harus sama atau setelah tanggal pinjam.',
            'tujuan_peminjaman.required' => 'Keperluan atau tujuan peminjaman wajib diisi.',
        ]);

        $barang = Barang::findOrFail($validated['barang_id']);

        if ($barang->jumlah_tersedia < $validated['jumlah_pinjam']) {
            return back()->withInput()->withErrors([
                'jumlah_pinjam' => "Stok barang saat ini tidak mencukupi. Tersedia hanya {$barang->jumlah_tersedia} unit.",
            ]);
        }

        // Generate Kode Tiket Pinjam Unik
        $awalan = 'PJ-'.date('ym');
        $urutan = Peminjaman::whereDate('created_at', today())->count() + 1;
        $kodePinjam = $awalan.'-'.str_pad((string) $urutan, 3, '0', STR_PAD_LEFT);

        while (Peminjaman::where('kode_pinjam', $kodePinjam)->exists()) {
            $urutan++;
            $kodePinjam = $awalan.'-'.str_pad((string) $urutan, 3, '0', STR_PAD_LEFT);
        }

        $peminjaman = Peminjaman::create([
            'kode_pinjam' => $kodePinjam,
            'barang_id' => $barang->id,
            'user_id' => auth()->id(), // jika ada user login, otomatis terhubung
            'nama_peminjam' => $validated['nama_peminjam'],
            'kontak_peminjam' => $validated['kontak_peminjam'],
            'alamat_rt_rw' => $validated['alamat_rt_rw'],
            'jumlah_pinjam' => $validated['jumlah_pinjam'],
            'tanggal_pinjam' => $validated['tanggal_pinjam'],
            'rencana_tanggal_kembali' => $validated['rencana_tanggal_kembali'],
            'tujuan_peminjaman' => $validated['tujuan_peminjaman'],
            'status' => 'menunggu',
        ]);

        // Buat teks pesan WhatsApp siap kirim
        $pesanWa = rawurlencode(
            "Halo Pengurus Karang Taruna Demangan,\n\n".
            "Saya telah mengajukan peminjaman inventaris via web:\n".
            "• Kode Resi: {$peminjaman->kode_pinjam}\n".
            "• Peminjam: {$peminjaman->nama_peminjam} ({$peminjaman->alamat_rt_rw})\n".
            "• Barang: {$barang->nama_barang} ({$peminjaman->jumlah_pinjam} unit)\n".
            "• Tgl Pinjam: {$peminjaman->tanggal_pinjam->format('d/m/Y')} s/d {$peminjaman->rencana_tanggal_kembali->format('d/m/Y')}\n".
            "• Keperluan: {$peminjaman->tujuan_peminjaman}\n\n".
            'Mohon konfirmasi persetujuannya. Terima kasih.'
        );

        return redirect()->route('peminjaman.sukses', ['kode' => $kodePinjam])
            ->with('pesan_wa', $pesanWa)
            ->with('sukses', 'Permohonan pinjaman berhasil dikirim!');
    }

    /**
     * Menampilkan halaman tanda bukti / struk tiket peminjaman setelah berhasil mengajukan.
     */
    public function suksesPinjam(string $kode): View
    {
        $peminjaman = Peminjaman::with('barang')
            ->where('kode_pinjam', $kode)
            ->firstOrFail();

        $pesanWa = rawurlencode(
            "Halo Pengurus Karang Taruna Demangan,\n\n".
            "Saya mengajukan peminjaman inventaris via web:\n".
            "• Kode Resi: {$peminjaman->kode_pinjam}\n".
            "• Peminjam: {$peminjaman->nama_peminjam} ({$peminjaman->alamat_rt_rw})\n".
            "• Barang: {$peminjaman->barang->nama_barang} ({$peminjaman->jumlah_pinjam} unit)\n".
            "• Tgl Pinjam: {$peminjaman->tanggal_pinjam->format('d/m/Y')} s/d {$peminjaman->rencana_tanggal_kembali->format('d/m/Y')}\n".
            "• Keperluan: {$peminjaman->tujuan_peminjaman}\n\n".
            'Mohon bantuannya untuk persetujuan. Terima kasih.'
        );

        return view('publik.sukses_pinjam', compact('peminjaman', 'pesanWa'));
    }

    /**
     * Halaman pelacakan status peminjaman untuk warga.
     */
    public function cekStatus(Request $request): View
    {
        $kataKunci = trim((string) $request->query('q'));
        $hasil = collect();

        if ($kataKunci !== '') {
            $hasil = Peminjaman::with('barang')
                ->where('kode_pinjam', 'like', "%{$kataKunci}%")
                ->orWhere('kontak_peminjam', 'like', "%{$kataKunci}%")
                ->orWhere('nama_peminjam', 'like', "%{$kataKunci}%")
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('publik.cek_status', compact('hasil', 'kataKunci'));
    }

    /**
     * Halaman transparansi penuh buku kas umum dusun untuk publik.
     */
    public function bukuKas(Request $request): View
    {
        $jenis = $request->query('jenis');
        $bulan = $request->query('bulan');
        $tahun = $request->query('tahun', date('Y'));

        $query = Keuangan::query();

        if ($jenis && in_array($jenis, ['masuk', 'keluar'], true)) {
            $query->where('jenis', $jenis);
        }

        if ($bulan) {
            $query->whereMonth('tanggal', $bulan);
        }

        if ($tahun) {
            $query->whereYear('tanggal', $tahun);
        }

        $transaksi = $query->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(20)
            ->withQueryString();

        $totalMasuk = Keuangan::where('jenis', 'masuk')->sum('nominal');
        $totalKeluar = Keuangan::where('jenis', 'keluar')->sum('nominal');
        $saldoKasSaatIni = $totalMasuk - $totalKeluar;

        return view('publik.buku_kas', compact(
            'transaksi',
            'totalMasuk',
            'totalKeluar',
            'saldoKasSaatIni',
            'jenis',
            'bulan',
            'tahun'
        ));
    }
}
