<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Keuangan;
use App\Models\Peminjaman;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard utama pengurus dan superadmin.
     */
    public function index(): View|RedirectResponse
    {
        if (! auth()->user()->hasMenuAkses('beranda')) {
            return redirect()->route(auth()->user()->getFirstAccessibleRoute());
        }
        // Statistik Barang Inventaris (1 Query Konsolidasi)
        $barangStats = Barang::selectRaw('
            COUNT(*) as total_jenis,
            COALESCE(SUM(jumlah_total), 0) as total_aset,
            COALESCE(SUM(jumlah_tersedia), 0) as total_tersedia
        ')->first();
        $totalJenisBarang = (int) ($barangStats->total_jenis ?? 0);
        $totalUnitAset = (int) ($barangStats->total_aset ?? 0);
        $totalUnitTersedia = (int) ($barangStats->total_tersedia ?? 0);

        // Statistik Pinjaman (1 Query Konsolidasi)
        $pinjamanStats = Peminjaman::selectRaw("
            COUNT(CASE WHEN status = 'menunggu' THEN 1 END) as menunggu,
            COUNT(CASE WHEN status = 'disetujui' THEN 1 END) as aktif
        ")->first();
        $pinjamanMenunggu = (int) ($pinjamanStats->menunggu ?? 0);
        $pinjamanAktif = (int) ($pinjamanStats->aktif ?? 0);

        // Statistik Keuangan & Kas (1 Query Konsolidasi)
        $currentMonth = (int) now()->month;
        $currentYear = (int) now()->year;

        $keuanganStats = Keuangan::selectRaw("
            COALESCE(SUM(CASE WHEN jenis = 'masuk' THEN nominal ELSE 0 END), 0) as total_masuk,
            COALESCE(SUM(CASE WHEN jenis = 'keluar' THEN nominal ELSE 0 END), 0) as total_keluar,
            COALESCE(SUM(CASE WHEN jenis = 'masuk' AND MONTH(tanggal) = ? AND YEAR(tanggal) = ? THEN nominal ELSE 0 END), 0) as masuk_bulan_ini,
            COALESCE(SUM(CASE WHEN jenis = 'keluar' AND MONTH(tanggal) = ? AND YEAR(tanggal) = ? THEN nominal ELSE 0 END), 0) as keluar_bulan_ini
        ", [$currentMonth, $currentYear, $currentMonth, $currentYear])->first();

        $totalMasuk = (int) ($keuanganStats->total_masuk ?? 0);
        $totalKeluar = (int) ($keuanganStats->total_keluar ?? 0);
        $saldoKasSaatIni = $totalMasuk - $totalKeluar;
        $pemasukanBulanIni = (int) ($keuanganStats->masuk_bulan_ini ?? 0);
        $pengeluaranBulanIni = (int) ($keuanganStats->keluar_bulan_ini ?? 0);

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
