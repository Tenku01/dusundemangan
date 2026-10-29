<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\ResetPasswordNotification;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'peran', 'no_hp', 'status', 'menu_akses'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Definisi master menu operasional sistem yang dapat dikonfigurasi hak aksesnya.
     */
    public const DAFTAR_MENU = [
        'beranda' => [
            'key' => 'beranda',
            'label' => 'Beranda',
            'deskripsi' => 'Dashboard ringkasan statistik & aktivitas',
            'icon' => 'home',
            'rute' => 'admin.dashboard',
        ],
        'rekap' => [
            'key' => 'rekap',
            'label' => 'Rekap Listrik',
            'deskripsi' => 'Pencatatan struk, siklus & saldo titipan',
            'icon' => 'clipboard',
            'rute' => 'admin.rekap-listrik.index',
        ],
        'bayar' => [
            'key' => 'bayar',
            'label' => 'Kasir Bayar Listrik',
            'deskripsi' => 'Loket pembayaran listrik & kembalian',
            'icon' => 'cash',
            'rute' => 'admin.pembayaran-listrik.index',
        ],
        'barang' => [
            'key' => 'barang',
            'label' => 'Inventaris & Barang',
            'deskripsi' => 'Kelola inventaris & approval pinjam',
            'icon' => 'box',
            'rute' => 'admin.barang.index',
        ],
        'kas' => [
            'key' => 'kas',
            'label' => 'Buku Kas & Laporan',
            'deskripsi' => 'Pencatatan kas dusun & laporan keuangan',
            'icon' => 'book',
            'rute' => 'admin.keuangan.index',
        ],
        'warga' => [
            'key' => 'warga',
            'label' => 'Master Warga PLN',
            'deskripsi' => 'Data master warga & nomor meter/struk',
            'icon' => 'users',
            'rute' => 'admin.warga.index',
        ],
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'menu_akses' => 'array',
        ];
    }

    public function isSuperadmin(): bool
    {
        return $this->peran === 'superadmin';
    }

    public function isAdmin(): bool
    {
        return $this->peran === 'admin';
    }

    public function isPengurus(): bool
    {
        return in_array($this->peran, ['superadmin', 'admin'], true);
    }

    public function isWarga(): bool
    {
        return $this->peran === 'warga';
    }

    /**
     * Kirim notifikasi pengaturan ulang kata sandi (lupa password).
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * Memeriksa apakah user memiliki hak akses ke menu tertentu.
     */
    public function hasMenuAkses(string $menu): bool
    {
        if ($this->isSuperadmin()) {
            return true;
        }

        if (! $this->isAdmin()) {
            return false;
        }

        // Jika belum diset (null), berikan akses default ke seluruh menu
        if ($this->menu_akses === null) {
            return true;
        }

        return in_array($menu, (array) $this->menu_akses, true);
    }

    /**
     * Mengambil daftar array key menu yang dapat diakses user.
     *
     * @return array<int, string>
     */
    public function getDaftarMenuAkses(): array
    {
        if ($this->isSuperadmin() || $this->menu_akses === null) {
            return array_keys(self::DAFTAR_MENU);
        }

        return (array) $this->menu_akses;
    }

    /**
     * Mengambil rute pertama yang dapat diakses user untuk pengalihan saat login / akses ditolak.
     */
    public function getFirstAccessibleRoute(): string
    {
        foreach (self::DAFTAR_MENU as $key => $info) {
            if ($this->hasMenuAkses($key)) {
                return $info['rute'];
            }
        }

        return 'admin.dashboard';
    }
}
