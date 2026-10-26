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
           class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-600 hover:text-gray-900 min-h-[44px]">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Rekap RT {{ $rt }}
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200/90 p-5 sm:p-6 shadow-xs space-y-4">
        <div class="border-b border-gray-100 pb-3">
            <span class="inline-block px-2 py-0.5 text-[10px] font-bold rounded-md bg-amber-100 text-amber-900 uppercase">
                RT {{ $rt }} | Periode {{ $periode }}
            </span>
            <h1 class="text-base sm:text-lg font-bold text-gray-900 mt-1">
                Catat Tagihan Struk Baru
            </h1>
            <p class="text-xs text-gray-500">Pilih warga atau ketik nama warga dan nomor struk fisik</p>
        </div>

        <form method="POST" action="{{ route('admin.rekap-listrik.store') }}" class="space-y-4 text-xs">
            @csrf
            <input type="hidden" name="periode" value="{{ $periode }}">
            <input type="hidden" name="rt" value="{{ $rt }}">

            <!-- Pilih Warga RT Aktif -->
            <div>
                <label class="block font-bold text-gray-700 uppercase mb-1">Pilih Master Warga RT {{ $rt }}</label>
                <select @change="pilihWarga($event)" class="w-full text-base sm:text-xs rounded-xl border-gray-300 focus:border-amber-500 py-2.5 min-h-[46px]">
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
                    <label class="block font-bold text-gray-700 uppercase mb-1">Nomor Struk [ # ] *</label>
                    <input type="text" name="nomor_struk" required placeholder="Contoh: 2 atau 28"
                           class="w-full text-base sm:text-xs rounded-xl border-gray-300 focus:border-amber-500 font-mono font-bold py-2.5 min-h-[46px]">
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block font-bold text-gray-700 uppercase">Tagihan Awal PLN (Rp) *</label>
                        <span class="text-[11px] text-gray-500 font-semibold" x-show="tagihanAwal > 0" x-text="'Terbaca: Rp ' + formatRupiah(tagihanAwal)"></span>
                    </div>
                    <div class="flex items-stretch rounded-2xl border border-gray-300 bg-white shadow-xs focus-within:border-[#CEA042] focus-within:ring-2 focus-within:ring-[#CEA042]/25 overflow-hidden transition-all min-h-[46px]">
                        <span class="flex items-center justify-center bg-gray-100/90 px-3.5 text-gray-500 font-bold text-xs select-none border-r border-gray-200 shrink-0">
                            Rp
                        </span>
                        <input type="text"
                               inputmode="numeric"
                               x-model="tagihanAwalDisplay"
                               @input="onInputTagihanAwal($event)"
                               placeholder="Contoh: 65.000"
                               required
                               class="w-full text-base sm:text-xs font-bold text-gray-900 border-0 focus:ring-0 focus:outline-none py-2.5 px-3 min-h-[46px] bg-transparent">
                    </div>
                    <input type="hidden" name="tagihan_awal" :value="tagihanAwal">
                </div>
            </div>

            <div>
                <label class="block font-bold text-gray-700 uppercase mb-1">Nama Warga (Penghuni) *</label>
                <input type="text" name="nama_warga" x-model="namaWarga" required placeholder="Contoh: SUGIYANTO (P. TEBEK)"
                       class="w-full text-base sm:text-xs rounded-xl border-gray-300 focus:border-amber-500 py-2.5 min-h-[46px]">
            </div>

            <div>
                <label class="block font-bold text-gray-700 uppercase mb-1">Atas Nama Rekening PLN *</label>
                <input type="text" name="nama_pelanggan" x-model="namaPelanggan" required placeholder="Contoh: SUGIYANTO"
                       class="w-full text-base sm:text-xs rounded-xl border-gray-300 focus:border-amber-500 font-mono py-2.5 min-h-[46px]">
            </div>

            <div class="bg-amber-50/80 rounded-xl p-3 border border-amber-200/80 space-y-1.5">
                <div class="flex items-center justify-between text-xs">
                    <span class="text-gray-600">Alokasi Kas Karang Taruna:</span>
                    <span class="font-bold text-amber-800">+Rp 3.000</span>
                </div>
                <div class="pt-1.5 border-t border-amber-200/60 flex items-center justify-between text-xs font-bold">
                    <span class="text-amber-950">Total Tagihan:</span>
                    <span class="text-emerald-700 text-sm font-black" x-text="'Rp ' + formatRupiah(totalTagihan)">Rp 0</span>
                </div>
            </div>

            <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2">
                <a href="{{ route('admin.rekap-listrik.index', ['periode' => $periode, 'rt' => $rt]) }}"
                   class="px-4 py-2.5 text-xs font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl min-h-[46px] flex items-center">
                    Batal
                </a>
                <button type="submit"
                        class="px-5 py-2.5 text-xs font-bold text-white bg-[#CEA042] hover:bg-[#BA8832] rounded-xl shadow-xs min-h-[46px] transition">
                    Simpan Rekap Struk
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
