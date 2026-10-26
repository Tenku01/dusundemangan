@extends('layouts.publik')

@section('title', 'Katalog Inventaris & Transparansi Kas Dusun Demangan')

@section('content')
<div class="space-y-12 pb-16">

    <!-- 1. Hero Section -->
    <section class="bg-white border-b border-gray-200/80 -mt-4 pt-10 pb-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl">
                <div class="flex items-center gap-3.5 mb-5">
                    <img src="{{ asset('logo-kartar-dmg-circle.png') }}"
                         alt="Logo Karang Taruna Demangan"
                         class="w-12 h-12 sm:w-14 sm:h-14 rounded-full object-cover shadow-xs border-2 border-amber-300 bg-white shrink-0">
                    <div>
                        <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-[#CEA042] border border-amber-200/80">
                            Karang Taruna Dusun Demangan
                        </span>
                        <div class="text-xs text-gray-500 font-medium mt-0.5">Layanan Inventaris & Keuangan Transparan Warga</div>
                    </div>
                </div>
                <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-900 tracking-tight leading-tight mb-4">
                    Peminjaman Inventaris & Transparansi Keuangan Kas Dusun
                </h1>
                <p class="text-base text-gray-600 leading-relaxed mb-6">
                    Mendigitalisasi pencatatan inventaris aset dusun, mempermudah warga meminjam perlengkapan acara dan gotong royong, serta membuka riwayat arus kas masuk dan keluar secara jujur dan transparan.
                </p>
                <div class="flex flex-wrap items-center gap-3">
                    <a href="#katalog" class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-semibold text-white bg-[#CEA042] hover:bg-[#BA8832] rounded-lg shadow-xs transition focus:outline-none focus-visible:ring-2 focus-visible:ring-[#CEA042]">
                        Lihat Katalog Barang
                    </a>
                    <a href="{{ route('peminjaman.cek-status') }}" class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-400">
                        Lacak Status Pinjaman
                    </a>
                    <a href="{{ route('publik.buku-kas') }}" class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium text-emerald-800 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 rounded-lg transition">
                        Buku Kas Dusun
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- 2. Ringkasan Status Transparansi (KPI Cards) -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            <!-- Saldo Kas -->
            <div class="bg-white rounded-xl p-5 border border-gray-200/90 shadow-xs">
                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">
                    Saldo Kas Dusun Saat Ini
                </div>
                <div class="text-2xl sm:text-3xl font-extrabold text-emerald-600 tracking-tight">
                    Rp {{ number_format($saldoKasSaatIni, 0, ',', '.') }}
                </div>
                <div class="mt-2 text-xs text-gray-500 flex items-center justify-between">
                    <span>Masuk: Rp {{ number_format($totalMasuk, 0, ',', '.') }}</span>
                    <span>Keluar: Rp {{ number_format($totalKeluar, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Total Unit Tersedia -->
            <div class="bg-white rounded-xl p-5 border border-gray-200/90 shadow-xs">
                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">
                    Unit Barang Siap Dipinjam
                </div>
                <div class="text-2xl sm:text-3xl font-extrabold text-[#CEA042] tracking-tight">
                    {{ $totalUnitTersedia }} <span class="text-base font-normal text-gray-500">unit</span>
                </div>
                <div class="mt-2 text-xs text-gray-500">
                    Dari total {{ $totalJenisBarang }} jenis inventaris dusun
                </div>
            </div>

            <!-- Prosedur Ringkas Warga -->
            <div class="bg-white rounded-xl p-5 border border-gray-200/90 shadow-xs">
                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">
                    Ketentuan Peminjaman
                </div>
                <div class="text-sm font-semibold text-gray-900 mt-1">
                    Bebas Biaya untuk Warga
                </div>
                <div class="mt-2 text-xs text-gray-500 leading-normal">
                    Pengambilan barang di pos RW setelah permohonan disetujui oleh pengurus.
                </div>
            </div>
        </div>
    </section>

    <!-- 3. Katalog Inventaris Barang -->
    <section id="katalog" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 tracking-tight">
                    Katalog Inventaris Barang
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    Pilih barang yang tersedia dan klik "Pinjam Barang" untuk mengajukan pinjaman.
                </p>
            </div>

            <!-- Form Pencarian & Filter Kategori -->
            <form method="GET" action="{{ route('beranda') }}#katalog" class="flex flex-wrap items-center gap-2">
                <input type="text" name="cari" value="{{ $cari }}" placeholder="Cari nama barang..."
                       class="text-sm rounded-lg border-gray-300 focus:border-[#CEA042] focus:ring-1 focus:ring-[#CEA042] px-3 py-2 w-48 sm:w-56 shadow-xs">

                <select name="kategori" onchange="this.form.submit()"
                        class="text-sm rounded-lg border-gray-300 focus:border-[#CEA042] focus:ring-1 focus:ring-[#CEA042] py-2 pl-3 pr-8 shadow-xs">
                    <option value="Semua">Semua Kategori</option>
                    @foreach($kategoriList as $kat)
                        <option value="{{ $kat }}" {{ $kategoriTerpilih === $kat ? 'selected' : '' }}>
                            {{ $kat }}
                        </option>
                    @endforeach
                </select>

                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-[#CEA042] hover:bg-[#BA8832] rounded-lg shadow-xs transition">
                    Cari
                </button>

                @if($cari || ($kategoriTerpilih && $kategoriTerpilih !== 'Semua'))
                    <a href="{{ route('beranda') }}#katalog" class="text-xs text-rose-600 hover:underline px-2 py-2">
                        Reset Filter
                    </a>
                @endif
            </form>
        </div>

        <!-- Grid Kartu Barang -->
        @if($daftarBarang->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($daftarBarang as $item)
                    <div class="bg-white rounded-xl border border-gray-200/90 shadow-xs flex flex-col justify-between overflow-hidden hover:border-amber-300 transition group">
                        <div class="p-5">
                            <div class="flex items-start justify-between gap-2 mb-3">
                                <span class="text-xs font-semibold px-2.5 py-1 rounded-md bg-gray-100 text-gray-700">
                                    {{ $item->kategori }}
                                </span>
                                @if($item->isTersedia())
                                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Tersedia ({{ $item->jumlah_tersedia }} unit)
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-md bg-rose-50 text-rose-700 border border-rose-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                        Habis Dipinjam
                                    </span>
                                @endif
                            </div>

                            <h3 class="text-lg font-bold text-gray-900 group-hover:text-[#CEA042] transition mb-1 leading-snug">
                                {{ $item->nama_barang }}
                            </h3>
                            <div class="text-xs text-gray-500 font-mono mb-3">
                                Kode: {{ $item->kode_barang }}
                            </div>

                            <p class="text-xs text-gray-600 leading-relaxed mb-4 line-clamp-2">
                                {{ $item->keterangan ?? 'Tidak ada keterangan khusus.' }}
                            </p>

                            <div class="space-y-1.5 text-xs text-gray-500 border-t border-gray-100 pt-3">
                                <div class="flex justify-between">
                                    <span>Total Kepemilikan Dusun:</span>
                                    <span class="font-semibold text-gray-700">{{ $item->jumlah_total }} unit</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Kondisi Barang:</span>
                                    <span class="font-semibold text-gray-700">{{ $item->kondisi }}</span>
                                </div>
                                @if($item->lokasi_penyimpanan)
                                    <div class="flex justify-between">
                                        <span>Lokasi Pos:</span>
                                        <span class="text-gray-700 truncate max-w-[180px]">{{ $item->lokasi_penyimpanan }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Card Action CTA -->
                        <div class="p-5 pt-0">
                            @if($item->isTersedia())
                                <button type="button"
                                        onclick="bukaModalPinjam({{ $item->id }}, '{{ addslashes($item->nama_barang) }}', {{ $item->jumlah_tersedia }})"
                                        class="w-full flex items-center justify-center gap-2 py-2.5 px-4 text-sm font-semibold text-white bg-[#CEA042] hover:bg-[#BA8832] rounded-lg shadow-xs transition focus:outline-none focus-visible:ring-2 focus-visible:ring-[#CEA042]">
                                    Ajukan Pinjam Barang
                                </button>
                            @else
                                <button type="button" disabled
                                        class="w-full py-2.5 px-4 text-sm font-medium text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed text-center">
                                    Sedang Tidak Tersedia
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $daftarBarang->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                <h3 class="text-base font-bold text-gray-900 mb-1">Barang tidak ditemukan</h3>
                <p class="text-xs text-gray-500 mb-4">Coba cari dengan kata kunci lain atau ubah pilihan kategori.</p>
                <a href="{{ route('beranda') }}#katalog" class="inline-flex items-center text-xs font-semibold text-[#CEA042] hover:underline">
                    Tampilkan Semua Barang
                </a>
            </div>
        @endif
    </section>

    <!-- 4. Transparansi Riwayat Kas Dusun -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
            <div>
                <h2 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight leading-tight">
                    Transparansi Buku Kas Terkini
                </h2>
                <p class="text-xs sm:text-sm text-gray-600 mt-1">
                    Riwayat 10 catatan mutasi kas masuk dan keluar terbaru dusun.
                </p>
            </div>
            <a href="{{ route('publik.buku-kas') }}" class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-semibold text-[#CEA042] hover:text-[#BA8832]">
                Buka Buku Kas Selengkapnya
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>

        <!-- TAMPILAN MOBILE KAS TERKINI (block md:hidden) -->
        <div class="block md:hidden space-y-3">
            @forelse($mutasiTerakhir as $kas)
                <div class="bg-white rounded-xl border border-gray-200/90 p-4 shadow-xs space-y-2">
                    <div class="flex items-center justify-between pb-1.5 border-b border-gray-100">
                        <span class="text-[11px] text-gray-500 font-medium">
                            {{ $kas->tanggal->format('d/m/Y') }}
                        </span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $kas->jenis === 'masuk' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                            {{ $kas->jenis === 'masuk' ? 'Kas Masuk' : 'Kas Keluar' }}
                        </span>
                    </div>

                    <div class="flex items-baseline justify-between">
                        <div class="text-xs font-bold text-gray-800">{{ $kas->kategori }}</div>
                        <div class="text-sm font-extrabold {{ $kas->jenis === 'masuk' ? 'text-emerald-700' : 'text-rose-700' }}">
                            {{ $kas->jenis === 'masuk' ? '+' : '-' }} Rp {{ number_format($kas->nominal, 0, ',', '.') }}
                        </div>
                    </div>

                    <p class="text-xs text-gray-600 bg-gray-50 p-2 rounded-lg leading-relaxed">
                        {{ $kas->keterangan }}
                    </p>
                </div>
            @empty
                <div class="bg-white rounded-xl border border-gray-200 p-8 text-center text-gray-400 text-xs">
                    Belum ada catatan mutasi kas.
                </div>
            @endforelse
        </div>

        <!-- TAMPILAN DESKTOP KAS TERKINI (hidden md:block) -->
        <div class="hidden md:block bg-white rounded-xl border border-gray-200/90 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-left text-xs">
                    <thead class="bg-gray-50 text-gray-600 font-semibold uppercase tracking-wider">
                        <tr>
                            <th class="py-3 px-4">Tanggal</th>
                            <th class="py-3 px-4">Kode</th>
                            <th class="py-3 px-4">Jenis</th>
                            <th class="py-3 px-4">Kategori / Sumber</th>
                            <th class="py-3 px-4">Uraian Keterangan</th>
                            <th class="py-3 px-4 text-right">Nominal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($mutasiTerakhir as $kas)
                            <tr class="hover:bg-gray-50/80 transition">
                                <td class="py-3 px-4 whitespace-nowrap text-gray-700 font-medium">
                                    {{ $kas->tanggal->format('d/m/Y') }}
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap text-gray-500 font-mono text-[11px]">
                                    {{ $kas->kode_transaksi }}
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap">
                                    @if($kas->jenis === 'masuk')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Kas Masuk
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                            Kas Keluar
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 font-medium text-gray-800 whitespace-nowrap">
                                    {{ $kas->kategori }}
                                </td>
                                <td class="py-3 px-4 text-gray-600 max-w-xs truncate">
                                    {{ $kas->keterangan }}
                                </td>
                                <td class="py-3 px-4 text-right font-semibold whitespace-nowrap {{ $kas->jenis === 'masuk' ? 'text-emerald-700' : 'text-rose-700' }}">
                                    {{ $kas->jenis === 'masuk' ? '+' : '-' }} Rp {{ number_format($kas->nominal, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-gray-400">
                                    Belum ada catatan mutasi kas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

</div>

<!-- MODAL FORM PENGAJUAN PINJAMAN (Bottom Sheet Ramah HP & Interaktif) -->
<div id="modal-pinjam" class="fixed inset-0 z-50 hidden bg-gray-900/60 backdrop-blur-xs flex items-end sm:items-center justify-center p-0 sm:p-4 overflow-y-auto">
    <div class="bg-white rounded-t-3xl sm:rounded-2xl max-w-lg w-full p-5 sm:p-6 shadow-2xl border border-gray-200 max-h-[92vh] overflow-y-auto">
        <!-- Drag Handle Indicator untuk Mobile Phone -->
        <div class="w-12 h-1.5 bg-gray-300 rounded-full mx-auto mb-3 sm:hidden"></div>

        <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-3">
            <div>
                <h3 class="text-base sm:text-lg font-bold text-gray-900 leading-tight">Formulir Peminjaman Barang</h3>
                <p class="text-xs text-gray-500 mt-0.5">Isi identitas Anda dengan jelas untuk konfirmasi.</p>
            </div>
            <button type="button" onclick="tutupModalPinjam()" class="text-gray-400 hover:text-gray-700 p-2 rounded-xl focus:outline-none min-h-[44px] min-w-[44px] flex items-center justify-center bg-gray-100 sm:bg-transparent">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form action="{{ route('peminjaman.ajukan') }}" method="POST" class="space-y-3.5">
            @csrf
            <input type="hidden" name="barang_id" id="modal-barang-id" value="">

            <!-- Nama Barang Terpilih -->
            <div class="bg-amber-50/90 border border-amber-200 rounded-2xl p-3.5">
                <div class="text-[11px] text-[#9B6C26] font-semibold">Barang yang Akan Dipinjam:</div>
                <div id="modal-nama-barang" class="text-sm sm:text-base font-bold text-gray-900 mt-0.5"></div>
                <div class="text-xs text-gray-600 mt-0.5">Stok siap pakai: <span id="modal-stok-tersedia" class="font-bold text-emerald-700"></span> unit</div>
            </div>

            <!-- Nama Lengkap Peminjam -->
            <div>
                <label for="nama_peminjam" class="block text-xs font-semibold text-gray-800 mb-1">
                    Nama Lengkap Anda <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="nama_peminjam" id="nama_peminjam" required value="{{ old('nama_peminjam') }}"
                       placeholder="Contoh: Pak Joko"
                       class="w-full text-base sm:text-xs rounded-xl border-gray-300 focus:border-[#CEA042] focus:ring-1 focus:ring-[#CEA042] py-2.5 px-3.5 shadow-xs min-h-[46px]">
            </div>

            <!-- No WhatsApp & RT/RW -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label for="kontak_peminjam" class="block text-xs font-semibold text-gray-800 mb-1">
                        Nomor WhatsApp / HP Aktif <span class="text-rose-500">*</span>
                    </label>
                    <input type="tel" inputmode="tel" name="kontak_peminjam" id="kontak_peminjam" required value="{{ old('kontak_peminjam') }}"
                           placeholder="Contoh: 08123456789"
                           class="w-full text-base sm:text-xs rounded-xl border-gray-300 focus:border-[#CEA042] focus:ring-1 focus:ring-[#CEA042] py-2.5 px-3.5 shadow-xs min-h-[46px]">
                </div>
                <div>
                    <label for="alamat_rt_rw" class="block text-xs font-semibold text-gray-800 mb-1">
                        Alamat / RT Dusun <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="alamat_rt_rw" id="alamat_rt_rw" required value="{{ old('alamat_rt_rw') }}"
                           placeholder="Contoh: RT 03 / RW 01"
                           class="w-full text-base sm:text-xs rounded-xl border-gray-300 focus:border-[#CEA042] focus:ring-1 focus:ring-[#CEA042] py-2.5 px-3.5 shadow-xs min-h-[46px]">
                </div>
            </div>

            <!-- Jumlah Pinjam dengan Stepper + dan - Ramah HP -->
            <div>
                <label for="jumlah_pinjam" class="block text-xs font-semibold text-gray-800 mb-1">
                    Jumlah Pinjam (Unit) <span class="text-rose-500">*</span>
                </label>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="ubahJumlah(-1)"
                            class="w-12 h-12 shrink-0 bg-gray-100 hover:bg-gray-200 active:bg-gray-300 text-gray-800 font-bold text-xl rounded-xl flex items-center justify-center transition border border-gray-200 select-none">
                        -
                    </button>
                    <input type="number" inputmode="numeric" name="jumlah_pinjam" id="jumlah_pinjam" required min="1" value="{{ old('jumlah_pinjam', 1) }}"
                           class="flex-1 text-center font-bold text-lg rounded-xl border-gray-300 focus:border-[#CEA042] focus:ring-1 focus:ring-[#CEA042] py-2.5 px-3 shadow-xs min-h-[46px]">
                    <button type="button" onclick="ubahJumlah(1)"
                            class="w-12 h-12 shrink-0 bg-amber-100 hover:bg-amber-200 active:bg-amber-300 text-[#9B6C26] font-bold text-xl rounded-xl flex items-center justify-center transition border border-amber-300 select-none">
                        +
                    </button>
                </div>
            </div>

            <!-- Tanggal Pinjam & Kembali -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label for="tanggal_pinjam" class="block text-xs font-semibold text-gray-800 mb-1">
                        Tanggal Pinjam <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="tanggal_pinjam" id="tanggal_pinjam" required value="{{ old('tanggal_pinjam', date('Y-m-d')) }}"
                           class="w-full text-base sm:text-xs rounded-xl border-gray-300 focus:border-[#CEA042] focus:ring-1 focus:ring-[#CEA042] py-2.5 px-3 shadow-xs min-h-[46px]">
                </div>
                <div>
                    <label for="rencana_tanggal_kembali" class="block text-xs font-semibold text-gray-800 mb-1">
                        Rencana Kembali <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="rencana_tanggal_kembali" id="rencana_tanggal_kembali" required value="{{ old('rencana_tanggal_kembali', date('Y-m-d', strtotime('+1 day'))) }}"
                           class="w-full text-base sm:text-xs rounded-xl border-gray-300 focus:border-[#CEA042] focus:ring-1 focus:ring-[#CEA042] py-2.5 px-3 shadow-xs min-h-[46px]">
                </div>
            </div>

            <!-- Keperluan Peminjaman -->
            <div>
                <label for="tujuan_peminjaman" class="block text-xs font-semibold text-gray-800 mb-1">
                    Keperluan / Acara <span class="text-rose-500">*</span>
                </label>
                <textarea name="tujuan_peminjaman" id="tujuan_peminjaman" rows="2" required
                          placeholder="Misal: Acara Hajatan di Rumah RT 02"
                          class="w-full text-base sm:text-xs rounded-lg border-gray-300 focus:border-[#CEA042] focus:ring-1 focus:ring-[#CEA042] py-2 px-3 shadow-xs">{{ old('tujuan_peminjaman') }}</textarea>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-gray-100">
                <button type="button" onclick="tutupModalPinjam()"
                        class="px-4 py-2.5 text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition min-h-[46px]">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2.5 text-xs font-bold text-white bg-[#CEA042] hover:bg-[#BA8832] rounded-xl shadow-xs transition focus:outline-none min-h-[46px]">
                    Kirim Permohonan Pinjam
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    let stokMaksimal = 1;

    function ubahJumlah(delta) {
        const input = document.getElementById('jumlah_pinjam');
        let current = parseInt(input.value) || 1;
        let baru = current + delta;
        if (baru < 1) baru = 1;
        if (baru > stokMaksimal) baru = stokMaksimal;
        input.value = baru;
    }

    function bukaModalPinjam(id, nama, stok) {
        stokMaksimal = stok;
        document.getElementById('modal-barang-id').value = id;
        document.getElementById('modal-nama-barang').textContent = nama;
        document.getElementById('modal-stok-tersedia').textContent = stok;
        const inputJumlah = document.getElementById('jumlah_pinjam');
        inputJumlah.max = stok;
        inputJumlah.value = 1;
        document.getElementById('modal-pinjam').classList.remove('hidden');
    }

    function tutupModalPinjam() {
        document.getElementById('modal-pinjam').classList.add('hidden');
    }

    // Tutup saat tekan tombol Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            tutupModalPinjam();
        }
    });
</script>
@endpush
@endsection

