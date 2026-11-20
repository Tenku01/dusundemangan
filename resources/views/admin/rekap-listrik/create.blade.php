@extends('layouts.admin')

@section('title', 'Catat Rekap Listrik Baru')

@section('content')
<div class="max-w-2xl mx-auto space-y-4"
     x-data="{
        wargaTerpilih: '',
        namaWarga: '',
        namaPelanggan: '',
        tagihanAwalDisplay: '',
        kasKartar: 3000,
        get tagihanAwal() {
            return parseInt(this.tagihanAwalDisplay.toString().replace(/[^0-9]/g, '')) || 0;
        },
        set tagihanAwal(val) {
            this.tagihanAwalDisplay = val ? this.formatRupiah(val) : '';
        },
        onInputTagihanAwal(e) {
            let clean = e.target.value.replace(/[^0-9]/g, '');
            this.tagihanAwalDisplay = clean ? this.formatRupiah(parseInt(clean)) : '';
        },
        get totalTagihan() {
            let awal = this.tagihanAwal;
            return awal > 0 ? (awal + this.kasKartar) : 0;
        },
        pilihWarga(e) {
            let selected = e.target.selectedOptions[0];
            if (selected && selected.value) {
                this.wargaTerpilih = selected.value;
                this.namaWarga = selected.dataset.nama || '';
                this.namaPelanggan = selected.dataset.pln || '';
            } else {
                this.wargaTerpilih = '';
            }
        },
        formatRupiah(num) {
            return new Intl.NumberFormat('id-ID').format(num || 0);
        }
     }">

    <div class="flex items-center justify-between">
        <a href="{{ route('admin.rekap-listrik.index', ['periode' => $periode, 'rt' => $rt]) }}"
           class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-600 hover:text-emas-800 min-h-[44px] py-1 px-2 -ml-2 rounded-lg transition">
            <svg class="w-4 h-4" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Kembali ke Rekap RT {{ $rt }}</span>
        </a>
    </div>

    <div class="card-surface p-5 sm:p-6 space-y-4">
        <div class="border-b border-gray-100 pb-3">
            <span class="inline-block px-2.5 py-0.5 text-[11px] font-bold rounded-md bg-emas-100 text-emas-900 border border-emas-200 uppercase">
                RT {{ $rt }} | Periode {{ $periode }}
            </span>
            <h1 class="text-base sm:text-lg font-bold text-gray-900 mt-1.5">
                Catat Tagihan Struk Baru
            </h1>
            <p class="text-xs text-gray-600">Pilih warga atau ketik nama warga dan nomor struk fisik</p>
        </div>

        <form method="POST" action="{{ route('admin.rekap-listrik.store') }}" class="space-y-4 text-xs">
            @csrf
            <input type="hidden" name="periode" value="{{ $periode }}">
            <input type="hidden" name="rt" value="{{ $rt }}">

            <div>
                <label for="create-pilih-warga" class="block text-xs font-bold text-gray-800 mb-1">Pilih Master Warga RT {{ $rt }}</label>
                <select id="create-pilih-warga" @change="pilihWarga($event)" class="select-field">
                    <option value="">-- Pilih Warga atau Ketik Manual --</option>
                    @foreach($wargaList as $w)
                        <option value="{{ $w->id }}"
                                data-nama="{{ $w->nama }}"
                                data-pln="{{ $w->nama_pelanggan_pln }}">
                            {{ $w->nama }} (PLN: {{ $w->nama_pelanggan_pln }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label for="create-nomor-struk" class="block text-xs font-bold text-gray-800 mb-1">Nomor Struk [ # ] <span class="text-rose-600">*</span></label>
                    <input type="text" id="create-nomor-struk" name="nomor_struk" required placeholder="Contoh: 2 atau 28"
                           class="input-field font-mono font-bold tabular-money">
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label for="create-tagihan-awal" class="block text-xs font-bold text-gray-800">Tagihan Awal PLN (Rp) <span class="text-rose-600">*</span></label>
                        <span class="text-[11px] text-emas-800 font-semibold tabular-money" x-show="tagihanAwal > 0" x-text="'Terbaca: Rp ' + formatRupiah(tagihanAwal)"></span>
                    </div>
                    <div class="input-addon-group">
                        <span class="flex items-center justify-center bg-gray-100 px-3.5 text-gray-600 font-bold text-xs select-none border-r border-gray-200 shrink-0">
                            Rp
                        </span>
                        <input type="text"
                               id="create-tagihan-awal"
                               inputmode="numeric"
                               x-model="tagihanAwalDisplay"
                               @input="onInputTagihanAwal($event)"
                               placeholder="Contoh: 65.000"
                               required
                               class="w-full text-base sm:text-sm font-bold text-gray-900 border-0 focus:ring-0 focus:outline-none py-2.5 px-3 bg-transparent tabular-money">
                    </div>
                    <input type="hidden" name="tagihan_awal" :value="tagihanAwal">
                </div>
            </div>

            <div>
                <label for="create-nama-warga" class="block text-xs font-bold text-gray-800 mb-1">Nama Warga (Penghuni) <span class="text-rose-600">*</span></label>
                <input type="text" id="create-nama-warga" name="nama_warga" x-model="namaWarga" required placeholder="Contoh: Sugiyanto (P. Tebek)"
                       class="input-field font-semibold">
            </div>

            <div>
                <label for="create-nama-pelanggan" class="block text-xs font-bold text-gray-800 mb-1">Atas Nama Rekening PLN <span class="text-rose-600">*</span></label>
                <input type="text" id="create-nama-pelanggan" name="nama_pelanggan" x-model="namaPelanggan" required placeholder="Contoh: SUGIYANTO"
                       class="input-field font-mono">
            </div>

            <div class="bg-emas-50 rounded-xl p-3.5 border border-emas-200 space-y-1.5">
                <div class="flex items-center justify-between text-xs">
                    <span class="text-gray-700">Alokasi Kas Karang Taruna:</span>
                    <span class="font-bold text-emas-800 tabular-money">+Rp 3.000</span>
                </div>
                <div class="pt-1.5 border-t border-emas-200 flex items-center justify-between text-xs font-bold">
                    <span class="text-emas-950">Total Tagihan:</span>
                    <span class="text-emerald-700 text-sm font-extrabold tabular-money" x-text="'Rp ' + formatRupiah(totalTagihan)">Rp 0</span>
                </div>
            </div>

            <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2.5">
                <a href="{{ route('admin.rekap-listrik.index', ['periode' => $periode, 'rt' => $rt]) }}"
                   class="btn-subtle px-4 text-xs">
                    Batal
                </a>
                <button type="submit" class="btn-primary px-5 text-xs">
                    Simpan Rekap Struk
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
