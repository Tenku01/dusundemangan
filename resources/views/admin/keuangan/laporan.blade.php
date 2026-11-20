@extends('layouts.admin')

@section('title', 'Laporan Ringkas Keuangan Dusun')

@section('content')
<div class="space-y-6">

    <!-- Header & Print Action -->
    <div class="card-surface p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 print:hidden">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">
                Laporan Ringkas Keuangan Kas
            </h1>
            <p class="text-xs text-gray-600 mt-1">
                Rekapitulasi penerimaan dan pengeluaran kas berdasarkan rentang bulan dan tahun.
            </p>
        </div>

        <button type="button" onclick="window.print()" class="btn-secondary px-4 text-xs self-start sm:self-auto">
            <svg class="w-4 h-4 text-gray-600" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            <span>Cetak / Unduh PDF</span>
        </button>
    </div>

    <!-- Filter Rentang Periode -->
    <div class="card-surface p-4 print:hidden">
        <form method="GET" action="{{ route('admin.keuangan.laporan') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3 items-end">
            <div>
                <label for="laporan-bulan-mulai" class="block text-xs font-semibold text-gray-700 mb-1">Bulan Mulai</label>
                <select id="laporan-bulan-mulai" name="bulan_mulai" class="select-field">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $bulanMulai == $m ? 'selected' : '' }}>
                            {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                        </option>
                    @endfor
                </select>
            </div>

            <div>
                <label for="laporan-bulan-selesai" class="block text-xs font-semibold text-gray-700 mb-1">Bulan Selesai</label>
                <select id="laporan-bulan-selesai" name="bulan_selesai" class="select-field">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $bulanSelesai == $m ? 'selected' : '' }}>
                            {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                        </option>
                    @endfor
                </select>
            </div>

            <div>
                <label for="laporan-tahun" class="block text-xs font-semibold text-gray-700 mb-1">Tahun</label>
                <select id="laporan-tahun" name="tahun" class="select-field">
                    @for($y = date('Y'); $y >= date('Y') - 3; $y--)
                        <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>

            <button type="submit" class="btn-primary px-5 text-xs">
                Tampilkan Laporan
            </button>
        </form>
    </div>

    <!-- Printable Report Container -->
    <div class="card-surface p-6 sm:p-8 space-y-6">

        <!-- Kop Laporan Resmi -->
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pb-5 border-b-2 border-gray-800 text-center sm:text-left">
            <img src="{{ asset('logo-kartar-dmg-circle.png') }}"
                 alt="Logo Karang Taruna Demangan"
                 width="64" height="64"
                 class="w-14 h-14 sm:w-16 sm:h-16 rounded-full object-cover shadow-sm border border-emas-300 bg-white shrink-0">
            <div>
                <h2 class="text-base sm:text-lg font-bold text-gray-900 uppercase tracking-wide">
                    Laporan Arus Kas Keuangan Dusun Demangan
                </h2>
                <div class="text-xs font-semibold text-gray-800 mt-0.5">
                    Periode: {{ DateTime::createFromFormat('!m', $bulanMulai)->format('F') }} s/d {{ DateTime::createFromFormat('!m', $bulanSelesai)->format('F') }} {{ $tahun }}
                </div>
                <div class="text-xs text-gray-600 mt-0.5">
                    Karang Taruna Dusun Demangan, Desa Candimulyo
                </div>
            </div>
        </div>

        <!-- 3 Ringkasan Angka Utama -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4 print:grid-cols-3">
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-center">
                <div class="text-xs font-bold text-emerald-900 uppercase">Total Pemasukan</div>
                <div class="text-xl sm:text-2xl font-extrabold text-emerald-800 mt-1 tabular-money">
                    Rp {{ number_format($totalMasuk, 0, ',', '.') }}
                </div>
            </div>

            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-center">
                <div class="text-xs font-bold text-rose-900 uppercase">Total Pengeluaran</div>
                <div class="text-xl sm:text-2xl font-extrabold text-rose-800 mt-1 tabular-money">
                    Rp {{ number_format($totalKeluar, 0, ',', '.') }}
                </div>
            </div>

            <div class="p-4 rounded-xl bg-emas-50 border border-emas-200 text-center">
                <div class="text-xs font-bold text-emas-900 uppercase">Surplus / Defisit Periode</div>
                <div class="text-xl sm:text-2xl font-extrabold tabular-money {{ $surplusDefisit >= 0 ? 'text-emerald-800' : 'text-rose-800' }} mt-1">
                    Rp {{ number_format($surplusDefisit, 0, ',', '.') }}
                </div>
            </div>
        </div>

        <!-- Breakdown per Kategori -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
            <div class="border border-gray-200 rounded-xl p-4">
                <h3 class="text-xs font-bold text-emerald-900 uppercase tracking-wide pb-2 border-b border-gray-100 mb-2">
                    Rincian Kategori Pemasukan
                </h3>
                <div class="space-y-1.5 text-xs">
                    @forelse($rekapKategoriMasuk as $rm)
                        <div class="flex justify-between py-1.5 border-b border-gray-50">
                            <span class="text-gray-700">{{ $rm->kategori }}</span>
                            <span class="font-bold text-emerald-700 tabular-money">Rp {{ number_format($rm->total, 0, ',', '.') }}</span>
                        </div>
                    @empty
                        <div class="text-gray-500 italic py-2">Tidak ada pemasukan pada periode ini.</div>
                    @endforelse
                </div>
            </div>

            <div class="border border-gray-200 rounded-xl p-4">
                <h3 class="text-xs font-bold text-rose-900 uppercase tracking-wide pb-2 border-b border-gray-100 mb-2">
                    Rincian Kategori Pengeluaran
                </h3>
                <div class="space-y-1.5 text-xs">
                    @forelse($rekapKategoriKeluar as $rk)
                        <div class="flex justify-between py-1.5 border-b border-gray-50">
                            <span class="text-gray-700">{{ $rk->kategori }}</span>
                            <span class="font-bold text-rose-700 tabular-money">Rp {{ number_format($rk->total, 0, ',', '.') }}</span>
                        </div>
                    @empty
                        <div class="text-gray-500 italic py-2">Tidak ada pengeluaran pada periode ini.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Riwayat Mutasi Periode -->
        <div>
            <h3 class="text-xs font-bold text-gray-800 uppercase tracking-wide mb-3">
                Daftar Lengkap Transaksi Periode Ini (<span class="tabular-money">{{ $transaksi->count() }}</span> Transaksi)
            </h3>

            <!-- TAMPILAN MOBILE -->
            <div class="block md:hidden print:hidden space-y-3">
                @forelse($transaksi as $t)
                    <div class="bg-[#F8F7F4] rounded-xl p-3.5 border border-gray-200 space-y-2">
                        <div class="flex items-center justify-between pb-1.5 border-b border-gray-200/70">
                            <span class="text-xs text-gray-600 font-medium tabular-money">
                                {{ $t->tanggal->format('d/m/Y') }} &bull; <span class="font-mono text-[11px]">{{ $t->kode_transaksi }}</span>
                            </span>
                            <span class="px-2 py-0.5 rounded-md text-[11px] font-bold {{ $t->jenis === 'masuk' ? 'bg-emerald-100 text-emerald-900' : 'bg-rose-100 text-rose-900' }}">
                                {{ $t->jenis === 'masuk' ? 'Masuk' : 'Keluar' }}
                            </span>
                        </div>
                        <div class="flex items-baseline justify-between gap-2">
                            <div class="text-xs font-bold text-gray-900">{{ $t->kategori }}</div>
                            <div class="text-sm font-extrabold tabular-money {{ $t->jenis === 'masuk' ? 'text-emerald-700' : 'text-rose-700' }}">
                                {{ $t->jenis === 'masuk' ? '+' : '-' }} Rp {{ number_format($t->nominal, 0, ',', '.') }}
                            </div>
                        </div>
                        <p class="text-xs text-gray-600 bg-white p-2.5 rounded-lg border border-gray-100">
                            {{ $t->keterangan }}
                        </p>
                    </div>
                @empty
                    <div class="p-6 text-center text-gray-500 text-xs">
                        Tidak ada data transaksi pada rentang periode yang dipilih.
                    </div>
                @endforelse
            </div>

            <!-- TAMPILAN DESKTOP & PRINT -->
            <div class="hidden md:block print:block border border-gray-200 rounded-xl overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 text-left text-xs">
                    <thead class="bg-[#F8F7F4] text-gray-700 font-bold uppercase text-[11px]">
                        <tr>
                            <th class="py-3 px-3">Tanggal</th>
                            <th class="py-3 px-3">Kode</th>
                            <th class="py-3 px-3">Pos Kategori</th>
                            <th class="py-3 px-3">Keterangan</th>
                            <th class="py-3 px-3 text-right">Pemasukan</th>
                            <th class="py-3 px-3 text-right">Pengeluaran</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($transaksi as $t)
                            <tr>
                                <td class="py-2.5 px-3 whitespace-nowrap tabular-money">{{ $t->tanggal->format('d/m/Y') }}</td>
                                <td class="py-2.5 px-3 font-mono text-[11px]">{{ $t->kode_transaksi }}</td>
                                <td class="py-2.5 px-3 font-semibold text-gray-900">{{ $t->kategori }}</td>
                                <td class="py-2.5 px-3 text-gray-600">{{ $t->keterangan }}</td>
                                <td class="py-2.5 px-3 text-right font-semibold text-emerald-700 whitespace-nowrap tabular-money">
                                    {{ $t->jenis === 'masuk' ? 'Rp ' . number_format($t->nominal, 0, ',', '.') : '-' }}
                                </td>
                                <td class="py-2.5 px-3 text-right font-semibold text-rose-700 whitespace-nowrap tabular-money">
                                    {{ $t->jenis === 'keluar' ? 'Rp ' . number_format($t->nominal, 0, ',', '.') : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-gray-500">
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
