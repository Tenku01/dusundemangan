<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class PenggunaController extends Controller
{
    /**
     * Menampilkan daftar akun pengurus dan admin dusun.
     */
    public function index(): View
    {
        $daftarPengguna = User::orderBy('peran', 'asc')
            ->orderBy('name', 'asc')
            ->paginate(15);

        return view('admin.pengguna.index', compact('daftarPengguna'));
    }

    /**
     * Membuat akun Admin baru (khusus Superadmin).
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|max:150|unique:users,email',
            'no_hp' => 'nullable|string|max:30',
            'password' => ['required', 'confirmed', Password::defaults()],
            'peran' => 'required|in:admin,warga',
            'menu_akses' => 'nullable|array',
            'menu_akses.*' => 'in:beranda,rekap,bayar,barang,kas,warga',
        ], [
            'name.required' => 'Nama lengkap pengurus wajib diisi.',
            'email.unique' => 'Email ini sudah terdaftar di sistem.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $menuAkses = null;
        if ($validated['peran'] === 'admin') {
            $menuAkses = ! empty($validated['menu_akses'])
                ? $validated['menu_akses']
                : array_keys(User::DAFTAR_MENU);
        }

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'no_hp' => $validated['no_hp'] ?? null,
            'password' => Hash::make($validated['password']),
            'peran' => $validated['peran'],
            'status' => 'aktif',
            'menu_akses' => $menuAkses,
        ]);

        return redirect()->route('admin.pengguna.index')->with('sukses', "Akun pengurus {$validated['name']} berhasil dibuat!");
    }

    /**
     * Mengubah konfigurasi hak akses menu untuk akun pengurus (khusus Superadmin).
     */
    public function updateMenuAkses(Request $request, User $user): RedirectResponse
    {
        if ($user->isSuperadmin()) {
            return back()->withErrors(['akses' => 'Hak akses Superadmin selalu penuh dan tidak dapat dibatasi.']);
        }

        $validated = $request->validate([
            'menu_akses' => 'nullable|array',
            'menu_akses.*' => 'in:beranda,rekap,bayar,barang,kas,warga',
        ]);

        $menuAkses = $validated['menu_akses'] ?? [];
        if ($user->isAdmin() && empty($menuAkses)) {
            return back()->withErrors(['akses' => 'Pengurus minimal harus diberikan hak akses ke 1 menu.']);
        }

        $user->update([
            'menu_akses' => $menuAkses,
        ]);

        return back()->with('sukses', "Hak akses menu untuk {$user->name} berhasil disimpan.");
    }

    /**
     * Mengubah status aktif / nonaktif akun pengurus.
     */
    public function toggleStatus(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['status' => 'Anda tidak dapat menonaktifkan akun Anda sendiri.']);
        }

        $statusBaru = $user->status === 'aktif' ? 'nonaktif' : 'aktif';
        $user->update(['status' => $statusBaru]);

        return back()->with('sukses', "Status akun {$user->name} berhasil diubah menjadi {$statusBaru}.");
    }

    /**
     * Menghapus akun pengurus.
     */
    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['hapus' => 'Anda tidak dapat menghapus akun Anda sendiri.']);
        }

        if ($user->peran === 'superadmin') {
            return back()->withErrors(['hapus' => 'Akun Superadmin tidak dapat dihapus.']);
        }

        $nama = $user->name;
        $user->delete();

        return redirect()->route('admin.pengguna.index')->with('sukses', "Akun {$nama} berhasil dihapus.");
    }
}
