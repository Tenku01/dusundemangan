@extends('layouts.admin')

@section('title', 'Buku Kas Utama Pengurus')

@section('content')
<div class="space-y-4 sm:space-y-6">

    <!-- Header & Action -->
    <div class="bg-white rounded-xl border border-gray-200/90 p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-lg sm:text-2xl font-bold text-gray-900 tracking-tight leading-tight">
                Buku Kas Keuangan Dusun
            </h1>
            <p class="text-xs text-gray-500 mt-0.5">
                Catat penerimaan iuran warga, sumbangan, serta pengeluaran operasional dusun.
            </p>
        </div>

        <div class="flex items-center gap-2 self-stretch sm:self-auto">
            <a href="{{ route('admin.keuangan.laporan') }}"
               class="flex-1 sm:flex-initial text-center px-3.5 py-2.5 text-xs font-bold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition min-h-[44px] flex items-center justify-center">
                Laporan
            </a>
            <button type="button" onclick="bukaModalKas()"
                    class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-1.5 px-4 py-2.5 text-xs font-bold text-white bg-[#CEA042] hover:bg-[#BA8832] rounded-lg shadow-xs transition min-h-[44px]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                + Catat Kas
            </button>
        </div>
    </div>

    <!-- Tiga Kartu Ringkasan Saldo -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-5">
        <div class="bg-white rounded-xl p-4 sm:p-5 border border-gray-200/90 shadow-xs">
            <span class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider block mb-0.5">Saldo Kas Akhir</span>
            <div class="text-2xl sm:text-3xl font-extrabold text-emerald-600 tracking-tight">
                Rp {{ number_format($saldoKasSaatIni, 0, ',', '.') }}
            </div>
            <p class="text-[11px] text-gray-500 mt-0.5">Saldo riil di kas bendahara saat ini.</p>
        </div>

        <div class="bg-white rounded-xl p-4 sm:p-5 border border-gray-200/90 shadow-xs">
            <span class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider block mb-0.5">Total Kas Masuk</span>
            <div class="text-2xl sm:text-3xl font-extrabold text-[#CEA042] tracking-tight">
                Rp {{ number_format($totalMasuk, 0, ',', '.') }}
            </div>
            <p class="text-[11px] text-gray-500 mt-0.5">Akumulasi iuran & donasi warga.</p>
        </div>

        <div class="bg-white rounded-xl p-4 sm:p-5 border border-gray-200/90 shadow-xs">
            <span class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider block mb-0.5">Total Kas Keluar</span>
            <div class="text-2xl sm:text-3xl font-extrabold text-rose-600 tracking-tight">
                Rp {{ number_format($totalKeluar, 0, ',', '.') }}
            </div>
            <p class="text-[11px] text-gray-500 mt-0.5">Akumulasi belanja fasilitas & kegiatan.</p>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white rounded-xl border border-gray-200/90 p-3.5 shadow-xs">
        <form method="GET" action="{{ route('admin.keuangan.index') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5">
            <div class="flex-1">
                <input type="text" name="cari" value="{{ $cari }}" placeholder="Cari uraian kas, pos, kode..."
                       class="w-full text-base sm:text-xs rounded-lg border-gray-300 focus:border-[#CEA042] focus:ring-1 focus:ring-[#CEA042] py-2 px-3 shadow-xs min-h-[44px] sm:min-h-0">
            </div>

            <div class="grid grid-cols-2 sm:flex gap-2.5">
                <select name="jenis" onchange="this.form.submit()" class="text-base sm:text-xs rounded-lg border-gray-300 focus:border-[#CEA042] focus:ring-1 focus:ring-[#CEA042] py-2 px-2.5 shadow-xs min-h-[44px] sm:min-h-0">
                    <option value="">Semua Arus</option>
                    <option value="masuk" {{ $jenis === 'masuk' ? 'selected' : '' }}>Kas Masuk (+)</option>
                    <option value="keluar" {{ $jenis === 'keluar' ? 'selected' : '' }}>Kas Keluar (-)</option>
                </select>

                <select name="bulan" onchange="this.form.submit()" class="text-base sm:text-xs rounded-lg border-gray-300 focus:border-[#CEA042] focus:ring-1 focus:ring-[#CEA042] py-2 px-2.5 shadow-xs min-h-[44px] sm:min-h-0">
                    <option value="">Semua Bulan</option>
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                            {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                        </option>
                    @endfor
                </select>
            </div>

            <button type="submit" class="px-4 py-2 text-xs font-semibold text-white bg-[#CEA042] hover:bg-[#BA8832] rounded-lg shadow-xs min-h-[44px]">
                Filter
            </button>

            @if($cari || $jenis || $bulan)
                <a href="{{ route('admin.keuangan.index') }}" class="text-xs text-rose-600 hover:underline text-center py-1">
                    Reset Filter
                </a>
            @endif
        </form>
    </div>

    <!-- TAMPILAN MOBILE: KARTU KAS RAMAH HP (block md:hidden) -->
    <div class="block md:hidden space-y-3.5">
        @forelse($daftarKas as $k)
            <div class="bg-white rounded-xl border border-gray-200/90 p-4 shadow-xs space-y-2.5">
                <div class="flex items-center justify-between pb-2 border-b border-gray-100">
                    <span class="text-xs text-gray-500 font-medium">
                        {{ $k->tanggal->format('d/m/Y') }} • <span class="font-mono text-[11px]">{{ $k->kode_transaksi }}</span>
                    </span>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $k->jenis === 'masuk' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                        {{ $k->jenis === 'masuk' ? 'Kas Masuk' : 'Kas Keluar' }}
                    </span>
                </div>

                <div class="flex items-baseline justify-between">
                    <div class="text-xs font-bold text-gray-800">{{ $k->kategori }}</div>
                    <div class="text-base font-extrabold {{ $k->jenis === 'masuk' ? 'text-emerald-700' : 'text-rose-700' }}">
                        {{ $k->jenis === 'masuk' ? '+' : '-' }} Rp {{ number_format($k->nominal, 0, ',', '.') }}
                    </div>
                </div>

                <p class="text-xs text-gray-600 bg-gray-50 p-2.5 rounded-lg leading-relaxed">
                    {{ $k->keterangan }}
                </p>

                <div class="flex items-center justify-between pt-1 text-[11px] text-gray-400">
                    <span>Oleh: {{ $k->pencatat->name ?? '-' }}</span>

                    <form action="{{ route('admin.keuangan.destroy', $k) }}" method="POST"
                          onsubmit="return confirm('Hapus catatan transaksi {{ $k->kode_transaksi }}? Saldo akan disesuaikan kembali.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-rose-600 hover:text-rose-800 font-medium py-1 px-2 rounded">
                            Hapus Transaksi
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl border border-gray-200 p-8 text-center text-gray-400 text-xs">
                Belum ada transaksi kas pada filter ini.
            </div>
        @endforelse
    </div>

    <!-- TAMPILAN DESKTOP: TABEL PENUH (hidden md:block) -->
    <div class="hidden md:block bg-white rounded-xl border border-gray-200/90 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-left text-xs">
                <thead class="bg-gray-50 text-gray-600 font-semibold uppercase tracking-wider">
                    <tr>
                        <th class="py-3.5 px-4">Tanggal</th>
                        <th class="py-3.5 px-4">Kode Transaksi</th>
                        <th class="py-3.5 px-4">Jenis</th>
                        <th class="py-3.5 px-4">Kategori / Pos</th>
                        <th class="py-3.5 px-4">Uraian Transaksi</th>
                        <th class="py-3.5 px-4">Dicatat Oleh</th>
                        <th class="py-3.5 px-4 text-right">Nominal</th>
                        <th class="py-3.5 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($daftarKas as $k)
                        <tr class="hover:bg-gray-50/80 transition">
                            <td class="py-3.5 px-4 whitespace-nowrap text-gray-700 font-medium">
                                {{ $k->tanggal->format('d/m/Y') }}
                            </td>
                            <td class="py-3.5 px-4 font-mono font-bold text-gray-700 whitespace-nowrap">
                                {{ $k->kode_transaksi }}
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $k->jenis === 'masuk' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                    {{ $k->jenis === 'masuk' ? 'Kas Masuk' : 'Kas Keluar' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 font-semibold text-gray-800 whitespace-nowrap">
                                {{ $k->kategori }}
                            </td>
                            <td class="py-3.5 px-4 text-gray-600 max-w-xs">
                                {{ $k->keterangan }}
                            </td>
                            <td class="py-3.5 px-4 text-gray-500 whitespace-nowrap">
                                {{ $k->pencatat->name ?? '-' }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-bold whitespace-nowrap {{ $k->jenis === 'masuk' ? 'text-emerald-700' : 'text-rose-700' }}">
                                {{ $k->jenis === 'masuk' ? '+' : '-' }} Rp {{ number_format($k->nominal, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <form action="{{ route('admin.keuangan.destroy', $k) }}" method="POST"
                                      onsubmit="return confirm('Hapus catatan kas {{ $k->kode_transaksi }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-600 hover:text-rose-800 p-1 rounded hover:bg-rose-50 min-h-[32px]">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-gray-400">
                                Belum ada catatan transaksi kas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="pt-2">
        {{ $daftarKas->links() }}
    </div>

</div>

<!-- MODAL CATAT KAS BARU (Bottom Sheet Ramah HP) -->
<div id="modal-kas" class="fixed inset-0 z-50 hidden bg-gray-900/60 backdrop-blur-xs flex items-end sm:items-center justify-center p-0 sm:p-4 overflow-y-auto">
    <div class="bg-white rounded-t-3xl sm:rounded-2xl max-w-lg w-full p-5 sm:p-6 shadow-2xl border border-gray-200 max-h-[92vh] overflow-y-auto">
        <!-- Drag Handle Indicator untuk Mobile Phone -->
        <div class="w-12 h-1.5 bg-gray-300 rounded-full mx-auto mb-3 sm:hidden"></div>

        <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
            <div>
                <h3 class="text-base font-bold text-gray-900 leading-tight">Catat Transaksi Kas Dusun</h3>
                <p class="text-xs text-gray-500 mt-0.5">Catat arus kas masuk atau pengeluaran dana warga.</p>
            </div>
            <button type="button" onclick="tutupModalKas()" class="text-gray-400 hover:text-gray-700 p-2 rounded-xl focus:outline-none min-h-[44px] min-w-[44px] flex items-center justify-center bg-gray-100 sm:bg-transparent">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form action="{{ route('admin.keuangan.store') }}" method="POST" class="space-y-3.5">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Jenis Arus Kas <span class="text-rose-500">*</span></label>
                    <select name="jenis" required class="w-full text-base sm:text-xs rounded-xl border-gray-300 focus:border-[#CEA042] focus:ring-1 focus:ring-[#CEA042] py-2.5 px-3.5 shadow-xs min-h-[46px]">
                        <option value="masuk">Kas Masuk (Penerimaan / Iuran)</option>
                        <option value="keluar">Kas Keluar (Belanja / Operasional)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Tanggal Transaksi <span class="text-rose-500">*</span></label>
                    <input type="date" name="tanggal" required value="{{ date('Y-m-d') }}"
                           class="w-full text-base sm:text-xs rounded-xl border-gray-300 focus:border-[#CEA042] focus:ring-1 focus:ring-[#CEA042] py-2.5 px-3.5 shadow-xs min-h-[46px]">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Nominal (Rupiah) <span class="text-rose-500">*</span></label>
                    <div class="flex items-stretch rounded-xl border border-gray-300 bg-white shadow-xs focus-within:border-[#CEA042] focus-within:ring-2 focus-within:ring-[#CEA042]/25 overflow-hidden transition-all min-h-[46px]">
                        <span class="flex items-center justify-center bg-gray-100/90 px-3.5 text-gray-500 font-bold text-xs select-none border-r border-gray-200 shrink-0">
                            Rp
                        </span>
                        <input type="text"
                               inputmode="numeric"
                               name="nominal"
                               required
                               placeholder="Contoh: 250.000"
                               class="input-rupiah w-full text-base sm:text-xs font-bold text-gray-900 border-0 focus:ring-0 focus:outline-none py-2.5 px-3 min-h-[46px] bg-transparent">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Pos / Kategori <span class="text-rose-500">*</span></label>
                    <input type="text" name="kategori" required placeholder="Contoh: Iuran Warga RT 02" list="list-pos"
                           class="w-full text-base sm:text-xs rounded-xl border-gray-300 focus:border-[#CEA042] focus:ring-1 focus:ring-[#CEA042] py-2.5 px-3.5 shadow-xs min-h-[46px]">
                    <datalist id="list-pos">
                        <option value="Iuran Warga">
                        <option value="Donasi & Sumbangan">
                        <option value="Kas RT / RW">
                        <option value="Pengadaan Inventaris">
                        <option value="Pemeliharaan Fasilitas">
                        <option value="Konsumsi & Rapat">
                        <option value="Kegiatan 17 Agustus">
                        <option value="Sosial & Lelayu">
                    </datalist>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Uraian Keterangan <span class="text-rose-500">*</span></label>
                <textarea name="keterangan" rows="2" required placeholder="Jelaskan rincian penggunaan atau sumber dana secara ringkas..."
                          class="w-full text-base sm:text-xs rounded-xl border-gray-300 focus:border-[#CEA042] focus:ring-1 focus:ring-[#CEA042] py-2.5 px-3.5 shadow-xs"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-gray-100">
                <button type="button" onclick="tutupModalKas()" class="px-4 py-2.5 text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition min-h-[44px]">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 text-xs font-bold text-white bg-[#CEA042] hover:bg-[#BA8832] rounded-xl shadow-xs transition min-h-[44px]">
                    Simpan Catatan Kas
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function bukaModalKas() {
        document.getElementById('modal-kas').classList.remove('hidden');
    }
    function tutupModalKas() {
        document.getElementById('modal-kas').classList.add('hidden');
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            tutupModalKas();
        }
    });
</script>
@endpush
@endsection

