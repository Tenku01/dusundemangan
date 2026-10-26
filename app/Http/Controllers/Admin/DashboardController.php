<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Keuangan;
use App\Models\Peminjaman;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard utama pengurus dan superadmin.
     */
    public function index(): View
    {
        $totalJenisBarang = Barang::count();
        $totalUnitAset = Barang::sum('jumlah_total');
        $totalUnitTersedia = Barang::sum('jumlah_tersedia');

        $pinjamanMenunggu = Peminjaman::where('status', 'menunggu')->count();
        $pinjamanAktif = Peminjaman::where('status', 'disetujui')->count();

        // Keuangan
        $totalMasuk = Keuangan::where('jenis', 'masuk')->sum('nominal');
        $totalKeluar = Keuangan::where('jenis', 'keluar')->sum('nominal');
        $saldoKasSaatIni = $totalMasuk - $totalKeluar;

        $pemasukanBulanIni = Keuangan::where('jenis', 'masuk')
            ->whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->sum('nominal');

        $pengeluaranBulanIni = Keuangan::where('jenis', 'keluar')
            ->whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->sum('nominal');

        // Antrean Pinjaman yang Butuh Tindakan Segera
        $antreanTerbaru = Peminjaman::with('barang')
            ->where('status', 'menunggu')
            ->orderBy('created_at', 'asc')
            ->take(5)
            ->get();

        // Riwayat Mutasi Kas Terkini
        $kasTerbaru = Keuangan::orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalJenisBarang',
            'totalUnitAset',
            'totalUnitTersedia',
            'pinjamanMenunggu',
            'pinjamanAktif',
            'saldoKasSaatIni',
            'pemasukanBulanIni',
            'pengeluaranBulanIni',
            'antreanTerbaru',
            'kasTerbaru'
        ));
    }
}
