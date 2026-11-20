<?php

namespace Tests\Feature;

use App\Models\DataChangeLog;
use App\Models\MethodStatusLog;
use App\Models\User;
use App\Models\Warga;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class StatusDanErrorPagesTest extends TestCase
{
    /** @var array<int, int> */
    protected array $createdUserIds = [];

    /** @var array<int, int> */
    protected array $createdWargaIds = [];

    protected function tearDown(): void
    {
        if (! empty($this->createdWargaIds)) {
            DataChangeLog::where('model_type', Warga::class)
                ->whereIn('model_id', $this->createdWargaIds)
                ->delete();
            Warga::whereIn('id', $this->createdWargaIds)->delete();
        }

        if (! empty($this->createdUserIds)) {
            DataChangeLog::whereIn('user_id', $this->createdUserIds)->delete();
            MethodStatusLog::whereIn('user_id', $this->createdUserIds)->delete();
            User::whereIn('id', $this->createdUserIds)->delete();
        }

        parent::tearDown();
    }

    protected function getSuperadmin(): User
    {
        return User::where('email', 'superadmin@demangan.id')->firstOrFail();
    }

    public function test_guest_dan_admin_biasa_tidak_bisa_mengakses_status(): void
    {
        $this->get('/status')->assertRedirect(route('login'));

        $admin = User::create([
            'name' => 'Admin Biasa Test',
            'email' => 'admin.status.'.uniqid().'@demangan.id',
            'password' => Hash::make('password123'),
            'peran' => 'admin',
            'status' => 'aktif',
            'menu_akses' => ['beranda'],
        ]);
        $this->createdUserIds[] = $admin->id;

        $this->actingAs($admin)->get('/status')->assertStatus(403);
    }

    public function test_superadmin_dapat_melihat_tiga_bagian_halaman_status(): void
    {
        $superadmin = $this->getSuperadmin();
        $this->actingAs($superadmin);

        $this->get('/status')
            ->assertStatus(200)
            ->assertSee('Log Perubahan Data')
            ->assertSee('Status Method')
            ->assertSee('Status Update Web');

        $this->get('/status?tab=method')
            ->assertStatus(200)
            ->assertSee('Bersihkan Memori');

        $this->get('/status?tab=update')
            ->assertStatus(200)
            ->assertSee('Terkunci (Permanen')
            ->assertSee(config('web_updates.current_version'));
    }

    public function test_perubahan_data_tercatat_dan_log_dapat_dibersihkan(): void
    {
        $superadmin = $this->getSuperadmin();
        $this->actingAs($superadmin);

        $warga = Warga::create([
            'nama' => 'Warga Audit '.uniqid(),
            'nama_pelanggan_pln' => 'PLN AUDIT',
            'rt' => 1,
            'nomor_meter' => '99'.random_int(100, 999),
            'is_aktif' => true,
        ]);
        $this->createdWargaIds[] = $warga->id;

        $warga->update(['nama' => 'Warga Audit Diubah']);

        $this->assertDatabaseHas('data_change_logs', [
            'user_id' => $superadmin->id,
            'user_name' => $superadmin->name,
            'model_type' => Warga::class,
            'model_id' => $warga->id,
            'aksi' => 'updated',
        ]);

        $response = $this->delete(route('status.clear-data-logs'));
        $response->assertRedirect(route('status.index', ['tab' => 'perubahan']));
        $this->assertSame(0, DataChangeLog::count());
        $this->assertDatabaseHas('warga', ['id' => $warga->id]);
    }

    public function test_status_method_dan_memori_dapat_dibersihkan_serta_update_web_tidak_bisa_dihapus(): void
    {
        $superadmin = $this->getSuperadmin();
        $this->actingAs($superadmin);

        $this->get('/status?tab=method')->assertStatus(200);
        $this->assertGreaterThan(0, MethodStatusLog::count());

        $this->delete(route('status.clear-method-logs'))
            ->assertRedirect(route('status.index', ['tab' => 'method']));

        $this->post(route('status.clear-memory'))
            ->assertRedirect(route('status.index', ['tab' => 'method']))
            ->assertSessionHas('sukses');

        $this->assertContains($this->delete('/status/update')->getStatusCode(), [404, 405]);
    }

    public function test_seluruh_halaman_error_3xx_4xx_5xx_dapat_dirender_valid(): void
    {
        $codes = [
            '300', '301', '302', '304', '307', '308', '3xx',
            '400', '401', '402', '403', '404', '405', '408', '419', '422', '429', '4xx',
            '500', '502', '503', '504', '5xx',
        ];

        foreach ($codes as $code) {
            $html = view("errors.{$code}")->render();
            $this->assertNotEmpty($html);
            $this->assertStringContainsString('Karang Taruna Dusun Demangan', $html);
        }

        $this->get('/halaman-tidak-ditemukan-uji-404')
            ->assertStatus(404)
            ->assertSee('404')
            ->assertSee('Kembali ke Beranda');
    }
}
