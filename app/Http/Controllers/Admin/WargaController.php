<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Warga;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WargaController extends Controller
{
    /**
     * Menampilkan daftar master data warga pelanggan listrik.
     */
    public function index(Request $request): View
    {
        $rt = $request->query('rt');
        $cari = $request->query('cari');

        $query = Warga::query();

        if ($rt) {
            $query->where('rt', (int) $rt);
        }

        if ($cari) {
            $query->where(function ($q) use ($cari) {
                $q->where('nama', 'like', "%{$cari}%")
                    ->orWhere('nama_pelanggan_pln', 'like', "%{$cari}%")
                    ->orWhere('nomor_meter', 'like', "%{$cari}%");
            });
        }

        $daftarWarga = $query->orderBy('rt', 'asc')->orderBy('nama', 'asc')->paginate(20)->withQueryString();
        $totalWarga = Warga::count();

        return view('admin.warga.index', compact('daftarWarga', 'rt', 'cari', 'totalWarga'));
    }

    /**
     * Menyimpan data master warga baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'nama_pelanggan_pln' => ['required', 'string', 'max:255'],
            'rt' => ['required', 'integer', 'in:1,2,3'],
            'nomor_meter' => ['nullable', 'string', 'max:100'],
            'no_telepon' => ['nullable', 'string', 'max:30'],
            'alamat' => ['nullable', 'string', 'max:255'],
        ]);

        $warga = Warga::create($validated);

        return back()->with('sukses', "Data warga {$warga->nama} (Atas Nama PLN: {$warga->nama_pelanggan_pln}) berhasil didaftarkan.");
    }

    /**
     * Memperbarui data master warga.
     */
    public function update(Request $request, Warga $warga): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'nama_pelanggan_pln' => ['required', 'string', 'max:255'],
            'rt' => ['required', 'integer', 'in:1,2,3'],
            'nomor_meter' => ['nullable', 'string', 'max:100'],
            'no_telepon' => ['nullable', 'string', 'max:30'],
            'alamat' => ['nullable', 'string', 'max:255'],
        ]);

        $warga->update($validated);

        return back()->with('sukses', "Data warga {$warga->nama} berhasil diperbarui.");
    }

    /**
     * Menghapus data master warga.
     */
    public function destroy(Warga $warga): RedirectResponse
    {
        $nama = $warga->nama;
        $warga->delete();

        return back()->with('sukses', "Data warga {$nama} telah dihapus dari master data.");
    }

    /**
     * Endpoint API JSON untuk autocomplete pilihan warga saat proses rekap tagihan.
     */
    public function searchJson(Request $request): JsonResponse
    {
        $rt = $request->query('rt');
        $q = $request->query('q');

        $query = Warga::where('is_aktif', true);

        if ($rt) {
            $query->where('rt', (int) $rt);
        }

        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('nama', 'like', "%{$q}%")
                    ->orWhere('nama_pelanggan_pln', 'like', "%{$q}%")
                    ->orWhere('nomor_meter', 'like', "%{$q}%");
            });
        }

        $wargaList = $query->orderBy('nama', 'asc')->limit(50)->get([
            'id',
            'nama',
            'nama_pelanggan_pln',
            'nomor_meter',
            'rt',
        ]);

        return response()->json($wargaList);
    }
}
