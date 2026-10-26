<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PeminjamanController extends Controller
{
    /**
     * Menampilkan daftar dan antrean permohonan peminjaman.
     */
    public function index(Request $request): View
    {
        $status = $request->query('status', 'menunggu');
        $cari = $request->query('cari');

        $query = Peminjaman::with(['barang', 'penyetuju']);

        if ($status && $status !== 'semua') {
            $query->where('status', $status);
        }

        if ($cari) {
            $query->where(function ($q) use ($cari) {
                $q->where('kode_pinjam', 'like', "%{$cari}%")
                    ->orWhere('nama_peminjam', 'like', "%{$cari}%")
                    ->orWhere('kontak_peminjam', 'like', "%{$cari}%")
                    ->orWhere('alamat_rt_rw', 'like', "%{$cari}%")
                    ->orWhereHas('barang', function ($b) use ($cari) {
                        $b->where('nama_barang', 'like', "%{$cari}%");
                    });
            });
        }

        $daftarPinjam = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        $jumlahMenunggu = Peminjaman::where('status', 'menunggu')->count();
        $jumlahDisetujui = Peminjaman::where('status', 'disetujui')->count();
        $jumlahDikembalikan = Peminjaman::where('status', 'dikembalikan')->count();
        $jumlahDitolak = Peminjaman::where('status', 'ditolak')->count();

        return view('admin.peminjaman.index', compact(
            'daftarPinjam',
            'status',
            'cari',
            'jumlahMenunggu',
            'jumlahDisetujui',
            'jumlahDikembalikan',
            'jumlahDitolak'
        ));
    }

    /**
     * Menyetujui permohonan pinjam: stok jumlah_tersedia otomatis berkurang.
     */
    public function setujui(Request $request, Peminjaman $peminjaman): RedirectResponse
    {
        if ($peminjaman->status !== 'menunggu') {
            return back()->withErrors(['status' => 'Hanya permohonan dengan status menunggu yang dapat disetujui.']);
        }

        $barang = $peminjaman->barang;

        if ($barang->jumlah_tersedia < $peminjaman->jumlah_pinjam) {
            return back()->withErrors([
                'stok' => "Gagal menyetujui. Stok tersedia {$barang->nama_barang} saat ini ({$barang->jumlah_tersedia} unit) kurang dari permintaan ({$peminjaman->jumlah_pinjam} unit).",
            ]);
        }

        DB::transaction(function () use ($peminjaman, $barang, $request) {
            // Kurangi stok tersedia
            $barang->decrement('jumlah_tersedia', $peminjaman->jumlah_pinjam);

            // Update status peminjaman
            $peminjaman->update([
                'status' => 'disetujui',
                'disetujui_oleh' => auth()->id(),
                'catatan_admin' => $request->input('catatan_admin'),
            ]);
        });

        return back()->with('sukses', "Permohonan pinjam {$peminjaman->kode_pinjam} ({$barang->nama_barang}) berhasil disetujui! Stok otomatis berkurang {$peminjaman->jumlah_pinjam} unit.");
    }

    /**
     * Menolak permohonan pinjam dengan alasan.
     */
    public function tolak(Request $request, Peminjaman $peminjaman): RedirectResponse
    {
        if ($peminjaman->status !== 'menunggu') {
            return back()->withErrors(['status' => 'Hanya permohonan dengan status menunggu yang dapat ditolak.']);
        }

        $request->validate([
            'catatan_admin' => 'required|string|max:500',
        ], [
            'catatan_admin.required' => 'Wajib memberikan alasan penolakan agar peminjam memahami kendala ketersediaan.',
        ]);

        $peminjaman->update([
            'status' => 'ditolak',
            'disetujui_oleh' => auth()->id(),
            'catatan_admin' => $request->input('catatan_admin'),
        ]);

        return back()->with('sukses', "Permohonan pinjam {$peminjaman->kode_pinjam} telah ditolak.");
    }

    /**
     * Menandai barang fisik telah dikembalikan ke pos/gudang: stok dikembalikan ke angka semula.
     */
    public function kembalikan(Request $request, Peminjaman $peminjaman): RedirectResponse
    {
        if ($peminjaman->status !== 'disetujui') {
            return back()->withErrors(['status' => 'Hanya peminjaman berstatus disetujui yang dapat diproses pengembalian.']);
        }

        $barang = $peminjaman->barang;

        DB::transaction(function () use ($peminjaman, $barang, $request) {
            // Kembalikan stok barang fisik
            $barang->increment('jumlah_tersedia', $peminjaman->jumlah_pinjam);

            // Update peminjaman selesai
            $peminjaman->update([
                'status' => 'dikembalikan',
                'tanggal_dikembalikan' => now(),
                'catatan_admin' => $request->input('catatan_admin') ?? $peminjaman->catatan_admin,
            ]);
        });

        return back()->with('sukses', "Barang {$barang->nama_barang} ({$peminjaman->jumlah_pinjam} unit) dari peminjaman {$peminjaman->kode_pinjam} telah kembali ke gudang!");
    }
}
