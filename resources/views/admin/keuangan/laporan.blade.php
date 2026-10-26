@extends('layouts.admin')

@section('title', 'Laporan Ringkas Keuangan Dusun')

@section('content')
<div class="space-y-6">

    <!-- Header & Print Action -->
    <div class="bg-white rounded-xl border border-gray-200/90 p-5 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4 print:hidden">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">
                Laporan Ringkas Keuangan Kas Dusun
            </h1>
            <p class="text-xs text-gray-500 mt-1">
                Rekapitulasi penerimaan dan pengeluaran kas berdasarkan rentang bulan dan tahun.
            </p>
        </div>

        <button type="button" onclick="window.print()"
                class="inline-flex items-center gap-2 px-4 py-2 text-xs font-semibold text-gray-800 bg-gray-100 hover:bg-gray-200 rounded-lg transition border border-gray-300">
            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Cetak / Unduh PDF
        </button>
    </div>

    <!-- Filter Rentang Periode -->
    <div class="bg-white rounded-xl border border-gray-200/90 p-4 shadow-xs print:hidden">
        <form method="GET" action="{{ route('admin.keuangan.laporan') }}" class="flex flex-wrap items-end gap-3">
            <div class="w-36">
                <label class="block text-[11px] font-semibold text-gray-600 mb-1">Bulan Mulai</label>
                <select name="bulan_mulai" class="w-full text-xs rounded-lg border-gray-300 focus:border-[#CEA042] focus:ring-1 focus:ring-[#CEA042] py-2 px-2.5">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $bulanMulai == $m ? 'selected' : '' }}>
                            {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                        </option>
                    @endfor
                </select>
            </div>

            <div class="w-36">
                <label class="block text-[11px] font-semibold text-gray-600 mb-1">Bulan Selesai</label>
                <select name="bulan_selesai" class="w-full text-xs rounded-lg border-gray-300 focus:border-[#CEA042] focus:ring-1 focus:ring-[#CEA042] py-2 px-2.5">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $bulanSelesai == $m ? 'selected' : '' }}>
                            {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                        </option>
                    @endfor
                </select>
            </div>

            <div class="w-28">
                <label class="block text-[11px] font-semibold text-gray-600 mb-1">Tahun</label>
                <select name="tahun" class="w-full text-xs rounded-lg border-gray-300 focus:border-[#CEA042] focus:ring-1 focus:ring-[#CEA042] py-2 px-2.5">
                    @for($y = date('Y'); $y >= date('Y') - 3; $y--)
                        <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>

            <button type="submit" class="px-4 py-2 text-xs font-semibold text-white bg-[#CEA042] hover:bg-[#BA8832] rounded-lg shadow-xs transition">
                Tampilkan Laporan
            </button>
        </form>
    </div>

    <!-- Printable Report Container -->
    <div class="bg-white rounded-xl border border-gray-200/90 p-6 sm:p-8 shadow-xs space-y-6">

        <!-- Kop Laporan Resmi -->
        <div class="flex items-center justify-center gap-4 pb-5 border-b-2 border-gray-800 text-center">
            <img src="{{ asset('logo-kartar-dmg-circle.png') }}"
                 alt="Logo Karang Taruna Demangan"
                 class="w-14 h-14 sm:w-16 sm:h-16 rounded-full object-cover shadow-xs border border-amber-300 bg-white shrink-0">
            <div>
                <h2 class="text-base sm:text-lg font-bold text-gray-900 uppercase tracking-wide">
                    Laporan Arus Kas Keuangan Dusun Demangan
                </h2>
                <div class="text-xs font-semibold text-gray-800 mt-0.5">
                    Periode: {{ DateTime::createFromFormat('!m', $bulanMulai)->format('F') }} s/d {{ DateTime::createFromFormat('!m', $bulanSelesai)->format('F') }} {{ $tahun }}
                </div>
                <div class="text-[11px] text-gray-500 mt-0.5">
                    Karang Taruna Dusun Demangan, Desa Bangunharjo
                </div>
            </div>
        </div>

        <!-- 3 Ringkasan Angka Utama (Responsif HP & Print) -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4 print:grid-cols-3">
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-center">
                <div class="text-xs font-semibold text-emerald-800 uppercase">Total Pemasukan</div>
                <div class="text-xl sm:text-2xl font-bold text-emerald-700 mt-1">
                    Rp {{ number_format($totalMasuk, 0, ',', '.') }}
                </div>
            </div>

            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-center">
                <div class="text-xs font-semibold text-rose-800 uppercase">Total Pengeluaran</div>
                <div class="text-xl sm:text-2xl font-bold text-rose-700 mt-1">
                    Rp {{ number_format($totalKeluar, 0, ',', '.') }}
                </div>
            </div>

            <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-center">
                <div class="text-xs font-semibold text-amber-800 uppercase">Surplus / Defisit Periode</div>
                <div class="text-xl sm:text-2xl font-bold {{ $surplusDefisit >= 0 ? 'text-emerald-700' : 'text-rose-700' }} mt-1">
                    Rp {{ number_format($surplusDefisit, 0, ',', '.') }}
                </div>
            </div>
        </div>

        <!-- Breakdown per Kategori -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
            <!-- Pos Pemasukan -->
            <div class="border border-gray-200 rounded-xl p-4">
                <h3 class="text-xs font-bold text-emerald-800 uppercase tracking-wide pb-2 border-b border-gray-100 mb-2">
                    Rincian Kategori Pemasukan
                </h3>
                <div class="space-y-1.5 text-xs">
                    @forelse($rekapKategoriMasuk as $rm)
                        <div class="flex justify-between py-1 border-b border-gray-50">
                            <span class="text-gray-700">{{ $rm->kategori }}</span>
                            <span class="font-semibold text-emerald-700">Rp {{ number_format($rm->total, 0, ',', '.') }}</span>
                        </div>
                    @empty
                        <div class="text-gray-400 italic py-2">Tidak ada pemasukan pada periode ini.</div>
                    @endforelse
                </div>
            </div>

            <!-- Pos Pengeluaran -->
            <div class="border border-gray-200 rounded-xl p-4">
                <h3 class="text-xs font-bold text-rose-800 uppercase tracking-wide pb-2 border-b border-gray-100 mb-2">
                    Rincian Kategori Pengeluaran
                </h3>
                <div class="space-y-1.5 text-xs">
                    @forelse($rekapKategoriKeluar as $rk)
                        <div class="flex justify-between py-1 border-b border-gray-50">
                            <span class="text-gray-700">{{ $rk->kategori }}</span>
                            <span class="font-semibold text-rose-700">Rp {{ number_format($rk->total, 0, ',', '.') }}</span>
                        </div>
                    @empty
                        <div class="text-gray-400 italic py-2">Tidak ada pengeluaran pada periode ini.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Riwayat Mutasi Periode -->
        <div>
            <h3 class="text-xs font-bold text-gray-800 uppercase tracking-wide mb-3">
                Daftar Lengkap Transaksi Periode Ini ({{ $transaksi->count() }} Transaksi)
            </h3>

            <!-- TAMPILAN MOBILE: KARTU MUTASI LAPORAN (block md:hidden print:hidden) -->
            <div class="block md:hidden print:hidden space-y-3">
                @forelse($transaksi as $t)
                    <div class="bg-gray-50/90 rounded-xl p-3.5 border border-gray-200 space-y-2">
                        <div class="flex items-center justify-between pb-1.5 border-b border-gray-200/60">
                            <span class="text-xs text-gray-500 font-medium">
                                {{ $t->tanggal->format('d/m/Y') }} • <span class="font-mono text-[11px]">{{ $t->kode_transaksi }}</span>
                            </span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $t->jenis === 'masuk' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                {{ $t->jenis === 'masuk' ? 'Masuk' : 'Keluar' }}
                            </span>
                        </div>
                        <div class="flex items-baseline justify-between">
                            <div class="text-xs font-bold text-gray-800">{{ $t->kategori }}</div>
                            <div class="text-sm font-extrabold {{ $t->jenis === 'masuk' ? 'text-emerald-700' : 'text-rose-700' }}">
                                {{ $t->jenis === 'masuk' ? '+' : '-' }} Rp {{ number_format($t->nominal, 0, ',', '.') }}
                            </div>
                        </div>
                        <p class="text-xs text-gray-600 bg-white p-2 rounded-lg border border-gray-100">
                            {{ $t->keterangan }}
                        </p>
                    </div>
                @empty
                    <div class="p-6 text-center text-gray-400 text-xs">
                        Tidak ada data transaksi pada rentang periode yang dipilih.
                    </div>
                @endforelse
            </div>

            <!-- TAMPILAN DESKTOP & PRINT: TABEL MUTASI (hidden md:block print:block) -->
            <div class="hidden md:block print:block border border-gray-200 rounded-xl overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 text-left text-xs">
                    <thead class="bg-gray-50 text-gray-700 font-semibold uppercase">
                        <tr>
                            <th class="py-2.5 px-3">Tanggal</th>
                            <th class="py-2.5 px-3">Kode</th>
                            <th class="py-2.5 px-3">Pos Kategori</th>
                            <th class="py-2.5 px-3">Keterangan</th>
                            <th class="py-2.5 px-3 text-right">Pemasukan</th>
                            <th class="py-2.5 px-3 text-right">Pengeluaran</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($transaksi as $t)
                            <tr>
                                <td class="py-2.5 px-3 whitespace-nowrap">{{ $t->tanggal->format('d/m/Y') }}</td>
                                <td class="py-2.5 px-3 font-mono text-[11px]">{{ $t->kode_transaksi }}</td>
                                <td class="py-2.5 px-3 font-medium">{{ $t->kategori }}</td>
                                <td class="py-2.5 px-3 text-gray-600">{{ $t->keterangan }}</td>
                                <td class="py-2.5 px-3 text-right font-medium text-emerald-700 whitespace-nowrap">
                                    {{ $t->jenis === 'masuk' ? 'Rp ' . number_format($t->nominal, 0, ',', '.') : '-' }}
                                </td>
                                <td class="py-2.5 px-3 text-right font-medium text-rose-700 whitespace-nowrap">
                                    {{ $t->jenis === 'keluar' ? 'Rp ' . number_format($t->nominal, 0, ',', '.') : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-6 text-center text-gray-400">
                                    Tidak ada data transaksi pada rentang periode yang dipilih.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tanda Tangan Pengurus (Cetak) -->
        <div class="hidden print:grid grid-cols-2 pt-10 text-xs text-center">
            <div>
                <div>Mengetahui,</div>
                <div class="font-bold mt-1">Ketua Karang Taruna Demangan</div>
                <div class="h-16"></div>
                <div class="font-bold underline">( Budi Santoso )</div>
            </div>
            <div>
                <div>Demangan, {{ date('d F Y') }}</div>
                <div class="font-bold mt-1">Bendahara Dusun Demangan</div>
                <div class="h-16"></div>
                <div class="font-bold underline">( {{ auth()->user()->name }} )</div>
            </div>
        </div>

    </div>

</div>
@endsection
