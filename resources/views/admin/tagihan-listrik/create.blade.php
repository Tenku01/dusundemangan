@extends('layouts.admin')

@section('title', 'Rekap Tagihan Listrik Warga')

@section('content')
<div class="max-w-4xl mx-auto space-y-5"
     x-data="{
        modeInput: 'single', // 'single' atau 'multi'
        periode: '{{ old('periode', $periodeDefault) }}',
        rt: '{{ old('rt', $rtDefault) }}',
        daftarWargaAll: @json($daftarWarga),
        selectedWargaId: '',
        namaWarga: '{{ old('nama_warga', '') }}',
        namaPelangganPln: '{{ old('nama_pelanggan', '') }}',
        nomorStruk: '{{ old('nomor_struk', '') }}',

        tagihanAwal: {{ old('tagihan_awal', 0) }},
        kasKarangTaruna: 3000,
        uangDibayar: {{ old('uang_dibayar', 0) }},
        langsungBayar: false,
        isLunas: {{ old('is_lunas') ? 'true' : 'false' }},
        isGabungan: {{ $kodeGabungan || old('kode_gabungan') ? 'true' : 'false' }},
        kodeGabungan: '{{ old('kode_gabungan', $kodeGabungan ?? '') }}',

        get wargaFilterRt() {
            return this.daftarWargaAll.filter(w => w.rt == this.rt);
        },

        pilihWarga() {
            const w = this.daftarWargaAll.find(item => item.id == this.selectedWargaId);
            if (w) {
                this.namaWarga = w.nama;
                this.namaPelangganPln = w.nama_pelanggan_pln || w.nama;
                if (w.nomor_meter && !this.nomorStruk) {
                    this.nomorStruk = w.nomor_meter;
                }
            }
        },

        get totalTagihan() {
            const val = parseInt(this.tagihanAwal) || 0;
            return val > 0 ? val + this.kasKarangTaruna : 0;
        },
        get selisihBayar() {
            const bayar = parseInt(this.uangDibayar) || 0;
            return this.totalTagihan > 0 ? bayar - this.totalTagihan : 0;
        },

        formatRupiah(angka) {
            return new Intl.NumberFormat('id-ID').format(angka);
        },

        generateKode() {
            if (!this.kodeGabungan) {
                const acak = Math.random().toString(36).substring(2, 8).toUpperCase();
                this.kodeGabungan = 'GAB-' + acak;
            }
        },

        toggleGabungan() {
            this.isGabungan = !this.isGabungan;
            if (this.isGabungan) {
                this.generateKode();
            }
        },

        // Multi items untuk rekap banyak warga sekaligus
        items: [
            {
                warga_id: '',
                nama_warga: '',
                nama_pelanggan: '',
                nomor_struk: '',
                tagihan_awal: 0,
                uang_dibayar: 0,
                is_lunas: false
            }
        ],
        tambahItem() {
            this.items.push({
                warga_id: '',
                nama_warga: '',
                nama_pelanggan: '',
                nomor_struk: '',
                tagihan_awal: 0,
                uang_dibayar: 0,
                is_lunas: false
            });
        },
        hapusItem(index) {
            if (this.items.length > 1) {
                this.items.splice(index, 1);
            }
        },
        pilihWargaItem(index) {
            const wId = this.items[index].warga_id;
            const w = this.daftarWargaAll.find(item => item.id == wId);
            if (w) {
                this.items[index].nama_warga = w.nama;
                this.items[index].nama_pelanggan = w.nama_pelanggan_pln || w.nama;
                if (w.nomor_meter) {
                    this.items[index].nomor_struk = w.nomor_meter;
                }
            }
        }
     }">

    <!-- Breadcrumb & Header -->
    <div class="flex items-center justify-between gap-3">
        <div>
            <a href="{{ route('admin.tagihan-listrik.index') }}"
               class="inline-flex items-center gap-1.5 text-xs font-semibold text-amber-600 hover:text-amber-700 transition mb-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke Rekap Tagihan
            </a>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">
                Proses Rekap Tagihan Listrik
            </h1>
            <p class="text-xs text-gray-500 mt-0.5">
                Tahap 1: Catat tagihan listrik masuk dari PLN per warga. Kas Karang Taruna otomatis dialokasikan Rp 3.000.
            </p>
        </div>

        @if($kodeGabungan)
            <div class="bg-amber-100 text-amber-900 border border-amber-300 rounded-lg px-3 py-1.5 text-xs font-semibold flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                Struk Gabungan: {{ $kodeGabungan }}
            </div>
        @endif
    </div>

    <!-- Info Anggota Struk Gabungan Sebelumnya (Jika Ada) -->
    @if(count($anggotaGabungan) > 0)
        <div class="bg-amber-50/80 border border-amber-200/90 rounded-xl p-4 shadow-xs">
            <div class="flex items-center gap-2 mb-2">
                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                </svg>
                <h3 class="text-xs font-bold text-amber-900 uppercase tracking-wide">
                    Warga yang sudah tercatat dalam struk gabungan ini ({{ $kodeGabungan }}):
                </h3>
            </div>
            <div class="divide-y divide-amber-200/60 text-xs">
                @foreach($anggotaGabungan as $ag)
                    <div class="py-1.5 flex items-center justify-between text-gray-800">
                        <div>
                            <span class="font-bold">{{ $ag->nama_warga ?: $ag->nama_pelanggan }}</span>
                            <span class="text-gray-500 text-[11px] font-mono">(PLN: {{ $ag->nama_pelanggan }}, Struk: {{ $ag->nomor_struk }})</span>
                        </div>
                        <div class="font-semibold text-gray-900">
                            Total: Rp {{ number_format($ag->total_tagihan, 0, ',', '.') }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Formulir Rekap Tagihan -->
    <div class="bg-white rounded-xl border border-gray-200/90 p-4 sm:p-6 shadow-xs">
        <form method="POST" action="{{ route('admin.tagihan-listrik.store') }}" id="formTagihan">
            @csrf

            <!-- Mode Input Toggle -->
            <div class="mb-5 pb-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-gray-700 uppercase tracking-wider">Model Pengisian:</span>
                    <div class="inline-flex rounded-lg border border-gray-200 p-0.5 bg-gray-50 text-xs">
                        <button type="button"
                                @click="modeInput = 'single'"
                                :class="modeInput === 'single' ? 'bg-white font-bold text-amber-600 shadow-xs' : 'text-gray-600 hover:text-gray-900'"
                                class="px-3 py-1.5 rounded-md transition min-h-[36px]">
                            Input Per Warga
                        </button>
                        <button type="button"
                                @click="modeInput = 'multi'; if(!isGabungan) toggleGabungan();"
                                :class="modeInput === 'multi' ? 'bg-white font-bold text-amber-600 shadow-xs' : 'text-gray-600 hover:text-gray-900'"
                                class="px-3 py-1.5 rounded-md transition min-h-[36px]">
                            Input Multi Warga Sekaligus
                        </button>
                    </div>
                </div>

                <div>
                    <button type="button"
                            @click="toggleGabungan()"
                            :class="isGabungan ? 'bg-amber-100 text-amber-900 border-amber-300' : 'bg-gray-100 text-gray-700 border-gray-200'"
                            class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-lg border text-xs font-bold transition min-h-[36px]">
                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                        </svg>
                        <span x-text="isGabungan ? 'Struk Gabungan Aktif' : 'Gabungkan dengan Tagihan Lain'"></span>
                    </button>
                </div>
            </div>

            <!-- Panel Struk Gabungan -->
            <div x-show="isGabungan" x-cloak class="mb-5 p-3.5 bg-amber-50/70 border border-amber-200 rounded-xl space-y-2">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div>
                        <span class="text-xs font-bold text-amber-900 block">Kode Struk Fisik Bersama</span>
                        <p class="text-[11px] text-amber-700">
                            Digunakan untuk menandai beberapa warga yang tagihannya dibayarkan dalam 1 struk fisik bersama.
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="text" name="kode_gabungan" x-model="kodeGabungan"
                               placeholder="Contoh: GAB-RT01"
                               class="text-xs font-mono font-bold rounded-lg border-amber-300 bg-white py-1.5 px-3 uppercase text-amber-900 focus:ring-1 focus:ring-amber-500">
                        <button type="button" @click="kodeGabungan = ''; generateKode()"
                                class="px-2.5 py-1.5 text-xs font-semibold bg-amber-200 hover:bg-amber-300 text-amber-900 rounded-lg transition">
                            Acak
                        </button>
                    </div>
                </div>
            </div>

            <!-- Periode & RT -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                <div>
                    <label for="periode" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                        Periode Tagihan <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="periode" id="periode" x-model="periode" required
                           placeholder="Misal: September 2026"
                           class="w-full text-base sm:text-xs rounded-lg border-gray-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 py-2.5 px-3 shadow-xs min-h-[44px]">
                </div>

                <div>
                    <label for="rt" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                        Rukun Tetangga (RT) <span class="text-rose-500">*</span>
                    </label>
                    <select name="rt" id="rt" x-model="rt" required
                            class="w-full text-base sm:text-xs rounded-lg border-gray-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 py-2.5 px-3 shadow-xs min-h-[44px]">
                        @for($i = 1; $i <= 10; $i++)
                            <option value="{{ $i }}">RT {{ sprintf('%02d', $i) }}</option>
                        @endfor
                    </select>
                </div>
            </div>

            <!-- ================= MODE SINGLE INPUT ================= -->
            <div x-show="modeInput === 'single'" class="space-y-4">
                <!-- Dropdown Pilih Dari Master Warga -->
                <div class="bg-gray-50 border border-gray-200 rounded-xl p-3.5 space-y-3">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-bold text-gray-800 uppercase tracking-wider">
                            Pilih Dari Master Data Warga
                        </label>
                        <a href="{{ route('admin.warga.index') }}" target="_blank" class="text-[11px] font-semibold text-amber-600 hover:underline">
                            + Kelola Master Warga
                        </a>
                    </div>

                    <select x-model="selectedWargaId" @change="pilihWarga()"
                            class="w-full text-xs rounded-lg border-gray-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 py-2.5 px-3">
                        <option value="">Pilih warga terdaftar di RT <span x-text="rt"></span> (Atau ketik manual di bawah)</option>
                        <template x-for="w in wargaFilterRt" :key="w.id">
                            <option :value="w.id" x-text="w.nama + ' — Rekening PLN: ' + (w.nama_pelanggan_pln || w.nama) + (w.nomor_meter ? ' (ID: ' + w.nomor_meter + ')' : '')"></option>
                        </template>
                    </select>
                    <input type="hidden" name="warga_id" :value="selectedWargaId">
                </div>

                <!-- Input Nama Warga & Nama PLN -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="nama_warga" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                            Nama Warga (Penghuni) <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="nama_warga" id="nama_warga"
                               x-model="namaWarga"
                               :required="modeInput === 'single'"
                               placeholder="Contoh: Budi (Pak RT), Mas Joko"
                               class="w-full text-base sm:text-xs rounded-lg border-gray-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 py-2.5 px-3 shadow-xs min-h-[44px]">
                        <span class="text-[10px] text-gray-400 mt-0.5 block">Nama yang dikenal di lingkungan RT</span>
                    </div>

                    <div>
                        <label for="nama_pelanggan" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                            Atas Nama Rekening PLN (Nama di Struk) <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="nama_pelanggan" id="nama_pelanggan"
                               x-model="namaPelangganPln"
                               :required="modeInput === 'single'"
                               placeholder="Contoh: SUWARNO / HJ SITI"
                               class="w-full text-base sm:text-xs rounded-lg border-gray-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 py-2.5 px-3 shadow-xs min-h-[44px] font-mono">
                        <span class="text-[10px] text-gray-400 mt-0.5 block">Sesuai nama saat pendaftaran listrik di PLN</span>
                    </div>
                </div>

                <!-- Nomor Struk PLN -->
                <div>
                    <label for="nomor_struk" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                        Nomor Struk / IDPEL Pembayaran <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="nomor_struk" id="nomor_struk"
                           x-model="nomorStruk"
                           :required="modeInput === 'single'"
                           placeholder="Contoh: STR-9812901 / No. IDPEL PLN"
                           class="w-full text-base sm:text-xs rounded-lg border-gray-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 py-2.5 px-3 shadow-xs min-h-[44px] font-mono">
                </div>

                <!-- Blok Perhitungan Nominal & Kas Karang Taruna -->
                <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 sm:p-5 space-y-4">
                    <h3 class="text-xs font-bold text-gray-700 uppercase tracking-wider border-b border-gray-200 pb-2">
                        Perhitungan Tagihan & Kas Karang Taruna
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <!-- Tagihan Awal Asli PLN -->
                        <div>
                            <label for="tagihan_awal" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                                Tagihan Asli PLN <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-xs text-gray-500 font-semibold pointer-events-none">Rp</span>
                                <input type="number" name="tagihan_awal" id="tagihan_awal"
                                       :required="modeInput === 'single'"
                                       x-model.number="tagihanAwal"
                                       min="0" step="500" placeholder="0"
                                       class="w-full pl-9 text-base sm:text-xs rounded-lg border-gray-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 py-2.5 px-3 shadow-xs min-h-[44px]">
                            </div>
                            <span class="text-[11px] text-gray-500 mt-1 block">Nominal asli tercetak di struk PLN</span>
                        </div>

                        <!-- Kas Karang Taruna (Tetap Rp 3.000) -->
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-xs font-bold text-amber-900 uppercase tracking-wider">
                                    Kas Karang Taruna
                                </label>
                                <span class="text-[10px] font-semibold text-amber-700 bg-amber-100 px-1.5 py-0.5 rounded">Tetap</span>
                            </div>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-xs text-amber-700 font-bold pointer-events-none">Rp</span>
                                <input type="text" readonly value="3.000"
                                       class="w-full pl-9 text-base sm:text-xs rounded-lg border-amber-200 bg-amber-50/50 text-amber-900 font-bold py-2.5 px-3 shadow-xs min-h-[44px] cursor-not-allowed">
                            </div>
                            <span class="text-[11px] text-amber-700 mt-1 block">Khusus kas karang taruna (kas dusun Rp 0)</span>
                        </div>

                        <!-- Total Tagihan Wajib Bayar -->
                        <div>
                            <label class="block text-xs font-bold text-gray-900 uppercase tracking-wider mb-1">
                                Total Tagihan Wajib
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-xs text-gray-900 font-bold pointer-events-none">Rp</span>
                                <input type="text" readonly
                                       :value="formatRupiah(totalTagihan)"
                                       class="w-full pl-9 text-base sm:text-sm font-extrabold text-gray-900 bg-white rounded-lg border-gray-300 py-2.5 px-3 shadow-xs min-h-[44px] cursor-not-allowed">
                            </div>
                            <span class="text-[11px] text-gray-500 mt-1 block">Rumus: Tagihan PLN + Kas Kartar Rp 3.000</span>
                        </div>
                    </div>

                    <!-- Opsi: Apakah Warga Langsung Bayar Saat Rekap? -->
                    <div class="pt-3 border-t border-gray-200">
                        <label class="flex items-center gap-2 cursor-pointer mb-3">
                            <input type="checkbox" x-model="langsungBayar"
                                   class="rounded border-gray-300 text-amber-500 focus:ring-amber-500 w-4 h-4">
                            <span class="text-xs font-bold text-gray-800">
                                Warga langsung membayar sekarang (Catat Kasir Langsung)
                            </span>
                        </label>

                        <div x-show="langsungBayar" x-cloak class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-white p-3.5 rounded-lg border border-gray-200">
                            <div>
                                <label for="uang_dibayar" class="block text-xs font-bold text-gray-700 uppercase mb-1">
                                    Uang Tunai Diterima (Rp)
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-xs text-gray-500">Rp</span>
                                    <input type="number" name="uang_dibayar" id="uang_dibayar"
                                           x-model.number="uangDibayar" min="0" step="500"
                                           class="w-full pl-9 rounded-lg border-gray-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 py-2 text-xs">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">
                                    Hasil Perhitungan Selisih
                                </label>
                                <div class="py-2 px-3 rounded-lg border text-xs font-bold"
                                     :class="selisihBayar > 0 ? 'bg-emerald-50 text-emerald-800 border-emerald-300' : (selisihBayar < 0 ? 'bg-rose-50 text-rose-800 border-rose-300' : 'bg-gray-100 text-gray-700 border-gray-200')">
                                    Rp <span x-text="formatRupiah(selisihBayar)"></span>
                                    <span class="text-[10px] font-normal block" x-text="selisihBayar > 0 ? '(Sisa/Titipan)' : (selisihBayar < 0 ? '(Kurang/Ninggal)' : '(Pas)')"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ================= MODE MULTI INPUT ================= -->
            <div x-show="modeInput === 'multi'" x-cloak class="space-y-4">
                <div class="bg-amber-50/50 border border-amber-200 rounded-xl p-3.5 text-xs text-amber-900 flex items-center justify-between">
                    <div>
                        <span class="font-bold">Mode Rekap Sekaligus Banyak Warga:</span>
                        <p class="text-[11px] text-amber-800 mt-0.5">
                            Struk gabungan: <strong class="font-mono" x-text="kodeGabungan"></strong>.
                        </p>
                    </div>
                    <button type="button" @click="tambahItem()"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-white bg-amber-500 hover:bg-amber-600 rounded-lg shadow-xs transition">
                        + Tambah Baris Warga
                    </button>
                </div>

                <div class="space-y-3">
                    <template x-for="(item, index) in items" :key="index">
                        <div class="bg-gray-50 border border-gray-200 rounded-xl p-3.5 space-y-3">
                            <div class="flex items-center justify-between border-b border-gray-200 pb-2">
                                <span class="text-xs font-bold text-gray-700" x-text="'Warga #' + (index + 1)"></span>
                                <button type="button" @click="hapusItem(index)" x-show="items.length > 1"
                                        class="text-xs text-rose-600 hover:text-rose-800 font-semibold">
                                    Hapus
                                </button>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                                <div>
                                    <label class="block font-semibold text-gray-700 mb-1">Pilih Master Warga</label>
                                    <select :name="'items[' + index + '][warga_id]'" x-model="item.warga_id" @change="pilihWargaItem(index)"
                                            class="w-full text-xs rounded-lg border-gray-300 py-1.5">
                                        <option value="">Pilih / Manual</option>
                                        <template x-for="w in wargaFilterRt" :key="w.id">
                                            <option :value="w.id" x-text="w.nama + ' (PLN: ' + (w.nama_pelanggan_pln || w.nama) + ')'"></option>
                                        </template>
                                    </select>
                                </div>
                                <div>
                                    <label class="block font-semibold text-gray-700 mb-1">Nama Warga (Penghuni) *</label>
                                    <input type="text" :name="'items[' + index + '][nama_warga]'" x-model="item.nama_warga"
                                           placeholder="Nama penghuni"
                                           class="w-full text-xs rounded-lg border-gray-300 py-1.5">
                                </div>
                                <div>
                                    <label class="block font-semibold text-gray-700 mb-1">Atas Nama Rekening PLN *</label>
                                    <input type="text" :name="'items[' + index + '][nama_pelanggan]'" x-model="item.nama_pelanggan"
                                           :required="modeInput === 'multi'"
                                           placeholder="Nama struk PLN"
                                           class="w-full text-xs rounded-lg border-gray-300 py-1.5 font-mono">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                                <div>
                                    <label class="block font-semibold text-gray-700 mb-1">Nomor Struk PLN *</label>
                                    <input type="text" :name="'items[' + index + '][nomor_struk]'" x-model="item.nomor_struk"
                                           :required="modeInput === 'multi'"
                                           placeholder="Nomor struk"
                                           class="w-full text-xs rounded-lg border-gray-300 py-1.5 font-mono">
                                </div>
                                <div>
                                    <label class="block font-semibold text-gray-700 mb-1">Tagihan Awal PLN *</label>
                                    <input type="number" :name="'items[' + index + '][tagihan_awal]'" x-model.number="item.tagihan_awal"
                                           :required="modeInput === 'multi'" min="0"
                                           class="w-full text-xs rounded-lg border-gray-300 py-1.5">
                                    <span class="text-[10px] text-gray-500 block mt-0.5">
                                        Wajib: Rp <span x-text="formatRupiah((parseInt(item.tagihan_awal) || 0) + 3000)"></span> (Termasuk Kas Kartar Rp 3.000)
                                    </span>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <input type="hidden" name="aksi_selanjutnya" id="aksi_selanjutnya" value="selesai">

            <div class="mt-6 pt-4 border-t border-gray-100 flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-2.5">
                <a href="{{ route('admin.tagihan-listrik.index') }}"
                   class="px-4 py-2.5 text-xs font-bold text-gray-600 hover:text-gray-900 bg-gray-100 hover:bg-gray-200 rounded-lg transition text-center min-h-[44px] flex items-center justify-center">
                    Batal
                </a>

                <button type="submit"
                        x-show="modeInput === 'single'"
                        onclick="document.getElementById('aksi_selanjutnya').value = 'lanjut_gabungan'"
                        class="px-4 py-2.5 text-xs font-bold text-amber-900 bg-amber-100 hover:bg-amber-200 border border-amber-300 rounded-lg shadow-xs transition min-h-[44px] flex items-center justify-center">
                    Simpan & Rekap Warga Lain (Struk Gabungan)
                </button>

                <button type="submit"
                        onclick="document.getElementById('aksi_selanjutnya').value = 'selesai'"
                        class="px-5 py-2.5 text-xs font-bold text-white bg-[#CEA042] hover:bg-[#BA8832] focus-visible:ring-2 focus-visible:ring-[#CEA042] rounded-xl shadow-xs transition min-h-[44px] flex items-center justify-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Data Rekap
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
