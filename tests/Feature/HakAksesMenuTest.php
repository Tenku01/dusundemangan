<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class HakAksesMenuTest extends TestCase
{
    protected array $createdUserIds = [];

    protected function tearDown(): void
    {
        if (! empty($this->createdUserIds)) {
            User::whereIn('id', $this->createdUserIds)->delete();
        }

        parent::tearDown();
    }

    protected function buatAdmin(array $menuAkses = ['rekap', 'bayar']): User
    {
        $user = User::create([
            'name' => 'Admin Test Akses',
            'email' => 'admin.test.'.uniqid().'@demangan.id',
            'password' => Hash::make('password123'),
            'peran' => 'admin',
            'status' => 'aktif',
            'menu_akses' => $menuAkses,
        ]);

        $this->createdUserIds[] = $user->id;

        return $user;
    }

    protected function getSuperadmin(): User
    {
        return User::where('email', 'superadmin@demangan.id')->firstOrFail();
    }

    public function test_superadmin_memiliki_akses_ke_seluruh_menu(): void
    {
        $superadmin = $this->getSuperadmin();
        $this->actingAs($superadmin);

        // Akses Beranda
        $this->get(route('admin.dashboard'))->assertStatus(200);

        // Akses Rekap Listrik
        $this->get(route('admin.rekap-listrik.index'))->assertStatus(200);

        // Akses Kasir Pembayaran
        $this->get(route('admin.pembayaran-listrik.index'))->assertStatus(200);

        // Akses Inventaris Barang
        $this->get(route('admin.barang.index'))->assertStatus(200);

        // Akses Keuangan
        $this->get(route('admin.keuangan.index'))->assertStatus(200);

        // Akses Kelola Pengguna
        $this->get(route('admin.pengguna.index'))->assertStatus(200);
    }

    public function test_admin_hanya_dapat_mengakses_menu_yang_diizinkan(): void
    {
        // Admin hanya punya akses ke Rekap dan Bayar
        $admin = $this->buatAdmin(['rekap', 'bayar']);
        $this->actingAs($admin);

        // Diizinkan: Rekap Listrik & Kasir Pembayaran
        $this->get(route('admin.rekap-listrik.index'))->assertStatus(200);
        $this->get(route('admin.pembayaran-listrik.index'))->assertStatus(200);

        // Dicegah: Inventaris Barang & Keuangan (dialihkan ke rute yang bisa diakses)
        $responseBarang = $this->get(route('admin.barang.index'));
        $responseBarang->assertRedirect(route('admin.rekap-listrik.index'));
        $responseBarang->assertSessionHasErrors('akses');

        $responseKeuangan = $this->get(route('admin.keuangan.index'));
        $responseKeuangan->assertRedirect(route('admin.rekap-listrik.index'));
        $responseKeuangan->assertSessionHasErrors('akses');
    }

    public function test_superadmin_dapat_mengubah_hak_akses_menu_admin(): void
    {
        $superadmin = $this->getSuperadmin();
        $admin = $this->buatAdmin(['barang']);

        $this->actingAs($superadmin);

        // Ubah hak akses admin menjadi hanya Rekap dan Kas
        $response = $this->patch(route('admin.pengguna.menu-akses', $admin), [
            'menu_akses' => ['rekap', 'kas'],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('sukses');

        $admin->refresh();
        $this->assertEqualsCanonicalizing(['rekap', 'kas'], $admin->menu_akses);
        $this->assertTrue($admin->hasMenuAkses('rekap'));
        $this->assertTrue($admin->hasMenuAkses('kas'));
        $this->assertFalse($admin->hasMenuAkses('barang'));
        $this->assertFalse($admin->hasMenuAkses('bayar'));
    }

    public function test_superadmin_dapat_membuat_admin_baru_dengan_pilihan_menu_akses(): void
    {
        $superadmin = $this->getSuperadmin();
        $this->actingAs($superadmin);

        $email = 'petugas.kasir.'.uniqid().'@demangan.id';

        $response = $this->post(route('admin.pengguna.store'), [
            'name' => 'Petugas Kasir Listrik',
            'email' => $email,
            'no_hp' => '089911223344',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'peran' => 'admin',
            'menu_akses' => ['rekap', 'bayar', 'beranda'],
        ]);

        $response->assertRedirect(route('admin.pengguna.index'));
        $response->assertSessionHas('sukses');

        $newUser = User::where('email', $email)->first();
        $this->assertNotNull($newUser);
        $this->createdUserIds[] = $newUser->id;

        $this->assertEqualsCanonicalizing(['rekap', 'bayar', 'beranda'], $newUser->menu_akses);
        $this->assertTrue($newUser->hasMenuAkses('bayar'));
        $this->assertFalse($newUser->hasMenuAkses('barang'));
    }

    public function test_sidebar_dan_bottom_bar_hanya_menampilkan_menu_yang_diizinkan(): void
    {
        // Admin khusus kasir listrik
        $admin = $this->buatAdmin(['rekap', 'bayar']);
        $this->actingAs($admin);

        $response = $this->get(route('admin.rekap-listrik.index'));
        $response->assertStatus(200);

        // Menu yang diizinkan harus tampil
        $response->assertSee('Rekap Listrik');
        $response->assertSee('Pembayaran Listrik');

        // Menu yang tidak diizinkan tidak boleh muncul di navigasi
        $response->assertDontSee('Data Inventaris');
        $response->assertDontSee('Buku Kas Utama');
        $response->assertDontSee('Kelola Akun Admin');
    }
}
