@extends('layouts.admin')

@section('title', 'Kasir Pembayaran Listrik - Dusun Demangan')

@section('content')
<!-- Data JSON aman disuntikkan tanpa merusak HTML attribute -->
<script id="tagihan-listrik-json" type="application/json">
    {!! json_encode($daftarTagihan) !!}
</script>

<div class="space-y-4 sm:space-y-5" x-data="kasirListrik()" @keydown.escape.window="modalGabungkan = false; modalBayar = false">

    <!-- Top Card & Periode Switcher -->
    <div class="card-surface p-4 sm:p-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <div class="flex items-center gap-2.5">
                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-emas-500 text-[#1F1406] font-bold shrink-0 shadow-sm">
                        <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </span>
                    <div>
                        <h1 class="text-lg sm:text-xl font-bold text-slate-900 leading-tight">
                            Kasir Pembayaran Listrik
                        </h1>
                        <p class="text-xs text-slate-600">Penerimaan kasir, potongan saldo titipan bulan lalu &amp; kembalian warga</p>
                    </div>
                </div>
            </div>

            <!-- Pemilih Periode Kustom -->
            <div class="flex flex-col sm:flex-row sm:items-center gap-1.5 sm:gap-2.5" x-data="{ openDropdownPeriode: false }" @keydown.escape.window="openDropdownPeriode = false">
                <span class="text-xs font-bold text-slate-600 uppercase tracking-wider shrink-0">Siklus Periode:</span>
                <div class="relative w-full sm:w-64">
                    <button type="button"
                            @click="openDropdownPeriode = !openDropdownPeriode"
                            :aria-expanded="openDropdownPeriode.toString()"
                            aria-haspopup="listbox"
                            class="w-full flex items-center justify-between gap-2 text-xs font-bold text-slate-900 bg-emas-50 hover:bg-emas-100 border border-emas-300 hover:border-emas-500 rounded-xl py-2.5 px-3.5 min-h-[44px] shadow-sm transition">
                        <div class="flex items-center gap-2 truncate">
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-lg bg-emas-100 text-emas-800 shrink-0">
                                <svg class="w-3.5 h-3.5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </span>
                            <span class="truncate">{{ $periodeAktif }}</span>
                        </div>
                        <svg class="w-4 h-4 text-slate-600 transition-transform duration-200 shrink-0"
                             :class="openDropdownPeriode ? 'rotate-180 text-emas-800' : ''" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="openDropdownPeriode"
                         x-cloak
                         @click.away="openDropdownPeriode = false"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                         x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                         role="listbox"
                         class="absolute right-0 mt-2 z-50 w-72 bg-white rounded-2xl border border-emas-200 shadow-2xl p-2 space-y-1">
                        
                        <div class="px-3 py-1.5 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-100 flex items-center justify-between">
                            <span>Pilih Siklus Bulan</span>
                            <span class="text-emas-800 font-semibold tabular-money">{{ count($daftarPeriode) }} Periode</span>
                        </div>

                        <div class="max-h-60 overflow-y-auto space-y-1 py-1">
                            @foreach($daftarPeriode as $idx => $p)
                                @php
                                    $isAktif = ($periodeAktif === $p);
                                @endphp
                                <a href="{{ route('admin.pembayaran-listrik.index', ['periode' => $p, 'rt' => $rtAktif, 'tab' => $tab]) }}"
                                   class="flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-bold transition min-h-[44px] group {{ $isAktif ? 'bg-emas-500 text-[#1F1406] shadow-sm' : 'text-slate-700 hover:bg-emas-50 hover:text-emas-900' }}">
                                    <div class="flex items-center gap-2.5">
                                        <svg class="w-4 h-4 {{ $isAktif ? 'text-[#1F1406]' : 'text-slate-400 group-hover:text-emas-800' }}" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <span>{{ $p }}</span>
                                    </div>

                                    @if($isAktif)
                                        <span class="inline-flex items-center gap-1 text-[10px] px-2 py-0.5 rounded-full bg-[#1F1406]/10 text-[#1F1406] font-extrabold">
                                            <svg class="w-3 h-3" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                            Aktif
                                        </span>
                                    @elseif($idx === 0)
                                        <span class="text-[10px] px-2 py-0.5 rounded-full bg-emas-100 text-emas-900 font-bold group-hover:bg-emas-500 group-hover:text-[#1F1406] transition">
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

    <!-- Segmented Tab Status Pembayaran -->
    <div class="grid grid-cols-2 gap-2 bg-slate-200/80 p-1.5 rounded-2xl">
        <a href="{{ route('admin.pembayaran-listrik.index', ['periode' => $periodeAktif, 'rt' => $rtAktif, 'tab' => 'belum_bayar']) }}"
           class="flex items-center justify-center gap-2 py-3 px-3 rounded-xl text-xs font-bold transition min-h-[48px] {{ $tab === 'belum_bayar' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
            <span>Belum Bayar</span>
            @if($totalBelumLunas > 0)
                <span class="text-[11px] px-2 py-0.5 rounded-full bg-rose-600 text-white font-extrabold tabular-money">
                    {{ $totalBelumLunas }}
                </span>
            @else
                <span class="text-[11px] px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-bold">
                    Nihil
                </span>
            @endif
        </a>

        <a href="{{ route('admin.pembayaran-listrik.index', ['periode' => $periodeAktif, 'rt' => $rtAktif, 'tab' => 'lunas']) }}"
           class="flex items-center justify-center gap-2 py-3 px-3 rounded-xl text-xs font-bold transition min-h-[48px] {{ $tab === 'lunas' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
            <span>Sudah Lunas</span>
            <span class="text-[11px] px-2 py-0.5 rounded-full tabular-money {{ $totalLunas > 0 ? 'bg-emerald-100 text-emerald-800 font-bold' : 'bg-slate-100 text-slate-600' }}">
                {{ $totalLunas }}
            </span>
        </a>
    </div>

    <!-- Filter Pilihan RT 1, 2, 3 -->
    <div class="flex items-center gap-2 overflow-x-auto pb-1 text-xs">
        <a href="{{ route('admin.pembayaran-listrik.index', ['periode' => $periodeAktif, 'rt' => 'semua', 'tab' => $tab]) }}"
           class="px-3.5 py-2 rounded-xl font-bold whitespace-nowrap min-h-[44px] flex items-center transition {{ $rtAktif === 'semua' ? 'bg-slate-900 text-white shadow-sm' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-50' }}">
            Semua RT
        </a>
        <a href="{{ route('admin.pembayaran-listrik.index', ['periode' => $periodeAktif, 'rt' => '1', 'tab' => $tab]) }}"
           class="px-3.5 py-2 rounded-xl font-bold whitespace-nowrap min-h-[44px] flex items-center transition {{ (string)$rtAktif === '1' ? 'bg-emas-500 text-[#1F1406] shadow-sm' : 'bg-white text-slate-700 border border-slate-200 hover:bg-emas-50 hover:border-emas-300' }}">
            RT 01
        </a>
        <a href="{{ route('admin.pembayaran-listrik.index', ['periode' => $periodeAktif, 'rt' => '2', 'tab' => $tab]) }}"
           class="px-3.5 py-2 rounded-xl font-bold whitespace-nowrap min-h-[44px] flex items-center transition {{ (string)$rtAktif === '2' ? 'bg-emas-500 text-[#1F1406] shadow-sm' : 'bg-white text-slate-700 border border-slate-200 hover:bg-emas-50 hover:border-emas-300' }}">
            RT 02
        </a>
        <a href="{{ route('admin.pembayaran-listrik.index', ['periode' => $periodeAktif, 'rt' => '3', 'tab' => $tab]) }}"
           class="px-3.5 py-2 rounded-xl font-bold whitespace-nowrap min-h-[44px] flex items-center transition {{ (string)$rtAktif === '3' ? 'bg-emas-500 text-[#1F1406] shadow-sm' : 'bg-white text-slate-700 border border-slate-200 hover:bg-emas-50 hover:border-emas-300' }}">
            RT 03
        </a>
    </div>

    <!-- 4 Kartu Rekonsiliasi Saldo -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-3.5">
        <!-- Kotak 1: Saldo Yang Disetor ke PLN -->
        <div class="card-surface p-3.5 sm:p-4 relative overflow-hidden">
            <div class="flex items-center justify-between gap-1">
                <span class="text-[11px] font-bold text-slate-600 uppercase tracking-wider block">Setoran Ke PLN</span>
                @if($totalBelumLunas === 0 && $totalTagihan > 0)
                    <span class="text-[10px] font-extrabold uppercase px-1.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800">Pas Rekap</span>
                @endif
            </div>
            <div class="text-lg sm:text-2xl font-extrabold text-slate-900 tracking-tight mt-1 tabular-money">
                Rp {{ number_format($plnTerkumpul, 0, ',', '.') }}
            </div>
            <div class="mt-1.5 pt-1.5 border-t border-slate-100 text-[11px] flex items-center justify-between gap-1">
                <span class="text-slate-500">Target Rekap:</span>
                <span class="font-bold text-slate-800 tabular-money">Rp {{ number_format($targetPlnAwal, 0, ',', '.') }}</span>
            </div>
            @if($plnBelumLunas > 0)
                <div class="mt-0.5 text-[11px] text-rose-700 font-semibold tabular-money">
                    Belum terkumpul: Rp {{ number_format($plnBelumLunas, 0, ',', '.') }}
                </div>
            @else
                <div class="mt-0.5 text-[11px] text-emerald-700 font-bold flex items-center gap-1">
                    <svg class="w-3 h-3 shrink-0" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    100% Siap Disetor ke PLN
                </div>
            @endif
        </div>

        <!-- Kotak 2: Saldo Masuk Kas Karang Taruna -->
        <div class="card-surface p-3.5 sm:p-4 relative overflow-hidden">
            <div class="flex items-center justify-between gap-1">
                <span class="text-[11px] font-bold text-emas-800 uppercase tracking-wider block">Kas Karang Taruna</span>
                @if($totalBelumLunas === 0 && $totalTagihan > 0)
                    <span class="text-[10px] font-extrabold uppercase px-1.5 py-0.5 rounded-full bg-emas-100 text-emas-900">Pas Rekap</span>
                @endif
            </div>
            <div class="text-lg sm:text-2xl font-extrabold text-emas-800 tracking-tight mt-1 tabular-money">
                Rp {{ number_format($kasKartarTerkumpul, 0, ',', '.') }}
            </div>
            <div class="mt-1.5 pt-1.5 border-t border-slate-100 text-[11px] flex items-center justify-between gap-1">
                <span class="text-slate-500">Target Rekap:</span>
                <span class="font-bold text-emas-900 tabular-money">Rp {{ number_format($targetKasKartar, 0, ',', '.') }}</span>
            </div>
            @if($kasBelumLunas > 0)
                <div class="mt-0.5 text-[11px] text-rose-700 font-semibold tabular-money">
                    Belum terkumpul: Rp {{ number_format($kasBelumLunas, 0, ',', '.') }}
                </div>
            @else
                <div class="mt-0.5 text-[11px] text-emerald-700 font-bold flex items-center gap-1">
                    <svg class="w-3 h-3 shrink-0" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    100% Masuk Kas Kartar
                </div>
            @endif
        </div>

        <!-- Kotak 3: Status Lunas Wajib Tagihan -->
        <div class="card-surface p-3.5 sm:p-4">
            <div class="flex items-center justify-between gap-1">
                <span class="text-[11px] font-bold text-slate-600 uppercase tracking-wider block">Total Tagihan Lunas</span>
                <span class="text-[11px] font-extrabold text-slate-800 bg-slate-100 px-1.5 py-0.5 rounded tabular-money">
                    {{ $persentaseLunas }}%
                </span>
            </div>
            <div class="text-lg sm:text-2xl font-extrabold text-slate-900 tracking-tight mt-1 tabular-money">
                Rp {{ number_format($nominalLunas, 0, ',', '.') }}
            </div>
            <div class="mt-1.5 pt-1.5 border-t border-slate-100 text-[11px] flex items-center justify-between gap-1">
                <span class="text-slate-500">Total Wajib:</span>
                <span class="font-bold text-slate-900 tabular-money">Rp {{ number_format($targetTotalWajib, 0, ',', '.') }}</span>
            </div>
            <div class="mt-0.5 text-[11px] text-slate-600 tabular-money">
                {{ $totalLunas }} dari {{ $totalTagihan }} warga lunas
            </div>
        </div>

        <!-- Kotak 4: Uang Fisik Kasir (Termasuk Titipan Sisa) -->
        <div class="card-surface p-3.5 sm:p-4">
            <span class="text-[11px] font-bold text-emerald-800 uppercase tracking-wider block">Total Uang Kasir Riil</span>
            <div class="text-lg sm:text-2xl font-extrabold text-emerald-800 tracking-tight mt-1 tabular-money">
                Rp {{ number_format($totalUangFisikTerkumpul, 0, ',', '.') }}
            </div>
            <div class="mt-1.5 pt-1.5 border-t border-slate-100 text-[11px] flex items-center justify-between gap-1">
                <span class="text-slate-500">Titipan Sisa:</span>
                <span class="font-bold text-emerald-800 tabular-money">+Rp {{ number_format($totalTitipan, 0, ',', '.') }}</span>
            </div>
            <div class="mt-0.5 text-[11px] text-slate-500">
                Uang fisik riil di tangan kasir
            </div>
        </div>
    </div>

    <!-- BANNER REKONSILIASI KETIKA SELURUH PEMBAYARAN SELESAI -->
    @if($totalBelumLunas === 0 && $totalTagihan > 0)
        <div class="bg-emerald-50 border border-emerald-300 rounded-2xl p-4 sm:p-5 shadow-sm space-y-3.5">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-start gap-3">
                    <span class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-bold shrink-0 shadow-sm">
                        <svg class="w-6 h-6" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    </span>
                    <div>
                        <h2 class="text-sm sm:text-base font-extrabold text-emerald-950">
                            Semua Pembayaran Lunas &amp; Selesai!
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
                            <svg class="w-4 h-4 text-emerald-800 shrink-0" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            Kas Kartar Dibukukan ({{ $transaksiKas->kode_transaksi }})
                        </div>
                    @else
                        <form method="POST" action="{{ route('admin.pembayaran-listrik.bukukan-kas') }}"
                              onsubmit="return confirm('Bukukan kas Karang Taruna sebesar Rp {{ number_format($kasKartarTerkumpul, 0, ',', '.') }} ke pencatatan Keuangan Dusun?')">
                            @csrf
                            <input type="hidden" name="periode" value="{{ $periodeAktif }}">
                            <input type="hidden" name="rt" value="{{ $rtAktif }}">
                            <input type="hidden" name="nominal" value="{{ $kasKartarTerkumpul }}">
                            <button type="submit" class="btn-primary text-xs px-3.5 py-2 tabular-money">
                                <svg class="w-4 h-4" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Bukukan Kas Kartar (Rp {{ number_format($kasKartarTerkumpul, 0, ',', '.') }})
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Rincian Alokasi Dana Hasil Pembayaran -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 pt-3 border-t border-emerald-200/80 text-xs">
                <div class="bg-white/90 rounded-xl p-3 border border-emerald-200">
                    <span class="text-[11px] text-slate-600 font-semibold uppercase block">1. Siap Disetor ke Loket PLN</span>
                    <div class="text-base font-extrabold text-slate-900 mt-0.5 tabular-money">Rp {{ number_format($plnTerkumpul, 0, ',', '.') }}</div>
                    <span class="text-[11px] text-emerald-800 font-bold inline-flex items-center gap-1 mt-0.5">
                        <svg class="w-3.5 h-3.5 shrink-0" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        100% Cocok Tagihan PLN Awal
                    </span>
                </div>

                <div class="bg-white/90 rounded-xl p-3 border border-emerald-200">
                    <span class="text-[11px] text-slate-600 font-semibold uppercase block">2. Masuk Kas Karang Taruna</span>
                    <div class="text-base font-extrabold text-emas-800 mt-0.5 tabular-money">Rp {{ number_format($kasKartarTerkumpul, 0, ',', '.') }}</div>
                    <span class="text-[11px] text-emas-900 font-bold inline-flex items-center gap-1 mt-0.5">
                        <svg class="w-3.5 h-3.5 shrink-0" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        100% Cocok Kas Kartar
                    </span>
                </div>

                <div class="bg-white/90 rounded-xl p-3 border border-emerald-200">
                    <span class="text-[11px] text-slate-600 font-semibold uppercase block">3. Total Uang Bersih di Kasir</span>
                    <div class="text-base font-extrabold text-emerald-800 mt-0.5 tabular-money">Rp {{ number_format($totalUangFisikTerkumpul, 0, ',', '.') }}</div>
                    <span class="text-[11px] text-slate-600 block mt-0.5 tabular-money">Wajib: Rp {{ number_format($targetTotalWajib, 0, ',', '.') }} + Titipan: Rp {{ number_format($totalTitipan, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    @endif

    <!-- PENCARIAN REAL-TIME INSTAN -->
    <div class="card-surface p-3">
        <div class="relative flex items-center">
            <svg class="w-4 h-4 text-emas-800 absolute left-3.5 pointer-events-none" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text"
                   x-model="cari"
                   aria-label="Cari nomor struk atau nama warga"
                   placeholder="Langsung ketik nomor struk [ # ] atau nama warga untuk memfilter..."
                   class="input-field pl-10 pr-10 min-h-[46px]">
            <button type="button"
                    x-show="cari.length > 0"
                    @click="cari = ''"
                    aria-label="Hapus kata kunci pencarian"
                    class="absolute right-2 p-2 text-slate-500 hover:text-slate-800 rounded-lg min-h-[40px] min-w-[40px] flex items-center justify-center">
                <svg class="w-4 h-4" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="flex items-center justify-between text-[11px] text-slate-600 mt-1.5 px-1" x-show="cari.length > 0">
            <span>Filter instan aktif untuk: "<strong class="text-slate-900" x-text="cari"></strong>"</span>
            <button type="button" @click="cari = ''" class="text-rose-700 font-semibold hover:underline">Hapus filter</button>
        </div>
    </div>

    <!-- DAFTAR TAGIHAN LIST KARTU MOBILE -->
    @if($daftarTagihan->isEmpty())
        <div class="card-surface border-dashed border-slate-300 p-8 text-center space-y-2">
            <p class="text-sm font-semibold text-slate-700">Tidak ada data tagihan listrik pada filter ini.</p>
            @if($tab === 'belum_bayar')
                <p class="text-xs text-emerald-700 font-bold">Semua warga pada filter ini sudah lunas!</p>
            @endif
        </div>
    @else
        <div class="space-y-3 pb-24">
            @foreach($daftarTampilan as $grup)
                @if($grup['is_gabungan'])
                    @php
                        $searchString = $grup['nama_pembayar'].' '.$grup['kode_gabungan'].' '.$grup['items']->pluck('nama_warga')->implode(' ').' '.$grup['items']->pluck('nama_pelanggan')->implode(' ').' '.$grup['items']->pluck('nomor_struk')->implode(' ');
                        $subStrukList = $grup['items']->pluck('nomor_struk')->map(fn($s) => '#'.$s)->implode(', ');
                    @endphp
                    <!-- KARTU TAGIHAN GABUNGAN (Hasil Rekapitulasi / Kasir) -->
                    <div class="bg-gradient-to-br from-emas-50 via-white to-slate-50 rounded-2xl border-2 border-emas-300 p-4 shadow-sm transition hover:shadow-md hover:border-emas-400 relative space-y-3"
                         x-data="{ rincianTerbuka: false }"
                         x-show="cocokSearch('{{ addslashes($searchString) }}')">
                        
                        <!-- Header Struk Gabungan -->
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-start gap-2.5 min-w-0 flex-1">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-emas-500 text-[#1F1406] font-bold shrink-0 shadow-sm mt-0.5">
                                    <svg class="w-4 h-4" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                </span>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-slate-900 text-emas-300 font-mono font-bold text-xs border border-slate-800">
                                            {{ $grup['kode_gabungan'] }}
                                        </span>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-emas-100 text-emas-900 text-[11px] font-bold">
                                            {{ count($grup['items']) }} Tagihan Gabungan
                                        </span>
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-md bg-slate-100 text-slate-700 text-[11px] font-bold">
                                            RT {{ sprintf('%02d', $grup['rt']) }}
                                        </span>
                                    </div>

                                    <!-- Judul Atas Nama Siapa -->
                                    <h2 class="text-base font-extrabold text-slate-900 mt-1 leading-snug">
                                        An. {{ $grup['nama_pembayar'] }}
                                    </h2>
                                    <p class="text-xs text-slate-600 font-medium mt-0.5">
                                        Struk: <span class="font-mono font-bold text-slate-800">{{ $subStrukList }}</span>
                                    </p>
                                </div>
                            </div>

                            <!-- Total Bersih Gabungan -->
                            <div class="text-right shrink-0">
                                <div class="text-[11px] text-slate-500 uppercase font-bold tracking-wider">Total Gabungan</div>
                                <div class="text-base sm:text-xl font-extrabold text-slate-900 tabular-money">
                                    Rp {{ number_format($grup['total_tagihan'], 0, ',', '.') }}
                                </div>
                            </div>
                        </div>

                        <!-- Daftar Warga / Struk di Dalam Gabungan -->
                        <div class="flex flex-wrap gap-1.5 pt-1">
                            @foreach($grup['items'] as $sub)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-xs text-slate-700 shadow-sm">
                                    <strong class="font-mono text-emas-900">#{{ $sub->nomor_struk }}</strong>
                                    <span>{{ $sub->nama_warga }}</span>
                                    <span class="text-slate-500 font-mono tabular-money">Rp {{ number_format($sub->total_tagihan, 0, ',', '.') }}</span>
                                </span>
                            @endforeach
                        </div>

                        <!-- Accordion Toggle Rincian Lengkap Meter -->
                        <div>
                            <button type="button"
                                    @click="rincianTerbuka = !rincianTerbuka"
                                    :aria-expanded="rincianTerbuka.toString()"
                                    class="text-xs font-bold text-emas-800 hover:text-emas-900 hover:underline inline-flex items-center gap-1.5 py-1 min-h-[36px]">
                                <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="rincianTerbuka ? 'rotate-180' : ''" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                                <span x-text="rincianTerbuka ? 'Sembunyikan Rincian Meter' : 'Lihat Rincian Tiap Meter ({{ count($grup['items']) }} Warga)'"></span>
                            </button>

                            <div x-show="rincianTerbuka" x-cloak class="mt-2 space-y-1.5 pt-2 border-t border-emas-200/70">
                                @foreach($grup['items'] as $sub)
                                    <div class="p-2.5 rounded-xl bg-white/90 border border-slate-200/80 text-xs flex items-center justify-between gap-2">
                                        <div>
                                            <span class="font-mono font-bold text-emas-900">[ #{{ $sub->nomor_struk }} ]</span>
                                            <strong class="text-slate-900 ml-1">{{ $sub->nama_warga }}</strong>
                                            <span class="text-slate-500 font-mono block text-[11px]">PLN: {{ $sub->nama_pelanggan }}</span>
                                        </div>
                                        <div class="text-right shrink-0">
                                            <div class="font-mono font-bold text-slate-900 tabular-money">Rp {{ number_format($sub->total_tagihan, 0, ',', '.') }}</div>
                                            <div class="text-[11px] text-slate-500 tabular-money">PLN: {{ number_format($sub->tagihan_awal, 0, ',', '.') }} + Kartar: {{ number_format($sub->kas_karang_taruna, 0, ',', '.') }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Tombol Aksi Kasir -->
                        <div class="pt-2.5 border-t border-emas-200/80 flex items-center justify-between gap-2 flex-wrap">
                            @if(! $grup['is_lunas'])
                                <!-- Tombol Pisahkan jika warga ingin bayar mandiri -->
                                <form method="POST" action="{{ route('admin.pembayaran-listrik.pisahkan') }}"
                                      onsubmit="return confirm('Pisahkan kelompok struk gabungan ini menjadi tagihan mandiri kembali?')">
                                    @csrf
                                    <input type="hidden" name="kode_gabungan" value="{{ $grup['kode_gabungan'] }}">
                                    <button type="submit"
                                            class="btn-subtle text-xs px-3 py-2">
                                        <svg class="w-3.5 h-3.5 text-slate-500" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                        Pisahkan Struk
                                    </button>
                                </form>

                                <!-- Tombol Bayar Gabungan Langsung -->
                                <button type="button"
                                        @click="bukaBayarGrupItem({{ json_encode($grup['items']->pluck('id')) }}, '{{ addslashes($grup['nama_pembayar']) }}', '{{ addslashes($subStrukList) }}', {{ $grup['total_tagihan'] }}, {{ $grup['saldo_periode_lalu'] }}, '{{ $grup['kode_gabungan'] }}')"
                                        class="btn-primary flex-1 sm:flex-initial px-5 py-2.5 text-xs min-h-[46px] tabular-money">
                                    <svg class="w-4 h-4" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                    <span>Bayar Gabungan (Rp {{ number_format($grup['total_tagihan'], 0, ',', '.') }})</span>
                                </button>
                            @else
                                <div class="flex items-center justify-between w-full gap-2 flex-wrap">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="inline-flex items-center gap-1 text-xs font-bold text-emerald-800 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200">
                                            <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                            Lunas Gabungan ({{ count($grup['items']) }} Tagihan)
                                        </span>
                                        @if($grup['selisih_bayar'] > 0)
                                            <span class="text-[11px] font-bold text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded-md tabular-money">
                                                Titipan: +Rp {{ number_format($grup['selisih_bayar'], 0, ',', '.') }}
                                            </span>
                                        @elseif($grup['selisih_bayar'] < 0)
                                            <span class="text-[11px] font-bold text-rose-700 bg-rose-50 px-2 py-0.5 rounded-md tabular-money">
                                                Kurang: -Rp {{ number_format(abs($grup['selisih_bayar']), 0, ',', '.') }}
                                            </span>
                                        @endif
                                    </div>

                                    <form method="POST" action="{{ route('admin.pembayaran-listrik.batal', $grup['items']->first()) }}"
                                          onsubmit="return confirm('Batalkan status lunas seluruh struk gabungan ini ({{ count($grup['items']) }} warga)?')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="px-3 py-1.5 text-xs font-semibold text-rose-700 hover:bg-rose-50 rounded-xl min-h-[44px] flex items-center transition">
                                            Batal Lunas Semua
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    @php
                        $item = $grup['single_item'];
                    @endphp
                    <!-- KARTU TAGIHAN MANDIRI -->
                    <div class="card-surface p-4 transition hover:border-emas-400 relative"
                         x-show="cocokSearch('{{ addslashes($item->nama_warga) }}', '{{ addslashes($item->nama_pelanggan) }}', '{{ addslashes($item->nomor_struk) }}', '{{ addslashes($item->nama_pembayar ?? '') }}')"
                         :class="selectedIds.includes({{ $item->id }}) ? 'border-emas-500 ring-2 ring-emas-500/20 bg-emas-50/40' : ''">
                        
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-start gap-2.5 min-w-0 flex-1">
                                @if(! $item->is_lunas)
                                    <!-- Checkbox Bayar Gabungan Dinamis di Kasir -->
                                    <button type="button"
                                            @click="toggleSelect({{ $item->id }})"
                                            aria-label="Pilih tagihan {{ $item->nama_warga }} nomor struk {{ $item->nomor_struk }}"
                                            :aria-pressed="selectedIds.includes({{ $item->id }}).toString()"
                                            class="mt-0.5 w-9 h-9 rounded-xl border-2 flex items-center justify-center transition shrink-0"
                                            :class="selectedIds.includes({{ $item->id }}) ? 'bg-emas-500 border-emas-500 text-[#1F1406]' : 'border-slate-300 bg-white text-transparent hover:border-emas-400'">
                                        <svg class="w-4 h-4" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                    </button>
                                @endif

                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-emas-100 text-emas-900 font-bold font-mono text-xs border border-emas-200">
                                            [ {{ $item->nomor_struk }} ]
                                        </span>
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-md bg-slate-100 text-slate-700 text-[11px] font-bold">
                                            RT {{ sprintf('%02d', $item->rt) }}
                                        </span>
                                    </div>

                                    <h2 class="text-sm font-bold text-slate-900 mt-1 leading-snug">
                                        {{ $item->nama_warga }}
                                    </h2>
                                    @if(!empty($item->nama_pembayar))
                                        <div class="inline-flex items-center gap-1 text-[11px] text-emas-900 font-bold bg-emas-100 px-2 py-0.5 rounded-md mt-1 border border-emas-300">
                                            <svg class="w-3 h-3 text-emas-800 shrink-0" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                            <span>Dibayar oleh: {{ $item->nama_pembayar }}</span>
                                        </div>
                                    @endif
                                    <p class="text-[11px] text-slate-500 font-mono mt-0.5">
                                        PLN: {{ $item->nama_pelanggan }}
                                    </p>
                                </div>
                            </div>

                            <!-- Nominal Tagihan Bersih -->
                            <div class="text-right shrink-0">
                                <div class="text-[11px] text-slate-500 uppercase font-semibold">Total Wajib</div>
                                <div class="text-base sm:text-lg font-extrabold text-slate-900 tabular-money">
                                    Rp {{ number_format($item->total_tagihan, 0, ',', '.') }}
                                </div>
                            </div>
                        </div>

                        <!-- Breakdown Biaya & Potongan Saldo Titipan Bulan Lalu -->
                        <div class="mt-3 pt-3 border-t border-slate-100 flex items-end justify-between gap-2 text-xs">
                            <div class="space-y-1">
                                <div class="text-[11px] text-slate-600 tabular-money">
                                    Struk: <span class="font-semibold text-slate-800">Rp {{ number_format($item->tagihan_awal, 0, ',', '.') }}</span> + Kartar: <span class="font-semibold text-emas-800">Rp {{ number_format($item->kas_karang_taruna, 0, ',', '.') }}</span>
                                </div>

                                @if($item->saldo_periode_lalu > 0)
                                    <div class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-800 text-[11px] font-bold border border-emerald-200 tabular-money">
                                        Potongan Titipan Bulan Lalu: -Rp {{ number_format($item->saldo_periode_lalu, 0, ',', '.') }}
                                    </div>
                                @elseif($item->saldo_periode_lalu < 0)
                                    <div class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-rose-50 text-rose-700 text-[11px] font-bold border border-rose-200 tabular-money">
                                        Tunggakan Bulan Lalu: +Rp {{ number_format(abs($item->saldo_periode_lalu), 0, ',', '.') }}
                                    </div>
                                @endif
                            </div>

                            @if(! $item->is_lunas)
                                <!-- Tombol Bayar CTA -->
                                <button type="button"
                                        @click="bukaBayarTunggal({{ $item->id }}, '{{ addslashes($item->nama_warga) }}', '{{ addslashes($item->nomor_struk) }}', {{ $item->total_tagihan }}, {{ $item->saldo_periode_lalu }})"
                                        class="btn-primary px-4 py-2 text-xs min-h-[46px] shrink-0">
                                    <svg class="w-4 h-4" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                    Bayar
                                </button>
                            @else
                                <div class="flex items-center gap-2">
                                    @if($item->selisih_bayar > 0)
                                        <span class="text-[11px] font-bold text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded-md tabular-money">
                                            Titipan: +Rp {{ number_format($item->selisih_bayar, 0, ',', '.') }}
                                        </span>
                                    @elseif($item->selisih_bayar < 0)
                                        <span class="text-[11px] font-bold text-rose-700 bg-rose-50 px-2 py-0.5 rounded-md tabular-money">
                                            Kurang: -Rp {{ number_format(abs($item->selisih_bayar), 0, ',', '.') }}
                                        </span>
                                    @else
                                        <span class="text-[11px] font-semibold text-slate-600">
                                            Uang Pas
                                        </span>
                                    @endif

                                    <form method="POST" action="{{ route('admin.pembayaran-listrik.batal', $item) }}"
                                          onsubmit="return confirm('Batalkan status lunas warga {{ $item->nama_warga }}?')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="px-2.5 py-1.5 text-[11px] font-semibold text-rose-700 hover:bg-rose-50 rounded-lg min-h-[44px] flex items-center transition">
                                            Batal Lunas
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    @endif

    <!-- FLOATING ACTION BAR: BAYAR / GABUNGKAN DINAMIS -->
    <div x-show="selectedIds.length > 0" x-cloak
         class="fixed bottom-16 lg:bottom-4 left-3 right-3 sm:left-auto sm:right-6 sm:w-[26rem] z-40 bg-slate-900 text-white rounded-2xl p-4 shadow-2xl flex items-center justify-between gap-2 border border-slate-800">
        <div>
            <div class="text-[11px] text-slate-300 font-semibold tabular-money" x-text="selectedIds.length + ' Tagihan Dipilih'">0 Tagihan Dipilih</div>
            <div class="text-sm font-extrabold text-white tabular-money" x-text="'Total: Rp ' + formatRupiah(totalTerpilih)">Total: Rp 0</div>
        </div>
        <div class="flex items-center gap-1.5">
            <button type="button" @click="selectedIds = []" class="px-2.5 py-2 text-xs text-slate-300 hover:text-white min-h-[44px] transition">
                Batal
            </button>
            <template x-if="selectedIds.length >= 2">
                <button type="button" @click="bukaModalGabungkanDulu()"
                        class="px-3 py-2 text-xs font-semibold text-emas-200 bg-slate-800 hover:bg-slate-700 border border-slate-700 rounded-xl transition min-h-[44px] flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 shrink-0" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                    Gabung Dulu
                </button>
            </template>
            <button type="button" @click="selectedIds.length >= 2 ? bukaBayarGabungan() : bukaBayarTerpilihTunggal()"
                    class="btn-primary px-3.5 py-2 text-xs shrink-0">
                <svg class="w-4 h-4" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                <span x-text="selectedIds.length >= 2 ? 'Bayar Gabungan' : 'Bayar'"></span>
            </button>
        </div>
    </div>

    <!-- MODAL GABUNGKAN TAGIHAN DINAMIS DI KASIR -->
    <div x-show="modalGabungkan" x-cloak
         class="modal-backdrop"
         role="dialog"
         aria-modal="true"
         aria-labelledby="modal-gabungkan-title">
        <div @click.away="modalGabungkan = false"
             class="modal-panel max-w-lg p-5 sm:p-6 space-y-4">
            <div class="w-10 h-1 rounded-full bg-slate-200 mx-auto sm:hidden"></div>
            
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2.5">
                    <span class="w-9 h-9 rounded-xl bg-emas-100 text-emas-800 flex items-center justify-center font-bold shrink-0">
                        <svg class="w-4 h-4" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                    </span>
                    <div>
                        <h3 id="modal-gabungkan-title" class="text-base font-bold text-slate-900 leading-tight">Gabungkan Tagihan Warga</h3>
                        <p class="text-xs text-slate-600">Kelompokkan menjadi satu struk gabungan di kasir</p>
                    </div>
                </div>
                <button type="button"
                        @click="modalGabungkan = false"
                        aria-label="Tutup modal gabungkan tagihan"
                        class="text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-xl p-2 min-h-[44px] min-w-[44px] flex items-center justify-center transition">
                    <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.pembayaran-listrik.gabungkan') }}" class="space-y-4 text-xs">
                @csrf
                <template x-for="id in selectedIds" :key="id">
                    <input type="hidden" name="tagihan_ids[]" :value="id">
                </template>

                <!-- Daftar Tagihan Terpilih -->
                <div class="bg-slate-50 rounded-2xl p-3.5 border border-slate-200 space-y-2">
                    <div class="text-[11px] font-bold text-slate-600 uppercase tracking-wider tabular-money">
                        Tagihan Yang Akan Digabung (<span x-text="selectedItems.length"></span> Struk)
                    </div>
                    <div class="space-y-1.5 max-h-48 overflow-y-auto pr-1">
                        <template x-for="item in selectedItems" :key="item.id">
                            <div class="flex items-center justify-between py-1.5 px-2.5 rounded-xl bg-white border border-slate-200/80 shadow-sm">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="px-1.5 py-0.5 rounded bg-emas-100 text-emas-900 font-mono font-bold text-[11px]" x-text="'[' + item.nomor_struk + ']'"></span>
                                    <span class="font-bold text-slate-900 truncate" x-text="item.nama_warga"></span>
                                </div>
                                <span class="font-mono font-bold text-slate-800 shrink-0 tabular-money" x-text="'Rp ' + formatRupiah(item.total_tagihan)"></span>
                            </div>
                        </template>
                    </div>
                    <div class="pt-2 border-t border-slate-200 flex items-center justify-between text-xs font-bold text-slate-900">
                        <span>Total Gabungan:</span>
                        <span class="text-base font-extrabold text-emas-800 tabular-money" x-text="'Rp ' + formatRupiah(totalTerpilih)"></span>
                    </div>
                </div>

                <!-- Input Atas Nama Siapa -->
                <div class="space-y-2">
                    <label for="kasir-nama-pembayar-gabungan" class="block font-bold text-slate-700 uppercase">
                        Atas Nama Siapa? *
                    </label>
                    <p class="text-[11px] text-slate-600">Pilih salah satu nama warga di atas atau ketik nama penanggung jawab / keluarga:</p>
                    
                    <!-- Quick chips nama warga -->
                    <div class="flex flex-wrap gap-1.5">
                        <template x-for="item in selectedItems" :key="item.id">
                            <button type="button" @click="namaPembayarGabungan = item.nama_warga"
                                    class="px-2.5 py-1.5 rounded-lg text-[11px] font-semibold transition border min-h-[36px]"
                                    :class="namaPembayarGabungan === item.nama_warga ? 'bg-emas-500 text-[#1F1406] border-emas-500 font-bold' : 'bg-slate-100 hover:bg-slate-200 text-slate-700 border-slate-200'">
                                <span x-text="item.nama_warga"></span>
                            </button>
                        </template>
                    </div>

                    <input id="kasir-nama-pembayar-gabungan"
                           type="text"
                           name="nama_pembayar"
                           x-model="namaPembayarGabungan"
                           placeholder="Contoh: Keluarga Pak Budi / Budi"
                           required
                           class="input-field min-h-[46px]">
                </div>

                <!-- Kode Gabungan (Opsional) -->
                <div>
                    <label for="kasir-kode-gabungan" class="block font-bold text-slate-700 uppercase mb-1">
                        Kode Gabungan <span class="text-slate-500 font-normal lowercase">(opsional)</span>
                    </label>
                    <input id="kasir-kode-gabungan"
                           type="text"
                           name="kode_gabungan"
                           x-model="kodeGabunganInput"
                           placeholder="Otomatis digenerate sistem jika kosong"
                           class="input-field font-mono bg-slate-50 min-h-[46px]">
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" @click="modalGabungkan = false" class="btn-subtle min-h-[46px]">
                        Batal
                    </button>
                    <button type="submit"
                            :disabled="!namaPembayarGabungan || selectedIds.length < 2"
                            class="btn-primary flex-1 min-h-[48px] disabled:opacity-50">
                        <svg class="w-4 h-4" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        Simpan Penggabungan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL KASIR CEPAT / FAST POS (Bottom Sheet di HP) -->
    <div x-show="modalBayar" x-cloak
         class="modal-backdrop"
         role="dialog"
         aria-modal="true"
         aria-labelledby="modal-bayar-title">
        <div @click.away="modalBayar = false"
             class="modal-panel max-w-md p-5 sm:p-6 space-y-4">
            <div class="w-10 h-1 rounded-full bg-slate-200 mx-auto sm:hidden"></div>
            
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                    <h3 id="modal-bayar-title" class="text-base font-bold text-slate-900">Kasir Pembayaran</h3>
                    <p class="text-xs text-slate-600 font-mono" x-text="'Struk: [' + bayarItem.struk + ']'"></p>
                </div>
                <button type="button"
                        @click="modalBayar = false"
                        aria-label="Tutup modal kasir pembayaran"
                        class="text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-xl p-2 min-h-[44px] min-w-[44px] flex items-center justify-center transition">
                    <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.pembayaran-listrik.bayar') }}" class="space-y-4 text-xs">
                @csrf
                <template x-for="id in selectedIds" :key="id">
                    <input type="hidden" name="tagihan_ids[]" :value="id">
                </template>
                <input type="hidden" name="kode_gabungan" x-model="kodeGabungan">
                <!-- Hidden input berisi nilai murni tanpa titik untuk backend -->
                <input type="hidden" name="uang_dibayar" :value="uangDibayar">
                <input type="hidden" name="uang_kembalian" :value="uangKembalian">
                <input type="hidden" name="jadikan_titipan" :value="sisaKembalian > 0 ? 1 : 0">

                <!-- Informasi Pembayaran & Potongan Saldo -->
                <div class="bg-slate-50 rounded-xl p-3.5 border border-slate-200 space-y-2">
                    <div>
                        <div class="text-[11px] text-slate-600 font-semibold">Nama Warga:</div>
                        <div class="text-sm font-bold text-slate-900 leading-snug" x-text="bayarItem.nama"></div>
                    </div>

                    <div x-show="bayarItem.saldoLalu > 0" class="pt-2 border-t border-slate-200 flex items-center justify-between text-emerald-800 text-xs font-semibold tabular-money">
                        <span>Potongan Titipan Bulan Lalu:</span>
                        <span x-text="'-Rp ' + formatRupiah(bayarItem.saldoLalu)"></span>
                    </div>

                    <div class="pt-2 border-t border-slate-200 flex items-center justify-between">
                        <span class="text-xs text-slate-700 font-bold">Total Wajib Bayar:</span>
                        <span class="text-base sm:text-lg font-extrabold text-slate-900 tabular-money" x-text="'Rp ' + formatRupiah(bayarItem.total)">Rp 0</span>
                    </div>
                </div>

                <!-- Input Nama Pembayar (Opsional) -->
                <div>
                    <label for="kasir-nama-pembayar" class="block font-bold text-slate-700 uppercase mb-1">
                        Nama Pembayar <span class="text-slate-500 font-normal lowercase">(opsional)</span>
                    </label>
                    <input id="kasir-nama-pembayar"
                           type="text"
                           name="nama_pembayar"
                           x-model="namaPembayar"
                           placeholder="Kosongkan jika yang membayar warga bersangkutan"
                           class="input-field min-h-[46px]">
                </div>

                <!-- Input Nominal Uang Diterima Real-Time Format Rupiah -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label for="kasir-uang-diterima" class="block font-bold text-slate-700 uppercase">Uang Diterima (Rp) *</label>
                        <span class="text-[11px] text-slate-600 font-semibold tabular-money" x-show="uangDibayar > 0" x-text="'Terbaca: Rp ' + formatRupiah(uangDibayar)"></span>
                    </div>
                    <div class="input-addon-group min-h-[48px]">
                        <span class="flex items-center justify-center bg-slate-100 px-3.5 text-slate-600 font-extrabold text-sm select-none border-r border-slate-200 shrink-0">
                            Rp
                        </span>
                        <input id="kasir-uang-diterima"
                               type="text"
                               inputmode="numeric"
                               x-model="uangDibayarInput"
                               @input="onInputUang($event)"
                               placeholder="Contoh: 50.000"
                               required
                               class="w-full text-lg font-extrabold text-slate-900 border-0 focus:ring-0 focus:outline-none py-2.5 px-3 min-h-[48px] bg-transparent tabular-money">
                    </div>
                    
                    <!-- Shortcut Cepat Nominal Uang Pas & Uang Lembaran -->
                    <div class="grid grid-cols-4 gap-1.5 mt-2">
                        <button type="button" @click="setNominal(bayarItem.total)"
                                class="py-1.5 px-2 bg-emas-50 hover:bg-emas-100 text-emas-800 rounded-lg font-bold text-[11px] min-h-[38px] border border-emas-300 transition">
                            Uang Pas
                        </button>
                        <button type="button" @click="tambahNominal(2000)"
                                class="py-1.5 px-2 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-lg font-semibold text-[11px] min-h-[38px] tabular-money transition">
                            +2.000
                        </button>
                        <button type="button" @click="tambahNominal(5000)"
                                class="py-1.5 px-2 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-lg font-semibold text-[11px] min-h-[38px] tabular-money transition">
                            +5.000
                        </button>
                        <button type="button" @click="tambahNominal(10000)"
                                class="py-1.5 px-2 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-lg font-semibold text-[11px] min-h-[38px] tabular-money transition">
                            +10.000
                        </button>
                    </div>
                    <div class="grid grid-cols-3 gap-1.5 mt-1.5">
                        <button type="button" @click="tambahNominal(20000)"
                                class="py-1.5 px-2 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-lg font-semibold text-[11px] min-h-[38px] tabular-money transition">
                            +20.000
                        </button>
                        <button type="button" @click="tambahNominal(50000)"
                                class="py-1.5 px-2 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-lg font-semibold text-[11px] min-h-[38px] tabular-money transition">
                            +50.000
                        </button>
                        <button type="button" @click="resetNominal()"
                                class="py-1.5 px-2 bg-rose-50 hover:bg-rose-100 text-rose-700 rounded-lg font-bold text-[11px] min-h-[38px] border border-rose-200 transition">
                            Reset (C)
                        </button>
                    </div>
                </div>

                <!-- Live Status Kembalian vs Kurang vs Uang Pas -->
                <!-- KONDISI 1: ADA KEMBALIAN -->
                <div x-show="selisihUang > 0" class="space-y-3 pt-1">
                    <!-- Rangkuman Kembalian Semestinya -->
                    <div class="rounded-xl p-3 bg-emerald-50 border border-emerald-200">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold text-emerald-900 uppercase tracking-wider">Kembalian Semestinya:</span>
                            <span class="text-base font-extrabold text-emerald-800 tabular-money" x-text="'Rp ' + formatRupiah(kembalianSemestinya)"></span>
                        </div>
                    </div>

                    <!-- Input Kembalian yang Diserahkan Tunai oleh Kasir -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label for="kasir-uang-kembalian" class="block font-bold text-slate-700 uppercase">Uang Kembalian Diserahkan (Rp) *</label>
                            <span class="text-[11px] text-slate-600 font-semibold tabular-money" x-text="'Diserahkan: Rp ' + formatRupiah(uangKembalian)"></span>
                        </div>
                        <div class="input-addon-group min-h-[48px]">
                            <span class="flex items-center justify-center bg-slate-100 px-3.5 text-slate-600 font-extrabold text-sm select-none border-r border-slate-200 shrink-0">
                                Rp
                            </span>
                            <input id="kasir-uang-kembalian"
                                   type="text"
                                   inputmode="numeric"
                                   x-model="uangKembalianInput"
                                   @input="onInputKembalian($event)"
                                   placeholder="0"
                                   class="w-full text-lg font-extrabold text-slate-900 border-0 focus:ring-0 focus:outline-none py-2.5 px-3 min-h-[48px] bg-transparent tabular-money">
                        </div>

                        <!-- Shortcut Cepat Uang Kembalian -->
                        <div class="flex flex-wrap gap-1.5 mt-2">
                            <button type="button" @click="setKembalianPas()"
                                    class="flex-1 py-1.5 px-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 rounded-lg font-bold text-[11px] min-h-[38px] border border-emerald-200 transition text-center whitespace-nowrap tabular-money">
                                Pas (<span x-text="'Rp ' + formatRupiah(kembalianSemestinya)"></span>)
                            </button>
                            <button type="button" @click="setKembalianNol()"
                                    class="py-1.5 px-2 bg-emas-50 hover:bg-emas-100 text-emas-800 rounded-lg font-bold text-[11px] min-h-[38px] border border-emas-300 transition whitespace-nowrap">
                                Gada Kembalian (Rp 0)
                            </button>
                            <template x-if="kembalianSemestinya >= 1000 && kembalianSemestinya < 10000">
                                <button type="button" @click="setKembalianNominal(10000)"
                                        class="py-1.5 px-2.5 bg-rose-50 hover:bg-rose-100 text-rose-800 rounded-lg font-bold text-[11px] min-h-[38px] border border-rose-200 transition tabular-money"
                                        title="Kembalikan selembar 10.000 (warga jadi kurang bayar)">
                                    10.000
                                </button>
                            </template>
                            <template x-if="kembalianSemestinya >= 5000">
                                <button type="button" @click="setKembalianNominal(5000)"
                                        class="py-1.5 px-2.5 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-lg font-semibold text-[11px] min-h-[38px] transition tabular-money">
                                    5.000
                                </button>
                            </template>
                            <template x-if="kembalianSemestinya >= 2000 && kembalianSemestinya !== 5000">
                                <button type="button" @click="setKembalianNominal(2000)"
                                        class="py-1.5 px-2.5 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-lg font-semibold text-[11px] min-h-[38px] transition tabular-money">
                                    2.000
                                </button>
                            </template>
                            <template x-if="kembalianSemestinya >= 10000">
                                <button type="button" @click="setKembalianNominal(10000)"
                                        class="py-1.5 px-2.5 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-lg font-semibold text-[11px] min-h-[38px] transition tabular-money">
                                    10.000
                                </button>
                            </template>
                            <template x-if="kembalianSemestinya >= 20000">
                                <button type="button" @click="setKembalianNominal(20000)"
                                        class="py-1.5 px-2.5 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-lg font-semibold text-[11px] min-h-[38px] transition tabular-money">
                                    20.000
                                </button>
                            </template>
                        </div>
                    </div>

                    <!-- Sub-kondisi 1: Kembalian Pas Diserahkan Penuh -->
                    <div x-show="selisihKembalian === 0" class="rounded-xl p-3 bg-emerald-50 border border-emerald-200 text-emerald-950">
                        <div class="flex items-center justify-between font-bold">
                            <span class="text-xs uppercase tracking-wider text-emerald-800">Status Kembalian:</span>
                            <span class="text-sm font-extrabold text-emerald-800">Pas Diserahkan Semua</span>
                        </div>
                        <p class="text-[11px] mt-1 text-emerald-800 leading-relaxed tabular-money">
                            Uang kembalian <strong x-text="'Rp ' + formatRupiah(uangKembalian)"></strong> diserahkan tunai ke warga. Tidak ada sisa saldo titipan (Rp 0).
                        </p>
                    </div>

                    <!-- Sub-kondisi 2: Kembalian Sebagian -->
                    <div x-show="uangKembalian > 0 && selisihKembalian > 0" class="rounded-xl p-3 bg-emas-50 border border-emas-300 text-emas-950 space-y-1.5">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold uppercase tracking-wider text-emas-900">Kembalian Diserahkan:</span>
                            <span class="text-sm font-bold text-slate-800 tabular-money" x-text="'Rp ' + formatRupiah(uangKembalian)"></span>
                        </div>
                        <div class="flex items-center justify-between pt-1 border-t border-emas-200">
                            <span class="text-xs font-bold uppercase tracking-wider text-emas-900">Sisa Kembalian (Kurang Pecahan):</span>
                            <span class="text-base font-extrabold text-emas-800 tabular-money" x-text="'Rp ' + formatRupiah(sisaKembalian)"></span>
                        </div>
                        <p class="text-[11px] text-emas-900 leading-relaxed pt-1 tabular-money">
                            Karena hanya ada pecahan <strong x-text="'Rp ' + formatRupiah(uangKembalian)"></strong>, maka sisa kembalian <strong class="text-emas-950" x-text="'Rp ' + formatRupiah(sisaKembalian)"></strong> otomatis disimpan sebagai <strong>saldo titipan warga</strong> bulan depan.
                        </p>
                    </div>

                    <!-- Sub-kondisi 3: Gada Kembalian Sama Sekali -->
                    <div x-show="uangKembalian === 0" class="rounded-xl p-3 bg-emas-50 border border-emas-300 text-emas-950 space-y-1.5">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold uppercase tracking-wider text-emas-900">Kembalian Diserahkan:</span>
                            <span class="text-sm font-bold text-slate-700 tabular-money">Rp 0 (Tidak Ada)</span>
                        </div>
                        <div class="flex items-center justify-between pt-1 border-t border-emas-200">
                            <span class="text-xs font-bold uppercase tracking-wider text-emas-900">Disimpan Sebagai Saldo Titipan:</span>
                            <span class="text-base font-extrabold text-emas-800 tabular-money" x-text="'Rp ' + formatRupiah(sisaKembalian)"></span>
                        </div>
                        <p class="text-[11px] text-emas-900 leading-relaxed pt-1 tabular-money">
                            Tidak ada uang kembalian diserahkan. Seluruh <strong class="text-emas-950" x-text="'Rp ' + formatRupiah(sisaKembalian)"></strong> otomatis disimpan sebagai <strong>saldo titipan warga</strong> bulan depan.
                        </p>
                    </div>

                    <!-- Sub-kondisi 4: Petugas Kembalikan Lebih Banyak -->
                    <div x-show="selisihKembalian < 0" class="rounded-xl p-3 bg-rose-50 border border-rose-300 text-rose-950 space-y-1.5">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold uppercase tracking-wider text-rose-900">Kembalian Diserahkan:</span>
                            <span class="text-sm font-extrabold text-rose-800 tabular-money" x-text="'Rp ' + formatRupiah(uangKembalian)"></span>
                        </div>
                        <div class="flex items-center justify-between pt-1 border-t border-rose-200">
                            <span class="text-xs font-bold uppercase tracking-wider text-rose-900">Kelebihan Kembalian (Warga Kurang Bayar):</span>
                            <span class="text-base font-extrabold text-rose-700 tabular-money" x-text="'-Rp ' + formatRupiah(kelebihanKembalian)"></span>
                        </div>
                        <p class="text-[11px] text-rose-800 leading-relaxed pt-1 tabular-money">
                            Petugas menyerahkan kembalian <strong x-text="'Rp ' + formatRupiah(uangKembalian)"></strong> (lebih <strong x-text="'Rp ' + formatRupiah(kelebihanKembalian)"></strong> dari semestinya <span x-text="'Rp ' + formatRupiah(kembalianSemestinya)"></span>). Maka kekurangan <strong x-text="'Rp ' + formatRupiah(kelebihanKembalian)"></strong> otomatis dicatat sebagai <strong>tunggakan warga</strong> di tagihan bulan depan.
                        </p>
                    </div>
                </div>

                <!-- KONDISI 2: KURANG / NINGGAL -->
                <div x-show="selisihUang < 0 && uangDibayar > 0" class="rounded-xl p-3 bg-rose-50 border border-rose-200 text-rose-950">
                    <div class="flex items-center justify-between font-bold">
                        <span class="text-xs uppercase tracking-wider text-rose-800">Kurang / Ninggal:</span>
                        <span class="text-base font-extrabold text-rose-700 tabular-money" x-text="'-Rp ' + formatRupiah(kurang)"></span>
                    </div>
                    <p class="text-[11px] mt-1 text-rose-700 leading-relaxed tabular-money">
                        Warga kurang bayar <strong x-text="'Rp ' + formatRupiah(kurang)"></strong>. Kekurangan ini otomatis dicatat sebagai tunggakan bulan depan.
                    </p>
                </div>

                <!-- KONDISI 3: UANG PAS -->
                <div x-show="selisihUang === 0 && uangDibayar > 0" class="rounded-xl p-3 bg-sky-50 border border-sky-200 text-sky-950">
                    <div class="flex items-center justify-between font-bold">
                        <span class="text-xs uppercase tracking-wider text-sky-800">Status Pembayaran:</span>
                        <span class="text-sm font-extrabold text-sky-800">Uang Pas (Lunas)</span>
                    </div>
                    <p class="text-[11px] mt-1 text-sky-800">
                        Kembalian Rp 0. Tidak ada sisa atau kekurangan.
                    </p>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" @click="modalBayar = false" class="btn-subtle min-h-[46px]">
                        Batal
                    </button>
                    <button type="submit" class="btn-primary flex-1 min-h-[48px]">
                        <svg class="w-4 h-4" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
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
        modalGabungkan: false,
        selectedIds: [],
        namaPembayarGabungan: '',
        kodeGabunganInput: '',
        bayarItem: {
            id: null,
            nama: '',
            struk: '',
            total: 0,
            saldoLalu: 0
        },
        uangDibayarInput: '',
        uangKembalianInput: '',
        namaPembayar: '',
        jadikanTitipan: false,
        kodeGabungan: '',

        cocokSearch(...args) {
            if (!this.cari || this.cari.trim() === '') return true;
            const q = this.cari.toLowerCase().trim();
            const combined = args.map(a => (a || '').toString().toLowerCase()).join(' ');
            return combined.includes(q);
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
            this.namaPembayar = '';
            this.jadikanTitipan = false;
            this.kodeGabungan = '';
            this.uangDibayar = total;
            this.modalBayar = true;
        },

        bukaBayarTerpilihTunggal() {
            if (this.selectedIds.length !== 1) return;
            const item = allData.find(t => t.id === this.selectedIds[0]);
            if (item) {
                this.bukaBayarTunggal(item.id, item.nama_warga, item.nomor_struk, item.total_tagihan, item.saldo_periode_lalu);
            }
        },

        bukaBayarGrupItem(ids, nama, struk, total, saldoLalu, kodeGabungan) {
            this.selectedIds = ids;
            this.bayarItem = {
                id: null,
                nama: 'An. ' + nama + ' (' + ids.length + ' Tagihan)',
                struk: struk,
                total: total,
                saldoLalu: saldoLalu || 0
            };
            this.namaPembayar = nama;
            this.jadikanTitipan = false;
            this.kodeGabungan = kodeGabungan;
            this.uangDibayar = total;
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
            this.namaPembayar = filtered.length > 0 ? filtered[0].nama_warga : '';
            this.jadikanTitipan = false;
            this.kodeGabungan = 'GAB-' + Math.floor(100000 + Math.random() * 900000);
            this.uangDibayar = total;
            this.modalBayar = true;
        },

        bukaModalGabungkanDulu() {
            if (this.selectedIds.length < 2) return;
            const filtered = this.selectedItems;
            this.kodeGabunganInput = 'GAB-' + Math.floor(100000 + Math.random() * 900000);
            this.namaPembayarGabungan = filtered.length > 0 ? filtered[0].nama_warga : '';
            this.modalGabungkan = true;
        },

        toggleSelect(id) {
            const index = this.selectedIds.indexOf(id);
            if (index > -1) {
                this.selectedIds.splice(index, 1);
            } else {
                this.selectedIds.push(id);
            }
        },

        get selectedItems() {
            return allData.filter(t => this.selectedIds.includes(t.id));
        },

        get totalTerpilih() {
            return this.selectedItems.reduce((acc, curr) => acc + (curr.total_tagihan || 0), 0);
        },

        get uangDibayar() {
            return parseInt(this.uangDibayarInput.toString().replace(/[^0-9]/g, '')) || 0;
        },

        set uangDibayar(val) {
            let num = parseInt(val) || 0;
            this.uangDibayarInput = num > 0 ? this.formatRupiah(num) : '';
            this.sinkronKembalianDefault();
        },

        get uangKembalian() {
            let clean = this.uangKembalianInput.toString().replace(/[^0-9]/g, '');
            if (clean === '') return 0;
            return parseInt(clean) || 0;
        },

        set uangKembalian(val) {
            let num = parseInt(val) || 0;
            this.uangKembalianInput = num > 0 ? this.formatRupiah(num) : '0';
        },

        get selisihUang() {
            return this.uangDibayar - (this.bayarItem.total || 0);
        },

        get kembalianSemestinya() {
            return Math.max(0, this.selisihUang);
        },

        get selisihKembalian() {
            return this.kembalianSemestinya - this.uangKembalian;
        },

        get sisaKembalian() {
            return Math.max(0, this.selisihKembalian);
        },

        get kelebihanKembalian() {
            return Math.max(0, -this.selisihKembalian);
        },

        get kurang() {
            return Math.max(0, -this.selisihUang);
        },

        sinkronKembalianDefault() {
            let s = this.kembalianSemestinya;
            if (s > 0) {
                this.uangKembalianInput = this.formatRupiah(s);
            } else {
                this.uangKembalianInput = '0';
            }
        },

        onInputUang(e) {
            let clean = e.target.value.replace(/[^0-9]/g, '');
            if (!clean) {
                this.uangDibayarInput = '';
                this.uangKembalianInput = '0';
                return;
            }
            this.uangDibayarInput = this.formatRupiah(parseInt(clean));
            this.sinkronKembalianDefault();
        },

        onInputKembalian(e) {
            let clean = e.target.value.replace(/[^0-9]/g, '');
            if (!clean || clean === '0') {
                this.uangKembalianInput = '0';
                return;
            }
            let val = parseInt(clean);
            this.uangKembalianInput = this.formatRupiah(val);
        },

        setKembalianPas() {
            this.uangKembalian = this.kembalianSemestinya;
        },

        setKembalianNol() {
            this.uangKembalianInput = '0';
        },

        setKembalianNominal(val) {
            this.uangKembalian = val;
        },

        setNominal(val) {
            this.uangDibayar = val;
        },

        tambahNominal(val) {
            this.uangDibayar = this.uangDibayar + val;
        },

        resetNominal() {
            this.uangDibayar = 0;
        },

        formatRupiah(num) {
            return new Intl.NumberFormat('id-ID').format(num || 0);
        }
    };
}
</script>
@endpush
@endsection
