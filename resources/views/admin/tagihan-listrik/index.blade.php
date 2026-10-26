@extends('layouts.admin')

@section('title', 'Pendataan Tagihan Listrik Warga')

@section('content')
<div class="space-y-4 sm:space-y-6"
     x-data="{
        tabAktif: '{{ $tab }}',
        modalBayar: false,
        bayarItem: {
            id: null,
            ids: [],
            nama_warga: '',
            nama_pelanggan: '',
            total_tagihan: 0,
            uang_dibayar: 0,
            kode_gabungan: '',
            get selisih() {
                const bayar = parseInt(this.uang_dibayar) || 0;
                return bayar - (this.total_tagihan || 0);
            }
        },
        selectedIds: [],
        bukaModalBayarTunggal(item) {
            this.bayarItem.ids = [item.id];
            this.bayarItem.nama_warga = item.nama_warga || item.nama_pelanggan;
            this.bayarItem.nama_pelanggan = item.nama_pelanggan;
            this.bayarItem.total_tagihan = item.total_tagihan;
            this.bayarItem.uang_dibayar = item.total_tagihan;
            this.bayarItem.kode_gabungan = item.kode_gabungan || '';
            this.modalBayar = true;
        },
        bukaModalBayarGabungan() {
            if (this.selectedIds.length === 0) {
                alert('Pilih minimal 1 warga dari daftar belum bayar untuk memproses pembayaran.');
                return;
            }
            let total = 0;
            let names = [];
            this.selectedIds.forEach(id => {
                const row = window.dataBelumBayar.find(d => d.id == id);
                if (row) {
                    total += row.total_tagihan;
                    names.push(row.nama_warga || row.nama_pelanggan);
                }
            });
            this.bayarItem.ids = this.selectedIds;
            this.bayarItem.nama_warga = names.slice(0, 3).join(', ') + (names.length > 3 ? ' (' + (names.length - 3) + ' lainnya)' : '');
            this.bayarItem.nama_pelanggan = names.length + ' Warga Digabung';
            this.bayarItem.total_tagihan = total;
            this.bayarItem.uang_dibayar = total;
            this.bayarItem.kode_gabungan = 'GAB-' + Math.random().toString(36).substring(2, 8).toUpperCase();
            this.modalBayar = true;
        },
        formatRupiah(angka) {
            return new Intl.NumberFormat('id-ID').format(angka);
        }
     }">

    <!-- Header & Action -->
    <div class="bg-white rounded-xl border border-gray-200/90 p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-amber-500 text-white font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </span>
                <h1 class="text-lg sm:text-2xl font-bold text-gray-900 tracking-tight leading-tight">
                    Pendataan Tagihan Listrik Warga
                </h1>
            </div>
            <p class="text-xs text-gray-500 mt-1">
                Alur rekap tagihan bulanan, pencatatan kas karang taruna Rp 3.000, dan kasir pembayaran struk gabungan.
            </p>
        </div>

        <div class="flex items-center gap-2 self-stretch sm:self-auto">
            <a href="{{ route('admin.warga.index') }}"
               class="px-3.5 py-2.5 text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition min-h-[44px] flex items-center gap-1.5">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                Master Warga PLN
            </a>

            <a href="{{ route('admin.tagihan-listrik.create') }}"
               class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-2 px-4 py-2.5 text-xs font-bold text-white bg-[#CEA042] hover:bg-[#BA8832] focus-visible:ring-2 focus-visible:ring-[#CEA042] rounded-xl shadow-xs transition min-h-[44px]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                + Rekap Tagihan Baru
            </a>
        </div>
    </div>

    <!-- 4 Kartu Rekapitulasi & Pemisahan Kas -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <!-- Total Tagihan Listrik PLN -->
        <div class="bg-white rounded-xl p-3.5 sm:p-4 border border-gray-200/90 shadow-xs">
            <span class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider block">Total Tagihan Awal PLN</span>
            <div class="text-lg sm:text-2xl font-extrabold text-gray-900 tracking-tight mt-0.5">
                Rp {{ number_format($totalTagihanAwal, 0, ',', '.') }}
            </div>
            <div class="text-[11px] text-gray-500 mt-1 flex items-center justify-between">
                <span>{{ $totalData }} tagihan</span>
                <span class="font-semibold text-gray-700">Wajib: Rp {{ number_format($grandTotalTagihan, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Kas Karang Taruna Terkumpul -->
        <div class="bg-white rounded-xl p-3.5 sm:p-4 border border-amber-200/80 bg-linear-to-b from-amber-50/50 to-white shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-amber-800 uppercase tracking-wider block">Kas Karang Taruna</span>
                <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded bg-amber-100 text-amber-800">Rp 3.000 / tagihan</span>
            </div>
            <div class="text-lg sm:text-2xl font-extrabold text-amber-600 tracking-tight mt-0.5">
                Rp {{ number_format($totalKasKarangTaruna, 0, ',', '.') }}
            </div>
            <p class="text-[11px] text-amber-700 mt-1">
                Dari {{ $totalLunas }} tagihan warga yang lunas
            </p>
        </div>

        <!-- Status Kasir Bayar -->
        <div class="bg-white rounded-xl p-3.5 sm:p-4 border border-gray-200/90 shadow-xs">
            <span class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider block">Status Kasir Pembayaran</span>
            <div class="text-lg sm:text-2xl font-extrabold text-emerald-600 tracking-tight mt-0.5">
                {{ $totalLunas }} <span class="text-xs font-normal text-gray-500">/ {{ $totalData }} lunas</span>
            </div>
            <p class="text-[11px] mt-1">
                @if($totalBelumLunas > 0)
                    <span class="text-rose-600 font-bold">{{ $totalBelumLunas }} warga belum bayar</span>
                @else
                    <span class="text-emerald-600 font-semibold">Semua tagihan terselesaikan</span>
                @endif
            </p>
        </div>

        <!-- Rekapitulasi Selisih -->
        <div class="bg-white rounded-xl p-3.5 sm:p-4 border border-gray-200/90 shadow-xs">
            <span class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider block">Pencatatan Selisih Uang</span>
            <div class="flex items-center justify-between mt-1 text-xs">
                <span class="text-emerald-700 font-medium">Titipan (+):</span>
                <span class="font-bold text-emerald-700">+Rp {{ number_format($totalLebih, 0, ',', '.') }}</span>
            </div>
            <div class="flex items-center justify-between mt-1 text-xs">
                <span class="text-rose-700 font-medium">Kurang (-):</span>
                <span class="font-bold text-rose-700">Rp {{ number_format($totalKurang, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <!-- TAB TAHAPAN (Semua Tagihan | Kasir Pembayaran / Belum Bayar | Riwayat Lunas) -->
    <div class="border-b border-gray-200 flex items-center justify-between gap-3">
        <div class="flex items-center gap-2 text-xs font-bold">
            <a href="{{ route('admin.tagihan-listrik.index', array_merge(request()->query(), ['tab' => 'semua'])) }}"
               class="px-4 py-2.5 border-b-2 transition flex items-center gap-2 {{ $tab === 'semua' ? 'border-amber-500 text-amber-600 font-extrabold' : 'border-transparent text-gray-500 hover:text-gray-800' }}">
                <span>Semua Data Rekap</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] {{ $tab === 'semua' ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-600' }}">{{ $totalData }}</span>
            </a>

            <a href="{{ route('admin.tagihan-listrik.index', array_merge(request()->query(), ['tab' => 'belum_bayar'])) }}"
               class="px-4 py-2.5 border-b-2 transition flex items-center gap-2 {{ $tab === 'belum_bayar' ? 'border-amber-500 text-amber-600 font-extrabold' : 'border-transparent text-gray-500 hover:text-gray-800' }}">
                <span>Kasir Pembayaran (Belum Bayar)</span>
                @if($totalBelumLunas > 0)
                    <span class="px-2 py-0.5 rounded-full text-[10px] bg-rose-100 text-rose-700 font-extrabold animate-pulse">{{ $totalBelumLunas }}</span>
                @else
                    <span class="px-2 py-0.5 rounded-full text-[10px] bg-gray-100 text-gray-600">0</span>
                @endif
            </a>

            <a href="{{ route('admin.tagihan-listrik.index', array_merge(request()->query(), ['tab' => 'lunas'])) }}"
               class="px-4 py-2.5 border-b-2 transition flex items-center gap-2 {{ $tab === 'lunas' ? 'border-amber-500 text-amber-600 font-extrabold' : 'border-transparent text-gray-500 hover:text-gray-800' }}">
                <span>Riwayat Lunas</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] {{ $tab === 'lunas' ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-600' }}">{{ $totalLunas }}</span>
            </a>
        </div>

        @if($tab === 'belum_bayar')
            <button type="button" @click="bukaModalBayarGabungan()"
                    class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-amber-900 bg-amber-100 hover:bg-amber-200 border border-amber-300 rounded-lg shadow-xs transition min-h-[36px]">
                <svg class="w-4 h-4 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                </svg>
                Bayar Terpilih Sekaligus (Struk Gabungan)
            </button>
        @endif
    </div>

    <!-- Filter & Pencarian -->
    <div class="bg-white rounded-xl border border-gray-200/90 p-3.5 sm:p-4 shadow-xs">
        <form method="GET" action="{{ route('admin.tagihan-listrik.index') }}" class="space-y-3">
            <input type="hidden" name="tab" value="{{ $tab }}">

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2.5">
                <!-- Pencarian -->
                <div class="lg:col-span-2">
                    <label class="block text-[11px] font-bold text-gray-600 uppercase mb-1">Cari Warga / Rekening PLN / Struk</label>
                    <input type="text" name="cari" value="{{ $cari }}" placeholder="Nama warga, nama rekening PLN, nomor struk, kode..."
                           class="w-full text-base sm:text-xs rounded-lg border-gray-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 py-2 px-3 shadow-xs min-h-[44px] sm:min-h-0">
                </div>

                <!-- Filter Periode -->
                <div>
                    <label class="block text-[11px] font-bold text-gray-600 uppercase mb-1">Periode</label>
                    <select name="periode" onchange="this.form.submit()"
                            class="w-full text-base sm:text-xs rounded-lg border-gray-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 py-2 px-2.5 shadow-xs min-h-[44px] sm:min-h-0">
                        <option value="">Semua Periode</option>
                        @foreach($daftarPeriode as $p)
                            <option value="{{ $p }}" {{ $periode === $p ? 'selected' : '' }}>{{ $p }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter RT -->
                <div>
                    <label class="block text-[11px] font-bold text-gray-600 uppercase mb-1">Rukun Tetangga (RT)</label>
                    <select name="rt" onchange="this.form.submit()"
                            class="w-full text-base sm:text-xs rounded-lg border-gray-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 py-2 px-2.5 shadow-xs min-h-[44px] sm:min-h-0">
                        <option value="">Semua RT</option>
                        @foreach($daftarRt as $rtItem)
                            <option value="{{ $rtItem }}" {{ (string)$rt === (string)$rtItem ? 'selected' : '' }}>RT {{ sprintf('%02d', $rtItem) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex items-center justify-between pt-1 border-t border-gray-100 text-xs">
                <span class="text-gray-500">
                    Menampilkan <strong>{{ $daftarTagihan->total() }}</strong> baris data tagihan.
                </span>
                <div class="flex items-center gap-2">
                    @if($cari || $periode || $rt || $statusBayar)
                        <a href="{{ route('admin.tagihan-listrik.index', ['tab' => $tab]) }}"
                           class="text-xs text-rose-600 hover:text-rose-700 font-semibold hover:underline">
                            Reset Filter
                        </a>
                    @endif
                    <button type="submit"
                            class="px-3.5 py-1.5 text-xs font-semibold text-white bg-amber-500 hover:bg-amber-600 rounded-lg shadow-xs transition">
                        Terapkan
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- TAMPILAN DESKTOP: TABEL LENGKAP (hidden md:block) -->
    <div class="hidden md:block bg-white rounded-xl border border-gray-200/90 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-600 font-bold uppercase tracking-wider text-[11px]">
                        @if($tab === 'belum_bayar')
                            <th class="py-3 px-3 text-center w-10">Pilih</th>
                        @else
                            <th class="py-3 px-3.5 text-center w-12">No</th>
                        @endif
                        <th class="py-3 px-3.5">Periode & RT</th>
                        <th class="py-3 px-3.5">Nama Warga vs Rekening PLN</th>
                        <th class="py-3 px-3.5">No. Struk PLN</th>
                        <th class="py-3 px-3.5 text-right">Tagihan Awal</th>
                        <th class="py-3 px-3.5 text-right">Kas Kartar</th>
                        <th class="py-3 px-3.5 text-right">Total Wajib</th>
                        <th class="py-3 px-3.5 text-center">Status Selisih</th>
                        <th class="py-3 px-3.5 text-center">Status Lunas</th>
                        <th class="py-3 px-3.5 text-center">Struk Gabungan</th>
                        <th class="py-3 px-3.5 text-center w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($daftarTagihan as $idx => $tagihan)
                        @php
                            $isGabungan = !empty($tagihan->kode_gabungan);
                        @endphp
                        <tr class="hover:bg-amber-50/40 transition {{ $isGabungan ? 'bg-amber-50/20' : '' }}">
                            @if($tab === 'belum_bayar')
                                <td class="py-3 px-3 text-center">
                                    <input type="checkbox" :value="{{ $tagihan->id }}" x-model="selectedIds"
                                           class="rounded border-gray-300 text-amber-500 focus:ring-amber-500 w-4 h-4">
                                </td>
                            @else
                                <td class="py-3 px-3.5 text-center text-gray-500 font-medium">
                                    {{ $daftarTagihan->firstItem() + $idx }}
                                </td>
                            @endif

                            <td class="py-3 px-3.5">
                                <span class="font-bold text-gray-900 block">{{ $tagihan->periode }}</span>
                                <span class="inline-block mt-0.5 px-1.5 py-0.5 text-[10px] font-semibold bg-gray-100 text-gray-700 rounded">
                                    RT {{ sprintf('%02d', $tagihan->rt) }}
                                </span>
                            </td>

                            <td class="py-3 px-3.5">
                                <div class="font-bold text-gray-900 text-sm">
                                    {{ $tagihan->nama_warga ?: $tagihan->nama_pelanggan }}
                                </div>
                                <div class="text-gray-500 text-[11px] font-mono flex items-center gap-1 mt-0.5">
                                    <span class="text-gray-400">PLN:</span>
                                    <span class="font-semibold text-gray-700">{{ $tagihan->nama_pelanggan }}</span>
                                </div>
                            </td>

                            <td class="py-3 px-3.5 font-mono text-gray-700">
                                {{ $tagihan->nomor_struk }}
                            </td>

                            <td class="py-3 px-3.5 text-right font-medium text-gray-700">
                                Rp {{ number_format($tagihan->tagihan_awal, 0, ',', '.') }}
                            </td>

                            <td class="py-3 px-3.5 text-right text-amber-700 font-semibold">
                                +Rp {{ number_format($tagihan->kas_karang_taruna ?: 3000, 0, ',', '.') }}
                            </td>

                            <td class="py-3 px-3.5 text-right font-extrabold text-gray-900">
                                Rp {{ number_format($tagihan->total_tagihan, 0, ',', '.') }}
                            </td>

                            <td class="py-3 px-3.5 text-center">
                                @if($tagihan->isLebih())
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[11px] font-bold rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        +Rp {{ number_format($tagihan->selisih_bayar, 0, ',', '.') }} (Titipan)
                                    </span>
                                @elseif($tagihan->isKurang())
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[11px] font-bold rounded-md bg-rose-50 text-rose-700 border border-rose-200">
                                        Rp {{ number_format($tagihan->selisih_bayar, 0, ',', '.') }} (Kurang)
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 text-[11px] font-semibold rounded-md bg-gray-100 text-gray-600">
                                        Pas (Rp 0)
                                    </span>
                                @endif
                            </td>

                            <td class="py-3 px-3.5 text-center">
                                @if($tagihan->is_lunas)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-bold rounded-full bg-emerald-100 text-emerald-800">
                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                        Lunas
                                    </span>
                                @else
                                    <button type="button"
                                            @click="bukaModalBayarTunggal({{ $tagihan->toJson() }})"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-bold rounded-full bg-rose-100 text-rose-800 hover:bg-amber-100 hover:text-amber-800 transition">
                                        Belum Bayar
                                    </button>
                                @endif
                            </td>

                            <td class="py-3 px-3.5 text-center">
                                @if($isGabungan)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-semibold bg-amber-100 text-amber-900 border border-amber-300 shadow-2xs"
                                          title="Struk fisik digabung dengan kode: {{ $tagihan->kode_gabungan }}">
                                        <svg class="w-3.5 h-3.5 text-amber-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                        </svg>
                                        {{ $tagihan->kode_gabungan }}
                                    </span>
                                @else
                                    <span class="text-gray-400 text-[11px]">Tunggal</span>
                                @endif
                            </td>

                            <td class="py-3 px-3.5 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    @if(!$tagihan->is_lunas)
                                        <button type="button" @click="bukaModalBayarTunggal({{ $tagihan->toJson() }})"
                                                class="px-2 py-1 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-md transition shadow-2xs"
                                                title="Proses bayar kasir">
                                            Bayar
                                        </button>
                                    @endif

                                    <a href="{{ route('admin.tagihan-listrik.edit', $tagihan) }}"
                                       class="p-1.5 text-gray-600 hover:text-amber-600 hover:bg-amber-50 rounded-md transition"
                                       title="Ubah data">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>

                                    <form method="POST" action="{{ route('admin.tagihan-listrik.destroy', $tagihan) }}"
                                          onsubmit="return confirm('Hapus data tagihan atas nama {{ $tagihan->nama_pelanggan }}?');"
                                          class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="p-1.5 text-gray-400 hover:text-rose-600 hover:bg-rose-50 rounded-md transition"
                                                title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="py-12 text-center">
                                <div class="max-w-sm mx-auto text-center space-y-2">
                                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-amber-50 text-amber-500 mb-2">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-sm font-bold text-gray-800">Tidak Ada Data Tagihan</h3>
                                    <p class="text-xs text-gray-500">
                                        Tidak ditemukan catatan tagihan listrik yang sesuai dengan tab atau filter saat ini.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($daftarTagihan->hasPages())
            <div class="p-3.5 border-t border-gray-100 bg-gray-50">
                {{ $daftarTagihan->links() }}
            </div>
        @endif
    </div>

    <!-- TAMPILAN MOBILE: KARTU RAMAH HP (block md:hidden) -->
    <div class="block md:hidden space-y-3">
        @forelse($daftarTagihan as $tagihan)
            @php
                $isGabungan = !empty($tagihan->kode_gabungan);
            @endphp
            <div class="bg-white rounded-xl border border-gray-200/90 p-4 shadow-xs space-y-3 {{ $isGabungan ? 'border-l-4 border-l-amber-500' : '' }}">
                <div class="flex items-start justify-between gap-2 pb-2.5 border-b border-gray-100">
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="font-bold text-gray-900 text-sm leading-tight">{{ $tagihan->nama_warga ?: $tagihan->nama_pelanggan }}</h3>
                            <span class="px-1.5 py-0.5 text-[10px] font-semibold bg-gray-100 text-gray-700 rounded">
                                RT {{ sprintf('%02d', $tagihan->rt) }}
                            </span>
                        </div>
                        <div class="text-[11px] text-gray-500 font-mono mt-0.5">
                            PLN: {{ $tagihan->nama_pelanggan }} &bull; Struk: {{ $tagihan->nomor_struk }}
                        </div>
                    </div>

                    @if($tagihan->is_lunas)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-bold rounded-full bg-emerald-100 text-emerald-800">
                            Lunas
                        </span>
                    @else
                        <button type="button" @click="bukaModalBayarTunggal({{ $tagihan->toJson() }})"
                                class="inline-flex items-center gap-1 px-2 py-1 text-[11px] font-bold rounded-lg bg-emerald-600 text-white shadow-xs">
                            Bayar
                        </button>
                    @endif
                </div>

                <div class="grid grid-cols-2 gap-2 text-xs">
                    <div class="bg-gray-50 rounded-lg p-2">
                        <span class="text-[10px] text-gray-500 block">Tagihan Awal</span>
                        <span class="font-semibold text-gray-800">Rp {{ number_format($tagihan->tagihan_awal, 0, ',', '.') }}</span>
                    </div>

                    <div class="bg-amber-50/60 rounded-lg p-2">
                        <span class="text-[10px] text-amber-700 block">Kas Kartar</span>
                        <span class="font-semibold text-amber-800">+Rp {{ number_format($tagihan->kas_karang_taruna ?: 3000, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="flex items-center justify-between text-xs pt-1">
                    <div>
                        <span class="text-[10px] text-gray-500 block">Total Wajib Bayar</span>
                        <span class="text-sm font-extrabold text-gray-900">
                            Rp {{ number_format($tagihan->total_tagihan, 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="text-right">
                        <span class="text-[10px] text-gray-500 block">Status Selisih</span>
                        @if($tagihan->isLebih())
                            <span class="font-bold text-emerald-700 text-xs">
                                +Rp {{ number_format($tagihan->selisih_bayar, 0, ',', '.') }} (Titipan)
                            </span>
                        @elseif($tagihan->isKurang())
                            <span class="font-bold text-rose-700 text-xs">
                                Rp {{ number_format($tagihan->selisih_bayar, 0, ',', '.') }} (Kurang)
                            </span>
                        @else
                            <span class="font-semibold text-gray-600 text-xs">Pas (Rp 0)</span>
                        @endif
                    </div>
                </div>

                @if($isGabungan)
                    <div class="bg-amber-50 border border-amber-200 rounded-lg p-2 flex items-center justify-between text-[11px]">
                        <span class="font-semibold text-amber-900">Struk Gabungan</span>
                        <span class="font-mono font-bold text-amber-900">{{ $tagihan->kode_gabungan }}</span>
                    </div>
                @endif

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-gray-100">
                    <a href="{{ route('admin.tagihan-listrik.edit', $tagihan) }}"
                       class="px-3 py-1.5 text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition min-h-[44px] flex items-center">
                        Edit
                    </a>
                    <form method="POST" action="{{ route('admin.tagihan-listrik.destroy', $tagihan) }}"
                          onsubmit="return confirm('Hapus catatan tagihan atas nama {{ $tagihan->nama_pelanggan }}?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="px-3 py-1.5 text-xs font-semibold text-rose-700 bg-rose-50 hover:bg-rose-100 rounded-lg transition min-h-[44px] flex items-center">
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl border border-gray-200/90 p-8 text-center shadow-xs">
                <p class="text-xs text-gray-500">Belum ada data tagihan.</p>
            </div>
        @endforelse
    </div>

    <!-- MODAL KASIR PEMBAYARAN INTERAKTIF -->
    <div x-show="modalBayar" x-cloak
         class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
        <div @click.away="modalBayar = false"
             class="bg-white rounded-2xl max-w-lg w-full p-5 sm:p-6 shadow-xl border border-gray-200 space-y-4">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <div class="flex items-center gap-2">
                    <span class="p-1.5 rounded-lg bg-emerald-100 text-emerald-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </span>
                    <h3 class="text-base font-bold text-gray-900">Kasir Pembayaran Listrik</h3>
                </div>
                <button type="button" @click="modalBayar = false" class="text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
            </div>

            <form method="POST" action="{{ route('admin.tagihan-listrik.proses-bayar') }}" class="space-y-4 text-xs">
                @csrf

                <!-- Hidden ids input -->
                <template x-for="id in bayarItem.ids" :key="id">
                    <input type="hidden" name="tagihan_ids[]" :value="id">
                </template>

                <div class="bg-gray-50 border border-gray-200 rounded-xl p-3.5 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500 font-semibold">Nama Warga / Penerima:</span>
                        <span class="font-bold text-gray-900 text-sm" x-text="bayarItem.nama_warga"></span>
                    </div>
                    <div class="flex items-center justify-between pt-1 border-t border-gray-200">
                        <span class="text-gray-500 font-semibold">Atas Nama Rekening PLN:</span>
                        <span class="font-mono text-gray-700" x-text="bayarItem.nama_pelanggan"></span>
                    </div>
                    <div class="flex items-center justify-between pt-1 border-t border-gray-200">
                        <span class="text-gray-900 font-bold">Total Wajib Bayar (PLN + Kas Kartar):</span>
                        <span class="text-base font-extrabold text-amber-600" x-text="'Rp ' + formatRupiah(bayarItem.total_tagihan)"></span>
                    </div>
                </div>

                <!-- Input Uang Diterima Fisik -->
                <div>
                    <label class="block font-bold text-gray-700 uppercase mb-1">
                        Uang Tunai Diterima Dari Warga (Rp) *
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-xs font-semibold text-gray-500">Rp</span>
                        <input type="number" name="uang_dibayar" x-model.number="bayarItem.uang_dibayar" required min="0" step="500"
                               class="w-full pl-9 rounded-lg border-gray-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 py-2.5 text-base font-bold text-gray-900">
                    </div>
                </div>

                <!-- Kalkulasi Real-time Selisih Bayar -->
                <div class="p-3 rounded-xl border transition"
                     :class="bayarItem.selisih > 0 ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : (bayarItem.selisih < 0 ? 'bg-rose-50 border-rose-200 text-rose-800' : 'bg-gray-50 border-gray-200 text-gray-700')">
                    <div class="flex items-center justify-between font-bold">
                        <span>Hasil Perhitungan Selisih:</span>
                        <span class="text-sm" x-text="'Rp ' + formatRupiah(bayarItem.selisih)"></span>
                    </div>
                    <div class="text-[11px] mt-1">
                        <template x-if="bayarItem.selisih > 0">
                            <span>Sisa uang kembalian sebesar +Rp <strong x-text="formatRupiah(bayarItem.selisih)"></strong> ditinggal warga sebagai titipan.</span>
                        </template>
                        <template x-if="bayarItem.selisih < 0">
                            <span>Warga kurang bayar sebesar Rp <strong x-text="formatRupiah(Math.abs(bayarItem.selisih))"></strong> (ninggal).</span>
                        </template>
                        <template x-if="bayarItem.selisih === 0">
                            <span>Uang yang diserahkan tepat sesuai total tagihan.</span>
                        </template>
                    </div>
                </div>

                <!-- Kode Struk Gabungan (Jika Multi Item) -->
                <div x-show="bayarItem.ids.length > 1">
                    <label class="block font-bold text-amber-900 uppercase mb-1">Kode Struk Fisik Bersama</label>
                    <input type="text" name="kode_gabungan" x-model="bayarItem.kode_gabungan"
                           class="w-full rounded-lg border-amber-300 bg-amber-50/50 py-2 font-mono font-bold text-amber-900">
                    <span class="text-[10px] text-amber-700 mt-0.5 block">Kode unik untuk menyatukan struk pembayaran beberapa warga ini.</span>
                </div>

                <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2">
                    <button type="button" @click="modalBayar = false"
                            class="px-4 py-2 text-xs font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition min-h-[44px]">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2 text-xs font-bold text-white bg-[#CEA042] hover:bg-[#BA8832] rounded-xl shadow-xs transition min-h-[44px] flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Konfirmasi Pembayaran Lunas
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
    // Menyimpan daftar tagihan belum bayar untuk kalkulasi pembayaran gabungan
    window.dataBelumBayar = @json($daftarTagihan->where('is_lunas', false)->values());
</script>
@endsection
