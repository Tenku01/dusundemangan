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
        ], [
            'name.required' => 'Nama lengkap pengurus wajib diisi.',
            'email.unique' => 'Email ini sudah terdaftar di sistem.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'no_hp' => $validated['no_hp'] ?? null,
            'password' => Hash::make($validated['password']),
            'peran' => $validated['peran'],
            'status' => 'aktif',
        ]);

        return redirect()->route('admin.pengguna.index')->with('sukses', "Akun pengurus {$validated['name']} berhasil dibuat!");
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
