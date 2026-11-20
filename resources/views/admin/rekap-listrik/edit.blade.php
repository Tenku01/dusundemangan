@extends('layouts.admin')

@section('title', 'Edit Rekap - ' . $tagihanListrik->nama_warga)

@section('content')
<div class="max-w-2xl mx-auto space-y-4"
     x-data="{
        tagihanAwalDisplay: '{{ number_format($tagihanListrik->tagihan_awal, 0, ',', '.') }}',
        saldoLalu: {{ $tagihanListrik->saldo_periode_lalu ?? 0 }},
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
            if (awal <= 0) return 0;
            return Math.max(0, (awal + this.kasKartar) - this.saldoLalu);
        },
        formatRupiah(num) {
            return new Intl.NumberFormat('id-ID').format(num || 0);
        }
     }">

    <div class="flex items-center justify-between">
        <a href="{{ route('admin.rekap-listrik.index', ['periode' => $tagihanListrik->periode, 'rt' => $tagihanListrik->rt]) }}"
           class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-600 hover:text-emas-800 min-h-[44px] py-1 px-2 -ml-2 rounded-lg transition">
            <svg class="w-4 h-4" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Kembali ke Rekap RT {{ $tagihanListrik->rt }}</span>
        </a>
    </div>

    <div class="card-surface p-5 sm:p-6 space-y-4">
        <div class="border-b border-gray-100 pb-3">
            <span class="inline-block px-2.5 py-0.5 text-[11px] font-bold rounded-md bg-emas-100 text-emas-900 border border-emas-200 uppercase">
                RT {{ $tagihanListrik->rt }} | Periode {{ $tagihanListrik->periode }}
            </span>
            <h1 class="text-base sm:text-lg font-bold text-gray-900 mt-1.5">
                Ubah Rekap Tagihan Listrik
            </h1>
            <p class="text-xs text-gray-600">Edit nominal tagihan awal PLN untuk warga terpilih</p>
        </div>

        <form method="POST" action="{{ route('admin.rekap-listrik.update', $tagihanListrik) }}" class="space-y-4 text-xs">
            @csrf
            @method('PUT')

            <!-- Informasi Warga & Nomor Struk -->
            <div class="bg-emas-50 rounded-2xl p-4 border border-emas-200 flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-emas-800 block">Warga:</span>
                    <h2 class="text-base font-bold text-gray-900 leading-tight truncate">{{ $tagihanListrik->nama_warga }}</h2>
                    @if($tagihanListrik->nama_pelanggan && $tagihanListrik->nama_pelanggan !== $tagihanListrik->nama_warga)
                        <p class="text-xs text-gray-600 font-mono mt-0.5 truncate">PLN: {{ $tagihanListrik->nama_pelanggan }}</p>
                    @endif
                </div>
                <div class="text-right shrink-0">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-emas-800 block">Nomor Struk:</span>
                    <span class="inline-block px-3 py-1 bg-[#CEA042] text-[#1F1406] font-mono font-bold text-sm rounded-xl mt-0.5 shadow-sm tabular-money">
                        [ #{{ $tagihanListrik->nomor_struk }} ]
                    </span>
                </div>
            </div>

            <!-- Input Tagihan Awal PLN -->
            <div>
                <div class="flex items-center justify-between mb-1">
                    <label for="edit-tagihan-awal" class="block text-xs font-bold text-gray-800">Tagihan Awal PLN (Rp) <span class="text-rose-600">*</span></label>
                    <span class="text-xs text-emas-800 font-semibold tabular-money" x-show="tagihanAwal > 0" x-text="'Terbaca: Rp ' + formatRupiah(tagihanAwal)"></span>
                </div>
                <div class="input-addon-group min-h-[50px]">
                    <span class="flex items-center justify-center bg-gray-100 px-4 text-gray-600 font-bold text-base select-none border-r border-gray-200 shrink-0">
                        Rp
                    </span>
                    <input type="text"
                           id="edit-tagihan-awal"
                           inputmode="numeric"
                           x-model="tagihanAwalDisplay"
                           @input="onInputTagihanAwal($event)"
                           placeholder="Contoh: 65.000"
                           required
                           autofocus
                           class="w-full text-lg sm:text-base rounded-none border-0 focus:ring-0 focus:outline-none py-3 px-3.5 font-extrabold text-gray-900 bg-transparent tabular-money">
                </div>
                <input type="hidden" name="tagihan_awal" :value="tagihanAwal">
            </div>

            <div class="bg-emas-50 rounded-xl p-3.5 border border-emas-200 space-y-1.5">
                <div class="flex items-center justify-between text-xs">
                    <span class="text-gray-700">Alokasi Kas Karang Taruna:</span>
                    <span class="font-bold text-emas-800 tabular-money">+Rp 3.000</span>
                </div>
                @if($tagihanListrik->saldo_periode_lalu > 0)
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-emerald-800 font-semibold">Potongan Titipan Bulan Lalu:</span>
                        <span class="font-bold text-emerald-800 tabular-money">-Rp {{ number_format($tagihanListrik->saldo_periode_lalu, 0, ',', '.') }}</span>
                    </div>
                @elseif($tagihanListrik->saldo_periode_lalu < 0)
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-rose-800 font-semibold">Tunggakan Periode Lalu:</span>
                        <span class="font-bold text-rose-800 tabular-money">+Rp {{ number_format(abs($tagihanListrik->saldo_periode_lalu), 0, ',', '.') }}</span>
                    </div>
                @endif
                <div class="pt-1.5 border-t border-emas-200 flex items-center justify-between text-xs font-bold">
                    <span class="text-emas-950">Total Tagihan Bersih:</span>
                    <span class="text-emerald-700 text-base font-extrabold tabular-money" x-text="'Rp ' + formatRupiah(totalTagihan)">Rp {{ number_format($tagihanListrik->total_tagihan, 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2.5">
                <a href="{{ route('admin.rekap-listrik.index', ['periode' => $tagihanListrik->periode, 'rt' => $tagihanListrik->rt]) }}"
                   class="btn-subtle px-4 text-xs">
                    Batal
                </a>
                <button type="submit" class="btn-primary px-5 text-xs">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
