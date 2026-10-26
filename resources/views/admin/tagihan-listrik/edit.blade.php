@extends('layouts.admin')

@section('title', 'Ubah Tagihan Listrik - ' . ($tagihanListrik->nama_warga ?: $tagihanListrik->nama_pelanggan))

@section('content')
<div class="max-w-3xl mx-auto space-y-5"
     x-data="{
        periode: '{{ old('periode', $tagihanListrik->periode) }}',
        rt: '{{ old('rt', $tagihanListrik->rt) }}',
        wargaId: '{{ old('warga_id', $tagihanListrik->warga_id ?? '') }}',
        namaWarga: '{{ old('nama_warga', $tagihanListrik->nama_warga ?? '') }}',
        namaPelanggan: '{{ old('nama_pelanggan', $tagihanListrik->nama_pelanggan) }}',
        nomorStruk: '{{ old('nomor_struk', $tagihanListrik->nomor_struk) }}',
        tagihanAwal: {{ old('tagihan_awal', $tagihanListrik->tagihan_awal) }},
        kasKarangTaruna: {{ $tagihanListrik->kas_karang_taruna ?: 3000 }},
        uangDibayar: {{ old('uang_dibayar', $uangDibayar) }},
        isLunas: {{ old('is_lunas', $tagihanListrik->is_lunas) ? 'true' : 'false' }},
        kodeGabungan: '{{ old('kode_gabungan', $tagihanListrik->kode_gabungan ?? '') }}',

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
        }
     }">

    <!-- Header & Breadcrumb -->
    <div>
        <a href="{{ route('admin.tagihan-listrik.index') }}"
           class="inline-flex items-center gap-1.5 text-xs font-semibold text-amber-600 hover:text-amber-700 transition mb-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke Rekap Tagihan
        </a>
        <h1 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">
            Ubah Data Tagihan Listrik
        </h1>
        <p class="text-xs text-gray-500 mt-0.5">
            Perbarui data tagihan atas nama <strong class="text-gray-800">{{ $tagihanListrik->nama_warga ?: $tagihanListrik->nama_pelanggan }}</strong>.
        </p>
    </div>

    <!-- Info Anggota Struk Gabungan Lain Jika Ada -->
    @if(count($anggotaGabungan) > 0)
        <div class="bg-amber-50/80 border border-amber-200/90 rounded-xl p-4 shadow-xs">
            <div class="flex items-center gap-2 mb-2">
                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                </svg>
                <h3 class="text-xs font-bold text-amber-900 uppercase tracking-wide">
                    Tergabung dalam struk fisik bersama (Kode: {{ $tagihanListrik->kode_gabungan }}):
                </h3>
            </div>
            <div class="divide-y divide-amber-200/60 text-xs">
                @foreach($anggotaGabungan as $ag)
                    <div class="py-1.5 flex items-center justify-between text-gray-800">
                        <div>
                            <span class="font-bold">{{ $ag->nama_warga ?: $ag->nama_pelanggan }}</span>
                            <span class="text-gray-500 text-[11px] font-mono">(PLN: {{ $ag->nama_pelanggan }}, RT {{ $ag->rt }})</span>
                        </div>
                        <div class="font-semibold text-gray-900">
                            Total: Rp {{ number_format($ag->total_tagihan, 0, ',', '.') }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Form Edit -->
    <div class="bg-white rounded-xl border border-gray-200/90 p-4 sm:p-6 shadow-xs">
        <form method="POST" action="{{ route('admin.tagihan-listrik.update', $tagihanListrik) }}">
            @csrf
            @method('PUT')

            <!-- Metadata Periode & RT -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                <div>
                    <label for="periode" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                        Periode Tagihan <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="periode" id="periode" x-model="periode" required
                           class="w-full text-base sm:text-xs rounded-lg border-gray-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 py-2.5 px-3 shadow-xs min-h-[44px]">
                </div>

                <div>
                    <label for="rt" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                        Rukun Tetangga (RT) <span class="text-rose-500">*</span>
                    </label>
                    <select name="rt" id="rt" x-model="rt" required
                            class="w-full text-base sm:text-xs rounded-lg border-gray-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 py-2.5 px-3 shadow-xs min-h-[44px]">
                        @for($i = 1; $i <= 10; $i++)
                            <option value="{{ $i }}" {{ $tagihanListrik->rt == $i ? 'selected' : '' }}>
                                RT {{ sprintf('%02d', $i) }}
                            </option>
                        @endfor
                    </select>
                </div>
            </div>

            <!-- Nama Warga & Nama PLN -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                <div>
                    <label for="nama_warga" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                        Nama Warga (Penghuni Lingkungan)
                    </label>
                    <input type="text" name="nama_warga" id="nama_warga"
                           x-model="namaWarga"
                           placeholder="Contoh: Budi (Pak RT)"
                           class="w-full text-base sm:text-xs rounded-lg border-gray-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 py-2.5 px-3 shadow-xs min-h-[44px]">
                    <span class="text-[10px] text-gray-400 mt-0.5 block">Nama yang dikenal pengurus</span>
                </div>

                <div>
                    <label for="nama_pelanggan" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                        Atas Nama Rekening PLN (Nama di Struk) <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="nama_pelanggan" id="nama_pelanggan"
                           x-model="namaPelanggan" required
                           class="w-full text-base sm:text-xs rounded-lg border-gray-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 py-2.5 px-3 shadow-xs min-h-[44px] font-mono">
                    <span class="text-[10px] text-gray-400 mt-0.5 block">Sesuai nama saat pendaftaran listrik di PLN</span>
                </div>
            </div>

            <!-- Nomor Struk PLN -->
            <div class="mb-5">
                <label for="nomor_struk" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                    Nomor Struk / IDPEL Pembayaran <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="nomor_struk" id="nomor_struk"
                       x-model="nomorStruk" required
                       class="w-full text-base sm:text-xs rounded-lg border-gray-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 py-2.5 px-3 shadow-xs min-h-[44px] font-mono">
            </div>

            <!-- Kode Struk Gabungan -->
            <div class="mb-5 p-3.5 bg-gray-50 border border-gray-200 rounded-xl space-y-2">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div>
                        <span class="text-xs font-bold text-gray-800 block">Kode Struk Gabungan (Opsional)</span>
                        <p class="text-[11px] text-gray-500">
                            Kosongkan jika tagihan ini dibayarkan secara mandiri tanpa gabungan.
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="text" name="kode_gabungan" x-model="kodeGabungan"
                               placeholder="Misal: GAB-RT01"
                               class="text-xs font-mono font-bold rounded-lg border-gray-300 bg-white py-1.5 px-3 uppercase text-gray-800 focus:ring-1 focus:ring-amber-500">
                        <button type="button" @click="generateKode()"
                                class="px-2.5 py-1.5 text-xs font-semibold bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg transition">
                            Buat Kode
                        </button>
                    </div>
                </div>
            </div>

            <!-- Blok Kalkulasi Nominal & Kas Karang Taruna -->
            <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 sm:p-5 space-y-4 mb-6">
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
                                   x-model.number="tagihanAwal" required
                                   min="0" step="500"
                                   class="w-full pl-9 text-base sm:text-xs rounded-lg border-gray-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 py-2.5 px-3 shadow-xs min-h-[44px]">
                        </div>
                    </div>

                    <!-- Kas Karang Taruna Tetap Rp 3.000 -->
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
                    </div>

                    <!-- Total Tagihan Otomatis -->
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
                    </div>
                </div>

                <!-- Input Uang Diterima & Selisih Bayar -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-3 border-t border-gray-200">
                    <div>
                        <label for="uang_dibayar" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                            Uang Tunai Diterima (Rp)
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-xs text-gray-500 font-semibold pointer-events-none">Rp</span>
                            <input type="number" name="uang_dibayar" id="uang_dibayar"
                                   x-model.number="uangDibayar" min="0" step="500"
                                   class="w-full pl-9 text-base sm:text-xs rounded-lg border-gray-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 py-2.5 px-3 shadow-xs min-h-[44px]">
                        </div>
                    </div>

                    <!-- Selisih Bayar Reaktif -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                            Selisih Bayar (Real-time)
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-xs font-bold pointer-events-none"
                                  :class="selisihBayar > 0 ? 'text-emerald-700' : (selisihBayar < 0 ? 'text-rose-700' : 'text-gray-600')">Rp</span>
                            <input type="text" readonly
                                   :value="formatRupiah(selisihBayar)"
                                   :class="selisihBayar > 0 ? 'bg-emerald-50 text-emerald-800 border-emerald-300 font-bold' : (selisihBayar < 0 ? 'bg-rose-50 text-rose-800 border-rose-300 font-bold' : 'bg-white text-gray-700 border-gray-300')"
                                   class="w-full pl-9 text-base sm:text-sm rounded-lg py-2.5 px-3 shadow-xs min-h-[44px] cursor-not-allowed">
                        </div>

                        <div class="mt-1 text-xs">
                            <template x-if="selisihBayar > 0">
                                <span class="font-bold text-emerald-700">
                                    Lebih / Titipan: +Rp <span x-text="formatRupiah(selisihBayar)"></span>
                                </span>
                            </template>
                            <template x-if="selisihBayar < 0">
                                <span class="font-bold text-rose-700">
                                    Kurang / Ninggal: Rp <span x-text="formatRupiah(selisihBayar)"></span>
                                </span>
                            </template>
                            <template x-if="selisihBayar === 0">
                                <span class="text-gray-600 font-semibold">
                                    Pembayaran Pas (Rp 0).
                                </span>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Checkbox Lunas -->
                <div class="pt-2">
                    <label class="relative flex items-start gap-2.5 cursor-pointer">
                        <input type="checkbox" name="is_lunas" value="1" x-model="isLunas"
                               class="rounded border-gray-300 text-amber-500 focus:ring-amber-500 w-4 h-4 mt-0.5">
                        <div>
                            <span class="text-xs font-bold text-gray-800">Tandai Tagihan Sudah Lunas (Pembayaran Selesai)</span>
                            <p class="text-[11px] text-gray-500">
                                Centang jika pembayaran warga ini telah diselesaikan.
                            </p>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Tombol Simpan Perubahan -->
            <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-2.5">
                <a href="{{ route('admin.tagihan-listrik.index') }}"
                   class="px-4 py-2.5 text-xs font-bold text-gray-600 hover:text-gray-900 bg-gray-100 hover:bg-gray-200 rounded-lg transition min-h-[44px] flex items-center">
                    Batal
                </a>

                <button type="submit"
                        class="px-5 py-2.5 text-xs font-bold text-white bg-amber-500 hover:bg-amber-600 focus-visible:ring-2 focus-visible:ring-amber-500 rounded-lg shadow-xs transition min-h-[44px] flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
