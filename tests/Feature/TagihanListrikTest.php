<?php

namespace Tests\Feature;

use App\Models\Keuangan;
use App\Models\TagihanListrik;
use App\Models\User;
use App\Models\Warga;
use Tests\TestCase;

class TagihanListrikTest extends TestCase
{
    protected User $admin;

    protected array $createdTagihanIds = [];

    protected array $createdWargaIds = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::firstOrCreate(
            ['email' => 'admin-test@demangan.id'],
            [
                'name' => 'Admin Pengurus Uji',
                'password' => bcrypt('password'),
                'peran' => 'admin',
                'status' => 'aktif',
            ]
        );
    }

    protected function tearDown(): void
    {
        if (! empty($this->createdTagihanIds)) {
            TagihanListrik::whereIn('id', $this->createdTagihanIds)->delete();
        }

        if (! empty($this->createdWargaIds)) {
            Warga::whereIn('id', $this->createdWargaIds)->delete();
        }

        parent::tearDown();
    }

    public function test_halaman_rekap_listrik_menampilkan_periode_dan_rt_aktif(): void
    {
        $warga = Warga::create([
            'nama' => 'Budi Pak RT Uji',
            'nama_pelanggan_pln' => 'SUWARNO KAKEK UJI',
            'rt' => 1,
            'nomor_meter' => '5299900011',
        ]);
        $this->createdWargaIds[] = $warga->id;

        $tagihan = TagihanListrik::create([
            'warga_id' => $warga->id,
            'periode' => 'September 2026',
            'rt' => 1,
            'nama_warga' => $warga->nama,
            'nama_pelanggan' => $warga->nama_pelanggan_pln,
            'nomor_struk' => 'STR-TEST-001',
            'tagihan_awal' => 50000,
            'kas_karang_taruna' => 3000,
            'kas_dusun' => 0,
            'kategori_kas' => 'karang_taruna',
            'total_tagihan' => 53000,
            'selisih_bayar' => 0,
            'is_lunas' => false,
        ]);
        $this->createdTagihanIds[] = $tagihan->id;

        $response = $this->actingAs($this->admin)
            ->get(route('admin.rekap-listrik.index', ['periode' => 'September 2026', 'rt' => 1]));

        $response->assertStatus(200);
        $response->assertSee('Budi Pak RT Uji');
        $response->assertSee('SUWARNO KAKEK UJI');
        $response->assertSee('STR-TEST-001');
        $response->assertSee('RT 01');
    }

    public function test_proses_rekap_tagihan_menghitung_kas_karang_taruna_otomatis(): void
    {
        $warga = Warga::create([
            'nama' => 'Pak Joko Uji',
            'nama_pelanggan_pln' => 'HJ FATIMAH UJI',
            'rt' => 3,
            'nomor_meter' => '5299900022',
        ]);
        $this->createdWargaIds[] = $warga->id;

        $response = $this->actingAs($this->admin)
            ->post(route('admin.rekap-listrik.store'), [
                'warga_id' => $warga->id,
                'periode' => 'September 2026',
                'rt' => 3,
                'nama_warga' => $warga->nama,
                'nama_pelanggan' => $warga->nama_pelanggan_pln,
                'nomor_struk' => 'STR-REKAP-999',
                'tagihan_awal' => 75000,
            ]);

        $response->assertRedirect(route('admin.rekap-listrik.index', ['periode' => 'September 2026', 'rt' => 3]));

        $tagihan = TagihanListrik::where('nomor_struk', 'STR-REKAP-999')->first();
        $this->assertNotNull($tagihan);
        $this->createdTagihanIds[] = $tagihan->id;

        $this->assertEquals(75000, $tagihan->tagihan_awal);
        $this->assertEquals(3000, $tagihan->kas_karang_taruna);
        $this->assertEquals(0, $tagihan->kas_dusun);
        $this->assertEquals(78000, $tagihan->total_tagihan);
        $this->assertFalse($tagihan->is_lunas);
    }

    public function test_halaman_pembayaran_listrik_memiliki_tab_belum_bayar_dan_lunas(): void
    {
        $tagihan = TagihanListrik::create([
            'periode' => 'September 2026',
            'rt' => 1,
            'nama_warga' => 'Warga Kasir Test',
            'nama_pelanggan' => 'PLN KASIR TEST',
            'nomor_struk' => 'STR-KASIR-01',
            'tagihan_awal' => 45000,
            'kas_karang_taruna' => 3000,
            'total_tagihan' => 48000,
            'selisih_bayar' => 0,
            'is_lunas' => false,
        ]);
        $this->createdTagihanIds[] = $tagihan->id;

        $response = $this->actingAs($this->admin)
            ->get(route('admin.pembayaran-listrik.index', ['periode' => 'September 2026', 'tab' => 'belum_bayar', 'rt' => '1']));

        $response->assertStatus(200);
        $response->assertSee('Warga Kasir Test');
        $response->assertSee('STR-KASIR-01');
    }

    public function test_proses_kasir_bayar_jika_ada_kembalian_maka_tidak_ada_titipan(): void
    {
        $tagihan = TagihanListrik::create([
            'periode' => 'September 2026',
            'rt' => 1,
            'nama_warga' => 'Pak Amin Uji',
            'nama_pelanggan' => 'SUKIRNO UJI',
            'nomor_struk' => 'STR-BAYAR-99',
            'tagihan_awal' => 50000,
            'kas_karang_taruna' => 3000,
            'kas_dusun' => 0,
            'total_tagihan' => 53000,
            'selisih_bayar' => 0,
            'is_lunas' => false,
        ]);
        $this->createdTagihanIds[] = $tagihan->id;

        // Bayar Rp 55.000 (bisa juga format bertitik "55.000") untuk tagihan Rp 53.000 tanpa jadikan_titipan
        // Aturan: Jika ada kembalian, yaudah gaada titipan (kembalian diserahkan ke warga, selisih = 0)
        $response = $this->actingAs($this->admin)
            ->post(route('admin.pembayaran-listrik.bayar'), [
                'tagihan_ids' => [$tagihan->id],
                'uang_dibayar' => '55.000',
            ]);

        $response->assertSessionHas('sukses');

        $tagihanFresh = $tagihan->fresh();
        $this->assertTrue($tagihanFresh->is_lunas);
        $this->assertEquals(0, $tagihanFresh->selisih_bayar); // Tanpa titipan
    }

    public function test_proses_kasir_bayar_titipan_berfungsi_hanya_jika_tidak_ada_kembalian(): void
    {
        $tagihan = TagihanListrik::create([
            'periode' => 'September 2026',
            'rt' => 1,
            'nama_warga' => 'Bu Siti Uji',
            'nama_pelanggan' => 'SITI FATIMAH UJI',
            'nomor_struk' => 'STR-BAYAR-100',
            'tagihan_awal' => 50000,
            'kas_karang_taruna' => 3000,
            'kas_dusun' => 0,
            'total_tagihan' => 53000,
            'selisih_bayar' => 0,
            'is_lunas' => false,
        ]);
        $this->createdTagihanIds[] = $tagihan->id;

        // Bayar Rp 55.000 dengan opsi jadikan_titipan = true (karena tidak ada uang kembalian)
        $response = $this->actingAs($this->admin)
            ->post(route('admin.pembayaran-listrik.bayar'), [
                'tagihan_ids' => [$tagihan->id],
                'uang_dibayar' => '55.000',
                'jadikan_titipan' => 1,
            ]);

        $response->assertSessionHas('sukses');

        $tagihanFresh = $tagihan->fresh();
        $this->assertTrue($tagihanFresh->is_lunas);
        $this->assertEquals(2000, $tagihanFresh->selisih_bayar);
        $this->assertTrue($tagihanFresh->isLebih());
    }

    public function test_proses_kasir_bayar_gabungan_beberapa_warga_dalam_satu_struk(): void
    {
        $tagihan1 = TagihanListrik::create([
            'periode' => 'September 2026',
            'rt' => 1,
            'nama_warga' => 'Keluarga A Uji',
            'nama_pelanggan' => 'NAMA PLN A UJI',
            'nomor_struk' => 'STR-GAB-A-UJI',
            'tagihan_awal' => 40000,
            'kas_karang_taruna' => 3000,
            'total_tagihan' => 43000,
            'selisih_bayar' => 0,
            'is_lunas' => false,
        ]);
        $this->createdTagihanIds[] = $tagihan1->id;

        $tagihan2 = TagihanListrik::create([
            'periode' => 'September 2026',
            'rt' => 1,
            'nama_warga' => 'Keluarga B Uji',
            'nama_pelanggan' => 'NAMA PLN B UJI',
            'nomor_struk' => 'STR-GAB-B-UJI',
            'tagihan_awal' => 50000,
            'kas_karang_taruna' => 3000,
            'total_tagihan' => 53000,
            'selisih_bayar' => 0,
            'is_lunas' => false,
        ]);
        $this->createdTagihanIds[] = $tagihan2->id;

        // Total 43.000 + 53.000 = 96.000. Bayar 100.000 -> sisa titipan +4.000
        $response = $this->actingAs($this->admin)
            ->post(route('admin.pembayaran-listrik.bayar'), [
                'tagihan_ids' => [$tagihan1->id, $tagihan2->id],
                'uang_dibayar' => 100000,
                'jadikan_titipan' => 1,
                'kode_gabungan' => 'GAB-TEST-123',
            ]);

        $response->assertSessionHas('sukses');

        $t1 = $tagihan1->fresh();
        $t2 = $tagihan2->fresh();

        $this->assertTrue($t1->is_lunas);
        $this->assertTrue($t2->is_lunas);
        $this->assertEquals('GAB-TEST-123', $t1->kode_gabungan);
        $this->assertEquals('GAB-TEST-123', $t2->kode_gabungan);
        $this->assertEquals(4000, $t1->selisih_bayar);
    }

    public function test_kasir_dapat_membatalkan_status_lunas(): void
    {
        $tagihan = TagihanListrik::create([
            'periode' => 'September 2026',
            'rt' => 1,
            'nama_warga' => 'Warga Batal Test',
            'nama_pelanggan' => 'PLN BATAL TEST',
            'nomor_struk' => 'STR-BATAL-01',
            'tagihan_awal' => 50000,
            'kas_karang_taruna' => 3000,
            'total_tagihan' => 53000,
            'selisih_bayar' => 2000,
            'is_lunas' => true,
            'tanggal_bayar' => now()->toDateString(),
        ]);
        $this->createdTagihanIds[] = $tagihan->id;

        $response = $this->actingAs($this->admin)
            ->patch(route('admin.pembayaran-listrik.batal', $tagihan));

        $response->assertSessionHas('sukses');

        $tagihanFresh = $tagihan->fresh();
        $this->assertFalse($tagihanFresh->is_lunas);
        $this->assertNull($tagihanFresh->tanggal_bayar);
        $this->assertEquals(0, $tagihanFresh->selisih_bayar);
    }

    public function test_admin_dapat_mengelola_master_data_warga(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.warga.store'), [
                'nama' => 'Warga Baru Demangan Uji',
                'nama_pelanggan_pln' => 'REKENING PLN LAMA UJI',
                'rt' => 2,
                'nomor_meter' => '52123456789',
                'no_telepon' => '081234999888',
                'alamat' => 'Samping pos RT 02',
            ]);

        $response->assertSessionHas('sukses');

        $warga = Warga::where('nama', 'Warga Baru Demangan Uji')->first();
        $this->assertNotNull($warga);
        $this->createdWargaIds[] = $warga->id;

        $this->assertEquals(2, $warga->rt);
        $this->assertEquals('REKENING PLN LAMA UJI', $warga->nama_pelanggan_pln);
    }

    public function test_duplikasi_siklus_memotong_saldo_titipan_bulan_lalu(): void
    {
        $warga = Warga::create([
            'nama' => 'Pak Bambang Siklus Uji',
            'nama_pelanggan_pln' => 'BAMBANG PLN UJI',
            'rt' => 1,
            'nomor_meter' => '5299900099',
        ]);
        $this->createdWargaIds[] = $warga->id;

        // Bulan September: Total 53.000, bayar 58.000 -> titipan selisih_bayar = 5.000
        $tagihanSeptember = TagihanListrik::create([
            'warga_id' => $warga->id,
            'periode' => 'September 2026',
            'rt' => 1,
            'nama_warga' => $warga->nama,
            'nama_pelanggan' => $warga->nama_pelanggan_pln,
            'nomor_struk' => 'STR-SEP-099',
            'tagihan_awal' => 50000,
            'kas_karang_taruna' => 3000,
            'kas_dusun' => 0,
            'total_tagihan' => 53000,
            'uang_dibayar' => 58000,
            'selisih_bayar' => 5000,
            'saldo_periode_lalu' => 0,
            'is_lunas' => true,
        ]);
        $this->createdTagihanIds[] = $tagihanSeptember->id;

        // Duplikasi ke Oktober 2026
        $response = $this->actingAs($this->admin)
            ->post(route('admin.rekap-listrik.duplikasi-siklus'), [
                'periode_asal' => 'September 2026',
                'periode_baru' => 'Oktober 2026 (Uji)',
                'rt' => '1',
            ]);

        $response->assertSessionHas('sukses');

        $tagihanOktober = TagihanListrik::where('periode', 'Oktober 2026 (Uji)')
            ->where('warga_id', $warga->id)
            ->first();

        $this->assertNotNull($tagihanOktober);
        $this->createdTagihanIds[] = $tagihanOktober->id;

        // Potongan saldo titipan bulan lalu otomatis tercatat 5.000
        $this->assertEquals(5000, $tagihanOktober->saldo_periode_lalu);
        $this->assertEquals(0, $tagihanOktober->total_tagihan);
        $this->assertFalse($tagihanOktober->is_lunas);

        // Saat admin mengisi struk baru PLN untuk bulan Oktober (misal tagihan awal Rp 50.000):
        $this->actingAs($this->admin)
            ->put(route('admin.rekap-listrik.update', $tagihanOktober), [
                'nama_warga' => $tagihanOktober->nama_warga,
                'nama_pelanggan' => $tagihanOktober->nama_pelanggan,
                'nomor_struk' => 'STR-OKT-099',
                'tagihan_awal' => 50000,
            ]);

        // Total tagihan = (50.000 + 3.000) - 5.000 = 48.000
        $this->assertEquals(48000, $tagihanOktober->fresh()->total_tagihan);

        // Hapus data uji Oktober lainnya jika ada yang tersalin
        $otherOktober = TagihanListrik::where('periode', 'Oktober 2026 (Uji)')->pluck('id')->toArray();
        $this->createdTagihanIds = array_merge($this->createdTagihanIds, $otherOktober);
    }

    public function test_admin_dapat_membukukan_kas_karang_taruna_ke_keuangan(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.pembayaran-listrik.bukukan-kas'), [
                'periode' => 'September 2026 (Uji)',
                'rt' => '1',
                'nominal' => 63000,
            ]);

        $response->assertSessionHas('sukses');

        $trx = Keuangan::where('keterangan', 'like', '%September 2026 (Uji)%')->first();
        $this->assertNotNull($trx);
        $this->assertEquals(63000, (int) $trx->nominal);
        $this->assertEquals('masuk', $trx->jenis);

        // Bersihkan data uji keuangan
        $trx->delete();
    }

    public function test_admin_dapat_merekap_banyak_warga_sekaligus_dari_hasil_scan_dokumen(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.rekap-listrik.batch-store'), [
                'periode' => 'September 2026',
                'rt' => 1,
                'items' => [
                    [
                        'nomor_struk' => 'STR-BATCH-1',
                        'nama_warga' => 'Warga Batch 1 Uji',
                        'nama_pelanggan' => 'PLN BATCH 1',
                        'tagihan_awal' => '45.000',
                    ],
                    [
                        'nomor_struk' => 'STR-BATCH-2',
                        'nama_warga' => 'Warga Batch 2 Uji',
                        'nama_pelanggan' => 'PLN BATCH 2',
                        'tagihan_awal' => '60.000',
                    ],
                ],
            ]);

        $response->assertSessionHas('sukses');

        $t1 = TagihanListrik::where('nomor_struk', 'STR-BATCH-1')->first();
        $t2 = TagihanListrik::where('nomor_struk', 'STR-BATCH-2')->first();

        $this->assertNotNull($t1);
        $this->assertNotNull($t2);
        $this->createdTagihanIds[] = $t1->id;
        $this->createdTagihanIds[] = $t2->id;

        $this->assertEquals(45000, $t1->tagihan_awal);
        $this->assertEquals(3000, $t1->kas_karang_taruna);
        $this->assertEquals(48000, $t1->total_tagihan);

        $this->assertEquals(60000, $t2->tagihan_awal);
        $this->assertEquals(3000, $t2->kas_karang_taruna);
        $this->assertEquals(63000, $t2->total_tagihan);
    }
}
