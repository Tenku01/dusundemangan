<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class BarangController extends Controller
{
    /**
     * Menampilkan daftar inventaris barang.
     */
    public function index(Request $request): View
    {
        $cari = $request->query('cari');
        $kategori = $request->query('kategori');

        $query = Barang::query();

        if ($cari) {
            $query->where(function ($q) use ($cari) {
                $q->where('nama_barang', 'like', "%{$cari}%")
                    ->orWhere('kode_barang', 'like', "%{$cari}%")
                    ->orWhere('lokasi_penyimpanan', 'like', "%{$cari}%");
            });
        }

        if ($kategori && $kategori !== 'Semua') {
            $query->where('kategori', $kategori);
        }

        $daftarBarang = $query->orderBy('nama_barang')->paginate(15)->withQueryString();
        $kategoriList = Barang::select('kategori')->distinct()->pluck('kategori');

        return view('admin.barang.index', compact('daftarBarang', 'kategoriList', 'cari', 'kategori'));
    }

    /**
     * Menyimpan barang baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_barang' => 'required|string|max:150',
            'kategori' => 'required|string|max:100',
            'jumlah_total' => 'required|integer|min:1',
            'kondisi' => 'required|string|in:Baik,Perlu Perbaikan,Rusak Ringan',
            'lokasi_penyimpanan' => 'nullable|string|max:150',
            'keterangan' => 'nullable|string|max:1000',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'nama_barang.required' => 'Nama barang wajib diisi.',
            'kategori.required' => 'Kategori barang wajib dipilih.',
            'jumlah_total.min' => 'Jumlah total barang minimal 1.',
            'foto.image' => 'File foto harus berupa gambar (jpg, png, webp).',
            'foto.max' => 'Ukuran foto maksimal 2MB.',
        ]);

        // Generate Kode Barang Otomatis jika tidak ada
        $awalan = 'BRG';
        $nomorUrut = Barang::count() + 1;
        $kodeBarang = sprintf('%s-%03d', $awalan, $nomorUrut);
        while (Barang::where('kode_barang', $kodeBarang)->exists()) {
            $nomorUrut++;
            $kodeBarang = sprintf('%s-%03d', $awalan, $nomorUrut);
        }

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('barang', 'public');
        }

        Barang::create([
            'kode_barang' => $kodeBarang,
            'nama_barang' => $validated['nama_barang'],
            'kategori' => $validated['kategori'],
            'foto' => $fotoPath,
            'jumlah_total' => $validated['jumlah_total'],
            'jumlah_tersedia' => $validated['jumlah_total'],
            'kondisi' => $validated['kondisi'],
            'lokasi_penyimpanan' => $validated['lokasi_penyimpanan'] ?? null,
            'keterangan' => $validated['keterangan'] ?? null,
        ]);

        return redirect()->route('admin.barang.index')->with('sukses', "Barang {$validated['nama_barang']} ({$kodeBarang}) berhasil ditambahkan!");
    }

    /**
     * Memperbarui data barang.
     */
    public function update(Request $request, Barang $barang): RedirectResponse
    {
        $validated = $request->validate([
            'nama_barang' => 'required|string|max:150',
            'kategori' => 'required|string|max:100',
            'jumlah_total' => 'required|integer|min:1',
            'kondisi' => 'required|string|in:Baik,Perlu Perbaikan,Rusak Ringan',
            'lokasi_penyimpanan' => 'nullable|string|max:150',
            'keterangan' => 'nullable|string|max:1000',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        // Hitung selisih ketersediaan jika jumlah total diubah
        $selisih = $validated['jumlah_total'] - $barang->jumlah_total;
        $jumlahTersediaBaru = max(0, $barang->jumlah_tersedia + $selisih);

        if ($request->hasFile('foto')) {
            if ($barang->foto) {
                Storage::disk('public')->delete($barang->foto);
            }
            $barang->foto = $request->file('foto')->store('barang', 'public');
        }

        $barang->nama_barang = $validated['nama_barang'];
        $barang->kategori = $validated['kategori'];
        $barang->jumlah_total = $validated['jumlah_total'];
        $barang->jumlah_tersedia = $jumlahTersediaBaru;
        $barang->kondisi = $validated['kondisi'];
        $barang->lokasi_penyimpanan = $validated['lokasi_penyimpanan'] ?? null;
        $barang->keterangan = $validated['keterangan'] ?? null;
        $barang->save();

        return redirect()->route('admin.barang.index')->with('sukses', "Data barang {$barang->nama_barang} berhasil diperbarui!");
    }

    /**
     * Menghapus barang dari sistem inventaris.
     */
    public function destroy(Barang $barang): RedirectResponse
    {
        // Cek jika barang sedang dipinjam aktif
        $pinjamAktif = $barang->peminjaman()->where('status', 'disetujui')->exists();
        if ($pinjamAktif) {
            return back()->withErrors(['hapus' => "Barang {$barang->nama_barang} tidak dapat dihapus karena saat ini masih ada peminjaman yang sedang berjalan."]);
        }

        if ($barang->foto) {
            Storage::disk('public')->delete($barang->foto);
        }

        $nama = $barang->nama_barang;
        $barang->delete();

        return redirect()->route('admin.barang.index')->with('sukses', "Barang {$nama} berhasil dihapus dari inventaris.");
    }
}
