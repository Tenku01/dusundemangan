<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SistemInventarisKeuanganTest extends TestCase
{
    // Jangan gunakan RefreshDatabase pada MySQL lokal pengguna


    public function test_halaman_beranda_publik_dapat_diakses_dan_menampilkan_katalog(): void
    {
        $response = $this->get(route('beranda'));

        $response->assertStatus(200);
        $response->assertSee('Tenda Terop Besi 4x6 Meter');
        $response->assertSee('Saldo Kas Dusun Saat Ini');
    }

    public function test_warga_dapat_mengajukan_pinjaman_tanpa_harus_login(): void
    {
        $barang = Barang::first();

        $response = $this->post(route('peminjaman.ajukan'), [
            'barang_id' => $barang->id,
            'nama_peminjam' => 'Pak Joko',
            'kontak_peminjam' => '081234567899',
            'alamat_rt_rw' => 'RT 03 / RW 01',
            'jumlah_pinjam' => 1,
            'tanggal_pinjam' => now()->format('Y-m-d'),
            'rencana_tanggal_kembali' => now()->addDays(2)->format('Y-m-d'),
            'tujuan_peminjaman' => 'Acara tasyakuran keluarga',
        ]);

        $this->assertDatabaseHas('peminjaman', [
            'barang_id' => $barang->id,
            'nama_peminjam' => 'Pak Joko',
            'status' => 'menunggu',
        ]);

        $peminjaman = Peminjaman::where('nama_peminjam', 'Pak Joko')->first();
        $response->assertRedirect(route('peminjaman.sukses', ['kode' => $peminjaman->kode_pinjam]));
    }

    public function test_warga_dapat_melacak_status_pinjaman_dengan_nomor_tiket(): void
    {
        $peminjaman = Peminjaman::create([
            'kode_pinjam' => 'PJ-TEST-001',
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

        $response = $this->get(route('peminjaman.cek-status', ['q' => 'PJ-TEST-001']));

        $response->assertStatus(200);
        $response->assertSee('PJ-TEST-001');
        $response->assertSee('Ibu Siti');
        $response->assertSee('Menunggu Persetujuan');
    }

    public function test_admin_dapat_menyetujui_pinjaman_dan_stok_tersedia_otomatis_berkurang(): void
    {
        $admin = User::where('peran', 'admin')->first();
        $barang = Barang::first();
        $stokAwal = $barang->jumlah_tersedia;

        $peminjaman = Peminjaman::create([
            'kode_pinjam' => 'PJ-SETUJUI-01',
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

        $response = $this->actingAs($admin)
            ->patch(route('admin.peminjaman.setujui', $peminjaman));

        $response->assertSessionHas('sukses');
        $this->assertEquals('disetujui', $peminjaman->fresh()->status);
        $this->assertEquals($stokAwal - 1, $barang->fresh()->jumlah_tersedia);
    }

    public function test_admin_dapat_mengonfirmasi_pengembalian_dan_stok_tersedia_kembali_semula(): void
    {
        $admin = User::where('peran', 'admin')->first();
        $barang = Barang::first();
        $stokAwal = $barang->jumlah_tersedia;

        // Simulasi barang sudah disetujui (stok sudah berkurang 1)
        $barang->decrement('jumlah_tersedia', 1);

        $peminjaman = Peminjaman::create([
            'kode_pinjam' => 'PJ-KEMBALI-01',
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

        $response = $this->actingAs($admin)
            ->patch(route('admin.peminjaman.kembalikan', $peminjaman));

        $response->assertSessionHas('sukses');
        $this->assertEquals('dikembalikan', $peminjaman->fresh()->status);
        $this->assertEquals($stokAwal, $barang->fresh()->jumlah_tersedia);
    }

    public function test_admin_dapat_mencatat_kas_masuk_dan_keluar(): void
    {
        $admin = User::where('peran', 'admin')->first();

        // Kas Masuk
        $this->actingAs($admin)
            ->post(route('admin.keuangan.store'), [
                'tanggal' => now()->format('Y-m-d'),
                'jenis' => 'masuk',
                'nominal' => 500000,
                'kategori' => 'Donasi Warga',
                'keterangan' => 'Sumbangan warga RT 01',
            ])->assertSessionHas('sukses');

        $this->assertDatabaseHas('keuangan', [
            'nominal' => 500000,
            'jenis' => 'masuk',
            'kategori' => 'Donasi Warga',
        ]);

        // Kas Keluar
        $this->actingAs($admin)
            ->post(route('admin.keuangan.store'), [
                'tanggal' => now()->format('Y-m-d'),
                'jenis' => 'keluar',
                'nominal' => 150000,
                'kategori' => 'Pembelian Lampu',
                'keterangan' => 'Beli lampu jalan pos kamling',
            ])->assertSessionHas('sukses');

        $this->assertDatabaseHas('keuangan', [
            'nominal' => 150000,
            'jenis' => 'keluar',
            'kategori' => 'Pembelian Lampu',
        ]);
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
