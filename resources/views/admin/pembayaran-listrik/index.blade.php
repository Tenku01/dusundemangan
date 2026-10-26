@extends('layouts.admin')

@section('title', 'Kasir Pembayaran Listrik - Dusun Demangan')

@section('content')
<!-- Data JSON aman disuntikkan tanpa merusak HTML attribute -->
<script id="tagihan-listrik-json" type="application/json">
    {!! json_encode($daftarTagihan) !!}
</script>

<div class="space-y-4 sm:space-y-5" x-data="kasirListrik()">

    <!-- Top Card & Periode Switcher -->
    <div class="bg-white rounded-2xl border border-gray-200/90 p-4 sm:p-5 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <div class="flex items-center gap-2.5">
                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-[#CEA042] text-white font-bold shrink-0 shadow-xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </span>
                    <div>
                        <h1 class="text-lg sm:text-xl font-bold text-gray-900 leading-tight">
                            Kasir Pembayaran Listrik
                        </h1>
                        <p class="text-xs text-gray-500">Penerimaan kasir, potongan saldo titipan bulan lalu & kembalian warga</p>
                    </div>
                </div>
            </div>

            <!-- Pemilih Periode Kustom Mewah (Bukan native select) -->
            <div class="flex items-center gap-2.5" x-data="{ openDropdownPeriode: false }">
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider shrink-0">Siklus Periode:</span>
                <div class="relative w-full sm:w-64">
                    <!-- Dropdown Trigger Button -->
                    <button type="button"
                            @click="openDropdownPeriode = !openDropdownPeriode"
                            class="w-full flex items-center justify-between gap-2 text-xs font-bold text-gray-900 bg-amber-50/70 hover:bg-amber-100/60 border border-amber-200/90 hover:border-[#CEA042] rounded-xl py-2.5 px-3.5 min-h-[44px] shadow-2xs transition focus:outline-none focus:ring-2 focus:ring-[#CEA042]/30">
                        <div class="flex items-center gap-2 truncate">
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-lg bg-amber-100 text-[#CEA042] shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </span>
                            <span class="truncate">{{ $periodeAktif }}</span>
                        </div>
                        <svg class="w-4 h-4 text-gray-500 transition-transform duration-200 shrink-0"
                             :class="openDropdownPeriode ? 'rotate-180 text-[#CEA042]' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <!-- Elegant Floating Dropdown Menu Panel -->
                    <div x-show="openDropdownPeriode"
                         x-cloak
                         @click.away="openDropdownPeriode = false"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                         x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                         class="absolute right-0 mt-2 z-50 w-72 bg-white rounded-2xl border border-amber-200/90 shadow-2xl p-2 space-y-1">
                        
                        <div class="px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider text-gray-400 border-b border-gray-100 flex items-center justify-between">
                            <span>Pilih Siklus Bulan</span>
                            <span class="text-[#CEA042] font-semibold">{{ count($daftarPeriode) }} Periode</span>
                        </div>

                        <div class="max-h-60 overflow-y-auto space-y-1 py-1">
                            @foreach($daftarPeriode as $idx => $p)
                                @php
                                    $isAktif = ($periodeAktif === $p);
                                @endphp
                                <a href="{{ route('admin.pembayaran-listrik.index', ['periode' => $p, 'rt' => $rtAktif, 'tab' => $tab]) }}"
                                   class="flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-bold transition group {{ $isAktif ? 'bg-[#CEA042] text-white shadow-xs' : 'text-gray-700 hover:bg-amber-50 hover:text-[#CEA042]' }}">
                                    <div class="flex items-center gap-2.5">
                                        <svg class="w-4 h-4 {{ $isAktif ? 'text-white' : 'text-gray-400 group-hover:text-[#CEA042]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <span>{{ $p }}</span>
                                    </div>

                                    @if($isAktif)
                                        <span class="inline-flex items-center gap-1 text-[10px] px-2 py-0.5 rounded-full bg-white/25 text-white font-black">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                            Aktif
                                        </span>
                                    @elseif($idx === 0)
                                        <span class="text-[10px] px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 font-bold group-hover:bg-[#CEA042] group-hover:text-white transition">
                                            Terbaru
                                        </span>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Segmented Tab Status Pembayaran (Sangat Nyaman untuk Jempol HP) -->
    <div class="grid grid-cols-2 gap-2 bg-gray-200/80 p-1.5 rounded-2xl">
        <a href="{{ route('admin.pembayaran-listrik.index', ['periode' => $periodeAktif, 'rt' => $rtAktif, 'tab' => 'belum_bayar']) }}"
           class="flex items-center justify-center gap-2 py-3 px-3 rounded-xl text-xs font-bold transition min-h-[48px] {{ $tab === 'belum_bayar' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
            <span>Belum Bayar</span>
            @if($totalBelumLunas > 0)
                <span class="text-[10px] px-2 py-0.5 rounded-full bg-rose-500 text-white font-black">
                    {{ $totalBelumLunas }}
                </span>
            @else
                <span class="text-[10px] px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800">
                    Nihil
                </span>
            @endif
        </a>

        <a href="{{ route('admin.pembayaran-listrik.index', ['periode' => $periodeAktif, 'rt' => $rtAktif, 'tab' => 'lunas']) }}"
           class="flex items-center justify-center gap-2 py-3 px-3 rounded-xl text-xs font-bold transition min-h-[48px] {{ $tab === 'lunas' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
            <span>Sudah Lunas</span>
            <span class="text-[10px] px-2 py-0.5 rounded-full {{ $totalLunas > 0 ? 'bg-emerald-100 text-emerald-800 font-bold' : 'bg-gray-100 text-gray-500' }}">
                {{ $totalLunas }}
            </span>
        </a>
    </div>

    <!-- Filter Pilihan RT 1, 2, 3 (Pills Mewah dengan Aksen Brand #CEA042) -->
    <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-xs">
        <a href="{{ route('admin.pembayaran-listrik.index', ['periode' => $periodeAktif, 'rt' => 'semua', 'tab' => $tab]) }}"
           class="px-3.5 py-2 rounded-xl font-bold whitespace-nowrap min-h-[44px] flex items-center {{ $rtAktif === 'semua' ? 'bg-gray-900 text-white shadow-xs' : 'bg-white text-gray-700 border border-gray-200 hover:bg-gray-50' }}">
            Semua RT
        </a>
        <a href="{{ route('admin.pembayaran-listrik.index', ['periode' => $periodeAktif, 'rt' => '1', 'tab' => $tab]) }}"
           class="px-3.5 py-2 rounded-xl font-bold whitespace-nowrap min-h-[44px] flex items-center {{ (string)$rtAktif === '1' ? 'bg-[#CEA042] text-white shadow-xs' : 'bg-white text-gray-700 border border-gray-200 hover:bg-gray-50' }}">
            RT 01
        </a>
        <a href="{{ route('admin.pembayaran-listrik.index', ['periode' => $periodeAktif, 'rt' => '2', 'tab' => $tab]) }}"
           class="px-3.5 py-2 rounded-xl font-bold whitespace-nowrap min-h-[44px] flex items-center {{ (string)$rtAktif === '2' ? 'bg-[#CEA042] text-white shadow-xs' : 'bg-white text-gray-700 border border-gray-200 hover:bg-gray-50' }}">
            RT 02
        </a>
        <a href="{{ route('admin.pembayaran-listrik.index', ['periode' => $periodeAktif, 'rt' => '3', 'tab' => $tab]) }}"
           class="px-3.5 py-2 rounded-xl font-bold whitespace-nowrap min-h-[44px] flex items-center {{ (string)$rtAktif === '3' ? 'bg-[#CEA042] text-white shadow-xs' : 'bg-white text-gray-700 border border-gray-200 hover:bg-gray-50' }}">
            RT 03
        </a>
    </div>

    <!-- SINKRONISASI SALDO REKAP VS REALISASI PEMBAYARAN KASIR -->
    <!-- 4 Kartu Rekonsiliasi Saldo (Harus 100% Sesuai Antara Rekap dan Pembayaran) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-3.5">
        <!-- Kotak 1: Saldo Yang Disetor ke PLN -->
        <div class="bg-white rounded-2xl border border-gray-200/90 p-3.5 sm:p-4 shadow-xs relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-[11px] font-bold text-gray-500 uppercase tracking-wider block">Setoran Ke PLN</span>
                @if($totalBelumLunas === 0 && $totalTagihan > 0)
                    <span class="text-[9px] font-black uppercase px-1.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800">Pas Rekap</span>
                @endif
            </div>
            <div class="text-lg sm:text-2xl font-black text-gray-900 tracking-tight mt-1">
                Rp {{ number_format($plnTerkumpul, 0, ',', '.') }}
            </div>
            <div class="mt-1.5 pt-1.5 border-t border-gray-100 text-[10px] sm:text-[11px] flex items-center justify-between">
                <span class="text-gray-500">Target Rekap:</span>
                <span class="font-bold text-gray-800">Rp {{ number_format($targetPlnAwal, 0, ',', '.') }}</span>
            </div>
            @if($plnBelumLunas > 0)
                <div class="mt-0.5 text-[10px] text-rose-600 font-semibold">
                    Belum terkumpul: Rp {{ number_format($plnBelumLunas, 0, ',', '.') }}
                </div>
            @else
                <div class="mt-0.5 text-[10px] text-emerald-700 font-bold flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    100% Siap Disetor ke PLN
                </div>
            @endif
        </div>

        <!-- Kotak 2: Saldo Masuk Kas Karang Taruna -->
        <div class="bg-white rounded-2xl border border-gray-200/90 p-3.5 sm:p-4 shadow-xs relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-[11px] font-bold text-[#CEA042] uppercase tracking-wider block">Kas Karang Taruna</span>
                @if($totalBelumLunas === 0 && $totalTagihan > 0)
                    <span class="text-[9px] font-black uppercase px-1.5 py-0.5 rounded-full bg-amber-100 text-amber-900">Pas Rekap</span>
                @endif
            </div>
            <div class="text-lg sm:text-2xl font-black text-[#CEA042] tracking-tight mt-1">
                Rp {{ number_format($kasKartarTerkumpul, 0, ',', '.') }}
            </div>
            <div class="mt-1.5 pt-1.5 border-t border-gray-100 text-[10px] sm:text-[11px] flex items-center justify-between">
                <span class="text-gray-500">Target Rekap:</span>
                <span class="font-bold text-amber-900">Rp {{ number_format($targetKasKartar, 0, ',', '.') }}</span>
            </div>
            @if($kasBelumLunas > 0)
                <div class="mt-0.5 text-[10px] text-rose-600 font-semibold">
                    Belum terkumpul: Rp {{ number_format($kasBelumLunas, 0, ',', '.') }}
                </div>
            @else
                <div class="mt-0.5 text-[10px] text-emerald-700 font-bold flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    100% Masuk Kas Kartar
                </div>
            @endif
        </div>

        <!-- Kotak 3: Status Lunas Wajib Tagihan -->
        <div class="bg-white rounded-2xl border border-gray-200/90 p-3.5 sm:p-4 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-[11px] font-bold text-gray-500 uppercase tracking-wider block">Total Tagihan Lunas</span>
                <span class="text-[10px] font-black text-gray-700 bg-gray-100 px-1.5 py-0.5 rounded">
                    {{ $persentaseLunas }}%
                </span>
            </div>
            <div class="text-lg sm:text-2xl font-black text-gray-900 tracking-tight mt-1">
                Rp {{ number_format($nominalLunas, 0, ',', '.') }}
            </div>
            <div class="mt-1.5 pt-1.5 border-t border-gray-100 text-[10px] sm:text-[11px] flex items-center justify-between">
                <span class="text-gray-500">Total Wajib:</span>
                <span class="font-bold text-gray-900">Rp {{ number_format($targetTotalWajib, 0, ',', '.') }}</span>
            </div>
            <div class="mt-0.5 text-[10px] text-gray-500">
                {{ $totalLunas }} dari {{ $totalTagihan }} warga lunas
            </div>
        </div>

        <!-- Kotak 4: Uang Fisik Kasir (Termasuk Titipan Sisa) -->
        <div class="bg-white rounded-2xl border border-gray-200/90 p-3.5 sm:p-4 shadow-xs">
            <span class="text-[10px] sm:text-[11px] font-bold text-emerald-700 uppercase tracking-wider block">Total Uang Kasir Riil</span>
            <div class="text-lg sm:text-2xl font-black text-emerald-700 tracking-tight mt-1">
                Rp {{ number_format($totalUangFisikTerkumpul, 0, ',', '.') }}
            </div>
            <div class="mt-1.5 pt-1.5 border-t border-gray-100 text-[10px] sm:text-[11px] flex items-center justify-between">
                <span class="text-gray-500">Titipan Sisa:</span>
                <span class="font-bold text-emerald-700">+Rp {{ number_format($totalTitipan, 0, ',', '.') }}</span>
            </div>
            <div class="mt-0.5 text-[10px] text-gray-400">
                Uang fisik riil di tangan kasir
            </div>
        </div>
    </div>

    <!-- BANNER REKONSILIASI KETIKA SELURUH PEMBAYARAN SELESAI -->
    @if($totalBelumLunas === 0 && $totalTagihan > 0)
        <div class="bg-emerald-50 border border-emerald-300 rounded-2xl p-4 sm:p-5 shadow-xs space-y-3.5">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-start gap-3">
                    <span class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-bold shrink-0 shadow-xs">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    </span>
                    <div>
                        <h2 class="text-sm sm:text-base font-black text-emerald-950">
                            Semua Pembayaran Lunas & Selesai!
                        </h2>
                        <p class="text-xs text-emerald-800 mt-0.5">
                            Saldo kasir terverifikasi 100% pas dan sinkron dengan rekap tagihan bulan {{ $periodeAktif }}{{ $rtAktif !== 'semua' ? ' RT '.$rtAktif : '' }}.
                        </p>
                    </div>
                </div>

                <!-- Status Pembukuan Kas Karang Taruna -->
                <div class="self-end sm:self-auto shrink-0">
                    @if($transaksiKas)
                        <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-200 text-emerald-900 text-xs font-bold border border-emerald-300">
                            <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            Kas Kartar Dibukukan ({{ $transaksiKas->kode_transaksi }})
                        </div>
                    @else
                        <form method="POST" action="{{ route('admin.pembayaran-listrik.bukukan-kas') }}"
                              onsubmit="return confirm('Bukukan kas Karang Taruna sebesar Rp {{ number_format($kasKartarTerkumpul, 0, ',', '.') }} ke pencatatan Keuangan Dusun?')">
                            @csrf
                            <input type="hidden" name="periode" value="{{ $periodeAktif }}">
                            <input type="hidden" name="rt" value="{{ $rtAktif }}">
                            <input type="hidden" name="nominal" value="{{ $kasKartarTerkumpul }}">
                            <button type="submit"
                                    class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-bold text-white bg-[#CEA042] hover:bg-[#BA8832] rounded-xl shadow-xs transition min-h-[44px]">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Bukukan Kas Kartar (Rp {{ number_format($kasKartarTerkumpul, 0, ',', '.') }})
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Rincian Alokasi Dana Hasil Pembayaran -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 pt-3 border-t border-emerald-200/80 text-xs">
                <div class="bg-white/80 rounded-xl p-3 border border-emerald-200">
                    <span class="text-[10px] text-gray-500 font-semibold uppercase block">1. Siap Disetor ke Loket PLN</span>
                    <div class="text-base font-black text-gray-900 mt-0.5">Rp {{ number_format($plnTerkumpul, 0, ',', '.') }}</div>
                    <span class="text-[10px] text-emerald-700 font-bold block mt-0.5">✓ 100% Cocok Tagihan PLN Awal</span>
                </div>

                <div class="bg-white/80 rounded-xl p-3 border border-emerald-200">
                    <span class="text-[10px] text-gray-500 font-semibold uppercase block">2. Masuk Kas Karang Taruna</span>
                    <div class="text-base font-black text-[#CEA042] mt-0.5">Rp {{ number_format($kasKartarTerkumpul, 0, ',', '.') }}</div>
                    <span class="text-[10px] text-amber-800 font-bold block mt-0.5">✓ 100% Cocok Kas Kartar</span>
                </div>

                <div class="bg-white/80 rounded-xl p-3 border border-emerald-200">
                    <span class="text-[10px] text-gray-500 font-semibold uppercase block">3. Total Uang Bersih di Kasir</span>
                    <div class="text-base font-black text-emerald-800 mt-0.5">Rp {{ number_format($totalUangFisikTerkumpul, 0, ',', '.') }}</div>
                    <span class="text-[10px] text-gray-500 block mt-0.5">Wajib: Rp {{ number_format($targetTotalWajib, 0, ',', '.') }} + Titipan: Rp {{ number_format($totalTitipan, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    @endif

    <!-- PENCARIAN REAL-TIME INSTAN (Tanpa Perlu Klik Tombol Cari) -->
    <div class="bg-white rounded-2xl border border-gray-200/80 p-3 shadow-2xs">
        <div class="relative flex items-center">
            <svg class="w-4 h-4 text-[#CEA042] absolute left-3 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text"
                   x-model="cari"
                   placeholder="Langsung ketik nomor struk [ # ] atau nama warga untuk memfilter..."
                   class="w-full text-sm rounded-xl border-gray-300 focus:border-[#CEA042] focus:ring-2 focus:ring-[#CEA042]/20 py-2.5 pl-9 pr-8 shadow-2xs min-h-[46px]">
            <button type="button"
                    x-show="cari.length > 0"
                    @click="cari = ''"
                    class="absolute right-2.5 p-1 text-gray-400 hover:text-gray-600 rounded-full">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="flex items-center justify-between text-[11px] text-gray-400 mt-1 px-1" x-show="cari.length > 0">
            <span>Filter instan aktif untuk: "<strong class="text-gray-700" x-text="cari"></strong>"</span>
            <button type="button" @click="cari = ''" class="text-rose-600 hover:underline">Hapus filter</button>
        </div>
    </div>

    <!-- DAFTAR TAGIHAN LIST KARTU MOBILE (Lapang, Tidak Padat Sesak) -->
    @if($daftarTagihan->isEmpty())
        <div class="bg-white rounded-2xl border border-dashed border-gray-300 p-8 text-center space-y-2">
            <p class="text-sm font-semibold text-gray-700">Tidak ada data tagihan listrik pada filter ini.</p>
            @if($tab === 'belum_bayar')
                <p class="text-xs text-emerald-600 font-bold">Semua warga pada filter ini sudah lunas!</p>
            @endif
        </div>
    @else
        <div class="space-y-3 pb-24">
            @foreach($daftarTagihan as $item)
                <div class="bg-white rounded-2xl border border-gray-200/90 p-4 shadow-xs transition hover:border-[#CEA042] relative"
                     x-show="cocokSearch('{{ addslashes($item->nama_warga) }}', '{{ addslashes($item->nama_pelanggan) }}', '{{ addslashes($item->nomor_struk) }}', '{{ addslashes($item->kode_gabungan ?? '') }}')"
                     :class="selectedIds.includes({{ $item->id }}) ? 'border-[#CEA042] ring-2 ring-[#CEA042]/20 bg-amber-50/20' : ''">
                    
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-start gap-2.5 min-w-0 flex-1">
                            @if(! $item->is_lunas)
                                <!-- Checkbox Bayar Gabungan -->
                                <button type="button" @click="toggleSelect({{ $item->id }})"
                                        class="mt-0.5 w-6 h-6 rounded-lg border-2 flex items-center justify-center transition shrink-0 min-h-[32px] min-w-[32px]"
                                        :class="selectedIds.includes({{ $item->id }}) ? 'bg-[#CEA042] border-[#CEA042] text-white' : 'border-gray-300 bg-white text-transparent'">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </button>
                            @endif

                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-amber-100 text-amber-900 font-bold font-mono text-xs border border-amber-200">
                                        [ {{ $item->nomor_struk }} ]
                                    </span>
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded-md bg-gray-100 text-gray-600 text-[10px] font-bold">
                                        RT {{ sprintf('%02d', $item->rt) }}
                                    </span>
                                    @if($item->kode_gabungan)
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-md bg-purple-100 text-purple-800 text-[10px] font-mono font-bold">
                                            {{ $item->kode_gabungan }}
                                        </span>
                                    @endif
                                </div>

                                <h2 class="text-sm font-bold text-gray-900 mt-1 leading-snug">
                                    {{ $item->nama_warga }}
                                </h2>
                                @if(!empty($item->nama_pembayar))
                                    <div class="inline-flex items-center gap-1 text-[11px] text-amber-900 font-bold bg-amber-100/80 px-2 py-0.5 rounded-md mt-1 border border-amber-300">
                                        <svg class="w-3 h-3 text-[#CEA042]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        <span>Dibayar oleh: {{ $item->nama_pembayar }}</span>
                                    </div>
                                @endif
                                <p class="text-[11px] text-gray-500 font-mono mt-0.5">
                                    PLN: {{ $item->nama_pelanggan }}
                                </p>
                            </div>
                        </div>

                        <!-- Nominal Tagihan Bersih Besar -->
                        <div class="text-right shrink-0">
                            <div class="text-[10px] text-gray-400 uppercase font-semibold">Total Wajib</div>
                            <div class="text-base sm:text-lg font-black text-gray-900">
                                Rp {{ number_format($item->total_tagihan, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>

                    <!-- Breakdown Biaya & Potongan Saldo Titipan Bulan Lalu -->
                    <div class="mt-3 pt-3 border-t border-gray-100 flex items-end justify-between text-xs">
                        <div class="space-y-1">
                            <div class="text-[11px] text-gray-500">
                                Struk: <span class="font-semibold text-gray-700">Rp {{ number_format($item->tagihan_awal, 0, ',', '.') }}</span> + Kartar: <span class="font-semibold text-[#CEA042]">Rp {{ number_format($item->kas_karang_taruna, 0, ',', '.') }}</span>
                            </div>

                            @if($item->saldo_periode_lalu > 0)
                                <div class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 text-[10px] font-bold border border-emerald-200">
                                    Potongan Titipan Bulan Lalu: -Rp {{ number_format($item->saldo_periode_lalu, 0, ',', '.') }}
                                </div>
                            @elseif($item->saldo_periode_lalu < 0)
                                <div class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-rose-50 text-rose-700 text-[10px] font-bold border border-rose-200">
                                    Tunggakan Bulan Lalu: +Rp {{ number_format(abs($item->saldo_periode_lalu), 0, ',', '.') }}
                                </div>
                            @endif
                        </div>

                        @if(! $item->is_lunas)
                            <!-- Tombol Bayar CTA (Konsisten Warna Brand #CEA042) -->
                            <button type="button"
                                    @click="bukaBayarTunggal({{ $item->id }}, '{{ addslashes($item->nama_warga) }}', '{{ addslashes($item->nomor_struk) }}', {{ $item->total_tagihan }}, {{ $item->saldo_periode_lalu }})"
                                    class="px-4 py-2 text-xs font-bold text-white bg-[#CEA042] hover:bg-[#BA8832] active:scale-95 rounded-xl shadow-xs transition min-h-[46px] flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                Bayar
                            </button>
                        @else
                            <div class="flex items-center gap-2">
                                @if($item->selisih_bayar > 0)
                                    <span class="text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md">
                                        Titipan: +Rp {{ number_format($item->selisih_bayar, 0, ',', '.') }}
                                    </span>
                                @elseif($item->selisih_bayar < 0)
                                    <span class="text-[11px] font-bold text-rose-700 bg-rose-50 px-2 py-0.5 rounded-md">
                                        Kurang: -Rp {{ number_format(abs($item->selisih_bayar), 0, ',', '.') }}
                                    </span>
                                @else
                                    <span class="text-[11px] font-semibold text-gray-500">
                                        Uang Pas
                                    </span>
                                @endif

                                <form method="POST" action="{{ route('admin.pembayaran-listrik.batal', $item) }}"
                                      onsubmit="return confirm('Batalkan status lunas warga {{ $item->nama_warga }}?')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                            class="px-2.5 py-1.5 text-[11px] font-semibold text-rose-600 hover:bg-rose-50 rounded-lg min-h-[44px] flex items-center">
                                        Batal Lunas
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- FLOATING ACTION BAR: BAYAR GABUNGAN (Warna Brand #CEA042) -->
    <div x-show="selectedIds.length > 0" x-cloak
         class="fixed bottom-16 lg:bottom-4 left-3 right-3 sm:left-auto sm:right-6 sm:w-96 z-40 bg-gray-900 text-white rounded-2xl p-4 shadow-2xl flex items-center justify-between gap-3 border border-gray-800 animate-slide-up">
        <div>
            <div class="text-[11px] text-gray-400 font-semibold" x-text="selectedIds.length + ' Tagihan Dipilih'">0 Tagihan Dipilih</div>
            <div class="text-sm font-black text-white" x-text="'Total: Rp ' + formatRupiah(totalTerpilih)">Total: Rp 0</div>
        </div>
        <button type="button" @click="bukaBayarGabungan()"
                class="px-4 py-2.5 text-xs font-bold text-white bg-[#CEA042] hover:bg-[#BA8832] rounded-xl transition min-h-[46px] flex items-center gap-1.5 shrink-0 shadow-xs">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
            Bayar Gabungan
        </button>
    </div>

    <!-- MODAL KASIR CEPAT / FAST POS (Bottom Sheet di HP) -->
    <div x-show="modalBayar" x-cloak
         class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-xs flex items-end sm:items-center justify-center p-0 sm:p-4">
        <div @click.away="modalBayar = false"
             class="bg-white rounded-t-3xl sm:rounded-2xl max-w-md w-full p-5 sm:p-6 shadow-2xl border border-gray-200 space-y-4 max-h-[90vh] overflow-y-auto">
            
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <div>
                    <h3 class="text-base font-bold text-gray-900">Kasir Pembayaran</h3>
                    <p class="text-xs text-gray-500 font-mono" x-text="'Struk: [' + bayarItem.struk + ']'"></p>
                </div>
                <button type="button" @click="modalBayar = false" class="text-gray-400 hover:text-gray-600 text-2xl p-1 leading-none">&times;</button>
            </div>

            <form method="POST" action="{{ route('admin.pembayaran-listrik.bayar') }}" class="space-y-4 text-xs">
                @csrf
                <template x-for="id in selectedIds" :key="id">
                    <input type="hidden" name="tagihan_ids[]" :value="id">
                </template>
                <input type="hidden" name="kode_gabungan" x-model="kodeGabungan">
                <!-- Hidden input berisi nilai murni tanpa titik untuk backend -->
                <input type="hidden" name="uang_dibayar" :value="uangDibayar">

                <!-- Informasi Pembayaran & Potongan Saldo -->
                <div class="bg-gray-50 rounded-xl p-3.5 border border-gray-200/80 space-y-2">
                    <div>
                        <div class="text-[11px] text-gray-500 font-semibold">Nama Warga:</div>
                        <div class="text-sm font-bold text-gray-900 leading-snug" x-text="bayarItem.nama"></div>
                    </div>

                    <div x-show="bayarItem.saldoLalu > 0" class="pt-2 border-t border-gray-200/60 flex items-center justify-between text-emerald-800 text-xs font-semibold">
                        <span>Potongan Titipan Bulan Lalu:</span>
                        <span x-text="'-Rp ' + formatRupiah(bayarItem.saldoLalu)"></span>
                    </div>

                    <div class="pt-2 border-t border-gray-200/60 flex items-center justify-between">
                        <span class="text-xs text-gray-700 font-bold">Total Wajib Bayar:</span>
                        <span class="text-base sm:text-lg font-black text-gray-900" x-text="'Rp ' + formatRupiah(bayarItem.total)">Rp 0</span>
                    </div>
                </div>

                <!-- Input Nama Pembayar (Opsional - Jika Diwakilkan / Titip Tetangga / Anak) -->
                <div>
                    <label class="block font-bold text-gray-700 uppercase mb-1">
                        Nama Pembayar <span class="text-gray-400 font-normal lowercase">(opsional)</span>
                    </label>
                    <input type="text"
                           name="nama_pembayar"
                           x-model="namaPembayar"
                           placeholder="Kosongkan jika yang membayar warga bersangkutan"
                           class="w-full text-base sm:text-xs rounded-xl border-gray-300 focus:border-[#CEA042] focus:ring-1 focus:ring-[#CEA042] py-2.5 px-3 min-h-[46px]">
                </div>

                <!-- Input Nominal Uang Diterima Real-Time Format Rupiah (20000 -> 20.000) -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block font-bold text-gray-700 uppercase">Uang Diterima (Rp) *</label>
                        <span class="text-[11px] text-gray-500 font-semibold" x-show="uangDibayar > 0" x-text="'Terbaca: Rp ' + formatRupiah(uangDibayar)"></span>
                    </div>
                    <div class="flex items-stretch rounded-2xl border border-gray-300 bg-white shadow-xs focus-within:border-[#CEA042] focus-within:ring-2 focus-within:ring-[#CEA042]/25 overflow-hidden transition-all min-h-[48px]">
                        <span class="flex items-center justify-center bg-gray-100/90 px-3.5 text-gray-500 font-black text-sm select-none border-r border-gray-200 shrink-0">
                            Rp
                        </span>
                        <input type="text"
                               inputmode="numeric"
                               x-model="uangDibayarInput"
                               @input="onInputUang($event)"
                               placeholder="Contoh: 50.000"
                               required
                               class="w-full text-lg font-black text-gray-900 border-0 focus:ring-0 focus:outline-none py-2.5 px-3 min-h-[48px] bg-transparent">
                    </div>
                    
                    <!-- Shortcut Cepat Nominal Uang Pas & Uang Lembaran -->
                    <div class="grid grid-cols-4 gap-1.5 mt-2">
                        <button type="button" @click="setNominal(bayarItem.total)"
                                class="py-1.5 px-2 bg-amber-50 hover:bg-amber-100 text-[#CEA042] rounded-lg font-bold text-[11px] min-h-[38px] border border-amber-200/70">
                            Uang Pas
                        </button>
                        <button type="button" @click="tambahNominal(2000)"
                                class="py-1.5 px-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg font-semibold text-[11px] min-h-[38px]">
                            +2.000
                        </button>
                        <button type="button" @click="tambahNominal(5000)"
                                class="py-1.5 px-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg font-semibold text-[11px] min-h-[38px]">
                            +5.000
                        </button>
                        <button type="button" @click="tambahNominal(10000)"
                                class="py-1.5 px-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg font-semibold text-[11px] min-h-[38px]">
                            +10.000
                        </button>
                    </div>
                    <div class="grid grid-cols-3 gap-1.5 mt-1.5">
                        <button type="button" @click="tambahNominal(20000)"
                                class="py-1.5 px-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg font-semibold text-[11px] min-h-[38px]">
                            +20.000
                        </button>
                        <button type="button" @click="tambahNominal(50000)"
                                class="py-1.5 px-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg font-semibold text-[11px] min-h-[38px]">
                            +50.000
                        </button>
                        <button type="button" @click="resetNominal()"
                                class="py-1.5 px-2 bg-rose-50 hover:bg-rose-100 text-rose-700 rounded-lg font-bold text-[11px] min-h-[38px] border border-rose-200/60">
                            Reset (C)
                        </button>
                    </div>
                </div>

                <!-- Live Status Kembalian vs Kurang vs Uang Pas -->
                <!-- KONDISI 1: ADA KEMBALIAN (Uang Diterima > Total Wajib) -->
                <div x-show="selisihUang > 0" class="space-y-2">
                    <div class="rounded-xl p-3 border"
                         :class="jadikanTitipan ? 'bg-amber-50 border-[#CEA042] text-amber-950' : 'bg-emerald-50 border-emerald-200 text-emerald-950'">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold uppercase tracking-wider"
                                  x-text="jadikanTitipan ? 'Titipan (Tidak Ada Kembalian):' : 'Kembalian Warga:'"></span>
                            <span class="text-base font-black"
                                  :class="jadikanTitipan ? 'text-[#CEA042]' : 'text-emerald-700'"
                                  x-text="'Rp ' + formatRupiah(kembalian)"></span>
                        </div>
                        <p class="text-[11px] mt-1 leading-relaxed"
                           :class="jadikanTitipan ? 'text-amber-800' : 'text-emerald-700'"
                           x-text="jadikanTitipan ? 'Sisa Rp ' + formatRupiah(kembalian) + ' disimpan sebagai saldo titipan bulan depan.' : 'Kembalikan uang Rp ' + formatRupiah(kembalian) + ' ke warga. Tidak ada titipan.'">
                        </p>
                    </div>

                    <!-- Pilihan Darurat: Titipan Hanya Berfungsi Jika Tidak Ada Kembalian -->
                    <label class="flex items-start gap-2.5 p-2.5 rounded-xl border cursor-pointer select-none transition"
                           :class="jadikanTitipan ? 'bg-amber-50/70 border-amber-300' : 'bg-gray-50 border-gray-200 hover:bg-gray-100/60'">
                        <input type="checkbox"
                               name="jadikan_titipan"
                               value="1"
                               x-model="jadikanTitipan"
                               class="mt-0.5 rounded border-gray-300 text-[#CEA042] focus:ring-[#CEA042] w-4 h-4">
                        <div class="text-[11px] leading-snug">
                            <span class="font-bold text-gray-900 block">Tidak ada uang kembalian?</span>
                            <span class="text-gray-500">Centang untuk menyimpan kembalian <strong class="text-gray-800" x-text="'Rp ' + formatRupiah(kembalian)"></strong> sebagai saldo titipan warga bulan depan.</span>
                        </div>
                    </label>
                </div>

                <!-- KONDISI 2: KURANG / NINGGAL (Uang Diterima < Total Wajib) -->
                <div x-show="selisihUang < 0 && uangDibayar > 0" class="rounded-xl p-3 bg-rose-50 border border-rose-200 text-rose-950">
                    <div class="flex items-center justify-between font-bold">
                        <span class="text-xs uppercase tracking-wider text-rose-800">Kurang / Ninggal:</span>
                        <span class="text-base font-black text-rose-700" x-text="'-Rp ' + formatRupiah(kurang)"></span>
                    </div>
                    <p class="text-[11px] mt-1 text-rose-700 leading-relaxed">
                        Warga kurang bayar <strong x-text="'Rp ' + formatRupiah(kurang)"></strong>. Kekurangan ini otomatis dicatat sebagai tunggakan bulan depan.
                    </p>
                </div>

                <!-- KONDISI 3: UANG PAS (Uang Diterima == Total Wajib) -->
                <div x-show="selisihUang === 0 && uangDibayar > 0" class="rounded-xl p-3 bg-blue-50 border border-blue-200 text-blue-950">
                    <div class="flex items-center justify-between font-bold">
                        <span class="text-xs uppercase tracking-wider text-blue-800">Status Pembayaran:</span>
                        <span class="text-sm font-black text-blue-700">Uang Pas (Lunas)</span>
                    </div>
                    <p class="text-[11px] mt-1 text-blue-700">
                        Kembalian Rp 0. Tidak ada sisa atau kekurangan.
                    </p>
                </div>

                <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2">
                    <button type="button" @click="modalBayar = false"
                            class="px-4 py-2.5 text-xs font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl min-h-[46px]">
                        Batal
                    </button>
                    <!-- Tombol Konfirmasi CTA (Konsisten Warna Brand #CEA042) -->
                    <button type="submit"
                            class="flex-1 px-4 py-2.5 text-xs font-bold text-white bg-[#CEA042] hover:bg-[#BA8832] rounded-xl shadow-xs min-h-[48px] flex items-center justify-center gap-2 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        Konfirmasi Lunas
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

@push('scripts')
<script>
function kasirListrik() {
    let allData = [];
    try {
        const rawEl = document.getElementById('tagihan-listrik-json');
        if (rawEl) {
            allData = JSON.parse(rawEl.textContent);
        }
    } catch (e) {
        console.error('Error parsing tagihan data', e);
    }

    return {
        tabAktif: '{{ $tab }}',
        cari: '{{ addslashes($cari ?? "") }}',
        modalBayar: false,
        selectedIds: [],
        bayarItem: {
            id: null,
            nama: '',
            struk: '',
            total: 0,
            saldoLalu: 0
        },
        uangDibayarInput: '',
        namaPembayar: '',
        jadikanTitipan: false,
        kodeGabungan: '',

        cocokSearch(warga, pln, struk, gab) {
            if (!this.cari || this.cari.trim() === '') return true;
            const q = this.cari.toLowerCase().trim();
            const w = (warga || '').toLowerCase();
            const p = (pln || '').toLowerCase();
            const s = (struk || '').toLowerCase();
            const g = (gab || '').toLowerCase();

            return w.includes(q) || p.includes(q) || s.includes(q) || g.includes(q);
        },

        bukaBayarTunggal(id, nama, struk, total, saldoLalu) {
            this.selectedIds = [id];
            this.bayarItem = {
                id: id,
                nama: nama,
                struk: struk,
                total: total,
                saldoLalu: saldoLalu || 0
            };
            this.uangDibayar = total;
            this.namaPembayar = '';
            this.jadikanTitipan = false;
            this.kodeGabungan = '';
            this.modalBayar = true;
        },

        bukaBayarGabungan() {
            if (this.selectedIds.length === 0) return;
            const filtered = allData.filter(t => this.selectedIds.includes(t.id));
            const total = filtered.reduce((acc, curr) => acc + (curr.total_tagihan || 0), 0);
            const names = filtered.map(t => t.nama_warga).join(', ');

            this.bayarItem = {
                id: null,
                nama: this.selectedIds.length + ' Warga (' + names + ')',
                struk: filtered.map(t => t.nomor_struk).join(', '),
                total: total,
                saldoLalu: 0
            };
            this.uangDibayar = total;
            this.namaPembayar = '';
            this.jadikanTitipan = false;
            this.kodeGabungan = 'GAB-' + Math.floor(1000 + Math.random() * 9000);
            this.modalBayar = true;
        },

        toggleSelect(id) {
            const index = this.selectedIds.indexOf(id);
            if (index > -1) {
                this.selectedIds.splice(index, 1);
            } else {
                this.selectedIds.push(id);
            }
        },

        get totalTerpilih() {
            return allData
                .filter(t => this.selectedIds.includes(t.id))
                .reduce((acc, curr) => acc + (curr.total_tagihan || 0), 0);
        },

        get uangDibayar() {
            return parseInt(this.uangDibayarInput.toString().replace(/[^0-9]/g, '')) || 0;
        },

        set uangDibayar(val) {
            let num = parseInt(val) || 0;
            this.uangDibayarInput = num > 0 ? this.formatRupiah(num) : '';
        },

        onInputUang(e) {
            let clean = e.target.value.replace(/[^0-9]/g, '');
            if (!clean) {
                this.uangDibayarInput = '';
                return;
            }
            this.uangDibayarInput = this.formatRupiah(parseInt(clean));
        },

        get selisihUang() {
            return this.uangDibayar - (this.bayarItem.total || 0);
        },

        get kembalian() {
            return Math.max(0, this.selisihUang);
        },

        get kurang() {
            return Math.max(0, -this.selisihUang);
        },

        setNominal(val) {
            this.uangDibayar = val;
            this.jadikanTitipan = false;
        },

        tambahNominal(val) {
            this.uangDibayar = this.uangDibayar + val;
        },

        resetNominal() {
            this.uangDibayar = 0;
            this.jadikanTitipan = false;
        },

        formatRupiah(num) {
            return new Intl.NumberFormat('id-ID').format(num || 0);
        }
    };
}
</script>
@endpush
@endsection
