<?php

namespace Tests\Feature;

use App\Models\TagihanListrik;
use App\Models\User;
use App\Models\Warga;
use Tests\TestCase;

class SecurityAuditTest extends TestCase
{
    protected array $createdUserIds = [];

    protected array $createdTagihanIds = [];

    protected array $createdWargaIds = [];

    protected function tearDown(): void
    {
        if (! empty($this->createdTagihanIds)) {
            TagihanListrik::whereIn('id', $this->createdTagihanIds)->delete();
        }

        if (! empty($this->createdWargaIds)) {
            Warga::whereIn('id', $this->createdWargaIds)->delete();
        }

        if (! empty($this->createdUserIds)) {
            User::whereIn('id', $this->createdUserIds)->delete();
        }

        parent::tearDown();
    }

    public function test_guest_is_redirected_to_login_when_accessing_admin_dashboard(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect('/login');
    }

    public function test_guest_is_redirected_to_login_when_accessing_superadmin_user_management(): void
    {
        $response = $this->get('/admin/pengguna');

        $response->assertRedirect('/login');
    }

    public function test_regular_admin_is_forbidden_from_accessing_superadmin_user_management(): void
    {
        $admin = User::create([
            'name' => 'Admin Regular Test',
            'email' => 'admin_audit_'.uniqid().'@example.com',
            'password' => bcrypt('password'),
            'peran' => 'admin',
            'status' => 'aktif',
            'menu_akses' => ['beranda', 'barang', 'kas'],
        ]);
        $this->createdUserIds[] = $admin->id;

        $response = $this->actingAs($admin)->get('/admin/pengguna');

        // Middleware peran:superadmin should deny access (403 Forbidden or redirect)
        $this->assertTrue(in_array($response->getStatusCode(), [403, 302]));
    }

    public function test_superadmin_can_access_user_management(): void
    {
        $superadmin = User::where('peran', 'superadmin')->first();
        if (! $superadmin) {
            $superadmin = User::create([
                'name' => 'Superadmin Audit Test',
                'email' => 'superadmin_audit_'.uniqid().'@example.com',
                'password' => bcrypt('password'),
                'peran' => 'superadmin',
                'status' => 'aktif',
            ]);
            $this->createdUserIds[] = $superadmin->id;
        }

        $response = $this->actingAs($superadmin)->get('/admin/pengguna');

        $response->assertStatus(200);
        $response->assertSee('Manajemen Akun Pengurus');
    }

    public function test_superadmin_cannot_delete_their_own_account(): void
    {
        $superadmin = User::create([
            'name' => 'Superadmin Self Delete Test',
            'email' => 'superadmin_delete_'.uniqid().'@example.com',
            'password' => bcrypt('password'),
            'peran' => 'superadmin',
            'status' => 'aktif',
        ]);
        $this->createdUserIds[] = $superadmin->id;

        $response = $this->actingAs($superadmin)->delete("/admin/pengguna/{$superadmin->id}");

        $response->assertSessionHasErrors(['hapus']);
        $this->assertDatabaseHas('users', ['id' => $superadmin->id]);
    }

    public function test_superadmin_cannot_delete_another_superadmin_account(): void
    {
        $superadminActor = User::create([
            'name' => 'Superadmin Actor Test',
            'email' => 'superadmin_actor_'.uniqid().'@example.com',
            'password' => bcrypt('password'),
            'peran' => 'superadmin',
            'status' => 'aktif',
        ]);
        $this->createdUserIds[] = $superadminActor->id;

        $targetSuperadmin = User::create([
            'name' => 'Superadmin Target Test',
            'email' => 'superadmin_target_'.uniqid().'@example.com',
            'password' => bcrypt('password'),
            'peran' => 'superadmin',
            'status' => 'aktif',
        ]);
        $this->createdUserIds[] = $targetSuperadmin->id;

        $response = $this->actingAs($superadminActor)->delete("/admin/pengguna/{$targetSuperadmin->id}");

        $response->assertSessionHasErrors(['hapus']);
        $this->assertDatabaseHas('users', ['id' => $targetSuperadmin->id]);
    }

    public function test_public_cek_listrik_does_not_reveal_bills_without_search_query(): void
    {
        $warga = Warga::create([
            'nama' => 'Warga Rahasia '.uniqid(),
            'nomor_meter' => 'MTR_SEC_'.rand(1000, 9999),
            'rt' => '01',
            'rw' => '01',
            'status_aktif' => true,
        ]);
        $this->createdWargaIds[] = $warga->id;

        $tagihan = TagihanListrik::create([
            'warga_id' => $warga->id,
            'periode' => 'November 2026',
            'rt' => 1,
            'nama_warga' => $warga->nama,
            'nama_pelanggan' => 'Pelanggan Rahasia '.uniqid(),
            'nomor_struk' => '99999',
            'tagihan_awal' => 45000,
            'kas_karang_taruna' => 3000,
            'total_tagihan' => 48000,
            'is_lunas' => false,
        ]);
        $this->createdTagihanIds[] = $tagihan->id;

        // Visit without query
        $response = $this->get(route('publik.cek-listrik'));

        $response->assertStatus(200);
        $response->assertSee('Masukkan Nama di Struk Listrik Anda');
        // Critical Privacy Assertion: The secret citizen name and bill MUST NOT appear
        $response->assertDontSee($tagihan->nama_pelanggan);
        $response->assertDontSee('Total Wajib Bayar');
    }

    public function test_public_cek_listrik_finds_bill_when_query_matches(): void
    {
        $uniqueName = 'WargaBudi'.uniqid();

        $warga = Warga::create([
            'nama' => $uniqueName,
            'nomor_meter' => 'MTR_BUDI_'.rand(1000, 9999),
            'rt' => '02',
            'rw' => '01',
            'status_aktif' => true,
        ]);
        $this->createdWargaIds[] = $warga->id;

        $tagihan = TagihanListrik::create([
            'warga_id' => $warga->id,
            'periode' => 'November 2026',
            'rt' => 2,
            'nama_warga' => $uniqueName,
            'nama_pelanggan' => $uniqueName,
            'nomor_struk' => '88888',
            'tagihan_awal' => 50000,
            'kas_karang_taruna' => 3000,
            'total_tagihan' => 53000,
            'is_lunas' => false,
        ]);
        $this->createdTagihanIds[] = $tagihan->id;

        $response = $this->get(route('publik.cek-listrik', ['q' => $uniqueName]));

        $response->assertStatus(200);
        $response->assertSee($uniqueName);
        $response->assertSee('Rp 53.000');
        $response->assertSee('BELUM LUNAS');
    }

    public function test_public_cek_listrik_resists_sql_injection_payload(): void
    {
        // Test SQL injection string
        $injectionPayload = "' OR '1'='1' -- ";

        $response = $this->get(route('publik.cek-listrik', ['q' => $injectionPayload]));

        $response->assertStatus(200);
        // It must NOT dump all bills; it must report not found or empty
        $response->assertSee('Tagihan Tidak Ditemukan');
    }
}
