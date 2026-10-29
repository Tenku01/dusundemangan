<?php

use App\Http\Controllers\Admin\BarangController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\KeuanganController;
use App\Http\Controllers\Admin\PembayaranListrikController;
use App\Http\Controllers\Admin\PeminjamanController;
use App\Http\Controllers\Admin\PenggunaController;
use App\Http\Controllers\Admin\RekapListrikController;
use App\Http\Controllers\Admin\WargaController;
use App\Http\Controllers\KatalogController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rute Publik (Warga & Umum Tanpa Wajib Login)
|--------------------------------------------------------------------------
*/
Route::get('/', [KatalogController::class, 'index'])->name('beranda');
Route::post('/peminjaman/ajukan', [KatalogController::class, 'ajukanPinjam'])->name('peminjaman.ajukan');
Route::get('/peminjaman/sukses/{kode}', [KatalogController::class, 'suksesPinjam'])->name('peminjaman.sukses');
Route::get('/cek-status', [KatalogController::class, 'cekStatus'])->name('peminjaman.cek-status');
Route::get('/transparansi-kas', [KatalogController::class, 'bukuKas'])->name('publik.buku-kas');

/*
|--------------------------------------------------------------------------
| Rute Pengurus (Admin & Superadmin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'peran:admin,superadmin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Manajemen Inventaris Barang
    Route::get('/barang', [BarangController::class, 'index'])->name('barang.index');
    Route::post('/barang', [BarangController::class, 'store'])->name('barang.store');
    Route::put('/barang/{barang}', [BarangController::class, 'update'])->name('barang.update');
    Route::delete('/barang/{barang}', [BarangController::class, 'destroy'])->name('barang.destroy');

    // Manajemen Approval Peminjaman
    Route::get('/peminjaman', [PeminjamanController::class, 'index'])->name('peminjaman.index');
    Route::patch('/peminjaman/{peminjaman}/setujui', [PeminjamanController::class, 'setujui'])->name('peminjaman.setujui');
    Route::patch('/peminjaman/{peminjaman}/tolak', [PeminjamanController::class, 'tolak'])->name('peminjaman.tolak');
    Route::patch('/peminjaman/{peminjaman}/kembalikan', [PeminjamanController::class, 'kembalikan'])->name('peminjaman.kembalikan');

    // Manajemen Buku Kas & Laporan Keuangan
    Route::get('/keuangan', [KeuanganController::class, 'index'])->name('keuangan.index');
    Route::post('/keuangan', [KeuanganController::class, 'store'])->name('keuangan.store');
    Route::get('/keuangan/laporan', [KeuanganController::class, 'laporan'])->name('keuangan.laporan');
    Route::delete('/keuangan/{keuangan}', [KeuanganController::class, 'destroy'])->name('keuangan.destroy');

    // Master Data Warga Pelanggan Listrik
    Route::get('/warga/search-json', [WargaController::class, 'searchJson'])->name('warga.search-json');
    Route::resource('warga', WargaController::class)->except(['create', 'show', 'edit']);

    // Rekapitulasi Tagihan Listrik Warga per Periode & RT
    Route::post('/rekap-listrik/batch-store', [RekapListrikController::class, 'batchStore'])->name('rekap-listrik.batch-store');
    Route::post('/rekap-listrik/duplikasi-siklus', [RekapListrikController::class, 'duplikasiSiklus'])->name('rekap-listrik.duplikasi-siklus');
    Route::post('/rekap-listrik/gabungkan', [RekapListrikController::class, 'gabungkan'])->name('rekap-listrik.gabungkan');
    Route::post('/rekap-listrik/pisahkan', [RekapListrikController::class, 'pisahkan'])->name('rekap-listrik.pisahkan');
    Route::resource('rekap-listrik', RekapListrikController::class)
        ->parameters(['rekap-listrik' => 'tagihanListrik'])
        ->except(['show']);

    // Kasir Pembayaran Listrik Warga
    Route::get('/pembayaran-listrik', [PembayaranListrikController::class, 'index'])->name('pembayaran-listrik.index');
    Route::post('/pembayaran-listrik/bayar', [PembayaranListrikController::class, 'bayar'])->name('pembayaran-listrik.bayar');
    Route::post('/pembayaran-listrik/gabungkan', [PembayaranListrikController::class, 'gabungkan'])->name('pembayaran-listrik.gabungkan');
    Route::post('/pembayaran-listrik/pisahkan', [PembayaranListrikController::class, 'pisahkan'])->name('pembayaran-listrik.pisahkan');
    Route::patch('/pembayaran-listrik/{tagihanListrik}/batal', [PembayaranListrikController::class, 'batalBayar'])->name('pembayaran-listrik.batal');
    Route::post('/pembayaran-listrik/bukukan-kas', [PembayaranListrikController::class, 'bukukanKas'])->name('pembayaran-listrik.bukukan-kas');

    // Kompatibilitas Menu Tagihan Listrik sebelumnya (redirect ke rekap-listrik)
    Route::get('/tagihan-listrik', function () {
        return redirect()->route('admin.rekap-listrik.index');
    })->name('tagihan-listrik.index');
    Route::post('/tagihan-listrik/proses-bayar', [PembayaranListrikController::class, 'bayar'])->name('tagihan-listrik.proses-bayar');
    Route::resource('tagihan-listrik', RekapListrikController::class)
        ->parameters(['tagihan-listrik' => 'tagihanListrik'])
        ->except(['show', 'index']);

    // Khusus Superadmin: Manajemen Akun Admin
    Route::middleware('peran:superadmin')->group(function () {
        Route::get('/pengguna', [PenggunaController::class, 'index'])->name('pengguna.index');
        Route::post('/pengguna', [PenggunaController::class, 'store'])->name('pengguna.store');
        Route::patch('/pengguna/{user}/status', [PenggunaController::class, 'toggleStatus'])->name('pengguna.status');
        Route::delete('/pengguna/{user}', [PenggunaController::class, 'destroy'])->name('pengguna.destroy');
    });
});

// Redirect route dashboard Breeze ke panel admin
Route::get('/dashboard', function () {
    if (auth()->check() && auth()->user()->isPengurus()) {
        return redirect()->route('admin.dashboard');
    }

    return redirect()->route('beranda');
})->middleware('auth')->name('dashboard');

// Profil Pengguna Breeze
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
