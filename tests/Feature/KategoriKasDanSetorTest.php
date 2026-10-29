<?php

namespace Tests\Feature;

use App\Models\Keuangan;
use App\Models\TagihanListrik;
use App\Models\User;
use Illuminate\Support\Str;
use Tests\TestCase;

class KategoriKasDanSetorTest extends TestCase
{
    protected array $createdKeuanganIds = [];

    protected array $createdTagihanIds = [];

    protected array $createdUserIds = [];

    protected ?User $admin = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::firstOrCreate(
            ['email' => 'superadmin@demangan.id'],
            [
                'name' => 'Super Administrator',
                'password' => bcrypt('password'),
                'peran' => 'superadmin',
            ]
        );
        $this->admin->update(['peran' => 'superadmin']);
    }

    protected function tearDown(): void
    {
        if (! empty($this->createdKeuanganIds)) {
            Keuangan::whereIn('id', $this->createdKeuanganIds)->delete();
        }

        if (! empty($this->createdTagihanIds)) {
            TagihanListrik::whereIn('id', $this->createdTagihanIds)->delete();
        }

        if (! empty($this->createdUserIds)) {
            User::whereIn('id', $this->createdUserIds)->delete();
        }

        parent::tearDown();
    }

    public function test_superadmin_can_view_keuangan_page_with_cash_categories(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.keuangan.index'));

        $response->assertStatus(200);
        $response->assertSee('Kas Karang Taruna');
        $response->assertSee('Kas Listrik (Tertahan)');
        $response->assertSee('Total Seluruh Kas');
    }

    public function test_can_store_keuangan_with_custom_kategori_kas(): void
    {
        $customKategori = 'Kas Sosial RT 01 '.Str::random(4);

        $response = $this->actingAs($this->admin)->post(route('admin.keuangan.store'), [
            'tanggal' => now()->toDateString(),
            'jenis' => 'masuk',
            'nominal' => '150.000',
            'kategori_kas' => $customKategori,
            'kategori' => 'Donasi Warga',
            'keterangan' => 'Uji coba kategori kas kustom',
        ]);

        $response->assertRedirect(route('admin.keuangan.index'));
        $response->assertSessionHas('sukses');

        $kas = Keuangan::where('kategori_kas', $customKategori)->latest('id')->first();
        $this->assertNotNull($kas);
        $this->createdKeuanganIds[] = $kas->id;
        $this->assertEquals(150000, $kas->nominal);
        $this->assertEquals('masuk', $kas->jenis);
    }

    public function test_bukukan_kas_listrik_enters_kas_listrik_category(): void
    {
        $uniquePeriode = 'Uji '.Str::random(5);

        $response = $this->actingAs($this->admin)->post(route('admin.pembayaran-listrik.bukukan-kas'), [
            'periode' => $uniquePeriode,
            'rt' => 'semua',
            'nominal' => '52.000',
        ]);

        $response->assertRedirect();

        $kasListrik = Keuangan::where('kategori_kas', Keuangan::KAS_LISTRIK)
            ->where('keterangan', 'like', "%{$uniquePeriode}%")
            ->latest('id')
            ->first();

        $this->assertNotNull($kasListrik);
        $this->createdKeuanganIds[] = $kasListrik->id;
        $this->assertEquals(Keuangan::KAS_LISTRIK, $kasListrik->kategori_kas);
        $this->assertEquals('masuk', $kasListrik->jenis);
        $this->assertEquals(52000, $kasListrik->nominal);
    }

    public function test_setor_kas_listrik_ke_karang_taruna_creates_two_transactions(): void
    {
        // 1. Setup available saldo on Kas Listrik
        $dummyIn = Keuangan::create([
            'kode_transaksi' => 'TEST-IN-'.Str::random(5),
            'tanggal' => now()->toDateString(),
            'jenis' => 'masuk',
            'nominal' => 200000,
            'kategori_kas' => Keuangan::KAS_LISTRIK,
            'kategori' => 'Listrik Masuk Test',
            'keterangan' => 'Kas masuk awal untuk test',
            'dicatat_oleh' => $this->admin->id,
        ]);
        $this->createdKeuanganIds[] = $dummyIn->id;

        // 2. Perform setoran of 75.000 to Kas Karang Taruna
        $response = $this->actingAs($this->admin)->post(route('admin.keuangan.setor-kartar'), [
            'nominal' => '75.000',
            'tanggal' => now()->toDateString(),
            'keterangan' => 'Setoran sebagian kas listrik ke Karang Taruna',
        ]);

        $response->assertRedirect(route('admin.keuangan.index'));
        $response->assertSessionHas('sukses');

        // Check kas keluar from Kas Listrik
        $transKeluar = Keuangan::where('kategori_kas', Keuangan::KAS_LISTRIK)
            ->where('jenis', 'keluar')
            ->where('nominal', 75000)
            ->latest('id')
            ->first();
        $this->assertNotNull($transKeluar);
        $this->createdKeuanganIds[] = $transKeluar->id;

        // Check kas masuk to Kas Karang Taruna
        $transMasuk = Keuangan::where('kategori_kas', Keuangan::KAS_KARANG_TARUNA)
            ->where('jenis', 'masuk')
            ->where('nominal', 75000)
            ->latest('id')
            ->first();
        $this->assertNotNull($transMasuk);
        $this->createdKeuanganIds[] = $transMasuk->id;

        $this->assertStringContainsString('Penerimaan setoran dana dari Kas Listrik', $transMasuk->keterangan);
    }

    public function test_cannot_setor_more_than_available_saldo_listrik(): void
    {
        // Calculate current saldo listrik
        $stats = Keuangan::where('kategori_kas', Keuangan::KAS_LISTRIK)->selectRaw("
            COALESCE(SUM(CASE WHEN jenis = 'masuk' THEN nominal ELSE 0 END), 0) as masuk,
            COALESCE(SUM(CASE WHEN jenis = 'keluar' THEN nominal ELSE 0 END), 0) as keluar
        ")->first();
        $saldoListrik = (int) (($stats->masuk ?? 0) - ($stats->keluar ?? 0));

        // Try to transfer more than saldo
        $excessNominal = $saldoListrik + 1000000;

        $response = $this->actingAs($this->admin)->post(route('admin.keuangan.setor-kartar'), [
            'nominal' => $excessNominal,
            'tanggal' => now()->toDateString(),
        ]);

        $response->assertSessionHasErrors(['nominal']);
    }
}
