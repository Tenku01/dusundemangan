@extends('layouts.publik')

@section('title', 'Buku Kas Umum Dusun Demangan (Transparansi Publik)')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 space-y-6 sm:space-y-8">

    <!-- Header & Deskripsi Transparansi -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 pb-6 border-b border-gray-200">
        <div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200 mb-2">
                Akuntabilitas Keuangan Terbuka
            </span>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">
                Buku Kas Umum Dusun Demangan
            </h1>
            <p class="text-xs sm:text-sm text-gray-600 mt-1 max-w-2xl leading-relaxed">
                Seluruh warga dapat melihat laporan penerimaan (iuran warga, donasi) serta pengeluaran operasional dan pemeliharaan sarana dusun secara terbuka.
            </p>
        </div>

        <a href="{{ route('beranda') }}" class="btn-secondary px-4 text-xs self-start md:self-auto">
            <svg class="w-4 h-4" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            <span>Kembali ke Beranda</span>
        </a>
    </div>

    <!-- Tiga Kartu Ringkasan Akuntansi -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-5">
        <div class="card-surface p-5 border-t-2 border-t-emerald-500">
            <div class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">
                Saldo Akhir Kas Dusun
            </div>
            <div class="text-2xl sm:text-3xl font-extrabold text-emerald-700 tracking-tight tabular-money">
                Rp {{ number_format($saldoKasSaatIni, 0, ',', '.') }}
            </div>
            <p class="text-xs text-gray-600 mt-1.5">Saldo riil di kas bendahara saat ini.</p>
        </div>

        <div class="card-surface p-5 border-t-2 border-t-[#CEA042]">
            <div class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">
                Total Pemasukan Kas
            </div>
            <div class="text-2xl sm:text-3xl font-extrabold text-emas-800 tracking-tight tabular-money">
                Rp {{ number_format($totalMasuk, 0, ',', '.') }}
            </div>
            <p class="text-xs text-gray-600 mt-1.5">Akumulasi iuran, sumbangan, dan donasi warga.</p>
        </div>

        <div class="card-surface p-5 border-t-2 border-t-rose-500">
            <div class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">
                Total Pengeluaran Kas
            </div>
            <div class="text-2xl sm:text-3xl font-extrabold text-rose-700 tracking-tight tabular-money">
                Rp {{ number_format($totalKeluar, 0, ',', '.') }}
            </div>
            <p class="text-xs text-gray-600 mt-1.5">Akumulasi belanja fasilitas dan kegiatan dusun.</p>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="card-surface p-4">
        <form method="GET" action="{{ route('publik.buku-kas') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3 items-end">
            <div>
                <label for="filter-jenis" class="block text-xs font-semibold text-gray-700 mb-1">Arus Kas</label>
                <select id="filter-jenis" name="jenis" onchange="this.form.submit()" class="select-field">
                    <option value="">Semua Mutasi</option>
                    <option value="masuk" {{ $jenis === 'masuk' ? 'selected' : '' }}>Hanya Pemasukan</option>
                    <option value="keluar" {{ $jenis === 'keluar' ? 'selected' : '' }}>Hanya Pengeluaran</option>
                </select>
            </div>

            <div>
                <label for="filter-bulan" class="block text-xs font-semibold text-gray-700 mb-1">Bulan</label>
                <select id="filter-bulan" name="bulan" onchange="this.form.submit()" class="select-field">
                    <option value="">Semua Bulan</option>
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                            {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                        </option>
                    @endfor
                </select>
            </div>

            <div>
                <label for="filter-tahun" class="block text-xs font-semibold text-gray-700 mb-1">Tahun</label>
                <select id="filter-tahun" name="tahun" onchange="this.form.submit()" class="select-field">
                    @for($y = date('Y'); $y >= date('Y') - 3; $y--)
                        <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>

            @if($jenis || $bulan)
                <div>
                    <a href="{{ route('publik.buku-kas') }}" class="btn-danger-soft w-full text-xs">
                        Reset Filter
                    </a>
                </div>
            @endif
        </form>
    </div>

    <!-- TAMPILAN MOBILE: KARTU BUKU KAS -->
    <div class="block md:hidden space-y-3">
        @forelse($transaksi as $kas)
            <div class="card-surface p-4 space-y-2.5">
                <div class="flex items-center justify-between pb-2 border-b border-gray-100">
                    <span class="text-xs text-gray-600 font-medium tabular-money">
                        {{ $kas->tanggal->format('d/m/Y') }} &bull; <span class="font-mono text-[11px]">{{ $kas->kode_transaksi }}</span>
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
                <p class="text-xs text-gray-600 bg-[#F8F7F4] p-2.5 rounded-xl border border-gray-100 leading-relaxed">
                    {{ $kas->keterangan }}
                </p>
            </div>
        @empty
            <div class="card-surface p-8 text-center text-gray-500 text-xs">
                Tidak ada catatan mutasi kas pada periode ini.
            </div>
        @endforelse
    </div>

    <!-- TAMPILAN DESKTOP: TABEL BUKU KAS PUBLIK -->
    <div class="hidden md:block card-surface overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-left text-xs">
                <thead class="bg-[#F8F7F4] text-gray-600 font-bold uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="py-3.5 px-4">Tanggal</th>
                        <th class="py-3.5 px-4">Kode</th>
                        <th class="py-3.5 px-4">Kategori / Pos</th>
                        <th class="py-3.5 px-4">Uraian Transaksi</th>
                        <th class="py-3.5 px-4 text-right">Pemasukan</th>
                        <th class="py-3.5 px-4 text-right">Pengeluaran</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($transaksi as $kas)
                        <tr class="hover:bg-emas-50/40 transition">
                            <td class="py-3.5 px-4 whitespace-nowrap text-gray-700 font-medium tabular-money">
                                {{ $kas->tanggal->format('d/m/Y') }}
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap text-gray-500 font-mono text-[11px]">
                                {{ $kas->kode_transaksi }}
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="font-semibold text-gray-900">{{ $kas->kategori }}</span>
                            </td>
                            <td class="py-3.5 px-4 text-gray-600 max-w-sm">
                                {{ $kas->keterangan }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-bold whitespace-nowrap tabular-money text-emerald-700">
                                @if($kas->jenis === 'masuk')
                                    Rp {{ number_format($kas->nominal, 0, ',', '.') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right font-bold whitespace-nowrap tabular-money text-rose-700">
                                @if($kas->jenis === 'keluar')
                                    Rp {{ number_format($kas->nominal, 0, ',', '.') }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-gray-500">
                                Tidak ada catatan mutasi kas pada periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-gray-100">
            {{ $transaksi->links() }}
        </div>
    </div>

    <div class="md:hidden">
        {{ $transaksi->links() }}
    </div>

</div>
@endsection
