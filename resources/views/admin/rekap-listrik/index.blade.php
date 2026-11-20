@extends('layouts.admin')

@section('title', 'Rekap Listrik - ' . $periodeAktif . ' RT ' . $rtAktif)

@section('content')
<!-- Data JSON Aman Disuntikkan Sebelum Komponen Alpine Dimuat -->
<script id="warga-rt-json" type="application/json">
    {!! json_encode($wargaRtAktif) !!}
</script>

<script id="daftar-rekap-json" type="application/json">
    {!! json_encode($daftarRekap) !!}
</script>

<script id="daftar-periode-json" type="application/json">
    {!! json_encode($daftarPeriodeWithStats) !!}
</script>

<div class="space-y-4 sm:space-y-5"
     x-data="rekapListrik()"
     @keydown.escape.window="if (modalTambah) tutupModalTambah(); modalDuplikasi = false; if (stepScan !== 'processing') modalScan = false; modalGabungkan = false; modalKelolaSiklus = false; modalRenameSiklus = false; modalHapusSiklus = false;">

    <!-- Top Sticky Bar / Header Mobile Friendly -->
    <div class="card-surface p-4 sm:p-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <div class="flex items-center gap-2.5">
                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-[#CEA042] text-[#FAF4E4CC] font-bold shrink-0 shadow-sm">
                        <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                    </span>
                    <div>
                        <h1 class="text-lg sm:text-xl font-bold text-gray-900 leading-tight">
                            Rekap Listrik Warga
                        </h1>
                        <p class="text-xs text-gray-600">Pencatatan struk, kas karang taruna &amp; saldo titipan antar bulan</p>
                    </div>
                </div>
            </div>

            <!-- Tombol CTA Utama -->
            <div class="flex items-center gap-2 flex-wrap sm:flex-nowrap">
                <a href="{{ route('admin.rekap-listrik.cetak', ['periode' => $periodeAktif, 'rt' => $rtAktif]) }}"
                   class="btn-secondary w-full sm:w-auto px-3.5 text-xs">
                    <svg class="w-4 h-4 text-gray-600" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak &amp; Ekspor A4</span>
                </a>

                <button type="button" @click="bukaModalTambah()"
                        class="btn-primary w-full sm:w-auto px-4 text-xs">
                    <svg class="w-4 h-4" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Catat Tagihan Struk</span>
                </button>
            </div>
        </div>

        <!-- Pemilih Siklus Periode (Bulan) & Tombol Buka Siklus Baru -->
        <div class="mt-4 pt-3.5 border-t border-gray-100 flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-2.5 flex-1 min-w-[260px]" x-data="{ openDropdownPeriode: false }" @keydown.escape.window="openDropdownPeriode = false">
                <span class="text-xs font-bold text-gray-600 uppercase tracking-wider shrink-0">Siklus Periode:</span>
                
                <div class="relative flex-1 max-w-xs">
                    <!-- Dropdown Trigger Button -->
                    <button type="button"
                            @click="openDropdownPeriode = !openDropdownPeriode"
                            aria-haspopup="true"
                            :aria-expanded="openDropdownPeriode.toString()"
                            class="w-full flex items-center justify-between gap-2 text-xs font-bold text-gray-900 bg-emas-50 hover:bg-emas-100/80 border border-emas-300 hover:border-[#CEA042] rounded-xl py-2.5 px-3.5 min-h-[44px] shadow-sm transition">
                        <div class="flex items-center gap-2 truncate">
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-lg bg-emas-100 text-emas-800 border border-emas-200 shrink-0">
                                <svg class="w-3.5 h-3.5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </span>
                            <span class="truncate">{{ $periodeAktif }}</span>
                        </div>
                        <svg class="w-4 h-4 text-gray-600 transition-transform duration-200 shrink-0"
                             aria-hidden="true"
                             :class="openDropdownPeriode ? 'rotate-180 text-emas-800' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <!-- Floating Dropdown Menu Panel -->
                    <div x-show="openDropdownPeriode"
                         x-cloak
                         @click.away="openDropdownPeriode = false"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                         x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                         class="absolute left-0 mt-2 z-50 w-72 bg-white rounded-2xl border border-emas-200 shadow-2xl p-2 space-y-1">
                        
                        <div class="px-3 py-1.5 text-[11px] font-bold uppercase tracking-wider text-gray-500 border-b border-gray-100 flex items-center justify-between">
                            <span>Daftar Bulan Siklus</span>
                            <span class="text-emas-800 font-bold tabular-money">{{ count($daftarPeriode) }} Periode</span>
                        </div>

                        <div class="max-h-60 overflow-y-auto space-y-1 py-1">
                            @foreach($daftarPeriode as $idx => $p)
                                @php
                                    $isAktif = ($periodeAktif === $p);
                                @endphp
                                <a href="{{ route('admin.rekap-listrik.index', ['periode' => $p, 'rt' => $rtAktif]) }}"
                                   class="flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-bold transition group {{ $isAktif ? 'bg-[#CEA042] text-[#FAF4E4CC] shadow-sm' : 'text-gray-700 hover:bg-emas-50 hover:text-emas-900' }}">
                                    <div class="flex items-center gap-2.5">
                                        <svg class="w-4 h-4 {{ $isAktif ? 'text-[#FAF4E4CC]' : 'text-gray-400 group-hover:text-emas-700' }}" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <span>{{ $p }}</span>
                                    </div>

                                    @if($isAktif)
                                        <span class="inline-flex items-center gap-1 text-[10px] px-2 py-0.5 rounded-full bg-[#FAF4E4]/20 text-[#FAF4E4CC] font-extrabold">
                                            <svg class="w-3 h-3" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                            Aktif
                                        </span>
                                    @elseif($idx === 0)
                                        <span class="text-[10px] px-2 py-0.5 rounded-full bg-emas-100 text-emas-900 font-bold group-hover:bg-[#CEA042] group-hover:text-[#FAF4E4CC] transition">
                                            Terbaru
                                        </span>
                                    @endif
                                </a>
                            @endforeach
                        </div>

                        <div class="pt-1.5 border-t border-gray-100 space-y-1">
                            <button type="button" @click="openDropdownPeriode = false; bukaModalKelolaSiklus()"
                                    class="w-full flex items-center justify-center gap-1.5 py-2.5 px-3 text-xs font-bold text-gray-800 hover:text-emas-900 hover:bg-emas-50 rounded-xl transition min-h-[40px]">
                                <svg class="w-4 h-4 text-emas-700" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span>Kelola Siklus (Ubah / Hapus)</span>
                            </button>
                            <button type="button" @click="openDropdownPeriode = false; bukaModalDuplikasi()"
                                    class="w-full flex items-center justify-center gap-1.5 py-2.5 px-3 text-xs font-bold text-emas-900 hover:bg-emas-50 rounded-xl transition min-h-[40px]">
                                <svg class="w-4 h-4 text-emas-700" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                <span>Buka Siklus Bulan Baru</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tombol Aksi Siklus -->
            <div class="flex flex-wrap items-center gap-2">
                <button type="button" @click.stop="bukaModalKelolaSiklus()" class="btn-secondary px-3.5 text-xs shrink-0">
                    <svg class="w-4 h-4 text-emas-700" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span>Kelola Siklus</span>
                </button>

                <button type="button" @click.stop="bukaModalScan()" class="btn-gold-soft px-3.5 text-xs shrink-0">
                    <svg class="w-4 h-4 text-emas-800" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span>Scan / Impor</span>
                </button>

                <button type="button" @click.stop="bukaModalDuplikasi()" class="btn-primary px-3.5 text-xs shrink-0">
                    <svg class="w-4 h-4" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Buka Siklus Baru</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Tab Pilihan RT 1, 2, 3 -->
    <div class="grid grid-cols-3 gap-2 bg-gray-200/80 p-1.5 rounded-2xl">
        <a href="{{ route('admin.rekap-listrik.index', ['periode' => $periodeAktif, 'rt' => 1]) }}"
           class="flex flex-col sm:flex-row items-center justify-center gap-1 sm:gap-2 py-2.5 px-3 rounded-xl text-xs font-bold transition min-h-[46px] {{ $rtAktif === 1 ? 'bg-[#CEA042] text-[#FAF4E4CC] shadow-sm border border-[#BA8832]/40' : 'bg-white text-gray-700 hover:bg-emas-50' }}">
            <span>RT 01</span>
            <span class="text-[11px] px-2 py-0.5 rounded-full tabular-money {{ $rtAktif === 1 ? 'bg-[#FAF4E4]/20 text-[#FAF4E4CC]' : 'bg-gray-100 text-gray-700' }}">
                {{ $countRt1 }}
            </span>
        </a>

        <a href="{{ route('admin.rekap-listrik.index', ['periode' => $periodeAktif, 'rt' => 2]) }}"
           class="flex flex-col sm:flex-row items-center justify-center gap-1 sm:gap-2 py-2.5 px-3 rounded-xl text-xs font-bold transition min-h-[46px] {{ $rtAktif === 2 ? 'bg-[#CEA042] text-[#FAF4E4CC] shadow-sm border border-[#BA8832]/40' : 'bg-white text-gray-700 hover:bg-emas-50' }}">
            <span>RT 02</span>
            <span class="text-[11px] px-2 py-0.5 rounded-full tabular-money {{ $rtAktif === 2 ? 'bg-[#FAF4E4]/20 text-[#FAF4E4CC]' : 'bg-gray-100 text-gray-700' }}">
                {{ $countRt2 }}
            </span>
        </a>

        <a href="{{ route('admin.rekap-listrik.index', ['periode' => $periodeAktif, 'rt' => 3]) }}"
           class="flex flex-col sm:flex-row items-center justify-center gap-1 sm:gap-2 py-2.5 px-3 rounded-xl text-xs font-bold transition min-h-[46px] {{ $rtAktif === 3 ? 'bg-[#CEA042] text-[#FAF4E4CC] shadow-sm border border-[#BA8832]/40' : 'bg-white text-gray-700 hover:bg-emas-50' }}">
            <span>RT 03</span>
            <span class="text-[11px] px-2 py-0.5 rounded-full tabular-money {{ $rtAktif === 3 ? 'bg-[#FAF4E4]/20 text-[#FAF4E4CC]' : 'bg-gray-100 text-gray-700' }}">
                {{ $countRt3 }}
            </span>
        </a>
    </div>

    <!-- Ringkasan Metrik Kompak -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 sm:gap-3">
        <div class="card-surface p-3.5">
            <div class="text-[11px] font-bold text-gray-500 uppercase tracking-wide">Terdata RT {{ $rtAktif }}</div>
            <div class="text-base sm:text-lg font-extrabold text-gray-900 mt-0.5 tabular-money">{{ $totalWargaTerdata }} Warga</div>
        </div>

        <div class="card-surface p-3.5">
            <div class="text-[11px] font-bold text-gray-500 uppercase tracking-wide">Total PLN Awal</div>
            <div class="text-base sm:text-lg font-extrabold text-gray-900 mt-0.5 tabular-money">Rp {{ number_format($totalTagihanAwal, 0, ',', '.') }}</div>
        </div>

        <div class="card-surface p-3.5">
            <div class="text-[11px] font-bold text-emas-800 uppercase tracking-wide">Kas Karang Taruna</div>
            <div class="text-base sm:text-lg font-extrabold text-emas-800 mt-0.5 tabular-money">Rp {{ number_format($totalKasKartar, 0, ',', '.') }}</div>
        </div>

        <div class="card-surface p-3.5">
            <div class="text-[11px] font-bold text-emerald-800 uppercase tracking-wide">Total Wajib Bayar</div>
            <div class="text-base sm:text-lg font-extrabold text-emerald-700 mt-0.5 tabular-money">Rp {{ number_format($grandTotalWajib, 0, ',', '.') }}</div>
        </div>
    </div>

    <!-- PENCARIAN REAL-TIME INSTAN -->
    <div class="card-surface p-3">
        <div class="relative flex items-center">
            <svg class="w-4 h-4 text-emas-700 absolute left-3.5 pointer-events-none" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                    aria-label="Bersihkan pencarian"
                    class="absolute right-2.5 p-1.5 text-gray-400 hover:text-gray-700 rounded-lg">
                <svg class="w-4 h-4" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="flex items-center justify-between text-xs text-gray-500 mt-1.5 px-1" x-show="cari.length > 0">
            <span>Menyaring otomatis: "<strong class="text-gray-800" x-text="cari"></strong>"</span>
            <button type="button" @click="cari = ''" class="text-rose-700 font-semibold hover:underline">Hapus filter</button>
        </div>
    </div>

    <!-- DAFTAR REKAP: KARTU MOBILE FRIENDLY -->
    @if($daftarRekap->isEmpty())
        <div class="bg-white rounded-2xl border border-dashed border-gray-300 p-8 text-center space-y-3 shadow-sm">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-emas-100 text-emas-800 border border-emas-200">
                <svg class="w-6 h-6" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <p class="text-sm font-semibold text-gray-800">Belum ada data rekap untuk RT {{ $rtAktif }} di periode {{ $periodeAktif }}</p>
            <button type="button" @click="bukaModalTambah()" class="btn-primary px-4 text-xs">
                + Mulai Catat Struk Pertama
            </button>
        </div>
    @else
        <div class="space-y-3 pb-8">
            @foreach($daftarRekap as $item)
                <div class="card-surface p-4 transition hover:border-[#CEA042] relative"
                     x-show="cocokSearch('{{ addslashes($item->nama_warga) }}', '{{ addslashes($item->nama_pelanggan) }}', '{{ addslashes($item->nomor_struk) }}', '{{ addslashes($item->kode_gabungan ?? '') }}', '{{ addslashes($item->nama_pembayar ?? '') }}')"
                     :class="selectedIds.includes({{ $item->id }}) ? 'border-[#CEA042] ring-2 ring-[#CEA042]/25 bg-emas-50/40' : ''">
                    <div class="flex items-start justify-between gap-2.5">
                        <div class="flex items-start gap-2.5 min-w-0 flex-1">
                            @if(! $item->is_lunas)
                                <!-- Checkbox Pilihan Tagihan Gabungan -->
                                <button type="button" @click="toggleSelect({{ $item->id }})"
                                        aria-label="Pilih tagihan {{ $item->nama_warga }}"
                                        class="mt-0.5 w-8 h-8 rounded-lg border-2 flex items-center justify-center transition shrink-0"
                                        :class="selectedIds.includes({{ $item->id }}) ? 'bg-[#CEA042] border-[#CEA042] text-[#FAF4E4CC]' : 'border-gray-300 bg-white text-transparent hover:border-[#CEA042]'">
                                    <svg class="w-4 h-4" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </button>
                            @endif

                            <!-- Nomor Struk & Info Warga -->
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-lg bg-emas-100 text-emas-950 font-bold font-mono text-xs shrink-0 border border-emas-200 tabular-money">
                                        [ {{ $item->nomor_struk }} ]
                                    </span>
                                    @if($item->kode_gabungan)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-slate-100 text-slate-800 text-[11px] font-mono font-bold border border-slate-300">
                                            <svg class="w-3 h-3 text-slate-600" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                            <span>{{ $item->kode_gabungan }}</span>
                                            @if($item->nama_pembayar)
                                                <span class="font-sans font-semibold">An. {{ $item->nama_pembayar }}</span>
                                            @endif
                                        </span>
                                    @endif
                                </div>

                                <h2 class="text-sm font-bold text-gray-900 leading-snug truncate mt-1">
                                    {{ $item->nama_warga }}
                                </h2>
                                <p class="text-xs text-gray-600 font-mono mt-0.5 truncate">
                                    PLN: {{ $item->nama_pelanggan }}
                                </p>
                            </div>
                        </div>

                        <!-- Status Bayar -->
                        <div class="shrink-0">
                            @if($item->is_lunas)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 text-[11px] font-bold rounded-full bg-emerald-100 text-emerald-900">
                                    <svg class="w-3 h-3" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Lunas
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 text-[11px] font-bold rounded-full bg-gray-100 text-gray-700">
                                    Belum Bayar
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Breakdown Biaya & Potongan Saldo Titipan Bulan Lalu -->
                    <div class="mt-3 pt-3 border-t border-gray-100 flex items-end justify-between gap-2 text-xs">
                        <div class="space-y-1">
                            <div class="text-xs text-gray-600 tabular-money">
                                Struk PLN: <span class="font-semibold text-gray-900">Rp {{ number_format($item->tagihan_awal, 0, ',', '.') }}</span> + Kartar: <span class="font-semibold text-emas-800">Rp {{ number_format($item->kas_karang_taruna, 0, ',', '.') }}</span>
                            </div>

                            @if($item->saldo_periode_lalu > 0)
                                <div class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-800 text-[11px] font-bold border border-emerald-200 tabular-money">
                                    Potongan Titipan Bulan Lalu: -Rp {{ number_format($item->saldo_periode_lalu, 0, ',', '.') }}
                                </div>
                            @elseif($item->saldo_periode_lalu < 0)
                                <div class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-rose-50 text-rose-800 text-[11px] font-bold border border-rose-200 tabular-money">
                                    Tunggakan Bulan Lalu: +Rp {{ number_format(abs($item->saldo_periode_lalu), 0, ',', '.') }}
                                </div>
                            @endif
                        </div>

                        <div class="text-right shrink-0">
                            <div class="text-[11px] text-gray-500 uppercase font-semibold">Total Wajib Bayar</div>
                            <div class="text-base font-extrabold text-gray-900 tabular-money">
                                Rp {{ number_format($item->total_tagihan, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Aksi HP -->
                    <div class="mt-3 pt-2.5 border-t border-gray-100 flex items-center justify-end gap-2 flex-wrap">
                        @if($item->kode_gabungan && ! $item->is_lunas)
                            <form method="POST" action="{{ route('admin.rekap-listrik.pisahkan') }}"
                                  onsubmit="return confirm('Pisahkan {{ addslashes($item->nama_warga) }} dari tagihan gabungan {{ $item->kode_gabungan }}?')">
                                @csrf
                                <input type="hidden" name="tagihan_id" value="{{ $item->id }}">
                                <button type="submit"
                                        class="px-3 py-2 text-xs font-bold text-slate-800 bg-slate-100 hover:bg-slate-200 border border-slate-300 rounded-xl transition min-h-[44px] flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                    <span>Pisahkan</span>
                                </button>
                            </form>
                        @endif

                        <a href="{{ route('admin.rekap-listrik.edit', $item) }}" class="btn-subtle px-3.5 text-xs">
                            <svg class="w-3.5 h-3.5 text-gray-600" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            <span>Edit</span>
                        </a>

                        <form method="POST" action="{{ route('admin.rekap-listrik.destroy', $item) }}"
                              onsubmit="return confirm('Hapus baris rekap untuk {{ addslashes($item->nama_warga) }} (Struk [{{ $item->nomor_struk }}])?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-danger-soft px-3 text-xs">
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- FLOATING ACTION BAR: GABUNGKAN TAGIHAN DI REKAP -->
        <div x-show="selectedIds.length >= 2" x-cloak
             class="fixed bottom-16 lg:bottom-4 left-3 right-3 sm:left-auto sm:right-6 sm:w-96 z-40 bg-slate-900 text-white rounded-2xl p-4 shadow-2xl flex items-center justify-between gap-3 border border-slate-700">
            <div>
                <div class="text-xs text-slate-300 font-semibold tabular-money" x-text="selectedIds.length + ' Tagihan Dipilih'">0 Tagihan Dipilih</div>
                <div class="text-sm font-extrabold text-emas-300 tabular-money" x-text="'Total: Rp ' + formatRupiah(totalTerpilih)">Total: Rp 0</div>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" @click="selectedIds = []" class="px-3 py-2 text-xs font-semibold text-slate-300 hover:text-white rounded-xl min-h-[44px] transition">
                    Batal
                </button>
                <button type="button" @click="bukaModalGabungkan()" class="btn-primary px-4 text-xs shrink-0">
                    <svg class="w-4 h-4" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                    <span>Gabungkan</span>
                </button>
            </div>
        </div>

        <!-- MODAL GABUNGKAN TAGIHAN REKAP -->
        <div x-show="modalGabungkan" x-cloak
             @click.self="modalGabungkan = false"
             role="dialog"
             aria-modal="true"
             aria-labelledby="modal-gabung-rekap-title"
             class="modal-backdrop">
            <div class="modal-panel max-w-lg p-5 sm:p-6 space-y-4">
                <div class="w-12 h-1.5 bg-gray-300 rounded-full mx-auto mb-1 sm:hidden"></div>
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <div class="flex items-center gap-2.5">
                        <span class="w-9 h-9 rounded-xl bg-emas-100 text-emas-800 border border-emas-200 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                        </span>
                        <div>
                            <h3 id="modal-gabung-rekap-title" class="text-base font-bold text-gray-900 leading-tight">Gabungkan Tagihan Warga</h3>
                            <p class="text-xs text-gray-600">Satu struk pembayaran untuk beberapa warga / meteran</p>
                        </div>
                    </div>
                    <button type="button" @click="modalGabungkan = false" aria-label="Tutup modal gabungkan tagihan"
                            class="text-gray-500 hover:text-gray-800 hover:bg-gray-100 rounded-xl p-2 min-h-[44px] min-w-[44px] flex items-center justify-center transition">
                        <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('admin.rekap-listrik.gabungkan') }}" class="space-y-4 text-xs">
                    @csrf
                    <template x-for="id in selectedIds" :key="id">
                        <input type="hidden" name="tagihan_ids[]" :value="id">
                    </template>

                    <div class="bg-[#F8F7F4] rounded-2xl p-3.5 border border-gray-200 space-y-2">
                        <div class="text-[11px] font-bold text-gray-600 uppercase tracking-wider">
                            Rincian Tagihan Yang Digabung (<span class="tabular-money" x-text="selectedItems.length"></span> Struk)
                        </div>
                        <div class="space-y-1.5 max-h-48 overflow-y-auto pr-1">
                            <template x-for="item in selectedItems" :key="item.id">
                                <div class="flex items-center justify-between py-2 px-3 rounded-xl bg-white border border-gray-200/80 shadow-sm">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <span class="px-1.5 py-0.5 rounded bg-emas-100 text-emas-950 font-mono font-bold text-[11px] tabular-money" x-text="'[' + item.nomor_struk + ']'"></span>
                                        <span class="font-bold text-gray-900 truncate" x-text="item.nama_warga"></span>
                                    </div>
                                    <span class="font-mono font-bold text-gray-900 shrink-0 tabular-money" x-text="'Rp ' + formatRupiah(item.total_tagihan)"></span>
                                </div>
                            </template>
                        </div>
                        <div class="pt-2 border-t border-gray-200 flex items-center justify-between text-xs font-bold text-gray-900">
                            <span>Total Gabungan:</span>
                            <span class="text-base font-extrabold text-emas-800 tabular-money" x-text="'Rp ' + formatRupiah(totalTerpilih)"></span>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label for="rekap-nama-pembayar-gabungan" class="block text-xs font-bold text-gray-800">
                            Atas Nama Siapa? <span class="text-rose-600">*</span>
                        </label>
                        <p class="text-[11px] text-gray-600">Pilih salah satu nama warga di atas atau ketik nama penanggung jawab / keluarga:</p>
                        
                        <div class="flex flex-wrap gap-1.5">
                            <template x-for="item in selectedItems" :key="item.id">
                                <button type="button" @click="namaPembayarGabungan = item.nama_warga"
                                        class="px-3 py-1.5 rounded-lg text-xs font-semibold transition border min-h-[36px]"
                                        :class="namaPembayarGabungan === item.nama_warga ? 'bg-[#CEA042] text-[#FAF4E4CC] border-[#BA8832]' : 'bg-gray-100 hover:bg-emas-50 text-gray-800 border-gray-200'">
                                    <span x-text="item.nama_warga"></span>
                                </button>
                            </template>
                        </div>

                        <input type="text"
                               id="rekap-nama-pembayar-gabungan"
                               name="nama_pembayar"
                               x-model="namaPembayarGabungan"
                               placeholder="Contoh: Keluarga Pak Budi / Budi"
                               required
                               class="input-field">
                    </div>

                    <div>
                        <label for="rekap-kode-gabungan" class="block text-xs font-bold text-gray-800 mb-1">
                            Kode Gabungan <span class="text-gray-500 font-normal">(opsional)</span>
                        </label>
                        <input type="text"
                               id="rekap-kode-gabungan"
                               name="kode_gabungan"
                               x-model="kodeGabunganInput"
                               placeholder="Otomatis digenerate sistem jika kosong"
                               class="input-field font-mono bg-[#F8F7F4]">
                    </div>

                    <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2">
                        <button type="button" @click="modalGabungkan = false" class="btn-subtle px-4 text-xs">
                            Batal
                        </button>
                        <button type="submit"
                                :disabled="!namaPembayarGabungan || selectedIds.length < 2"
                                class="btn-primary flex-1 px-4 text-xs">
                            <svg class="w-4 h-4" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            <span>Simpan Penggabungan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- MODAL CATAT REKAP STRUK TERARAH -->
    <div x-show="modalTambah" x-cloak
         @click.self="tutupModalTambah()"
         role="dialog"
         aria-modal="true"
         aria-labelledby="modal-tambah-rekap-title"
         class="modal-backdrop">
        <div class="modal-panel max-w-lg p-5 sm:p-6 space-y-4">
            <div class="w-12 h-1.5 bg-gray-300 rounded-full mx-auto mb-1 sm:hidden"></div>
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <div class="flex items-center gap-2.5">
                    <span class="w-9 h-9 rounded-xl bg-emas-100 text-emas-800 border border-emas-200 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </span>
                    <div>
                        <h3 id="modal-tambah-rekap-title" class="text-base font-bold text-gray-900 leading-tight">Input Rekap Tagihan Terarah</h3>
                        <p class="text-xs text-gray-600">Periode: {{ $periodeAktif }} | RT {{ $rtAktif }}</p>
                    </div>
                </div>
                <button type="button" @click="tutupModalTambah()" aria-label="Tutup modal input rekap"
                        class="text-gray-500 hover:text-gray-800 hover:bg-gray-100 rounded-xl p-2 min-h-[44px] min-w-[44px] flex items-center justify-center transition">
                    <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Notifikasi Kilat Sukses Simpan -->
            <div x-show="notifSukses" x-cloak
                 x-transition
                 role="status"
                 class="p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs font-bold flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                <span x-text="notifSukses"></span>
            </div>

            <!-- Kartu Panduan Warga & Nomor Struk -->
            <div class="bg-gradient-to-br from-emas-50 to-white rounded-2xl p-4 border border-emas-300 shadow-sm space-y-2.5">
                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase tracking-wider bg-emas-200 text-emas-950 tabular-money"
                              x-text="'Warga ' + (wargaIndexAktif + 1) + ' dari ' + masterWarga.length"></span>
                        <template x-if="wargaAktif.is_tercatat">
                            <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-900 flex items-center gap-1">
                                <svg class="w-3 h-3 text-emerald-700" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                Sudah Tercatat
                            </span>
                        </template>
                    </div>

                    <span class="inline-flex items-center px-2.5 py-1 rounded-xl bg-[#CEA042] text-[#FAF4E4CC] font-mono font-extrabold text-xs sm:text-sm shadow-sm tabular-money shrink-0">
                        Struk [ #<span x-text="wargaAktif.nomor_struk || wargaAktif.nomor_meter || '-'"></span> ]
                    </span>
                </div>

                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-emas-800 block">Nama Warga:</span>
                    <h4 class="text-lg font-extrabold text-gray-900 leading-tight mt-0.5" x-text="wargaAktif.nama"></h4>
                    <p class="text-xs text-gray-600 font-mono mt-0.5" x-show="wargaAktif.nama_pelanggan_pln && wargaAktif.nama_pelanggan_pln !== wargaAktif.nama">
                        Atas nama PLN: <span class="font-semibold text-gray-800" x-text="wargaAktif.nama_pelanggan_pln"></span>
                    </p>
                </div>

                <template x-if="wargaAktif.saldo_lalu !== 0">
                    <div class="pt-2 border-t border-emas-200 flex items-center justify-between text-xs font-bold tabular-money"
                         :class="wargaAktif.saldo_lalu > 0 ? 'text-emerald-800' : 'text-rose-800'">
                        <span x-text="wargaAktif.saldo_lalu > 0 ? 'Potongan Titipan Bulan Lalu:' : 'Tunggakan Bulan Lalu:'"></span>
                        <span x-text="(wargaAktif.saldo_lalu > 0 ? '-Rp ' : '+Rp ') + formatRupiah(Math.abs(wargaAktif.saldo_lalu))"></span>
                    </div>
                </template>
            </div>

            <!-- Form Tagihan Awal -->
            <form @submit.prevent="simpanDanLanjut()" class="space-y-4 text-xs">
                <div>
                    <label for="input-tagihan-awal-modal" class="block font-bold text-gray-800 text-sm mb-1.5">
                        Tagihan Awal PLN (Rp) <span class="text-rose-600">*</span>
                    </label>
                    <div class="input-addon-group rounded-2xl min-h-[54px]">
                        <span class="flex items-center justify-center bg-gray-100 px-4 text-gray-600 font-bold text-lg sm:text-xl border-r border-gray-200 select-none shrink-0">
                            Rp
                        </span>
                        <input type="text"
                               id="input-tagihan-awal-modal"
                               inputmode="numeric"
                               x-model="tagihanAwalDisplay"
                               @input="onInputTagihanAwal($event)"
                               placeholder="0"
                               required
                               autocomplete="off"
                               class="w-full text-xl sm:text-2xl font-extrabold text-gray-900 px-4 py-3 border-0 focus:ring-0 focus:outline-none placeholder:text-gray-300 bg-transparent tabular-money">
                    </div>
                    <div class="flex items-center justify-between text-[11px] text-gray-600 mt-1">
                        <span>Hanya masukkan angka struk PLN</span>
                        <span class="font-bold text-emas-800 tabular-money" x-show="tagihanAwal > 0" x-text="'Terbaca: Rp ' + formatRupiah(tagihanAwal)"></span>
                    </div>
                </div>

                <!-- Kartu Rincian Kalkulasi -->
                <div class="bg-emas-50 rounded-xl p-3.5 border border-emas-200 space-y-1.5">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-gray-700">Alokasi Kas Karang Taruna:</span>
                        <span class="font-bold text-emas-800 tabular-money">+Rp 3.000</span>
                    </div>
                    <div class="flex items-center justify-between text-xs tabular-money" x-show="saldoLalu !== 0">
                        <span class="text-gray-700" x-text="saldoLalu > 0 ? 'Potongan Titipan Bulan Lalu:' : 'Penambahan Tunggakan Lalu:'"></span>
                        <span class="font-bold" :class="saldoLalu > 0 ? 'text-emerald-800' : 'text-rose-800'"
                              x-text="(saldoLalu > 0 ? '-Rp ' : '+Rp ') + formatRupiah(Math.abs(saldoLalu))"></span>
                    </div>
                    <div class="pt-1.5 border-t border-emas-200 flex items-center justify-between text-xs font-bold">
                        <span class="text-emas-950">Total Tagihan Bersih:</span>
                        <span class="text-emerald-700 text-base font-extrabold tabular-money" x-text="'Rp ' + formatRupiah(totalTagihan)">Rp 0</span>
                    </div>
                </div>

                <!-- Dropdown Lompat Warga -->
                <div class="pt-2 border-t border-gray-100 flex items-center justify-between gap-2 text-xs text-gray-600">
                    <label for="rekap-lompat-warga" class="font-medium shrink-0">Lompat ke warga lain:</label>
                    <select id="rekap-lompat-warga" @change="pilihWargaIndex($event.target.value)"
                            class="select-field py-1.5 px-3 text-xs font-bold max-w-[220px] truncate min-h-[40px]">
                        <template x-for="(w, idx) in masterWarga" :key="w.id">
                            <option :value="idx" :selected="idx === wargaIndexAktif"
                                    x-text="'[#' + (w.nomor_struk || w.nomor_meter || '-') + '] ' + w.nama + (w.is_tercatat ? ' (Sudah)' : '')">
                            </option>
                        </template>
                    </select>
                </div>

                <!-- Tombol Aksi Navigasi & Simpan -->
                <div class="pt-3 border-t border-gray-100 flex items-center gap-2">
                    <button type="button"
                            @click="navigasiWarga(-1)"
                            :disabled="wargaIndexAktif <= 0"
                            class="btn-subtle px-3.5 text-xs min-h-[48px]">
                        &larr; Sebelumnya
                    </button>
                    <button type="submit"
                            :disabled="saving"
                            class="btn-primary flex-1 px-4 text-xs min-h-[48px]">
                        <span x-show="!saving">Simpan &amp; Lanjut &rarr;</span>
                        <span x-show="saving" x-cloak>Menyimpan...</span>
                    </button>
                    <button type="button" @click="tutupModalTambah()" class="btn-secondary px-3.5 text-xs min-h-[48px]">
                        Selesai
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL DUPLIKASI / BUKA SIKLUS BARU -->
    <div x-show="modalDuplikasi" x-cloak
         @click.self="modalDuplikasi = false"
         role="dialog"
         aria-modal="true"
         aria-labelledby="modal-duplikasi-title"
         class="modal-backdrop">
        <div class="modal-panel max-w-md p-5 sm:p-6 space-y-4">
            <div class="w-12 h-1.5 bg-gray-300 rounded-full mx-auto mb-1 sm:hidden"></div>
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <div class="flex items-center gap-2.5">
                    <span class="w-9 h-9 rounded-xl bg-emas-100 text-emas-800 border border-emas-200 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </span>
                    <div>
                        <h3 id="modal-duplikasi-title" class="text-sm sm:text-base font-bold text-gray-900">Buka Siklus Periode Baru</h3>
                        <p class="text-xs text-gray-600">Mempersiapkan data rekap untuk bulan berikutnya</p>
                    </div>
                </div>
                <button type="button" @click="modalDuplikasi = false" aria-label="Tutup modal buka siklus baru"
                        class="text-gray-500 hover:text-gray-800 hover:bg-gray-100 rounded-xl p-2 min-h-[44px] min-w-[44px] flex items-center justify-center transition">
                    <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            
            <form method="POST" action="{{ route('admin.rekap-listrik.duplikasi-siklus') }}"
                  x-data="{
                      bulanBaru: '{{ $saranBulan }}',
                      tahunBaru: {{ $saranTahun }},
                      modeSiklus: 'salin_saldo',
                      get periodeBaru() {
                          return this.bulanBaru + ' ' + this.tahunBaru;
                      }
                  }"
                  class="space-y-3.5 text-xs">
                @csrf

                <div class="space-y-1.5">
                    <span class="block font-bold text-gray-800">Metode Pembuatan Siklus:</span>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <label class="flex items-start gap-2 p-3 rounded-xl border cursor-pointer transition"
                               :class="modeSiklus === 'salin_saldo' ? 'bg-emas-50 border-[#CEA042] text-emas-950 font-bold' : 'border-gray-200 text-gray-700 hover:bg-gray-50'">
                            <input type="radio" name="mode" value="salin_saldo" x-model="modeSiklus" class="mt-0.5 text-[#CEA042] focus:ring-[#CEA042]">
                            <div>
                                <span class="block leading-tight text-xs">Bawa Saldo Titipan</span>
                                <span class="text-[11px] text-gray-600 font-normal">Kelebihan/kurang bulan lalu otomatis terpotong</span>
                            </div>
                        </label>

                        <label class="flex items-start gap-2 p-3 rounded-xl border cursor-pointer transition"
                               :class="modeSiklus === 'kosong' ? 'bg-emas-50 border-[#CEA042] text-emas-950 font-bold' : 'border-gray-200 text-gray-700 hover:bg-gray-50'">
                            <input type="radio" name="mode" value="kosong" x-model="modeSiklus" class="mt-0.5 text-[#CEA042] focus:ring-[#CEA042]">
                            <div>
                                <span class="block leading-tight text-xs">Mulai Bersih</span>
                                <span class="text-[11px] text-gray-600 font-normal">Ambil warga tanpa bawa saldo bulan lalu</span>
                            </div>
                        </label>
                    </div>
                </div>

                <div x-show="modeSiklus === 'salin_saldo'">
                    <label for="dup-periode-asal" class="block font-bold text-gray-800 mb-1">Ambil Data &amp; Saldo Dari Siklus:</label>
                    <input type="text" id="dup-periode-asal" name="periode_asal" value="{{ $periodeAktif }}" readonly
                           class="input-field font-bold bg-gray-100">
                </div>

                <div class="space-y-1.5">
                    <span class="block font-bold text-gray-800">Siklus Bulan &amp; Tahun Baru <span class="text-rose-600">*</span></span>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label for="dup-bulan-baru" class="block text-[11px] text-gray-600 mb-1">Bulan:</label>
                            <select id="dup-bulan-baru" x-model="bulanBaru" required class="select-field font-bold">
                                @foreach($daftarBulan as $num => $namaBulan)
                                    <option value="{{ $namaBulan }}" {{ $saranBulan === $namaBulan ? 'selected' : '' }}>
                                        Bulan {{ $namaBulan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="dup-tahun-baru" class="block text-[11px] text-gray-600 mb-1">Tahun:</label>
                            <input type="number" id="dup-tahun-baru" x-model.number="tahunBaru" required min="2020" max="2035"
                                   class="input-field font-bold tabular-money">
                        </div>
                    </div>

                    <input type="hidden" name="periode_baru" :value="periodeBaru">

                    <div class="flex items-center justify-between p-2.5 bg-emas-50 rounded-xl border border-emas-200">
                        <span class="text-gray-700 text-xs">Siklus yang akan dibuka:</span>
                        <span class="font-extrabold text-emas-900 text-xs font-mono" x-text="periodeBaru"></span>
                    </div>
                </div>

                <div>
                    <label for="dup-rt" class="block font-bold text-gray-800 mb-1">Wilayah RT Yang Disalin:</label>
                    <select id="dup-rt" name="rt" class="select-field font-bold">
                        <option value="semua">Semua RT (RT 01, RT 02, RT 03)</option>
                        <option value="1">Khusus RT 01</option>
                        <option value="2">Khusus RT 02</option>
                        <option value="3">Khusus RT 03</option>
                    </select>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                    <button type="button" @click="modalDuplikasi = false" class="btn-subtle px-4 text-xs">
                        Batal
                    </button>
                    <button type="submit" class="btn-primary px-5 text-xs">
                        + Buka Siklus Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL KELOLA SIKLUS PERIODE -->
    <div x-show="modalKelolaSiklus" x-cloak
         @click.self="modalKelolaSiklus = false"
         role="dialog"
         aria-modal="true"
         aria-labelledby="modal-kelola-siklus-title"
         class="modal-backdrop">
        <div class="modal-panel max-w-xl p-5 sm:p-6 space-y-4 flex flex-col">
            <div class="w-12 h-1.5 bg-gray-300 rounded-full mx-auto -mb-1 sm:hidden shrink-0"></div>
            <div class="flex items-center justify-between border-b border-gray-100 pb-3 shrink-0">
                <div class="flex items-center gap-2.5">
                    <span class="w-9 h-9 rounded-xl bg-emas-100 text-emas-800 border border-emas-200 flex items-center justify-center font-bold shrink-0">
                        <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </span>
                    <div>
                        <h3 id="modal-kelola-siklus-title" class="text-base font-bold text-gray-900 leading-tight">Kelola Siklus Periode</h3>
                        <p class="text-xs text-gray-600">Daftar siklus, ubah nama (rename), atau hapus siklus</p>
                    </div>
                </div>
                <button type="button" @click="modalKelolaSiklus = false" aria-label="Tutup modal kelola siklus"
                        class="text-gray-500 hover:text-gray-800 hover:bg-gray-100 rounded-xl p-2 min-h-[44px] min-w-[44px] flex items-center justify-center transition">
                    <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="flex items-center justify-between gap-2 bg-emas-50 p-3.5 rounded-xl border border-emas-200 shrink-0">
                <div>
                    <span class="text-xs font-bold text-emas-950 block">Ingin menambah bulan baru?</span>
                    <span class="text-[11px] text-emas-800">Otomatis salin data warga &amp; bawa saldo titipan</span>
                </div>
                <button type="button" @click="modalKelolaSiklus = false; bukaModalDuplikasi()"
                        class="btn-primary px-3.5 py-2 text-xs min-h-[40px] shrink-0">
                    <svg class="w-4 h-4" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    <span>Siklus Baru</span>
                </button>
            </div>

            <div class="overflow-y-auto space-y-2.5 pr-0.5 flex-1 max-h-[50vh]">
                <template x-for="item in siklusStats" :key="item.nama">
                    <div class="p-3.5 rounded-xl border transition"
                         :class="item.nama === '{{ $periodeAktif }}' ? 'bg-emas-50/70 border-[#CEA042] shadow-sm' : 'bg-white border-gray-200 hover:border-gray-300'">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="text-sm font-extrabold text-gray-900" x-text="item.nama"></span>
                                    <template x-if="item.nama === '{{ $periodeAktif }}'">
                                        <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-[#CEA042] text-[#FAF4E4CC]">
                                            Sedang Dibuka
                                        </span>
                                    </template>
                                </div>
                                <div class="flex items-center gap-2 mt-1 text-xs text-gray-600 flex-wrap tabular-money">
                                    <span><strong class="text-gray-900" x-text="item.total_tagihan"></strong> Warga</span>
                                    <span>&bull;</span>
                                    <span class="text-emerald-700 font-semibold"><strong x-text="item.total_lunas"></strong> Lunas</span>
                                    <span>&bull;</span>
                                    <span class="text-amber-800 font-semibold"><strong x-text="item.total_belum_lunas"></strong> Belum</span>
                                    <span>&bull;</span>
                                    <span class="font-bold text-emas-800" x-text="'Rp ' + formatRupiah(item.total_nominal)"></span>
                                </div>
                            </div>

                            <div class="flex items-center gap-1.5 shrink-0">
                                <a :href="'{{ route('admin.rekap-listrik.index') }}?periode=' + encodeURIComponent(item.nama) + '&rt={{ $rtAktif }}'"
                                   x-show="item.nama !== '{{ $periodeAktif }}'"
                                   title="Buka Rekap Periode Ini"
                                   aria-label="Buka rekap periode ini"
                                   class="p-2 text-xs font-bold text-emas-800 hover:bg-emas-50 rounded-lg border border-emas-200 transition min-h-[40px] min-w-[40px] flex items-center justify-center">
                                    <svg class="w-4 h-4" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>

                                <button type="button"
                                        @click="bukaModalRenameSiklus(item.nama)"
                                        title="Ubah Nama Siklus"
                                        aria-label="Ubah nama siklus"
                                        class="p-2 text-xs font-bold text-gray-700 hover:text-emas-800 hover:bg-emas-50 rounded-lg border border-gray-200 transition min-h-[40px] min-w-[40px] flex items-center justify-center">
                                    <svg class="w-4 h-4" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                    </svg>
                                </button>

                                <button type="button"
                                        @click="bukaModalHapusSiklus(item.nama, item.total_tagihan, item.total_lunas)"
                                        title="Hapus Siklus Ini"
                                        aria-label="Hapus siklus ini"
                                        class="p-2 text-xs font-bold text-rose-700 hover:bg-rose-50 rounded-lg border border-rose-200 transition min-h-[40px] min-w-[40px] flex items-center justify-center">
                                    <svg class="w-4 h-4" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <div class="pt-3 border-t border-gray-100 flex items-center justify-end shrink-0">
                <button type="button" @click="modalKelolaSiklus = false" class="btn-subtle px-4 text-xs">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL RENAME / UBAH NAMA SIKLUS -->
    <div x-show="modalRenameSiklus" x-cloak
         @click.self="modalRenameSiklus = false"
         role="dialog"
         aria-modal="true"
         aria-labelledby="modal-rename-siklus-title"
         class="modal-backdrop">
        <div class="modal-panel max-w-md p-5 sm:p-6 space-y-4">
            <div class="w-12 h-1.5 bg-gray-300 rounded-full mx-auto mb-1 sm:hidden"></div>
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <div>
                    <h3 id="modal-rename-siklus-title" class="text-base font-bold text-gray-900">Ubah Nama Siklus</h3>
                    <p class="text-xs text-gray-600">Sesuaikan nama bulan atau tahun siklus tagihan</p>
                </div>
                <button type="button" @click="modalRenameSiklus = false" aria-label="Tutup modal ubah nama siklus"
                        class="text-gray-500 hover:text-gray-800 hover:bg-gray-100 rounded-xl p-2 min-h-[44px] min-w-[44px] flex items-center justify-center transition">
                    <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.rekap-listrik.siklus.rename') }}" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label for="rename-periode-lama" class="block font-bold text-gray-700 mb-1">Nama Siklus Saat Ini:</label>
                    <input type="text" id="rename-periode-lama" name="periode_lama" x-model="siklusTerpilih" readonly
                           class="input-field font-bold bg-gray-100">
                </div>

                <div class="space-y-1.5">
                    <span class="block font-bold text-gray-800">Ubah Menjadi Bulan &amp; Tahun Baru <span class="text-rose-600">*</span></span>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label for="rename-bulan" class="block text-[11px] text-gray-600 mb-1">Bulan:</label>
                            <select id="rename-bulan" x-model="bulanRename" required class="select-field font-bold">
                                @foreach($daftarBulan as $num => $namaBulan)
                                    <option value="{{ $namaBulan }}">{{ $namaBulan }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="rename-tahun" class="block text-[11px] text-gray-600 mb-1">Tahun:</label>
                            <input type="number" id="rename-tahun" x-model.number="tahunRename" required min="2020" max="2035"
                                   class="input-field font-bold tabular-money">
                        </div>
                    </div>

                    <input type="hidden" name="periode_baru" :value="(bulanRename + ' ' + tahunRename).trim()">

                    <div class="flex items-center justify-between p-2.5 bg-emas-50 rounded-xl border border-emas-200 mt-2">
                        <span class="text-gray-700 text-xs">Nama Baru:</span>
                        <span class="font-extrabold text-emas-900 text-xs font-mono" x-text="bulanRename + ' ' + tahunRename"></span>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                    <button type="button" @click="modalRenameSiklus = false" class="btn-subtle px-4 text-xs">
                        Batal
                    </button>
                    <button type="submit" class="btn-primary px-5 text-xs">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL HAPUS SIKLUS -->
    <div x-show="modalHapusSiklus" x-cloak
         @click.self="modalHapusSiklus = false"
         role="dialog"
         aria-modal="true"
         aria-labelledby="modal-hapus-siklus-title"
         class="modal-backdrop">
        <div class="modal-panel max-w-md p-5 sm:p-6 space-y-4">
            <div class="w-12 h-1.5 bg-gray-300 rounded-full mx-auto mb-1 sm:hidden"></div>
            <div class="flex items-center gap-2.5 border-b border-gray-100 pb-3">
                <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center font-bold shrink-0">
                    <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </div>
                <div>
                    <h3 id="modal-hapus-siklus-title" class="text-base font-bold text-gray-900">Hapus Siklus Periode</h3>
                    <p class="text-xs text-gray-600">Tindakan ini tidak dapat dibatalkan</p>
                </div>
            </div>

            <div class="p-3.5 bg-rose-50 border border-rose-200 rounded-xl text-rose-950 text-xs space-y-2">
                <p>
                    Apakah Anda yakin ingin menghapus siklus <strong class="text-rose-900 font-extrabold" x-text="siklusTerpilih"></strong>?
                </p>
                <p class="text-xs text-rose-800">
                    Semua data tagihan warga (<strong class="tabular-money" x-text="siklusHapusTotalData"></strong> data) di dalam siklus ini akan dihapus permanen.
                </p>
                <template x-if="siklusHapusTotalLunas > 0">
                    <p class="text-xs font-bold text-amber-950 bg-amber-100 p-2.5 rounded-lg border border-amber-300 flex items-center gap-2">
                        <svg class="w-4 h-4 text-amber-700 shrink-0" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span>Peringatan: Ada <span class="tabular-money" x-text="siklusHapusTotalLunas"></span> tagihan yang sudah tercatat LUNAS pada siklus ini.</span>
                    </p>
                </template>
            </div>

            <form method="POST" action="{{ route('admin.rekap-listrik.siklus.hapus') }}" class="space-y-4 text-xs">
                @csrf
                <input type="hidden" name="periode" :value="siklusTerpilih">

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                    <button type="button" @click="modalHapusSiklus = false" class="btn-subtle px-4 text-xs">
                        Batal
                    </button>
                    <button type="submit" class="btn-danger px-5 text-xs">
                        Ya, Hapus Siklus Ini
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL SCAN & IMPOR DOKUMEN -->
    <div x-show="modalScan" x-cloak
         @click.self="if(stepScan !== 'processing') modalScan = false"
         role="dialog"
         aria-modal="true"
         aria-labelledby="modal-scan-title"
         class="modal-backdrop">
        <div class="modal-panel max-w-4xl p-4 sm:p-6 space-y-4 flex flex-col">
            <div class="w-12 h-1.5 bg-gray-300 rounded-full mx-auto -mb-1 sm:hidden shrink-0"></div>
            <div class="flex items-center justify-between border-b border-gray-100 pb-3 shrink-0">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-emas-100 text-emas-800 border border-emas-200 flex items-center justify-center font-bold shrink-0">
                        <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <div>
                        <h3 id="modal-scan-title" class="text-sm sm:text-base font-bold text-gray-900 leading-tight">Scan &amp; Impor Struk (Banyak Warga)</h3>
                        <p class="text-xs text-gray-600">Membaca lembaran foto/dokumen &amp; cross-check sebelum simpan</p>
                    </div>
                </div>
                <button type="button" @click="modalScan = false" x-show="stepScan !== 'processing'" aria-label="Tutup modal scan"
                        class="text-gray-500 hover:text-gray-800 hover:bg-gray-100 rounded-xl p-2 min-h-[44px] min-w-[44px] flex items-center justify-center transition">
                    <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- TAHAP 1: UPLOAD / FOTO DOKUMEN -->
            <div x-show="stepScan === 'upload'" class="space-y-4 py-2 overflow-y-auto">
                <div class="bg-emas-50 rounded-xl p-3.5 border border-emas-200 text-xs text-emas-950 flex items-start gap-2.5">
                    <svg class="w-5 h-5 text-emas-700 shrink-0 mt-0.5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div class="leading-relaxed text-xs">
                        <strong>Cara Kerja Cepat:</strong> Foto 1 lembar kertas rekap/buku yang berisi banyak nama warga &amp; tagihan, atau upload file Excel/PDF. Sistem akan <strong>mendeteksi semua warga &amp; nominalnya sekaligus</strong>, lalu menampilkan <strong>layar cross-check</strong> agar Anda bisa memeriksa dan mengoreksi data sebelum disimpan.
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                    <label class="flex flex-col items-center justify-center p-5 sm:p-6 border-2 border-dashed border-[#CEA042] bg-emas-50/60 hover:bg-emas-50 rounded-2xl cursor-pointer transition text-center group min-h-[140px] sm:min-h-[160px]">
                        <input type="file" accept="image/*" capture="environment" @change="prosesFile($event)" class="hidden">
                        <div class="w-12 h-12 rounded-full bg-white shadow-sm border border-emas-200 flex items-center justify-center text-emas-800 mb-2.5 group-hover:scale-105 transition">
                            <svg class="w-6 h-6" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <span class="font-bold text-gray-900 text-xs sm:text-sm">Foto Lembaran via Kamera HP</span>
                        <span class="text-xs text-gray-600 mt-0.5">Arahkan kamera ke lembaran rekap fisik</span>
                    </label>

                    <label class="flex flex-col items-center justify-center p-5 sm:p-6 border-2 border-dashed border-gray-300 hover:border-[#CEA042] bg-[#F8F7F4] hover:bg-emas-50/40 rounded-2xl cursor-pointer transition text-center group min-h-[140px] sm:min-h-[160px]">
                        <input type="file" accept="image/*,.pdf,.xlsx,.xls,.csv" @change="prosesFile($event)" class="hidden">
                        <div class="w-12 h-12 rounded-full bg-white shadow-sm border border-gray-200 flex items-center justify-center text-gray-700 mb-2.5 group-hover:scale-105 transition">
                            <svg class="w-6 h-6" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <span class="font-bold text-gray-900 text-xs sm:text-sm">Pilih Dokumen / Foto Galeri</span>
                        <span class="text-xs text-gray-600 mt-0.5">Mendukung Foto JPG/PNG, File PDF, atau Excel (.xlsx/.csv)</span>
                    </label>
                </div>
            </div>

            <!-- TAHAP 2: PROSES PEMINDAIAN -->
            <div x-show="stepScan === 'processing'" class="py-12 px-4 text-center space-y-4">
                <div class="relative w-16 h-16 mx-auto">
                    <div class="w-16 h-16 rounded-full border-4 border-emas-200 border-t-[#CEA042] animate-spin"></div>
                    <div class="absolute inset-0 flex items-center justify-center text-emas-800">
                        <svg class="w-6 h-6" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                </div>

                <div>
                    <h4 class="text-sm font-bold text-gray-900" x-text="statusScanText">Sedang Membaca Dokumen...</h4>
                    <p class="text-xs text-gray-600 mt-1">Mendeteksi baris nama warga &amp; nominal tagihan...</p>
                </div>

                <div class="max-w-xs mx-auto w-full bg-gray-200 rounded-full h-2 overflow-hidden" x-show="progressScan > 0">
                    <div class="bg-[#CEA042] h-2 transition-all duration-300" :style="'width: ' + progressScan + '%'"></div>
                </div>
            </div>

            <!-- TAHAP 3: LAYAR CROSS-CHECK MASSAL -->
            <div x-show="stepScan === 'crosscheck'" class="flex-1 overflow-y-auto space-y-3">
                <div class="bg-emerald-50 rounded-xl p-3 border border-emerald-200 text-xs text-emerald-950 flex flex-wrap items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-700 shrink-0" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Ditemukan <strong class="font-extrabold tabular-money" x-text="hasilScan.length"></strong> warga dari dokumen ini. Periksa kebenaran data di bawah:</span>
                    </div>
                    <button type="button" @click="tambahBarisScan()" class="text-xs font-bold text-emas-800 hover:underline py-1">
                        + Tambah Baris Manual
                    </button>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-3">
                    <div class="lg:col-span-4 bg-gray-100 rounded-xl p-2.5 border border-gray-200 space-y-2" x-show="previewFileUrl">
                        <div class="flex items-center justify-between text-xs font-bold text-gray-800 px-1">
                            <span>Foto Dokumen Asli:</span>
                            <button type="button" @click="tampilkanPreview = !tampilkanPreview" class="text-emas-800 hover:underline text-xs lg:hidden">
                                <span x-text="tampilkanPreview ? 'Sembunyikan' : 'Buka Foto'"></span>
                            </button>
                        </div>
                        <div x-show="tampilkanPreview || window.innerWidth >= 1024" class="max-h-56 sm:max-h-72 lg:max-h-96 overflow-auto rounded-lg bg-black/5 p-1 flex items-center justify-center">
                            <img :src="previewFileUrl" alt="Preview Dokumen" class="max-w-full h-auto object-contain rounded shadow-sm">
                        </div>
                    </div>

                    <div :class="previewFileUrl ? 'lg:col-span-8' : 'lg:col-span-12'" class="space-y-3">
                        <form method="POST" action="{{ route('admin.rekap-listrik.batch-store') }}" id="form-batch-rekap" class="space-y-3">
                            @csrf
                            <input type="hidden" name="periode" value="{{ $periodeAktif }}">
                            <input type="hidden" name="rt" value="{{ $rtAktif }}">

                            <div class="overflow-x-auto rounded-xl border border-gray-200 shadow-sm max-h-72 lg:max-h-96">
                                <table class="w-full text-left text-xs min-w-[550px]">
                                    <thead class="bg-[#F8F7F4] border-b border-gray-200 text-gray-600 font-bold uppercase text-[11px] sticky top-0 z-10">
                                        <tr>
                                            <th class="py-2.5 px-2.5 w-16">Struk</th>
                                            <th class="py-2.5 px-2.5">Warga RT {{ $rtAktif }}</th>
                                            <th class="py-2.5 px-2.5 w-32">Tagihan PLN (Rp)</th>
                                            <th class="py-2.5 px-2.5 w-20 text-right">Kartar</th>
                                            <th class="py-2.5 px-2.5 w-28 text-right">Total Bersih</th>
                                            <th class="py-2.5 px-2 w-10 text-center"></th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 bg-white">
                                        <template x-for="(item, idx) in hasilScan" :key="idx">
                                            <tr class="hover:bg-emas-50/40 transition">
                                                <td class="py-2 px-2.5 align-middle">
                                                    <input type="text" :name="'items[' + idx + '][nomor_struk]'" x-model="item.nomor_struk" required
                                                           aria-label="Nomor struk"
                                                           class="w-full text-xs font-mono font-bold rounded-lg border-gray-300 focus:border-[#CEA042] focus:ring-[#CEA042] py-1.5 px-2 min-h-[38px] tabular-money">
                                                </td>

                                                <td class="py-2 px-2.5 align-middle">
                                                    <div class="space-y-1">
                                                        <select :name="'items[' + idx + '][warga_id]'" x-model="item.warga_id" @change="onPilihWargaRow(idx, $event)"
                                                                aria-label="Pilih warga"
                                                                class="w-full text-xs rounded-lg border-gray-300 focus:border-[#CEA042] focus:ring-[#CEA042] py-1.5 px-2 min-h-[38px]">
                                                            <option value="">-- Ketik Manual / Luar RT --</option>
                                                            @foreach($wargaRtAktif as $w)
                                                                <option value="{{ $w->id }}"
                                                                        data-nama="{{ $w->nama }}"
                                                                        data-pln="{{ $w->nama_pelanggan_pln }}"
                                                                        data-saldo-lalu="{{ $w->saldo_lalu ?? 0 }}">
                                                                    {{ $w->nama }} (PLN: {{ $w->nama_pelanggan_pln }})
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <input type="hidden" :name="'items[' + idx + '][nama_warga]'" :value="item.nama_warga">
                                                        <input type="hidden" :name="'items[' + idx + '][nama_pelanggan]'" :value="item.nama_pelanggan">
                                                    </div>
                                                </td>

                                                <td class="py-2 px-2.5 align-middle">
                                                    <input type="text" inputmode="numeric"
                                                           :name="'items[' + idx + '][tagihan_awal]'"
                                                           x-model="item.tagihan_awal_display"
                                                           @input="onInputTagihanRow(idx, $event)"
                                                           placeholder="65.000"
                                                           aria-label="Nominal tagihan awal"
                                                           required
                                                           class="w-full text-xs font-bold text-gray-900 rounded-lg border-gray-300 focus:border-[#CEA042] focus:ring-[#CEA042] py-1.5 px-2 min-h-[38px] tabular-money">
                                                </td>

                                                <td class="py-2 px-2.5 text-right font-semibold text-emas-800 align-middle text-[11px] tabular-money">
                                                    +Rp 3.000
                                                </td>

                                                <td class="py-2 px-2.5 text-right font-extrabold text-gray-900 align-middle text-xs tabular-money">
                                                    <span x-text="'Rp ' + formatRupiah(item.total_tagihan)"></span>
                                                    <template x-if="item.saldo_lalu > 0">
                                                        <div class="text-[10px] text-emerald-800 font-bold" x-text="'-Rp ' + formatRupiah(item.saldo_lalu) + ' (Titip)'"></div>
                                                    </template>
                                                </td>

                                                <td class="py-2 px-2 text-center align-middle">
                                                    <button type="button" @click="hapusBarisScan(idx)" aria-label="Hapus baris ini"
                                                            class="text-rose-600 hover:text-rose-800 hover:bg-rose-50 rounded-lg p-1 font-bold text-sm min-h-[36px] min-w-[36px] flex items-center justify-center transition">&times;</button>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="bg-[#F8F7F4] rounded-xl p-3 border border-gray-200 flex flex-wrap items-center justify-between gap-3 text-xs">
                    <div>
                        <span class="text-gray-600">Total Warga Direkap:</span>
                        <strong class="text-gray-900 ml-1 tabular-money" x-text="hasilScan.length + ' Warga'"></strong>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-gray-600">Estimasi Total Tagihan:</span>
                        <strong class="text-sm font-extrabold text-emas-800 tabular-money" x-text="'Rp ' + formatRupiah(totalAkumulasiScan)"></strong>
                    </div>
                </div>

                <div class="pt-3 border-t border-gray-100 flex flex-wrap items-center justify-between gap-2">
                    <button type="button" @click="resetScan()" class="btn-subtle px-3.5 text-xs">
                        Scan Dokumen Lain
                    </button>

                    <button type="button" @click="submitBatchForm()" class="btn-primary px-5 text-xs">
                        <svg class="w-4 h-4" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span x-text="'Data Sudah Sesuai, Simpan Semua (' + hasilScan.length + ' Warga)'"></span>
                    </button>
                </div>
            </div>

        </div>
    </div>

</div>

@push('scripts')
<script>
function rekapListrik() {
    let masterWarga = [];
    let allRekap = [];
    let allPeriode = [];
    try {
        const elJson = document.getElementById('warga-rt-json');
        if (elJson && elJson.textContent) {
            masterWarga = JSON.parse(elJson.textContent);
        }
        const elRekap = document.getElementById('daftar-rekap-json');
        if (elRekap && elRekap.textContent) {
            allRekap = JSON.parse(elRekap.textContent);
        }
        const elPeriode = document.getElementById('daftar-periode-json');
        if (elPeriode && elPeriode.textContent) {
            allPeriode = JSON.parse(elPeriode.textContent);
        }
    } catch (e) {
        console.error('Error parsing warga master / rekap / periode json', e);
    }

    return {
        cari: '{{ addslashes($cari ?? "") }}',
        modalTambah: false,
        modalDuplikasi: false,
        modalScan: false,
        modalGabungkan: false,
        modalKelolaSiklus: false,
        modalRenameSiklus: false,
        modalHapusSiklus: false,

        siklusTerpilih: '{{ $periodeAktif }}',
        siklusHapusTotalData: 0,
        siklusHapusTotalLunas: 0,
        bulanRename: '{{ $saranBulan }}',
        tahunRename: {{ $saranTahun }},
        siklusStats: allPeriode || [],

        selectedIds: [],
        namaPembayarGabungan: '',
        kodeGabunganInput: '',
        allRekap: allRekap || [],
        hasilScan: [],
        stepScan: 'upload', // 'upload', 'processing', 'crosscheck'
        statusScanText: 'Membaca Dokumen...',
        progressScan: 0,
        previewFileUrl: null,
        tampilkanPreview: true,
        masterWarga: masterWarga || [],
        wargaIndexAktif: 0,
        saving: false,
        notifSukses: '',
        adaPerubahan: false,

        wargaTerpilih: '',
        namaWarga: '',
        namaPelanggan: '',
        nomorStruk: '',
        tagihanAwalDisplay: '',
        kasKartar: 3000,

        init() {
            this.refreshData();
        },

        refreshData() {
            try {
                if (!this.masterWarga || this.masterWarga.length === 0) {
                    const elJson = document.getElementById('warga-rt-json');
                    if (elJson && elJson.textContent) {
                        this.masterWarga = JSON.parse(elJson.textContent);
                    }
                }
                if (!this.allRekap || this.allRekap.length === 0) {
                    const elRekap = document.getElementById('daftar-rekap-json');
                    if (elRekap && elRekap.textContent) {
                        this.allRekap = JSON.parse(elRekap.textContent);
                    }
                }
                if (!this.siklusStats || this.siklusStats.length === 0) {
                    const elPeriode = document.getElementById('daftar-periode-json');
                    if (elPeriode && elPeriode.textContent) {
                        this.siklusStats = JSON.parse(elPeriode.textContent);
                    }
                }
            } catch (e) {
                console.error('Error refreshing rekap data', e);
            }
        },

        bukaModalKelolaSiklus() {
            this.refreshData();
            this.modalKelolaSiklus = true;
        },

        bukaModalRenameSiklus(namaSiklus) {
            this.siklusTerpilih = namaSiklus;
            const parts = (namaSiklus || '').trim().split(' ');
            this.bulanRename = parts[0] || '{{ $saranBulan }}';
            this.tahunRename = parseInt(parts[1]) || {{ date('Y') }};
            this.modalRenameSiklus = true;
        },

        bukaModalHapusSiklus(namaSiklus, totalData, totalLunas) {
            this.siklusTerpilih = namaSiklus;
            this.siklusHapusTotalData = totalData || 0;
            this.siklusHapusTotalLunas = totalLunas || 0;
            this.modalHapusSiklus = true;
        },

        bukaModalDuplikasi() {
            this.modalDuplikasi = true;
        },

        get wargaAktif() {
            if (!this.masterWarga || this.masterWarga.length === 0) {
                return { nama: '', nomor_struk: '', nomor_meter: '', saldo_lalu: 0, is_tercatat: false };
            }
            return this.masterWarga[this.wargaIndexAktif] || { nama: '', nomor_struk: '', nomor_meter: '', saldo_lalu: 0, is_tercatat: false };
        },

        get saldoLalu() {
            return parseInt(this.wargaAktif.saldo_lalu || 0);
        },

        get tagihanAwal() {
            return parseInt(this.tagihanAwalDisplay.toString().replace(/[^0-9]/g, '')) || 0;
        },

        set tagihanAwal(val) {
            let num = parseInt(val) || 0;
            this.tagihanAwalDisplay = num > 0 ? this.formatRupiah(num) : '';
        },

        onInputTagihanAwal(e) {
            let clean = e.target.value.replace(/[^0-9]/g, '');
            if (!clean) {
                this.tagihanAwalDisplay = '';
                return;
            }
            this.tagihanAwalDisplay = this.formatRupiah(parseInt(clean));
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
            if (!this.allRekap || !Array.isArray(this.allRekap)) return [];
            return this.allRekap.filter(item => this.selectedIds.includes(item.id));
        },

        get totalTerpilih() {
            return this.selectedItems.reduce((acc, curr) => acc + (curr.total_tagihan || 0), 0);
        },

        bukaModalGabungkan() {
            if (this.selectedIds.length < 2) return;
            const items = this.selectedItems;
            this.kodeGabunganInput = 'GAB-' + Math.floor(100000 + Math.random() * 900000);
            this.namaPembayarGabungan = items.length > 0 ? items[0].nama_warga : '';
            this.modalGabungkan = true;
        },

        cocokSearch(warga, pln, struk, gab, pembayar) {
            if (!this.cari || this.cari.trim() === '') return true;
            const q = this.cari.toLowerCase().trim();
            const w = (warga || '').toLowerCase();
            const p = (pln || '').toLowerCase();
            const s = (struk || '').toLowerCase();
            const g = (gab || '').toLowerCase();
            const b = (pembayar || '').toLowerCase();

            return w.includes(q) || p.includes(q) || s.includes(q) || g.includes(q) || b.includes(q);
        },

        get totalTagihan() {
            const awal = this.tagihanAwal;
            if (awal <= 0) return 0;
            const bruto = awal + this.kasKartar;
            const netto = bruto - this.saldoLalu;
            return Math.max(0, netto);
        },

        bukaModalTambah(index = null) {
            this.refreshData();
            if (!this.masterWarga || this.masterWarga.length === 0) {
                alert('Belum ada data warga terdaftar di RT {{ $rtAktif }}.');
                return;
            }
            if (index !== null) {
                this.wargaIndexAktif = parseInt(index);
            } else {
                // Cari warga pertama yang belum tercatat di periode ini
                const belumCatatIndex = this.masterWarga.findIndex(w => !w.is_tercatat);
                this.wargaIndexAktif = belumCatatIndex !== -1 ? belumCatatIndex : 0;
            }
            this.muatDataWargaAktif();
            this.modalTambah = true;
            setTimeout(() => {
                const inp = document.getElementById('input-tagihan-awal-modal');
                if (inp) {
                    inp.focus();
                    inp.select();
                }
            }, 100);
        },

        muatDataWargaAktif() {
            const w = this.wargaAktif;
            if (w && w.is_tercatat && w.tagihan_awal_tercatat) {
                this.tagihanAwalDisplay = this.formatRupiah(w.tagihan_awal_tercatat);
            } else {
                this.tagihanAwalDisplay = '';
            }
        },

        pilihWargaIndex(idx) {
            this.wargaIndexAktif = parseInt(idx);
            this.muatDataWargaAktif();
            setTimeout(() => {
                const inp = document.getElementById('input-tagihan-awal-modal');
                if (inp) {
                    inp.focus();
                    inp.select();
                }
            }, 50);
        },

        navigasiWarga(step) {
            const nextIdx = this.wargaIndexAktif + step;
            if (nextIdx >= 0 && nextIdx < this.masterWarga.length) {
                this.wargaIndexAktif = nextIdx;
                this.muatDataWargaAktif();
                setTimeout(() => {
                    const inp = document.getElementById('input-tagihan-awal-modal');
                    if (inp) {
                        inp.focus();
                        inp.select();
                    }
                }, 50);
            }
        },

        tutupModalTambah() {
            this.modalTambah = false;
            if (this.adaPerubahan) {
                window.location.reload();
            }
        },

        async simpanDanLanjut() {
            if (this.tagihanAwal <= 0) {
                alert('Mohon masukkan nominal tagihan awal PLN (harus lebih dari 0).');
                return;
            }
            this.saving = true;
            const w = this.wargaAktif;

            try {
                const response = await fetch('{{ route("admin.rekap-listrik.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        periode: '{{ $periodeAktif }}',
                        rt: {{ (int) $rtAktif }},
                        warga_id: w.id,
                        nomor_struk: w.nomor_struk || w.nomor_meter || '',
                        tagihan_awal: this.tagihanAwal
                    })
                });

                const res = await response.json();
                if (response.ok && (res.success || res.status === 'success')) {
                    this.adaPerubahan = true;
                    w.is_tercatat = true;
                    w.tagihan_awal_tercatat = this.tagihanAwal;

                    const namaW = w.nama;
                    const strukW = w.nomor_struk || w.nomor_meter || '-';
                    const nominalW = this.formatRupiah(this.tagihanAwal);
                    this.notifSukses = `✓ Tersimpan: [Struk #${strukW}] ${namaW} (Rp ${nominalW})`;

                    setTimeout(() => {
                        this.notifSukses = '';
                    }, 3500);

                    // Jika masih ada warga berikutnya, lompat ke warga berikutnya
                    if (this.wargaIndexAktif < this.masterWarga.length - 1) {
                        this.wargaIndexAktif++;
                        this.muatDataWargaAktif();
                        setTimeout(() => {
                            const inp = document.getElementById('input-tagihan-awal-modal');
                            if (inp) {
                                inp.focus();
                                inp.select();
                            }
                        }, 50);
                    } else {
                        alert('Semua warga telah selesai dicatat!');
                        window.location.reload();
                    }
                } else {
                    alert(res.message || 'Gagal menyimpan rekap tagihan.');
                }
            } catch (err) {
                console.error(err);
                alert('Terjadi kesalahan koneksi saat menyimpan rekap.');
            } finally {
                this.saving = false;
            }
        },

        pilihWarga(e) {
            const selected = e.target.selectedOptions[0];
            if (selected && selected.value) {
                this.wargaTerpilih = selected.value;
                this.namaWarga = selected.dataset.nama || '';
                this.namaPelanggan = selected.dataset.pln || '';
                this.saldoLalu = parseInt(selected.dataset.saldoLalu || 0);
            } else {
                this.wargaTerpilih = '';
                this.saldoLalu = 0;
            }
        },

        // --- FITUR SCAN & IMPOR DOKUMEN PINTAR (BANYAK WARGA) ---
        bukaModalScan() {
            this.stepScan = 'upload';
            this.progressScan = 0;
            this.previewFileUrl = null;
            this.tampilkanPreview = true;
            this.hasilScan = [];
            this.modalScan = true;
        },

        resetScan() {
            this.stepScan = 'upload';
            this.previewFileUrl = null;
            this.hasilScan = [];
        },

        async prosesFile(event) {
            const file = event.target.files[0];
            if (!file) return;

            this.stepScan = 'processing';
            this.progressScan = 10;
            this.statusScanText = 'Memuat berkas...';

            const ext = file.name.split('.').pop().toLowerCase();

            if (['xlsx', 'xls', 'csv'].includes(ext)) {
                await this.prosesExcel(file);
            } else if (file.type.startsWith('image/')) {
                this.previewFileUrl = URL.createObjectURL(file);
                await this.prosesGambarOCR(file);
            } else if (ext === 'pdf') {
                await this.prosesPdf(file);
            } else {
                alert('Format berkas tidak didukung. Gunakan Foto (JPG/PNG), File PDF, atau Excel/CSV.');
                this.stepScan = 'upload';
            }
        },

        async prosesExcel(file) {
            this.statusScanText = 'Membaca lembar Excel...';
            this.progressScan = 40;

            try {
                const data = await file.arrayBuffer();
                const workbook = XLSX.read(data, { type: 'array' });
                const firstSheetName = workbook.SheetNames[0];
                const worksheet = workbook.Sheets[firstSheetName];
                const rows = XLSX.utils.sheet_to_json(worksheet, { header: 1, defval: '' });

                this.progressScan = 70;
                this.statusScanText = 'Mencocokkan baris warga & nominal...';

                let detected = [];
                let counterStruk = 1;

                rows.forEach((row, rowIdx) => {
                    if (!Array.isArray(row) || row.length === 0) return;
                    const rowStr = row.join(' ').toLowerCase();
                    // Lewati baris header umum
                    if (rowStr.includes('nama') && rowStr.includes('tagihan')) return;

                    let rowNominal = 0;
                    let rowNama = '';
                    let rowStruk = '';

                    row.forEach(cell => {
                        const cellStr = cell.toString().trim();
                        const num = parseInt(cellStr.replace(/[^0-9]/g, ''));
                        if (num >= 5000 && num <= 5000000 && rowNominal === 0) {
                            rowNominal = num;
                        } else if (cellStr.length > 2 && isNaN(cellStr) && !rowNama) {
                            rowNama = cellStr;
                        } else if (!rowStruk && num > 0 && num < 1000) {
                            rowStruk = num.toString();
                        }
                    });

                    if (rowNominal > 0 || rowNama.length > 0) {
                        const matched = this.cariWargaCocok(rowNama);
                        const wargaObj = matched || {};
                        const tagihanAwal = rowNominal;
                        const kas = tagihanAwal > 0 ? 3000 : 0;
                        const saldoLalu = parseInt(wargaObj.saldo_lalu || 0);
                        const totalBersih = Math.max(0, tagihanAwal + kas - saldoLalu);

                        detected.push({
                            nomor_struk: rowStruk || (counterStruk++).toString(),
                            warga_id: wargaObj.id || '',
                            nama_warga: wargaObj.nama || rowNama || ('Warga ' + counterStruk),
                            nama_pelanggan: wargaObj.nama_pelanggan_pln || rowNama || '',
                            tagihan_awal: tagihanAwal,
                            tagihan_awal_display: tagihanAwal > 0 ? this.formatRupiah(tagihanAwal) : '',
                            kas_karang_taruna: kas,
                            saldo_lalu: saldoLalu,
                            total_tagihan: totalBersih
                        });
                    }
                });

                if (detected.length === 0) {
                    alert('Tidak menemukan baris data yang valid di file Excel ini.');
                    this.stepScan = 'upload';
                    return;
                }

                this.hasilScan = detected;
                this.progressScan = 100;
                this.stepScan = 'crosscheck';

            } catch (err) {
                console.error(err);
                alert('Gagal membaca file Excel. Pastikan format file valid.');
                this.stepScan = 'upload';
            }
        },

        async prosesGambarOCR(file) {
            this.statusScanText = 'Memindai teks pada foto struk/lembaran...';
            this.progressScan = 30;

            try {
                if (typeof Tesseract === 'undefined') {
                    alert('Library OCR sedang dimuat, silakan coba beberapa detik lagi.');
                    this.stepScan = 'upload';
                    return;
                }

                const result = await Tesseract.recognize(
                    file,
                    'ind',
                    {
                        logger: m => {
                            if (m.status === 'recognizing text') {
                                this.progressScan = Math.min(95, Math.round(m.progress * 100));
                            }
                        }
                    }
                );

                this.statusScanText = 'Menganalisis nama warga & tagihan...';
                this.progressScan = 95;

                const text = result.data.text || '';
                this.parseTeksKeBanyakWarga(text);

            } catch (err) {
                console.error(err);
                alert('Gagal memproses gambar OCR. Silakan periksa koneksi internet atau gunakan file Excel/CSV.');
                this.stepScan = 'upload';
            }
        },

        async prosesPdf(file) {
            this.statusScanText = 'Membaca dokumen PDF...';
            this.progressScan = 40;

            try {
                const arrayBuffer = await file.arrayBuffer();
                const pdf = await pdfjsLib.getDocument({ data: arrayBuffer }).promise;
                let fullText = '';

                for (let i = 1; i <= pdf.numPages; i++) {
                    const page = await pdf.getPage(i);
                    const content = await page.getTextContent();
                    const pageText = content.items.map(item => item.str).join(' ');
                    fullText += '\n' + pageText;
                }

                this.parseTeksKeBanyakWarga(fullText);

            } catch (err) {
                console.error(err);
                alert('Gagal membaca file PDF.');
                this.stepScan = 'upload';
            }
        },

        parseTeksKeBanyakWarga(text) {
            const lines = text.split('\n').map(l => l.trim()).filter(l => l.length > 0);
            let detected = [];
            let counterStruk = 1;

            // Periksa setiap warga master: apakah namanya muncul di teks dokumen ini?
            (this.masterWarga || []).forEach(w => {
                const namaTokens = w.nama.toLowerCase().split(' ').filter(t => t.length > 2);
                let foundLine = null;

                for (let line of lines) {
                    const lLower = line.toLowerCase();
                    const matchCount = namaTokens.filter(token => lLower.includes(token)).length;
                    if (matchCount >= Math.min(2, namaTokens.length) || lLower.includes(w.nama.toLowerCase())) {
                        foundLine = line;
                        break;
                    }
                }

                if (foundLine) {
                    // Cari nominal angka di baris tersebut
                    let numbers = foundLine.match(/\d[\d.,]+/g) || [];
                    let tagihanAwal = 0;
                    for (let n of numbers) {
                        let cleanNum = parseInt(n.replace(/[^0-9]/g, ''));
                        if (cleanNum >= 5000 && cleanNum <= 5000000) {
                            tagihanAwal = cleanNum;
                            break;
                        }
                    }

                    // Cari nomor struk jika ada di awal baris misal "1." atau "[1]"
                    let strukMatch = foundLine.match(/(?:^|\[|#|\b)(\d{1,3})(?:\]|\.|\b)/);
                    let noStruk = strukMatch ? strukMatch[1] : (counterStruk++).toString();

                    const kas = tagihanAwal > 0 ? 3000 : 0;
                    const saldoLalu = parseInt(w.saldo_lalu || 0);
                    const totalBersih = Math.max(0, tagihanAwal + kas - saldoLalu);

                    detected.push({
                        nomor_struk: noStruk,
                        warga_id: w.id,
                        nama_warga: w.nama,
                        nama_pelanggan: w.nama_pelanggan_pln,
                        tagihan_awal: tagihanAwal,
                        tagihan_awal_display: tagihanAwal > 0 ? this.formatRupiah(tagihanAwal) : '',
                        kas_karang_taruna: kas,
                        saldo_lalu: saldoLalu,
                        total_tagihan: totalBersih
                    });
                }
            });

            // Jika tidak ada nama master yang cocok (misal struk bebas), parse baris per baris numerik
            if (detected.length === 0) {
                lines.forEach((line, idx) => {
                    let numbers = line.match(/\d[\d.,]+/g) || [];
                    let tagihanAwal = 0;
                    for (let n of numbers) {
                        let cleanNum = parseInt(n.replace(/[^0-9]/g, ''));
                        if (cleanNum >= 5000 && cleanNum <= 5000000) {
                            tagihanAwal = cleanNum;
                            break;
                        }
                    }

                    if (tagihanAwal > 0 && line.length > 5) {
                        const namaClean = line.replace(/[^a-zA-Z\s]/g, '').trim();
                        const matched = this.cariWargaCocok(namaClean);
                        const wObj = matched || {};
                        const kas = 3000;
                        const saldoLalu = parseInt(wObj.saldo_lalu || 0);

                        detected.push({
                            nomor_struk: (counterStruk++).toString(),
                            warga_id: wObj.id || '',
                            nama_warga: wObj.nama || namaClean || ('Warga ' + counterStruk),
                            nama_pelanggan: wObj.nama_pelanggan_pln || namaClean || '',
                            tagihan_awal: tagihanAwal,
                            tagihan_awal_display: this.formatRupiah(tagihanAwal),
                            kas_karang_taruna: kas,
                            saldo_lalu: saldoLalu,
                            total_tagihan: Math.max(0, tagihanAwal + kas - saldoLalu)
                        });
                    }
                });
            }

            if (detected.length === 0) {
                // Beri minimal 1 baris kosong agar user bisa isi
                this.tambahBarisScan();
            } else {
                this.hasilScan = detected;
            }

            this.progressScan = 100;
            this.stepScan = 'crosscheck';
        },

        cariWargaCocok(namaInput) {
            if (!namaInput) return null;
            const q = namaInput.toLowerCase();
            return (this.masterWarga || []).find(w => {
                const n = w.nama.toLowerCase();
                const p = (w.nama_pelanggan_pln || '').toLowerCase();
                return n.includes(q) || q.includes(n) || (p && (p.includes(q) || q.includes(p)));
            }) || null;
        },

        onPilihWargaRow(idx, event) {
            const selected = event.target.selectedOptions[0];
            if (selected && selected.value) {
                const item = this.hasilScan[idx];
                item.warga_id = selected.value;
                item.nama_warga = selected.dataset.nama || '';
                item.nama_pelanggan = selected.dataset.pln || '';
                item.saldo_lalu = parseInt(selected.dataset.saldoLalu || 0);
                this.recalculateRow(idx);
            }
        },

        onInputTagihanRow(idx, event) {
            const raw = event.target.value.replace(/[^0-9]/g, '');
            const item = this.hasilScan[idx];
            item.tagihan_awal = parseInt(raw) || 0;
            item.tagihan_awal_display = raw ? this.formatRupiah(parseInt(raw)) : '';
            this.recalculateRow(idx);
        },

        recalculateRow(idx) {
            const item = this.hasilScan[idx];
            const awal = item.tagihan_awal || 0;
            item.kas_karang_taruna = awal > 0 ? 3000 : 0;
            const netto = (awal + item.kas_karang_taruna) - (item.saldo_lalu || 0);
            item.total_tagihan = Math.max(0, netto);
        },

        tambahBarisScan() {
            this.hasilScan.push({
                nomor_struk: (this.hasilScan.length + 1).toString(),
                warga_id: '',
                nama_warga: '',
                nama_pelanggan: '',
                tagihan_awal: 0,
                tagihan_awal_display: '',
                kas_karang_taruna: 3000,
                saldo_lalu: 0,
                total_tagihan: 0
            });
        },

        hapusBarisScan(idx) {
            this.hasilScan.splice(idx, 1);
        },

        get totalAkumulasiScan() {
            if (!this.hasilScan || !Array.isArray(this.hasilScan)) return 0;
            return this.hasilScan.reduce((acc, curr) => acc + (curr.total_tagihan || 0), 0);
        },

        submitBatchForm() {
            if (this.hasilScan.length === 0) {
                alert('Belum ada data warga untuk disimpan.');
                return;
            }
            const form = document.getElementById('form-batch-rekap');
            if (form) {
                form.submit();
            }
        },

        formatRupiah(num) {
            return new Intl.NumberFormat('id-ID').format(num || 0);
        }
    };
}
</script>
<!-- Library CDN untuk OCR Gambar, Pembaca Excel, dan PDF (Deferred agar tidak memblokir inisialisasi Alpine) -->
<script defer src="https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.min.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.mini.min.js"></script>
<script defer src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
@endpush
@endsection
