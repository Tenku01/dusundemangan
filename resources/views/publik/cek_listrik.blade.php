@extends('layouts.publik')

@section('title', 'Cek Tagihan Listrik Warga | Dusun Demangan')

@section('content')
<div class="py-6 sm:py-10 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6 sm:space-y-8">

    <!-- Header Section -->
    <div class="text-center max-w-2xl mx-auto space-y-2.5">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-50 border border-amber-200/90 text-[#CEA042] text-xs font-bold uppercase tracking-wider shadow-2xs">
            <span>⚡ Layanan Warga Dusun Demangan</span>
        </div>
        <h1 class="text-2xl sm:text-4xl font-extrabold text-gray-900 tracking-tight leading-tight">
            Cek Tagihan Listrik Warga
        </h1>
        <p class="text-xs sm:text-sm text-gray-600 leading-relaxed">
            Periksa rincian dan status pembayaran tagihan listrik bulanan Anda secara mandiri, transparan, dan cepat.
        </p>
    </div>

    <!-- Kotak Pencarian Utama (Search Bar) -->
    <div class="bg-white rounded-2xl border border-gray-200/90 p-4 sm:p-6 shadow-xs max-w-3xl mx-auto">
        <form method="GET" action="{{ route('publik.cek-listrik') }}" class="space-y-3.5">
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5">
                <!-- Input Nama di Struk -->
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text"
                           name="q"
                           value="{{ $keyword }}"
                           required
                           minlength="2"
                           placeholder="Ketik nama di struk PLN (contoh: Budi, Agus, Sri)..."
                           class="w-full pl-10 pr-4 py-3 text-base sm:text-sm rounded-xl border border-gray-300 focus:border-[#CEA042] focus:ring-2 focus:ring-[#CEA042]/20 shadow-2xs min-h-[48px] text-gray-900 placeholder:text-gray-400 font-medium">
                </div>

                <!-- Filter Periode (Opsional) -->
                @if(!empty($daftarPeriode) && count($daftarPeriode) > 0)
                    <div class="w-full sm:w-48 shrink-0">
                        <select name="periode"
                                class="w-full py-3 px-3.5 text-base sm:text-xs rounded-xl border border-gray-300 focus:border-[#CEA042] focus:ring-2 focus:ring-[#CEA042]/20 shadow-2xs min-h-[48px] text-gray-700 font-semibold bg-white">
                            <option value="semua">Semua Periode</option>
                            @foreach($daftarPeriode as $p)
                                <option value="{{ $p }}" {{ $periodeTerpilih === $p ? 'selected' : '' }}>
                                    {{ $p }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <!-- Tombol Cari -->
                <button type="submit"
                        class="inline-flex items-center justify-center gap-2 px-6 py-3 text-sm font-bold text-white bg-[#CEA042] hover:bg-[#BA8832] rounded-xl shadow-xs transition min-h-[48px] shrink-0 focus:outline-none focus:ring-2 focus:ring-amber-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <span>Cari Tagihan</span>
                </button>
            </div>

            <div class="flex items-center justify-between text-xs text-gray-500 pt-1">
                <span class="flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Pencarian mencocokkan Nama Pelanggan di Struk, Nama Warga, atau Nomor Struk.</span>
                </span>
                @if($sudahCari)
                    <a href="{{ route('publik.cek-listrik') }}" class="text-xs font-bold text-rose-600 hover:underline">
                        Bersihkan Pencarian
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- AREA KONTEN: HASIL PENCARIAN ATAU STATE AWAL -->
    @if(!$sudahCari)
        <!-- State 1: Awal Datang (Belum Mengetik Pencarian) -->
        <div class="bg-white rounded-2xl border border-gray-200/90 p-6 sm:p-10 text-center max-w-2xl mx-auto shadow-xs space-y-4">
            <div class="w-16 h-16 rounded-2xl bg-amber-50 border border-amber-200/80 text-[#CEA042] flex items-center justify-center mx-auto text-2xl shadow-xs">
                ⚡
            </div>
            <div class="space-y-1.5">
                <h2 class="text-lg sm:text-xl font-bold text-gray-900">
                    Masukkan Nama di Struk Listrik Anda
                </h2>
                <p class="text-xs sm:text-sm text-gray-600 leading-relaxed">
                    Demi menjaga privasi warga Dusun Demangan, data tagihan tidak ditampilkan sekaligus secara terbuka.
                    Ketik nama Anda pada kolom di atas untuk mencari rincian tagihan listrik rumah Anda.
                </p>
            </div>

            <div class="bg-gray-50 rounded-xl p-4 border border-gray-200/80 text-left text-xs text-gray-600 space-y-2">
                <div class="font-bold text-gray-800 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-[#CEA042]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Petunjuk Pencarian Cepat:</span>
                </div>
                <ul class="list-disc list-inside space-y-1 text-gray-600 pl-1">
                    <li>Gunakan nama yang tertera pada bukti kuitansi / struk PLN Anda.</li>
                    <li>Bisa juga menggunakan nama warga kepala keluarga atau nomor struk (#...).</li>
                    <li>Setelah ditemukan, Anda dapat melihat rincian biaya awal dan iuran kas Karang Taruna.</li>
                </ul>
            </div>
        </div>

    @elseif($hasilTagihan->isEmpty())
        <!-- State 2: Sudah Cari tapi Tidak Ditemukan -->
        <div class="bg-white rounded-2xl border border-rose-200/90 p-8 sm:p-10 text-center max-w-2xl mx-auto shadow-xs space-y-4">
            <div class="w-14 h-14 rounded-2xl bg-rose-50 border border-rose-200 text-rose-600 flex items-center justify-center mx-auto text-xl shadow-xs">
                🔍
            </div>
            <div class="space-y-1">
                <h2 class="text-base sm:text-lg font-bold text-gray-900">
                    Tagihan Tidak Ditemukan
                </h2>
                <p class="text-xs sm:text-sm text-gray-600">
                    Tidak ditemukan data tagihan listrik dengan kata kunci <strong class="text-gray-900">"{{ $keyword }}"</strong>
                    @if($periodeTerpilih && $periodeTerpilih !== 'semua')
                        pada periode <strong>{{ $periodeTerpilih }}</strong>
                    @endif.
                </p>
            </div>
            <div class="text-xs text-gray-500 bg-gray-50 p-4 rounded-xl text-left border border-gray-200 space-y-1">
                <p class="font-bold text-gray-700">Saran untuk Anda:</p>
                <ul class="list-disc list-inside space-y-0.5 text-gray-600">
                    <li>Periksa kembali penulisan nama di struk PLN Anda (coba gunakan nama panggilan atau sebagian kata nama).</li>
                    <li>Pastikan memilih pilihan <strong>"Semua Periode"</strong> jika mencari riwayat bulan-bulan lalu.</li>
                    <li>Jika nama meter baru saja dipasang atau dialihkan, silakan tanyakan kepada pengurus RT atau petugas di Balai RW.</li>
                </ul>
            </div>
        </div>

    @else
        <!-- State 3: Data Tagihan Ditemukan -->
        <div class="space-y-4 max-w-4xl mx-auto">
            <!-- Summary Bar -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 bg-amber-50/70 border border-amber-200/90 p-3.5 sm:p-4 rounded-2xl shadow-2xs">
                <div class="flex items-center gap-2">
                    <span class="w-8 h-8 rounded-xl bg-amber-100 text-[#CEA042] flex items-center justify-center font-bold text-sm shrink-0">
                        ⚡
                    </span>
                    <div>
                        <div class="text-xs sm:text-sm font-bold text-gray-900">
                            Ditemukan {{ $hasilTagihan->count() }} data tagihan untuk <span class="text-[#CEA042]">"{{ $keyword }}"</span>
                        </div>
                        <div class="text-[11px] text-gray-500">
                            Data diurutkan dari periode tagihan yang paling terbaru.
                        </div>
                    </div>
                </div>

                <div class="text-xs text-gray-500">
                    Pembayaran dilayani di Balai RW
                </div>
            </div>

            <!-- List of Bills (Cards) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($hasilTagihan as $t)
                    <div class="bg-white rounded-2xl border {{ $t->is_lunas ? 'border-emerald-200/90' : 'border-amber-300' }} p-4 sm:p-5 shadow-xs space-y-3.5 transition hover:shadow-md">
                        <!-- Top Header: Periode, Struk, Status -->
                        <div class="flex items-start justify-between gap-2 pb-3 border-b border-gray-100">
                            <div>
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-700 font-mono">
                                        Struk #{{ $t->nomor_struk ?: '-' }}
                                    </span>
                                    <span class="text-xs font-bold text-gray-900">
                                        {{ $t->periode }}
                                    </span>
                                </div>
                                <div class="text-[11px] text-gray-500 mt-0.5">
                                    Wilayah: RT {{ str_pad((string)$t->rt, 2, '0', STR_PAD_LEFT) }} Dusun Demangan
                                </div>
                            </div>

                            <div>
                                @if($t->is_lunas)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        LUNAS
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-amber-50 text-amber-800 border border-amber-300">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        BELUM LUNAS
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Data Nama -->
                        <div class="space-y-1">
                            <div class="text-[11px] text-gray-400 font-bold uppercase tracking-wider">Nama di Struk PLN:</div>
                            <div class="text-sm sm:text-base font-extrabold text-gray-900 leading-snug">
                                {{ $t->nama_pelanggan ?: ($t->nama_warga ?: ($t->warga->nama ?? 'Warga Dusun')) }}
                            </div>
                            @if($t->nama_warga && $t->nama_pelanggan && $t->nama_warga !== $t->nama_pelanggan)
                                <div class="text-xs text-gray-600">
                                    Nama Warga: <span class="font-medium text-gray-800">{{ $t->nama_warga }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Rincian Nominal Tagihan -->
                        <div class="bg-gray-50/80 rounded-xl p-3 border border-gray-100 space-y-1.5 text-xs">
                            <div class="flex items-center justify-between text-gray-600">
                                <span>Tagihan Awal PLN:</span>
                                <span class="font-semibold text-gray-800 font-mono">
                                    Rp {{ number_format($t->tagihan_awal, 0, ',', '.') }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between text-gray-600">
                                <span>Kas Karang Taruna:</span>
                                <span class="font-semibold text-gray-800 font-mono">
                                    + Rp {{ number_format($t->kas_karang_taruna ?: 3000, 0, ',', '.') }}
                                </span>
                            </div>

                            @if($t->saldo_periode_lalu != 0)
                                <div class="flex items-center justify-between {{ $t->saldo_periode_lalu > 0 ? 'text-emerald-700' : 'text-rose-600' }}">
                                    <span>{{ $t->saldo_periode_lalu > 0 ? 'Potongan Saldo Titipan:' : 'Tunggakan Bulan Lalu:' }}</span>
                                    <span class="font-semibold font-mono">
                                        {{ $t->saldo_periode_lalu > 0 ? '- ' : '+ ' }} Rp {{ number_format(abs($t->saldo_periode_lalu), 0, ',', '.') }}
                                    </span>
                                </div>
                            @endif

                            <div class="flex items-baseline justify-between pt-2 border-t border-gray-200">
                                <span class="font-bold text-gray-900">Total Wajib Bayar:</span>
                                <span class="text-base font-black {{ $t->is_lunas ? 'text-emerald-700' : 'text-[#CEA042]' }} font-mono">
                                    Rp {{ number_format($t->total_tagihan, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>

                        <!-- Info Footer Status Bayar -->
                        <div class="text-[11px] text-gray-500 pt-1 flex items-center justify-between">
                            @if($t->is_lunas)
                                <span class="text-emerald-700 font-medium flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    @if($t->tanggal_bayar)
                                        Dibayar: {{ $t->tanggal_bayar->format('d/m/Y') }}
                                    @else
                                        Telah dibayarkan ke kasir
                                    @endif
                                </span>
                            @else
                                <span class="text-amber-800 font-medium">
                                    Mohon selesaikan pembayaran di Balai RW
                                </span>
                            @endif

                            @if($t->kode_gabungan)
                                <span class="text-[10px] bg-purple-50 text-purple-700 border border-purple-200 px-1.5 py-0.5 rounded font-mono font-bold">
                                    {{ $t->kode_gabungan }}
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</div>
@endsection

