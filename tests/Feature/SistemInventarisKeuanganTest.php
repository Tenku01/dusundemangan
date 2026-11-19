<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\Keuangan;
use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SistemInventarisKeuanganTest extends TestCase
{
    // Jangan gunakan RefreshDatabase pada MySQL lokal pengguna
    protected array $createdPeminjamanIds = [];

    protected array $createdKeuanganIds = [];

    protected function tearDown(): void
    {
        if (! empty($this->createdPeminjamanIds)) {
            Peminjaman::whereIn('id', $this->createdPeminjamanIds)->delete();
        }

        if (! empty($this->createdKeuanganIds)) {
            Keuangan::whereIn('id', $this->createdKeuanganIds)->delete();
        }

        // Pastikan stok barang kembali normal jika terpengaruh test
        $barang = Barang::find(43);
        if ($barang && $barang->jumlah_tersedia !== 2) {
            $barang->update(['jumlah_tersedia' => 2]);
        }

        parent::tearDown();
    }

    public function test_halaman_beranda_publik_dapat_diakses_dan_menampilkan_katalog(): void
    {
        $response = $this->get(route('beranda'));

        $response->assertStatus(200);
        $response->assertSee('Tenda Terop Besi 4x6 Meter');
        $response->assertSee('Saldo Kas Dusun Saat Ini');
    }

    public function test_warga_dapat_mengajukan_pinjaman_tanpa_harus_login(): void
    {
        $barang = Barang::find(43) ?: Barang::first();
        if ($barang->jumlah_tersedia < 1) {
            $barang->update(['jumlah_tersedia' => 2]);
        }

        $namaPeminjam = 'Pak Joko '.uniqid();

        $response = $this->post(route('peminjaman.ajukan'), [
            'barang_id' => $barang->id,
            'nama_peminjam' => $namaPeminjam,
            'kontak_peminjam' => '081234567899',
            'alamat_rt_rw' => 'RT 03 / RW 01',
            'jumlah_pinjam' => 1,
            'tanggal_pinjam' => now()->format('Y-m-d'),
            'rencana_tanggal_kembali' => now()->addDays(2)->format('Y-m-d'),
            'tujuan_peminjaman' => 'Acara tasyakuran keluarga',
        ]);

        $this->assertDatabaseHas('peminjaman', [
            'barang_id' => $barang->id,
            'nama_peminjam' => $namaPeminjam,
            'status' => 'menunggu',
        ]);

        $peminjaman = Peminjaman::where('nama_peminjam', $namaPeminjam)->first();
        $this->createdPeminjamanIds[] = $peminjaman->id;
        $response->assertRedirect(route('peminjaman.sukses', ['kode' => $peminjaman->kode_pinjam]));
    }

    public function test_warga_dapat_melacak_status_pinjaman_dengan_nomor_tiket(): void
    {
        $kodePinjam = 'PJ-TEST-'.uniqid();
        $peminjaman = Peminjaman::create([
            'kode_pinjam' => $kodePinjam,
            'barang_id' => Barang::first()->id,
            'nama_peminjam' => 'Ibu Siti',
            'kontak_peminjam' => '08111222333',
            'alamat_rt_rw' => 'RT 01',
            'jumlah_pinjam' => 1,
            'tanggal_pinjam' => now()->format('Y-m-d'),
            'rencana_tanggal_kembali' => now()->addDay()->format('Y-m-d'),
            'tujuan_peminjaman' => 'Pengajian rutin',
            'status' => 'menunggu',
        ]);
        $this->createdPeminjamanIds[] = $peminjaman->id;

        $response = $this->get(route('peminjaman.cek-status', ['q' => $kodePinjam]));

        $response->assertStatus(200);
        $response->assertSee($kodePinjam);
        $response->assertSee('Ibu Siti');
        $response->assertSee('Menunggu Persetujuan');
    }

    public function test_admin_dapat_menyetujui_pinjaman_dan_stok_tersedia_otomatis_berkurang(): void
    {
        $admin = User::where('peran', 'admin')->first();
        $barang = Barang::find(43) ?: Barang::first();
        if ($barang->jumlah_tersedia < 1) {
            $barang->update(['jumlah_tersedia' => 2]);
        }
        $stokAwal = $barang->jumlah_tersedia;

        $kodePinjam = 'PJ-SETUJUI-'.uniqid();
        $peminjaman = Peminjaman::create([
            'kode_pinjam' => $kodePinjam,
            'barang_id' => $barang->id,
            'nama_peminjam' => 'Warga Uji',
            'kontak_peminjam' => '081999888',
            'alamat_rt_rw' => 'RT 04',
            'jumlah_pinjam' => 1,
            'tanggal_pinjam' => now()->format('Y-m-d'),
            'rencana_tanggal_kembali' => now()->addDay()->format('Y-m-d'),
            'tujuan_peminjaman' => 'Kerja bakti',
            'status' => 'menunggu',
        ]);
        $this->createdPeminjamanIds[] = $peminjaman->id;

        $response = $this->actingAs($admin)
            ->patch(route('admin.peminjaman.setujui', $peminjaman));

        $response->assertSessionHas('sukses');
        $this->assertEquals('disetujui', $peminjaman->fresh()->status);
        $this->assertEquals($stokAwal - 1, $barang->fresh()->jumlah_tersedia);
    }

    public function test_admin_dapat_mengonfirmasi_pengembalian_dan_stok_tersedia_kembali_semula(): void
    {
        $admin = User::where('peran', 'admin')->first();
        $barang = Barang::find(43) ?: Barang::first();
        $stokAwal = $barang->jumlah_tersedia;

        // Simulasi barang sudah disetujui (stok sudah berkurang 1)
        if ($barang->jumlah_tersedia > 0) {
            $barang->decrement('jumlah_tersedia', 1);
        }

        $kodePinjam = 'PJ-KEMBALI-'.uniqid();
        $peminjaman = Peminjaman::create([
            'kode_pinjam' => $kodePinjam,
            'barang_id' => $barang->id,
            'nama_peminjam' => 'Warga Kembali',
            'kontak_peminjam' => '081777666',
            'alamat_rt_rw' => 'RT 02',
            'jumlah_pinjam' => 1,
            'tanggal_pinjam' => now()->subDay()->format('Y-m-d'),
            'rencana_tanggal_kembali' => now()->format('Y-m-d'),
            'tujuan_peminjaman' => 'Acara rapat',
            'status' => 'disetujui',
        ]);
        $this->createdPeminjamanIds[] = $peminjaman->id;

        $response = $this->actingAs($admin)
            ->patch(route('admin.peminjaman.kembalikan', $peminjaman));

        $response->assertSessionHas('sukses');
        $this->assertEquals('dikembalikan', $peminjaman->fresh()->status);
        $this->assertEquals($stokAwal, $barang->fresh()->jumlah_tersedia);
    }

    public function test_admin_dapat_mencatat_kas_masuk_dan_keluar(): void
    {
        $admin = User::where('peran', 'admin')->first();
        $kategoriMasuk = 'Donasi Warga '.uniqid();
        $kategoriKeluar = 'Pembelian Lampu '.uniqid();

        // Kas Masuk
        $this->actingAs($admin)
            ->post(route('admin.keuangan.store'), [
                'tanggal' => now()->format('Y-m-d'),
                'jenis' => 'masuk',
                'nominal' => 500000,
                'kategori' => $kategoriMasuk,
                'keterangan' => 'Sumbangan warga RT 01',
            ])->assertSessionHas('sukses');

        $this->assertDatabaseHas('keuangan', [
            'nominal' => 500000,
            'jenis' => 'masuk',
            'kategori' => $kategoriMasuk,
        ]);

        $kasMasuk = Keuangan::where('kategori', $kategoriMasuk)->first();
        if ($kasMasuk) {
            $this->createdKeuanganIds[] = $kasMasuk->id;
        }

        // Kas Keluar
        $this->actingAs($admin)
            ->post(route('admin.keuangan.store'), [
                'tanggal' => now()->format('Y-m-d'),
                'jenis' => 'keluar',
                'nominal' => 150000,
                'kategori' => $kategoriKeluar,
                'keterangan' => 'Beli lampu jalan pos kamling',
            ])->assertSessionHas('sukses');

        $this->assertDatabaseHas('keuangan', [
            'nominal' => 150000,
            'jenis' => 'keluar',
            'kategori' => $kategoriKeluar,
        ]);

        $kasKeluar = Keuangan::where('kategori', $kategoriKeluar)->first();
        if ($kasKeluar) {
            $this->createdKeuanganIds[] = $kasKeluar->id;
        }
    }

    public function test_superadmin_dapat_mengakses_halaman_manajemen_pengurus(): void
    {
        $superadmin = User::where('peran', 'superadmin')->first();

        $response = $this->actingAs($superadmin)
            ->get(route('admin.pengguna.index'));

        $response->assertStatus(200);
        $response->assertSee('Manajemen Akun Pengurus');
    }
}
