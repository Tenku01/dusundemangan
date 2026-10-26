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
           class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-600 hover:text-gray-900 min-h-[44px]">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Rekap RT {{ $tagihanListrik->rt }}
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200/90 p-5 sm:p-6 shadow-xs space-y-4">
        <div class="border-b border-gray-100 pb-3">
            <span class="inline-block px-2 py-0.5 text-[10px] font-bold rounded-md bg-amber-100 text-amber-900 uppercase">
                RT {{ $tagihanListrik->rt }} | Periode {{ $tagihanListrik->periode }}
            </span>
            <h1 class="text-base sm:text-lg font-bold text-gray-900 mt-1">
                Ubah Rekap Tagihan Listrik
            </h1>
            <p class="text-xs text-gray-500">Edit nomor struk fisik atau nominal tagihan awal PLN</p>
        </div>

        <form method="POST" action="{{ route('admin.rekap-listrik.update', $tagihanListrik) }}" class="space-y-4 text-xs">
            @csrf
            @method('PUT')

            <!-- Informasi Warga & Nomor Struk (Otomatis & Terarah) -->
            <div class="bg-amber-50/70 rounded-2xl p-4 border border-amber-200/90 flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-amber-800">Warga:</span>
                    <h2 class="text-base font-bold text-gray-900 leading-tight">{{ $tagihanListrik->nama_warga }}</h2>
                    @if($tagihanListrik->nama_pelanggan && $tagihanListrik->nama_pelanggan !== $tagihanListrik->nama_warga)
                        <p class="text-[11px] text-gray-500 font-mono mt-0.5">PLN: {{ $tagihanListrik->nama_pelanggan }}</p>
                    @endif
                </div>
                <div class="text-right">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-amber-800 block">Nomor Struk:</span>
                    <span class="inline-block px-3 py-1 bg-[#CEA042] text-white font-mono font-bold text-sm rounded-xl mt-0.5 shadow-2xs">
                        [ #{{ $tagihanListrik->nomor_struk }} ]
                    </span>
                </div>
            </div>

            <!-- Input Tagihan Awal PLN (Satu-satunya yang perlu diisi/diubah) -->
            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="block font-bold text-gray-800 uppercase">Tagihan Awal PLN (Rp) *</label>
                    <span class="text-xs text-gray-500 font-semibold" x-show="tagihanAwal > 0" x-text="'Terbaca: Rp ' + formatRupiah(tagihanAwal)"></span>
                </div>
                <div class="flex items-stretch rounded-2xl border border-gray-300 bg-white shadow-xs focus-within:border-[#CEA042] focus-within:ring-2 focus-within:ring-[#CEA042]/25 overflow-hidden transition-all min-h-[50px]">
                    <span class="flex items-center justify-center bg-gray-100/90 px-4 text-gray-500 font-black text-base select-none border-r border-gray-200 shrink-0">
                        Rp
                    </span>
                    <input type="text"
                           inputmode="numeric"
                           x-model="tagihanAwalDisplay"
                           @input="onInputTagihanAwal($event)"
                           placeholder="Contoh: 65.000"
                           required
                           autofocus
                           class="w-full text-lg sm:text-base rounded-none border-0 focus:ring-0 focus:outline-none py-3 px-3.5 font-black text-gray-900 bg-transparent">
                </div>
                <input type="hidden" name="tagihan_awal" :value="tagihanAwal">
            </div>

            <div class="bg-amber-50/80 rounded-xl p-3 border border-amber-200/80 space-y-1.5">
                <div class="flex items-center justify-between text-xs">
                    <span class="text-gray-600">Alokasi Kas Karang Taruna:</span>
                    <span class="font-bold text-[#CEA042]">+Rp 3.000</span>
                </div>
                @if($tagihanListrik->saldo_periode_lalu > 0)
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-emerald-700 font-semibold">Potongan Titipan Bulan Lalu:</span>
                        <span class="font-bold text-emerald-700">-Rp {{ number_format($tagihanListrik->saldo_periode_lalu, 0, ',', '.') }}</span>
                    </div>
                @elseif($tagihanListrik->saldo_periode_lalu < 0)
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-rose-700 font-semibold">Tunggakan Periode Lalu:</span>
                        <span class="font-bold text-rose-700">+Rp {{ number_format(abs($tagihanListrik->saldo_periode_lalu), 0, ',', '.') }}</span>
                    </div>
                @endif
                <div class="pt-1.5 border-t border-amber-200/60 flex items-center justify-between text-xs font-bold">
                    <span class="text-amber-950">Total Tagihan Bersih:</span>
                    <span class="text-emerald-700 text-sm font-black" x-text="'Rp ' + formatRupiah(totalTagihan)">Rp {{ number_format($tagihanListrik->total_tagihan, 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2">
                <a href="{{ route('admin.rekap-listrik.index', ['periode' => $tagihanListrik->periode, 'rt' => $tagihanListrik->rt]) }}"
                   class="px-4 py-2.5 text-xs font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl min-h-[46px] flex items-center">
                    Batal
                </a>
                <button type="submit"
                        class="px-5 py-2.5 text-xs font-bold text-white bg-[#CEA042] hover:bg-[#BA8832] rounded-xl shadow-xs min-h-[46px] transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
