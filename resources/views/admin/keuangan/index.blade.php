@extends('layouts.admin')

@section('title', 'Buku Kas Utama Pengurus')

@section('content')
<div class="space-y-4 sm:space-y-6">

    <!-- Header & Action -->
    <div class="card-surface p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-lg sm:text-2xl font-bold text-gray-900 tracking-tight leading-tight">
                Buku Kas Keuangan Dusun
            </h1>
            <p class="text-xs text-gray-600 mt-0.5">
                Kelola Kas Karang Taruna, Kas Listrik warga yang ditahan, serta pos kas lainnya.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2 self-stretch sm:self-auto">
            @if($saldoListrik > 0)
                <button type="button" onclick="bukaModalSetorListrik()"
                        class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-1.5 px-3.5 py-2.5 text-xs font-bold text-slate-900 bg-slate-100 hover:bg-slate-200 border border-slate-300 rounded-xl transition min-h-[44px]">
                    <svg class="w-4 h-4 text-slate-700" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    <span class="tabular-money">Setor Kas Listrik (Rp {{ number_format($saldoListrik, 0, ',', '.') }})</span>
                </button>
            @endif

            <a href="{{ route('admin.keuangan.laporan') }}" class="btn-secondary px-3.5 text-xs">
                Laporan
            </a>
            <button type="button" onclick="bukaModalKas()" class="btn-primary px-4 text-xs">
                <svg class="w-4 h-4" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Catat Kas</span>
            </button>
        </div>
    </div>

    <!-- Empat Kartu Ringkasan Saldo per Kategori Kas -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <!-- Saldo Kas Karang Taruna -->
        <div class="bg-gradient-to-br from-emas-50 to-white rounded-2xl p-4 sm:p-5 border border-emas-300 shadow-sm">
            <div class="flex items-center justify-between mb-1.5">
                <span class="text-[11px] font-bold text-emas-800 uppercase tracking-wider">Kas Karang Taruna</span>
                <span class="w-7 h-7 rounded-lg bg-emas-100 text-emas-800 border border-emas-200 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <div class="text-xl sm:text-2xl font-extrabold text-gray-900 tracking-tight tabular-money">
                Rp {{ number_format($saldoKartar, 0, ',', '.') }}
            </div>
            <p class="text-[11px] text-gray-600 mt-1">Kas operasional &amp; kegiatan pemuda</p>
        </div>

        <!-- Saldo Kas Listrik (Ditahan Sementara) -->
        <div class="bg-gradient-to-br from-slate-100/80 to-white rounded-2xl p-4 sm:p-5 border border-slate-300 shadow-sm">
            <div class="flex items-center justify-between mb-1.5">
                <span class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Kas Listrik (Tertahan)</span>
                <span class="w-7 h-7 rounded-lg bg-slate-200 text-slate-800 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </span>
            </div>
            <div class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight tabular-money">
                Rp {{ number_format($saldoListrik, 0, ',', '.') }}
            </div>
            <div class="flex items-center justify-between mt-1 gap-2">
                <span class="text-[11px] text-gray-600">Ditahan sebelum disetor</span>
                @if($saldoListrik > 0)
                    <button type="button" onclick="bukaModalSetorListrik()" class="text-xs font-bold text-emas-800 hover:underline">
                        Setor &rarr;
                    </button>
                @endif
            </div>
        </div>

        <!-- Saldo Total Gabungan -->
        <div class="card-surface p-4 sm:p-5">
            <div class="flex items-center justify-between mb-1.5">
                <span class="text-[11px] font-bold text-gray-600 uppercase tracking-wider">Total Seluruh Kas</span>
                <span class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <div class="text-xl sm:text-2xl font-extrabold text-emerald-700 tracking-tight tabular-money">
                Rp {{ number_format($saldoKasSaatIni, 0, ',', '.') }}
            </div>
            <p class="text-[11px] text-gray-600 mt-1">Total riil seluruh saldo pos kas</p>
        </div>

        <!-- Total Arus Kas -->
        <div class="card-surface p-4 sm:p-5 flex flex-col justify-between">
            <span class="text-[11px] font-bold text-gray-600 uppercase tracking-wider block mb-2">Arus Pemasukan / Belanja</span>
            <div class="space-y-1.5 text-xs font-semibold">
                <div class="flex items-center justify-between text-emerald-800">
                    <span>Masuk:</span>
                    <span class="font-bold font-mono tabular-money">Rp {{ number_format($totalMasuk, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between text-rose-700">
                    <span>Keluar:</span>
                    <span class="font-bold font-mono tabular-money">Rp {{ number_format($totalKeluar, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="card-surface p-3.5 sm:p-4">
        <form method="GET" action="{{ route('admin.keuangan.index') }}" class="flex flex-col lg:flex-row items-stretch lg:items-center gap-2.5">
            <div class="flex-1">
                <label for="cari-kas" class="sr-only">Cari uraian kas, pos, atau kode</label>
                <input type="text" id="cari-kas" name="cari" value="{{ $cari }}" placeholder="Cari uraian kas, pos, kode..."
                       class="input-field">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                <select name="kategori_kas" aria-label="Filter kategori kas" onchange="this.form.submit()" class="select-field">
                    <option value="">Semua Kategori Kas</option>
                    @foreach($daftarKategoriKas as $kKas)
                        <option value="{{ $kKas }}" {{ ($kategoriKas ?? '') === $kKas ? 'selected' : '' }}>
                            {{ $kKas }}
                        </option>
                    @endforeach
                </select>

                <select name="jenis" aria-label="Filter arus kas" onchange="this.form.submit()" class="select-field">
                    <option value="">Semua Arus</option>
                    <option value="masuk" {{ $jenis === 'masuk' ? 'selected' : '' }}>Kas Masuk (+)</option>
                    <option value="keluar" {{ $jenis === 'keluar' ? 'selected' : '' }}>Kas Keluar (-)</option>
                </select>

                <select name="bulan" aria-label="Filter bulan" onchange="this.form.submit()" class="select-field">
                    <option value="">Semua Bulan</option>
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                            {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                        </option>
                    @endfor
                </select>
            </div>

            <button type="submit" class="btn-primary px-5 text-xs shrink-0">
                Filter
            </button>

            @if($cari || $jenis || $bulan || $kategoriKas)
                <a href="{{ route('admin.keuangan.index') }}" class="btn-danger-soft text-xs shrink-0">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- TAMPILAN MOBILE: KARTU KAS -->
    <div class="block md:hidden space-y-3.5">
        @forelse($daftarKas as $k)
            <div class="card-surface p-4 space-y-2.5">
                <div class="flex items-center justify-between pb-2 border-b border-gray-100">
                    <span class="text-xs text-gray-600 font-medium tabular-money">
                        {{ $k->tanggal->format('d/m/Y') }} &bull; <span class="font-mono text-[11px]">{{ $k->kode_transaksi }}</span>
                    </span>
                    <span class="px-2 py-0.5 rounded-md text-[11px] font-bold {{ $k->jenis === 'masuk' ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-rose-50 text-rose-800 border border-rose-200' }}">
                        {{ $k->jenis === 'masuk' ? 'Kas Masuk' : 'Kas Keluar' }}
                    </span>
                </div>

                <div class="space-y-1.5">
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-bold {{ $k->kategori_kas === \App\Models\Keuangan::KAS_LISTRIK ? 'bg-slate-100 text-slate-800 border border-slate-300' : ($k->kategori_kas === \App\Models\Keuangan::KAS_KARANG_TARUNA ? 'bg-emas-100 text-emas-900 border border-emas-200' : 'bg-sky-50 text-sky-800 border border-sky-200') }}">
                            {{ $k->kategori_kas }}
                        </span>
                        <span class="text-xs font-bold text-gray-900">{{ $k->kategori }}</span>
                    </div>
                    <div class="text-base font-extrabold tabular-money {{ $k->jenis === 'masuk' ? 'text-emerald-700' : 'text-rose-700' }}">
                        {{ $k->jenis === 'masuk' ? '+' : '-' }} Rp {{ number_format($k->nominal, 0, ',', '.') }}
                    </div>
                </div>

                <p class="text-xs text-gray-600 bg-[#F8F7F4] p-2.5 rounded-xl leading-relaxed border border-gray-100">
                    {{ $k->keterangan }}
                </p>

                <div class="flex items-center justify-between pt-1 text-xs text-gray-500">
                    <span>Oleh: {{ $k->pencatat->name ?? '-' }}</span>

                    <form action="{{ route('admin.keuangan.destroy', $k) }}" method="POST"
                          onsubmit="return confirm('Hapus catatan transaksi {{ $k->kode_transaksi }}? Saldo akan disesuaikan kembali.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-rose-700 hover:bg-rose-50 font-semibold py-1.5 px-2.5 rounded-lg min-h-[36px] transition">
                            Hapus Transaksi
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="card-surface p-8 text-center text-gray-500 text-xs">
                Belum ada transaksi kas pada filter ini.
            </div>
        @endforelse
    </div>

    <!-- TAMPILAN DESKTOP: TABEL PENUH -->
    <div class="hidden md:block card-surface overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-left text-xs">
                <thead class="bg-[#F8F7F4] text-gray-600 font-bold uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="py-3.5 px-4">Tanggal</th>
                        <th class="py-3.5 px-4">Kode Transaksi</th>
                        <th class="py-3.5 px-4">Jenis</th>
                        <th class="py-3.5 px-4">Kategori Kas</th>
                        <th class="py-3.5 px-4">Pos / Uraian</th>
                        <th class="py-3.5 px-4">Keterangan</th>
                        <th class="py-3.5 px-4">Dicatat Oleh</th>
                        <th class="py-3.5 px-4 text-right">Nominal</th>
                        <th class="py-3.5 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($daftarKas as $k)
                        <tr class="hover:bg-emas-50/40 transition">
                            <td class="py-3.5 px-4 whitespace-nowrap text-gray-700 font-medium tabular-money">
                                {{ $k->tanggal->format('d/m/Y') }}
                            </td>
                            <td class="py-3.5 px-4 font-mono font-bold text-gray-700 whitespace-nowrap">
                                {{ $k->kode_transaksi }}
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded-md text-[11px] font-bold {{ $k->jenis === 'masuk' ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-rose-50 text-rose-800 border border-rose-200' }}">
                                    {{ $k->jenis === 'masuk' ? 'Kas Masuk' : 'Kas Keluar' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-bold {{ $k->kategori_kas === \App\Models\Keuangan::KAS_LISTRIK ? 'bg-slate-100 text-slate-800 border border-slate-300' : ($k->kategori_kas === \App\Models\Keuangan::KAS_KARANG_TARUNA ? 'bg-emas-100 text-emas-900 border border-emas-200' : 'bg-sky-50 text-sky-800 border border-sky-200') }}">
                                    {{ $k->kategori_kas }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 font-semibold text-gray-900 whitespace-nowrap">
                                {{ $k->kategori }}
                            </td>
                            <td class="py-3.5 px-4 text-gray-600 max-w-xs">
                                {{ $k->keterangan }}
                            </td>
                            <td class="py-3.5 px-4 text-gray-600 whitespace-nowrap">
                                {{ $k->pencatat->name ?? '-' }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-bold whitespace-nowrap tabular-money {{ $k->jenis === 'masuk' ? 'text-emerald-700' : 'text-rose-700' }}">
                                {{ $k->jenis === 'masuk' ? '+' : '-' }} Rp {{ number_format($k->nominal, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <form action="{{ route('admin.keuangan.destroy', $k) }}" method="POST"
                                      onsubmit="return confirm('Hapus catatan kas {{ $k->kode_transaksi }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2.5 py-1 text-xs font-semibold text-rose-700 hover:bg-rose-50 rounded-lg min-h-[36px] transition">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-12 text-center text-gray-500">
                                Belum ada catatan transaksi kas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="pt-2">
        {{ $daftarKas->links() }}
    </div>

</div>

<!-- MODAL CATAT KAS BARU -->
<div id="modal-kas"
     onclick="if (event.target === this) tutupModalKas()"
     role="dialog"
     aria-modal="true"
     aria-labelledby="modal-kas-title"
     class="modal-backdrop hidden">
    <div class="modal-panel max-w-lg p-5 sm:p-6">
        <div class="w-12 h-1.5 bg-gray-300 rounded-full mx-auto mb-3 sm:hidden"></div>

        <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
            <div>
                <h3 id="modal-kas-title" class="text-base font-bold text-gray-900 leading-tight">Catat Transaksi Kas</h3>
                <p class="text-xs text-gray-600 mt-0.5">Catat arus kas masuk atau pengeluaran dana warga.</p>
            </div>
            <button type="button" onclick="tutupModalKas()" aria-label="Tutup modal catat kas"
                    class="text-gray-500 hover:text-gray-800 p-2 rounded-xl min-h-[44px] min-w-[44px] flex items-center justify-center bg-gray-100 sm:bg-transparent hover:bg-gray-100 transition">
                <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form action="{{ route('admin.keuangan.store') }}" method="POST" class="space-y-3.5">
            @csrf

            <div>
                <label for="kas-kategori-kas" class="block text-xs font-semibold text-gray-800 mb-1">
                    Kategori Kas (Rekening Kas) <span class="text-rose-600">*</span>
                </label>
                <input type="text"
                       id="kas-kategori-kas"
                       name="kategori_kas"
                       required
                       value="{{ \App\Models\Keuangan::KAS_KARANG_TARUNA }}"
                       placeholder="Pilih atau ketik kategori kas..."
                       list="list-kategori-kas"
                       class="input-field">
                <datalist id="list-kategori-kas">
                    @foreach($daftarKategoriKas as $kKas)
                        <option value="{{ $kKas }}">
                    @endforeach
                </datalist>
                <p class="text-[11px] text-gray-500 mt-1">
                    Pilih kas (Kas Karang Taruna, Kas Listrik) atau ketik nama rekening kas baru untuk membuat kategori sendiri.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label for="kas-jenis" class="block text-xs font-semibold text-gray-800 mb-1">Jenis Arus Kas <span class="text-rose-600">*</span></label>
                    <select id="kas-jenis" name="jenis" required class="select-field">
                        <option value="masuk">Kas Masuk (Penerimaan / Iuran)</option>
                        <option value="keluar">Kas Keluar (Belanja / Operasional)</option>
                    </select>
                </div>
                <div>
                    <label for="kas-tanggal" class="block text-xs font-semibold text-gray-800 mb-1">Tanggal Transaksi <span class="text-rose-600">*</span></label>
                    <input type="date" id="kas-tanggal" name="tanggal" required value="{{ date('Y-m-d') }}" class="input-field">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label for="kas-nominal" class="block text-xs font-semibold text-gray-800 mb-1">Nominal (Rupiah) <span class="text-rose-600">*</span></label>
                    <div class="input-addon-group">
                        <span class="flex items-center justify-center bg-gray-100 px-3.5 text-gray-600 font-bold text-xs select-none border-r border-gray-200 shrink-0">
                            Rp
                        </span>
                        <input type="text"
                               id="kas-nominal"
                               inputmode="numeric"
                               name="nominal"
                               required
                               placeholder="Contoh: 250.000"
                               class="input-rupiah w-full text-base sm:text-sm font-bold text-gray-900 border-0 focus:ring-0 focus:outline-none py-2.5 px-3 bg-transparent tabular-money">
                    </div>
                </div>
                <div>
                    <label for="kas-pos" class="block text-xs font-semibold text-gray-800 mb-1">Pos / Kategori <span class="text-rose-600">*</span></label>
                    <input type="text" id="kas-pos" name="kategori" required placeholder="Contoh: Iuran Warga RT 02" list="list-pos"
                           class="input-field">
                    <datalist id="list-pos">
                        <option value="Iuran Warga">
                        <option value="Donasi & Sumbangan">
                        <option value="Kas RT / RW">
                        <option value="Pengadaan Inventaris">
                        <option value="Pemeliharaan Fasilitas">
                        <option value="Konsumsi & Rapat">
                        <option value="Kegiatan 17 Agustus">
                        <option value="Sosial & Lelayu">
                    </datalist>
                </div>
            </div>

            <div>
                <label for="kas-keterangan" class="block text-xs font-semibold text-gray-800 mb-1">Uraian Keterangan <span class="text-rose-600">*</span></label>
                <textarea id="kas-keterangan" name="keterangan" rows="2" required placeholder="Jelaskan rincian penggunaan atau sumber dana secara ringkas..."
                          class="input-field py-2.5"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-gray-100">
                <button type="button" onclick="tutupModalKas()" class="btn-subtle px-4 text-xs">
                    Batal
                </button>
                <button type="submit" class="btn-primary px-5 text-xs">
                    Simpan Catatan Kas
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL SETOR KAS LISTRIK KE KAS KARANG TARUNA -->
<div id="modal-setor-listrik"
     onclick="if (event.target === this) tutupModalSetorListrik()"
     role="dialog"
     aria-modal="true"
     aria-labelledby="modal-setor-title"
     class="modal-backdrop hidden">
    <div class="modal-panel max-w-md p-5 sm:p-6">
        <div class="w-12 h-1.5 bg-gray-300 rounded-full mx-auto mb-3 sm:hidden"></div>

        <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
            <div>
                <h3 id="modal-setor-title" class="text-base font-bold text-gray-900 leading-tight">Setor Kas Listrik ke Karang Taruna</h3>
                <p class="text-xs text-gray-600 mt-0.5">Pindahkan saldo kas listrik warga ke kas operasional Karang Taruna.</p>
            </div>
            <button type="button" onclick="tutupModalSetorListrik()" aria-label="Tutup modal setor kas listrik"
                    class="text-gray-500 hover:text-gray-800 p-2 rounded-xl min-h-[44px] min-w-[44px] flex items-center justify-center bg-gray-100 sm:bg-transparent hover:bg-gray-100 transition">
                <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="bg-slate-100/80 border border-slate-200 rounded-xl p-3.5 mb-4 text-xs">
            <div class="flex items-center justify-between font-semibold text-slate-900 mb-1">
                <span>Saldo Kas Listrik Tersedia:</span>
                <span class="font-bold font-mono text-sm tabular-money">Rp {{ number_format($saldoListrik, 0, ',', '.') }}</span>
            </div>
            <p class="text-[11px] text-slate-700 leading-relaxed">
                Setoran ini otomatis membukukan <strong>Kas Keluar</strong> pada Kas Listrik dan <strong>Kas Masuk</strong> pada Kas Karang Taruna.
            </p>
        </div>

        <form action="{{ route('admin.keuangan.setor-kartar') }}" method="POST" class="space-y-3.5">
            @csrf

            <div>
                <label for="input-setor-nominal" class="block text-xs font-semibold text-gray-800 mb-1">Nominal Setoran (Rupiah) <span class="text-rose-600">*</span></label>
                <div class="input-addon-group">
                    <span class="flex items-center justify-center bg-emas-50 px-3.5 text-emas-800 font-bold text-xs select-none border-r border-emas-200 shrink-0">
                        Rp
                    </span>
                    <input type="text"
                           inputmode="numeric"
                           name="nominal"
                           id="input-setor-nominal"
                           required
                           value="{{ number_format($saldoListrik, 0, ',', '.') }}"
                           placeholder="0"
                           class="input-rupiah w-full text-base sm:text-sm font-bold text-gray-900 border-0 focus:ring-0 focus:outline-none py-2.5 px-3 bg-transparent tabular-money">
                </div>
                <div class="flex justify-end mt-1.5">
                    <button type="button" onclick="setNominalMaksimal({{ $saldoListrik }})" class="text-xs font-bold text-emas-800 hover:underline tabular-money">
                        Setor Semua (Rp {{ number_format($saldoListrik, 0, ',', '.') }})
                    </button>
                </div>
            </div>

            <div>
                <label for="setor-tanggal" class="block text-xs font-semibold text-gray-800 mb-1">Tanggal Setor <span class="text-rose-600">*</span></label>
                <input type="date" id="setor-tanggal" name="tanggal" required value="{{ date('Y-m-d') }}" class="input-field">
            </div>

            <div>
                <label for="setor-keterangan" class="block text-xs font-semibold text-gray-800 mb-1">Catatan Tambahan (Opsional)</label>
                <textarea id="setor-keterangan" name="keterangan" rows="2" placeholder="Contoh: Setoran uang kas listrik warga periode berjalan ke kas Karang Taruna"
                          class="input-field py-2.5"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-gray-100">
                <button type="button" onclick="tutupModalSetorListrik()" class="btn-subtle px-4 text-xs">
                    Batal
                </button>
                <button type="submit" class="btn-primary px-5 text-xs">
                    Konfirmasi Setor ke Karang Taruna
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function bukaModalKas() {
        document.getElementById('modal-kas').classList.remove('hidden');
    }
    function tutupModalKas() {
        document.getElementById('modal-kas').classList.add('hidden');
    }

    function bukaModalSetorListrik() {
        const modal = document.getElementById('modal-setor-listrik');
        if (modal) modal.classList.remove('hidden');
    }
    function tutupModalSetorListrik() {
        const modal = document.getElementById('modal-setor-listrik');
        if (modal) modal.classList.add('hidden');
    }
    function setNominalMaksimal(maxVal) {
        const el = document.getElementById('input-setor-nominal');
        if (el) {
            el.value = new Intl.NumberFormat('id-ID').format(maxVal);
        }
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            tutupModalKas();
            tutupModalSetorListrik();
        }
    });
</script>
@endpush
@endsection
