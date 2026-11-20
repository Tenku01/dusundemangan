@extends('layouts.publik')

@section('title', 'Dusun Demangan')

@section('content')
<div class="space-y-10 sm:space-y-12 pb-16">

    <!-- 1. Hero Section -->
    <section class="bg-white border-b border-emas-200/70 pt-8 sm:pt-12 pb-10 sm:pb-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl">
                <div class="flex items-center gap-3.5 mb-5">
                    <img src="{{ asset('logo-kartar-dmg-circle.png') }}"
                         alt="Logo Dusun Demangan"
                         width="56" height="56"
                         fetchpriority="high"
                         class="w-12 h-12 sm:w-14 sm:h-14 rounded-full object-cover shadow-sm border-2 border-emas-300 bg-white shrink-0">
                    <div>
                        <div class="text-xs font-bold text-emas-800 uppercase tracking-wider">Karang Taruna Dusun Demangan</div>
                        <div class="text-xs text-gray-500 font-medium mt-0.5">Portal Layanan &amp; Informasi Warga</div>
                    </div>
                </div>
                <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-900 tracking-tight leading-tight mb-3.5">
                    Dusun Demangan
                </h1>
                <p class="text-sm sm:text-base text-gray-600 leading-relaxed mb-6 max-w-2xl">
                    Pusat keterbukaan informasi publik, cek tagihan listrik, dan transparansi keuangan kas Dusun Demangan. Warga dapat memantau mutasi kas masuk dan keluar serta mengajukan pinjaman inventaris secara terbuka.
                </p>
                <div class="flex flex-wrap items-center gap-2.5 sm:gap-3">
                    <a href="#transparansi-kas" class="btn-primary px-5 py-2.5 text-sm">
                        Transparansi Kas Dusun
                    </a>
                    <a href="{{ route('publik.buku-kas') }}" class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-semibold text-emerald-800 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 rounded-xl transition min-h-[44px]">
                        Buku Kas Lengkap
                    </a>
                    <a href="#katalog" class="btn-secondary px-5 py-2.5 text-sm">
                        Katalog Inventaris
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- 2. Ringkasan Status Transparansi (KPI Cards) -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-5">
            <!-- Saldo Kas -->
            <div class="card-surface p-5 border-t-2 border-t-emerald-500">
                <div class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">
                    Saldo Kas Dusun Saat Ini
                </div>
                <div class="text-2xl sm:text-3xl font-extrabold text-emerald-700 tracking-tight tabular-money">
                    Rp {{ number_format($saldoKasSaatIni, 0, ',', '.') }}
                </div>
                <div class="mt-2.5 pt-2.5 border-t border-gray-100 text-xs text-gray-600 flex items-center justify-between gap-2 tabular-money">
                    <span>Masuk: <strong class="text-emerald-700">Rp {{ number_format($totalMasuk, 0, ',', '.') }}</strong></span>
                    <span>Keluar: <strong class="text-rose-700">Rp {{ number_format($totalKeluar, 0, ',', '.') }}</strong></span>
                </div>
            </div>

            <!-- Total Unit Tersedia -->
            <div class="card-surface p-5 border-t-2 border-t-[#CEA042]">
                <div class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">
                    Unit Barang Siap Dipinjam
                </div>
                <div class="text-2xl sm:text-3xl font-extrabold text-emas-800 tracking-tight tabular-money">
                    {{ $totalUnitTersedia }} <span class="text-sm font-medium text-gray-500">unit</span>
                </div>
                <div class="mt-2.5 pt-2.5 border-t border-gray-100 text-xs text-gray-600">
                    Dari total <strong class="text-gray-800 tabular-money">{{ $totalJenisBarang }}</strong> jenis inventaris dusun
                </div>
            </div>

            <!-- Prosedur Ringkas Warga -->
            <div class="card-surface p-5 border-t-2 border-t-gray-300">
                <div class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">
                    Ketentuan Peminjaman
                </div>
                <div class="text-base font-bold text-gray-900 mt-1">
                    Bebas Biaya untuk Warga
                </div>
                <div class="mt-2 text-xs text-gray-600 leading-relaxed">
                    Pengambilan barang di pos RW setelah permohonan disetujui oleh pengurus.
                </div>
            </div>
        </div>
    </section>

    <!-- 3. Transparansi Riwayat Kas Dusun -->
    <section id="transparansi-kas" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-3 mb-5">
            <div>
                <h2 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight leading-tight">
                    Transparansi Buku Kas Terkini
                </h2>
                <p class="text-xs sm:text-sm text-gray-600 mt-1">
                    Riwayat 10 catatan mutasi kas masuk dan keluar terbaru dusun.
                </p>
            </div>
            <a href="{{ route('publik.buku-kas') }}" class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-bold text-emas-800 hover:text-emas-950 hover:underline py-1">
                <span>Buka Buku Kas Selengkapnya</span>
                <svg class="w-4 h-4" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>

        <!-- TAMPILAN MOBILE KAS TERKINI -->
        <div class="block md:hidden space-y-3">
            @forelse($mutasiTerakhir as $kas)
                <div class="card-surface p-4 space-y-2.5">
                    <div class="flex items-center justify-between pb-2 border-b border-gray-100">
                        <span class="text-xs text-gray-500 font-medium tabular-money">
                            {{ $kas->tanggal->format('d/m/Y') }}
                        </span>
                        <span class="px-2.5 py-0.5 rounded-md text-[11px] font-bold {{ $kas->jenis === 'masuk' ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-rose-50 text-rose-800 border border-rose-200' }}">
                            {{ $kas->jenis === 'masuk' ? 'Kas Masuk' : 'Kas Keluar' }}
                        </span>
                    </div>

                    <div class="flex items-baseline justify-between gap-2">
                        <div class="text-xs font-bold text-gray-900">{{ $kas->kategori }}</div>
                        <div class="text-sm font-extrabold tabular-money shrink-0 {{ $kas->jenis === 'masuk' ? 'text-emerald-700' : 'text-rose-700' }}">
                            {{ $kas->jenis === 'masuk' ? '+' : '-' }} Rp {{ number_format($kas->nominal, 0, ',', '.') }}
                        </div>
                    </div>

                    <p class="text-xs text-gray-600 bg-[#F8F7F4] p-2.5 rounded-xl leading-relaxed border border-gray-100">
                        {{ $kas->keterangan }}
                    </p>
                </div>
            @empty
                <div class="card-surface p-8 text-center text-gray-500 text-xs">
                    Belum ada catatan mutasi kas.
                </div>
            @endforelse
        </div>

        <!-- TAMPILAN DESKTOP KAS TERKINI -->
        <div class="hidden md:block card-surface overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-left text-xs">
                    <thead class="bg-[#F8F7F4] text-gray-600 font-bold uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="py-3.5 px-4">Tanggal</th>
                            <th class="py-3.5 px-4">Kode</th>
                            <th class="py-3.5 px-4">Jenis</th>
                            <th class="py-3.5 px-4">Kategori / Sumber</th>
                            <th class="py-3.5 px-4">Uraian Keterangan</th>
                            <th class="py-3.5 px-4 text-right">Nominal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($mutasiTerakhir as $kas)
                            <tr class="hover:bg-emas-50/40 transition">
                                <td class="py-3.5 px-4 whitespace-nowrap text-gray-700 font-medium tabular-money">
                                    {{ $kas->tanggal->format('d/m/Y') }}
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap text-gray-500 font-mono text-[11px]">
                                    {{ $kas->kode_transaksi }}
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    @if($kas->jenis === 'masuk')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[11px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                            Kas Masuk
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[11px] font-semibold bg-rose-50 text-rose-800 border border-rose-200">
                                            Kas Keluar
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 font-semibold text-gray-900 whitespace-nowrap">
                                    {{ $kas->kategori }}
                                </td>
                                <td class="py-3.5 px-4 text-gray-600 max-w-xs truncate">
                                    {{ $kas->keterangan }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-bold whitespace-nowrap tabular-money {{ $kas->jenis === 'masuk' ? 'text-emerald-700' : 'text-rose-700' }}">
                                    {{ $kas->jenis === 'masuk' ? '+' : '-' }} Rp {{ number_format($kas->nominal, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-10 text-center text-gray-500">
                                    Belum ada catatan mutasi kas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- 4. Katalog Inventaris Barang -->
    <section id="katalog" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-4 mb-6">
            <div>
                <h2 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">
                    Katalog Inventaris Barang
                </h2>
                <p class="text-xs sm:text-sm text-gray-600 mt-1">
                    Pilih barang yang tersedia dan klik "Ajukan Pinjam Barang" untuk mengajukan pinjaman.
                </p>
            </div>

            <!-- Form Pencarian & Filter Kategori -->
            <form method="GET" action="{{ route('beranda') }}#katalog" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                <input type="text" name="cari" value="{{ $cari }}" placeholder="Cari nama barang..."
                       aria-label="Cari nama barang"
                       class="input-field sm:w-56">

                <select name="kategori" onchange="this.form.submit()"
                        aria-label="Filter kategori barang"
                        class="select-field sm:w-48">
                    <option value="Semua">Semua Kategori</option>
                    @foreach($kategoriList as $kat)
                        <option value="{{ $kat }}" {{ $kategoriTerpilih === $kat ? 'selected' : '' }}>
                            {{ $kat }}
                        </option>
                    @endforeach
                </select>

                <button type="submit" class="btn-primary px-5 py-2.5 text-xs sm:text-sm shrink-0">
                    Cari
                </button>

                @if($cari || ($kategoriTerpilih && $kategoriTerpilih !== 'Semua'))
                    <a href="{{ route('beranda') }}#katalog" class="btn-danger-soft px-3.5 text-xs shrink-0">
                        Reset Filter
                    </a>
                @endif
            </form>
        </div>

        <!-- Grid Kartu Barang -->
        @if($daftarBarang->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($daftarBarang as $item)
                    <div class="card-surface flex flex-col justify-between overflow-hidden hover:border-emas-400 transition group">
                        <div class="p-5">
                            <div class="flex items-start justify-between gap-2 mb-3">
                                <span class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-gray-100 text-gray-700">
                                    {{ $item->kategori }}
                                </span>
                                @if($item->isTersedia())
                                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200 tabular-money">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600" aria-hidden="true"></span>
                                        Tersedia ({{ $item->jumlah_tersedia }} unit)
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-lg bg-rose-50 text-rose-800 border border-rose-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-600" aria-hidden="true"></span>
                                        Habis Dipinjam
                                    </span>
                                @endif
                            </div>

                            <h3 class="text-lg font-bold text-gray-900 group-hover:text-emas-800 transition mb-1 leading-snug">
                                {{ $item->nama_barang }}
                            </h3>
                            <div class="text-xs text-gray-500 font-mono mb-3">
                                Kode: {{ $item->kode_barang }}
                            </div>

                            <p class="text-xs text-gray-600 leading-relaxed mb-4 line-clamp-2">
                                {{ $item->keterangan ?? 'Tidak ada keterangan khusus.' }}
                            </p>

                            <div class="space-y-1.5 text-xs text-gray-600 border-t border-gray-100 pt-3">
                                <div class="flex justify-between gap-2">
                                    <span>Total Kepemilikan Dusun:</span>
                                    <span class="font-semibold text-gray-900 tabular-money">{{ $item->jumlah_total }} unit</span>
                                </div>
                                <div class="flex justify-between gap-2">
                                    <span>Kondisi Barang:</span>
                                    <span class="font-semibold text-gray-900">{{ $item->kondisi }}</span>
                                </div>
                                @if($item->lokasi_penyimpanan)
                                    <div class="flex justify-between gap-2">
                                        <span>Lokasi Pos:</span>
                                        <span class="text-gray-800 font-medium truncate max-w-[180px]">{{ $item->lokasi_penyimpanan }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Card Action CTA -->
                        <div class="p-5 pt-0">
                            @if($item->isTersedia())
                                <button type="button"
                                        onclick="bukaModalPinjam({{ $item->id }}, '{{ addslashes($item->nama_barang) }}', {{ $item->jumlah_tersedia }})"
                                        class="btn-primary w-full text-xs sm:text-sm">
                                    Ajukan Pinjam Barang
                                </button>
                            @else
                                <button type="button" disabled
                                        class="w-full py-2.5 px-4 text-xs sm:text-sm font-medium text-gray-500 bg-gray-100 rounded-xl border border-gray-200 min-h-[44px]">
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
            <div class="card-surface p-10 sm:p-12 text-center">
                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                <h3 class="text-base font-bold text-gray-900 mb-1">Barang tidak ditemukan</h3>
                <p class="text-xs text-gray-600 mb-4">Coba cari dengan kata kunci lain atau ubah pilihan kategori.</p>
                <a href="{{ route('beranda') }}#katalog" class="btn-gold-soft px-4 text-xs">
                    Tampilkan Semua Barang
                </a>
            </div>
        @endif
    </section>

</div>

<!-- MODAL FORM PENGAJUAN PINJAMAN -->
<div id="modal-pinjam"
     onclick="if (event.target === this) tutupModalPinjam()"
     role="dialog"
     aria-modal="true"
     aria-labelledby="modal-pinjam-title"
     class="modal-backdrop hidden">
    <div class="modal-panel max-w-lg p-5 sm:p-6">
        <div class="w-12 h-1.5 bg-gray-300 rounded-full mx-auto mb-3 sm:hidden"></div>

        <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
            <div>
                <h3 id="modal-pinjam-title" class="text-base sm:text-lg font-bold text-gray-900 leading-tight">Formulir Peminjaman Barang</h3>
                <p class="text-xs text-gray-600 mt-0.5">Isi identitas Anda dengan jelas untuk konfirmasi.</p>
            </div>
            <button type="button"
                    onclick="tutupModalPinjam()"
                    aria-label="Tutup formulir peminjaman"
                    class="text-gray-500 hover:text-gray-800 p-2 rounded-xl min-h-[44px] min-w-[44px] flex items-center justify-center bg-gray-100 sm:bg-transparent hover:bg-gray-100 transition">
                <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form action="{{ route('peminjaman.ajukan') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="barang_id" id="modal-barang-id" value="">

            <!-- Nama Barang Terpilih -->
            <div class="bg-emas-50 border border-emas-200 rounded-2xl p-3.5">
                <div class="text-xs text-emas-800 font-semibold">Barang yang Akan Dipinjam:</div>
                <div id="modal-nama-barang" class="text-sm sm:text-base font-bold text-gray-900 mt-0.5"></div>
                <div class="text-xs text-gray-700 mt-0.5">Stok siap pakai: <span id="modal-stok-tersedia" class="font-bold text-emerald-700 tabular-money"></span> unit</div>
            </div>

            <!-- Nama Lengkap Peminjam -->
            <div>
                <label for="nama_peminjam" class="block text-xs font-semibold text-gray-800 mb-1">
                    Nama Lengkap Anda <span class="text-rose-600">*</span>
                </label>
                <input type="text" name="nama_peminjam" id="nama_peminjam" required value="{{ old('nama_peminjam') }}"
                       placeholder="Contoh: Pak Joko"
                       class="input-field">
            </div>

            <!-- No WhatsApp & RT/RW -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label for="kontak_peminjam" class="block text-xs font-semibold text-gray-800 mb-1">
                        Nomor WhatsApp / HP Aktif <span class="text-rose-600">*</span>
                    </label>
                    <input type="tel" inputmode="tel" name="kontak_peminjam" id="kontak_peminjam" required value="{{ old('kontak_peminjam') }}"
                           placeholder="Contoh: 08123456789"
                           class="input-field">
                </div>
                <div>
                    <label for="alamat_rt_rw" class="block text-xs font-semibold text-gray-800 mb-1">
                        Alamat / RT Dusun <span class="text-rose-600">*</span>
                    </label>
                    <input type="text" name="alamat_rt_rw" id="alamat_rt_rw" required value="{{ old('alamat_rt_rw') }}"
                           placeholder="Contoh: RT 03 / RW 01"
                           class="input-field">
                </div>
            </div>

            <!-- Jumlah Pinjam dengan Stepper -->
            <div>
                <label for="jumlah_pinjam" class="block text-xs font-semibold text-gray-800 mb-1">
                    Jumlah Pinjam (Unit) <span class="text-rose-600">*</span>
                </label>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="ubahJumlah(-1)" aria-label="Kurangi jumlah pinjam"
                            class="btn-subtle w-12 h-12 shrink-0 font-bold text-xl border border-gray-200 px-0">
                        -
                    </button>
                    <input type="number" inputmode="numeric" name="jumlah_pinjam" id="jumlah_pinjam" required min="1" value="{{ old('jumlah_pinjam', 1) }}"
                           class="input-field flex-1 text-center font-bold text-lg tabular-money">
                    <button type="button" onclick="ubahJumlah(1)" aria-label="Tambah jumlah pinjam"
                            class="btn-gold-soft w-12 h-12 shrink-0 font-bold text-xl px-0">
                        +
                    </button>
                </div>
            </div>

            <!-- Tanggal Pinjam & Kembali -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label for="tanggal_pinjam" class="block text-xs font-semibold text-gray-800 mb-1">
                        Tanggal Pinjam <span class="text-rose-600">*</span>
                    </label>
                    <input type="date" name="tanggal_pinjam" id="tanggal_pinjam" required value="{{ old('tanggal_pinjam', date('Y-m-d')) }}"
                           class="input-field">
                </div>
                <div>
                    <label for="rencana_tanggal_kembali" class="block text-xs font-semibold text-gray-800 mb-1">
                        Rencana Kembali <span class="text-rose-600">*</span>
                    </label>
                    <input type="date" name="rencana_tanggal_kembali" id="rencana_tanggal_kembali" required value="{{ old('rencana_tanggal_kembali', date('Y-m-d', strtotime('+1 day'))) }}"
                           class="input-field">
                </div>
            </div>

            <!-- Keperluan Peminjaman -->
            <div>
                <label for="tujuan_peminjaman" class="block text-xs font-semibold text-gray-800 mb-1">
                    Keperluan / Acara <span class="text-rose-600">*</span>
                </label>
                <textarea name="tujuan_peminjaman" id="tujuan_peminjaman" rows="2" required
                          placeholder="Misal: Acara Hajatan di Rumah RT 02"
                          class="input-field py-2.5">{{ old('tujuan_peminjaman') }}</textarea>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-gray-100">
                <button type="button" onclick="tutupModalPinjam()" class="btn-subtle px-4 text-xs">
                    Batal
                </button>
                <button type="submit" class="btn-primary px-5 text-xs">
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

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            tutupModalPinjam();
        }
    });
</script>
@endpush
@endsection
