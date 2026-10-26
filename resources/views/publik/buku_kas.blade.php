@extends('layouts.publik')

@section('title', 'Buku Kas Umum Dusun Demangan (Transparansi Publik)')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

    <!-- Header & Deskripsi Transparansi -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 pb-6 border-b border-gray-200">
        <div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 mb-2">
                Akuntabilitas Keuangan Terbuka
            </span>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">
                Buku Kas Umum Dusun Demangan
            </h1>
            <p class="text-xs sm:text-sm text-gray-600 mt-1 max-w-2xl">
                Seluruh warga dapat melihat laporan penerimaan (iuran warga, donasi) serta pengeluaran operasional dan pemeliharaan sarana dusun secara terbuka.
            </p>
        </div>

        <a href="{{ route('beranda') }}" class="inline-flex items-center gap-1 text-xs font-semibold text-[#CEA042] hover:underline self-start md:self-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Beranda
        </a>
    </div>

    <!-- Tiga Kartu Ringkasan Akuntansi -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <!-- Saldo Akhir -->
        <div class="bg-white rounded-xl p-5 border border-gray-200/90 shadow-xs">
            <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">
                Saldo Akhir Kas Dusun
            </div>
            <div class="text-2xl sm:text-3xl font-extrabold text-emerald-600 tracking-tight">
                Rp {{ number_format($saldoKasSaatIni, 0, ',', '.') }}
            </div>
            <p class="text-xs text-gray-500 mt-1">Saldo riil di kas bendahara saat ini.</p>
        </div>

        <!-- Total Pemasukan -->
        <div class="bg-white rounded-xl p-5 border border-gray-200/90 shadow-xs">
            <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">
                Total Pemasukan Kas
            </div>
            <div class="text-2xl sm:text-3xl font-extrabold text-[#CEA042] tracking-tight">
                Rp {{ number_format($totalMasuk, 0, ',', '.') }}
            </div>
            <p class="text-xs text-gray-500 mt-1">Akumulasi iuran, sumbangan, & donasi warga.</p>
        </div>

        <!-- Total Pengeluaran -->
        <div class="bg-white rounded-xl p-5 border border-gray-200/90 shadow-xs">
            <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">
                Total Pengeluaran Kas
            </div>
            <div class="text-2xl sm:text-3xl font-extrabold text-rose-600 tracking-tight">
                Rp {{ number_format($totalKeluar, 0, ',', '.') }}
            </div>
            <p class="text-xs text-gray-500 mt-1">Akumulasi belanja fasilitas & kegiatan dusun.</p>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white rounded-xl border border-gray-200/90 p-4 shadow-xs">
        <form method="GET" action="{{ route('publik.buku-kas') }}" class="flex flex-wrap items-center gap-3">
            <div class="flex-1 min-w-[140px]">
                <label class="block text-[11px] font-semibold text-gray-600 mb-1">Arus Kas</label>
                <select name="jenis" onchange="this.form.submit()" class="w-full text-xs rounded-lg border-gray-300 focus:border-[#CEA042] focus:ring-1 focus:ring-[#CEA042] py-2 px-2.5">
                    <option value="">Semua Mutasi</option>
                    <option value="masuk" {{ $jenis === 'masuk' ? 'selected' : '' }}>Hanya Pemasukan</option>
                    <option value="keluar" {{ $jenis === 'keluar' ? 'selected' : '' }}>Hanya Pengeluaran</option>
                </select>
            </div>

            <div class="w-36">
                <label class="block text-[11px] font-semibold text-gray-600 mb-1">Bulan</label>
                <select name="bulan" onchange="this.form.submit()" class="w-full text-xs rounded-lg border-gray-300 focus:border-[#CEA042] focus:ring-1 focus:ring-[#CEA042] py-2 px-2.5">
                    <option value="">Semua Bulan</option>
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                            {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                        </option>
                    @endfor
                </select>
            </div>

            <div class="w-28">
                <label class="block text-[11px] font-semibold text-gray-600 mb-1">Tahun</label>
                <select name="tahun" onchange="this.form.submit()" class="w-full text-xs rounded-lg border-gray-300 focus:border-[#CEA042] focus:ring-1 focus:ring-[#CEA042] py-2 px-2.5">
                    @for($y = date('Y'); $y >= date('Y') - 3; $y--)
                        <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>

            @if($jenis || $bulan)
                <div class="self-end pb-1">
                    <a href="{{ route('publik.buku-kas') }}" class="text-xs text-rose-600 hover:underline">
                        Reset Filter
                    </a>
                </div>
            @endif
        </form>
    </div>

    <!-- Tabel Buku Kas Publik -->
    <div class="bg-white rounded-xl border border-gray-200/90 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-left text-xs">
                <thead class="bg-gray-50 text-gray-600 font-semibold uppercase tracking-wider">
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
                        <tr class="hover:bg-gray-50/80 transition">
                            <td class="py-3.5 px-4 whitespace-nowrap text-gray-700 font-medium">
                                {{ $kas->tanggal->format('d/m/Y') }}
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap text-gray-500 font-mono text-[11px]">
                                {{ $kas->kode_transaksi }}
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="font-semibold text-gray-800">{{ $kas->kategori }}</span>
                            </td>
                            <td class="py-3.5 px-4 text-gray-600 max-w-sm">
                                {{ $kas->keterangan }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-semibold whitespace-nowrap text-emerald-700">
                                @if($kas->jenis === 'masuk')
                                    Rp {{ number_format($kas->nominal, 0, ',', '.') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right font-semibold whitespace-nowrap text-rose-700">
                                @if($kas->jenis === 'keluar')
                                    Rp {{ number_format($kas->nominal, 0, ',', '.') }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-gray-400">
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

</div>
@endsection
