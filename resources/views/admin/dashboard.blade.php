@extends('layouts.admin')

@section('title', 'Dashboard Ringkasan Pengurus')

@section('content')
<div class="space-y-6">

    <!-- Welcome Header -->
    <div class="bg-white rounded-xl border border-gray-200/90 p-6 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <span class="text-xs font-semibold text-[#CEA042] uppercase tracking-wider block mb-1">
                Selamat Datang, {{ auth()->user()->name }}
            </span>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">
                Ringkasan Operasional Inventaris & Keuangan Dusun
            </h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">
                Kelola inventaris aset dusun, verifikasi antrean pinjaman warga, dan pantau pembukuan kas.
            </p>
        </div>

        <div class="flex items-center gap-2 self-start sm:self-auto">
            <a href="{{ route('admin.barang.index') }}"
               class="px-3.5 py-2 text-xs font-bold text-white bg-[#CEA042] hover:bg-[#BA8832] rounded-lg shadow-xs transition min-h-[40px] flex items-center">
                + Tambah Barang
            </a>
            <a href="{{ route('admin.keuangan.index') }}"
               class="px-3.5 py-2 text-xs font-bold text-white bg-[#CEA042] hover:bg-[#BA8832] rounded-lg shadow-xs transition min-h-[40px] flex items-center">
                + Catat Kas
            </a>
        </div>
    </div>

    <!-- 6 KPI Grid Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        <!-- Saldo Kas Riil -->
        <div class="bg-white rounded-xl p-5 border border-gray-200/90 shadow-xs">
            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider block mb-1">Saldo Kas Dusun</span>
            <div class="text-2xl sm:text-3xl font-extrabold text-emerald-600 tracking-tight">
                Rp {{ number_format($saldoKasSaatIni, 0, ',', '.') }}
            </div>
            <div class="mt-2 text-xs text-gray-500 flex justify-between">
                <span>Bulan Ini Masuk: <strong class="text-emerald-700">Rp {{ number_format($pemasukanBulanIni, 0, ',', '.') }}</strong></span>
            </div>
        </div>

        <!-- Antrean Pinjaman -->
        <div class="bg-white rounded-xl p-5 border border-gray-200/90 shadow-xs">
            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider block mb-1">Permohonan Pinjam Menunggu</span>
            <div class="text-2xl sm:text-3xl font-extrabold text-amber-600 tracking-tight flex items-baseline gap-2">
                {{ $pinjamanMenunggu }} <span class="text-xs font-medium text-gray-500">permohonan</span>
            </div>
            <div class="mt-2 text-xs text-gray-500">
                <span>Sedang dipinjam aktif: <strong class="text-gray-800">{{ $pinjamanAktif }} transaksi</strong></span>
            </div>
        </div>

        <!-- Inventaris Unit Siap Pakai -->
        <div class="bg-white rounded-xl p-5 border border-gray-200/90 shadow-xs">
            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider block mb-1">Unit Barang Tersedia</span>
            <div class="text-2xl sm:text-3xl font-extrabold text-[#CEA042] tracking-tight">
                {{ $totalUnitTersedia }} <span class="text-xs font-medium text-gray-500">/ {{ $totalUnitAset }} unit fisik</span>
            </div>
            <div class="mt-2 text-xs text-gray-500">
                Tersebar di {{ $totalJenisBarang }} kategori barang dusun
            </div>
        </div>
    </div>

    <!-- Antrean Pinjaman yang Butuh Tindakan Segera -->
    <div class="bg-white rounded-xl border border-gray-200/90 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-gray-200 flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-gray-900 leading-tight">Antrean Permohonan Pinjaman Terbaru</h2>
                <p class="text-xs text-gray-500 mt-0.5">Permohonan warga yang perlu disetujui atau ditolak.</p>
            </div>
            <a href="{{ route('admin.peminjaman.index') }}" class="text-xs font-semibold text-[#CEA042] hover:underline">
                Lihat Semua Antrean &rarr;
            </a>
        </div>

        <!-- TAMPILAN MOBILE: KARTU ANTREAN RAMAH HP (block md:hidden) -->
        <div class="block md:hidden p-4 space-y-3">
            @forelse($antreanTerbaru as $p)
                <div class="bg-gray-50/80 rounded-xl p-3.5 border border-gray-200 space-y-2.5">
                    <div class="flex items-center justify-between pb-1.5 border-b border-gray-200/60">
                        <span class="font-mono font-bold text-xs text-[#CEA042]">
                            {{ $p->kode_pinjam }}
                        </span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                            Menunggu Approval
                        </span>
                    </div>

                    <div class="text-xs space-y-1">
                        <div>
                            <span class="text-gray-500">Peminjam:</span>
                            <span class="font-bold text-gray-900">{{ $p->nama_peminjam }}</span> ({{ $p->alamat_rt_rw }})
                        </div>
                        <div>
                            <span class="text-gray-500">Barang:</span>
                            <span class="font-bold text-gray-900">{{ $p->barang->nama_barang }}</span>
                            <span class="bg-gray-200 text-gray-800 font-semibold px-1.5 py-0.2 rounded ml-1">{{ $p->jumlah_pinjam }} unit</span>
                        </div>
                        <div class="text-gray-600">
                            <span class="text-gray-500">Jadwal:</span>
                            {{ $p->tanggal_pinjam->format('d/m') }} s/d {{ $p->rencana_tanggal_kembali->format('d/m/Y') }}
                        </div>
                    </div>

                    <!-- Tombol Aksi Jempol HP (44px min height) -->
                    <div class="grid grid-cols-2 gap-2 pt-1 border-t border-gray-200/60">
                        <form action="{{ route('admin.peminjaman.setujui', $p) }}" method="POST"
                              onsubmit="return confirm('Setujui permohonan pinjam ini? Stok barang akan otomatis berkurang.');">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="w-full py-2.5 px-3 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 rounded-xl shadow-xs min-h-[44px]">
                                Setujui
                            </button>
                        </form>

                        <button type="button" onclick="bukaModalTolak({{ $p->id }}, '{{ $p->kode_pinjam }}')"
                                class="w-full py-2.5 px-3 text-xs font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 active:bg-rose-200 rounded-xl border border-rose-200 min-h-[44px]">
                            Tolak
                        </button>
                    </div>
                </div>
            @empty
                <div class="p-6 text-center text-gray-400 text-xs">
                    Tidak ada permohonan pinjaman yang sedang menunggu persetujuan.
                </div>
            @endforelse
        </div>

        <!-- TAMPILAN DESKTOP: TABEL ANTREAN (hidden md:block) -->
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-left text-xs">
                <thead class="bg-gray-50 text-gray-600 font-semibold uppercase tracking-wider">
                    <tr>
                        <th class="py-3 px-4">Tiket</th>
                        <th class="py-3 px-4">Peminjam</th>
                        <th class="py-3 px-4">Barang</th>
                        <th class="py-3 px-4">Jumlah</th>
                        <th class="py-3 px-4">Jadwal Pinjam</th>
                        <th class="py-3 px-4">Keperluan</th>
                        <th class="py-3 px-4 text-center">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($antreanTerbaru as $p)
                        <tr class="hover:bg-gray-50/80 transition">
                            <td class="py-3 px-4 font-mono font-bold text-[#CEA042] whitespace-nowrap">
                                {{ $p->kode_pinjam }}
                            </td>
                            <td class="py-3 px-4 whitespace-nowrap">
                                <div class="font-bold text-gray-900">{{ $p->nama_peminjam }}</div>
                                <div class="text-[11px] text-gray-500">{{ $p->alamat_rt_rw }} • {{ $p->kontak_peminjam }}</div>
                            </td>
                            <td class="py-3 px-4 font-medium text-gray-800 whitespace-nowrap">
                                {{ $p->barang->nama_barang }}
                            </td>
                            <td class="py-3 px-4 font-semibold text-gray-900 whitespace-nowrap">
                                {{ $p->jumlah_pinjam }} unit
                            </td>
                            <td class="py-3 px-4 whitespace-nowrap text-gray-600">
                                {{ $p->tanggal_pinjam->format('d/m') }} s/d {{ $p->rencana_tanggal_kembali->format('d/m/Y') }}
                            </td>
                            <td class="py-3 px-4 text-gray-600 max-w-xs truncate">
                                {{ $p->tujuan_peminjaman }}
                            </td>
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    <!-- Tombol Setujui -->
                                    <form action="{{ route('admin.peminjaman.setujui', $p) }}" method="POST" onsubmit="return confirm('Setujui permohonan pinjam ini? Stok barang akan otomatis berkurang.');">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="px-2.5 py-1 text-xs font-semibold text-emerald-800 bg-emerald-100 hover:bg-emerald-200 rounded-md transition min-h-[36px]">
                                            Setujui
                                        </button>
                                    </form>

                                    <!-- Tombol Tolak Modal trigger -->
                                    <button type="button" onclick="bukaModalTolak({{ $p->id }}, '{{ $p->kode_pinjam }}')"
                                            class="px-2.5 py-1 text-xs font-semibold text-rose-800 bg-rose-100 hover:bg-rose-200 rounded-md transition min-h-[36px]">
                                        Tolak
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-gray-400">
                                Tidak ada permohonan pinjaman yang sedang menunggu persetujuan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Mutasi Kas Terkini -->
    <div class="bg-white rounded-xl border border-gray-200/90 shadow-xs overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-gray-200 flex items-center justify-between">
            <div>
                <h2 class="text-sm sm:text-base font-bold text-gray-900 leading-tight">Mutasi Kas Terakhir</h2>
                <p class="text-[11px] sm:text-xs text-gray-500 mt-0.5">5 transaksi penerimaan dan pengeluaran kas terkini.</p>
            </div>
            <a href="{{ route('admin.keuangan.index') }}" class="text-xs font-semibold text-[#CEA042] hover:underline">
                Buka Buku Kas &rarr;
            </a>
        </div>

        <!-- TAMPILAN MOBILE: KARTU KAS TERKINI (block md:hidden) -->
        <div class="block md:hidden p-4 space-y-3">
            @forelse($kasTerbaru as $kas)
                <div class="bg-gray-50/80 rounded-xl p-3.5 border border-gray-200 space-y-2">
                    <div class="flex items-center justify-between pb-1.5 border-b border-gray-200/60">
                        <span class="text-xs text-gray-500 font-medium">
                            {{ $kas->tanggal->format('d/m/Y') }}
                        </span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $kas->jenis === 'masuk' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                            {{ $kas->jenis === 'masuk' ? 'Kas Masuk' : 'Kas Keluar' }}
                        </span>
                    </div>
                    <div class="flex items-baseline justify-between">
                        <div class="text-xs font-bold text-gray-800">{{ $kas->kategori }}</div>
                        <div class="text-sm font-extrabold {{ $kas->jenis === 'masuk' ? 'text-emerald-700' : 'text-rose-700' }}">
                            {{ $kas->jenis === 'masuk' ? '+' : '-' }} Rp {{ number_format($kas->nominal, 0, ',', '.') }}
                        </div>
                    </div>
                    <p class="text-xs text-gray-600 bg-white p-2 rounded-lg border border-gray-100">
                        {{ $kas->keterangan }}
                    </p>
                </div>
            @empty
                <div class="p-6 text-center text-gray-400 text-xs">
                    Belum ada transaksi kas yang dicatat.
                </div>
            @endforelse
        </div>

        <!-- TAMPILAN DESKTOP: TABEL KAS (hidden md:block) -->
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-left text-xs">
                <thead class="bg-gray-50 text-gray-600 font-semibold uppercase tracking-wider">
                    <tr>
                        <th class="py-3 px-4">Tanggal</th>
                        <th class="py-3 px-4">Kode</th>
                        <th class="py-3 px-4">Jenis</th>
                        <th class="py-3 px-4">Kategori</th>
                        <th class="py-3 px-4">Keterangan</th>
                        <th class="py-3 px-4 text-right">Nominal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($kasTerbaru as $kas)
                        <tr class="hover:bg-gray-50/80 transition">
                            <td class="py-3 px-4 whitespace-nowrap text-gray-700 font-medium">
                                {{ $kas->tanggal->format('d/m/Y') }}
                            </td>
                            <td class="py-3 px-4 font-mono text-gray-500 text-[11px] whitespace-nowrap">
                                {{ $kas->kode_transaksi }}
                            </td>
                            <td class="py-3 px-4 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold {{ $kas->jenis === 'masuk' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                    {{ $kas->jenis === 'masuk' ? 'Masuk' : 'Keluar' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 font-medium text-gray-800 whitespace-nowrap">
                                {{ $kas->kategori }}
                            </td>
                            <td class="py-3 px-4 text-gray-600 max-w-xs truncate">
                                {{ $kas->keterangan }}
                            </td>
                            <td class="py-3 px-4 text-right font-bold whitespace-nowrap {{ $kas->jenis === 'masuk' ? 'text-emerald-700' : 'text-rose-700' }}">
                                {{ $kas->jenis === 'masuk' ? '+' : '-' }} Rp {{ number_format($kas->nominal, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-400">
                                Belum ada transaksi kas yang dicatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Modal Tolak Pinjaman (Bottom Sheet Ramah HP) -->
<div id="modal-tolak" class="fixed inset-0 z-50 hidden bg-gray-900/60 backdrop-blur-xs flex items-end sm:items-center justify-center p-0 sm:p-4 overflow-y-auto">
    <div class="bg-white rounded-t-3xl sm:rounded-2xl max-w-md w-full p-5 sm:p-6 shadow-2xl border border-gray-200 max-h-[90vh] overflow-y-auto">
        <!-- Drag Handle Indicator untuk Mobile Phone -->
        <div class="w-12 h-1.5 bg-gray-300 rounded-full mx-auto mb-3 sm:hidden"></div>

        <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-3">
            <div>
                <h3 class="text-base font-bold text-gray-900 leading-tight">Tolak Permohonan Pinjam</h3>
                <p class="text-xs text-gray-500 mt-0.5" id="modal-tolak-info"></p>
            </div>
            <button type="button" onclick="tutupModalTolak()" class="text-gray-400 hover:text-gray-700 p-2 rounded-xl focus:outline-none min-h-[44px] min-w-[44px] flex items-center justify-center bg-gray-100 sm:bg-transparent">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form id="form-tolak" method="POST" action="">
            @csrf
            @method('PATCH')
            <div class="mb-4">
                <label for="catatan_admin" class="block text-xs font-semibold text-gray-700 mb-1">
                    Alasan Penolakan <span class="text-rose-500">*</span>
                </label>
                <textarea name="catatan_admin" id="catatan_admin" rows="3" required
                          placeholder="Jelaskan alasan penolakan (misal: barang sedang dalam pemeliharaan atau diprioritaskan untuk acara dusun)"
                          class="w-full text-base sm:text-xs rounded-xl border-gray-300 focus:border-rose-500 focus:ring-1 focus:ring-rose-500 py-2.5 px-3.5 shadow-xs"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-2 border-t border-gray-100">
                <button type="button" onclick="tutupModalTolak()" class="px-4 py-2.5 text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition min-h-[44px]">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 active:bg-rose-800 rounded-xl shadow-xs transition min-h-[44px]">
                    Tolak Permohonan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function bukaModalTolak(id, kode) {
        document.getElementById('form-tolak').action = `/admin/peminjaman/${id}/tolak`;
        document.getElementById('modal-tolak-info').textContent = `Berikan alasan penolakan untuk kode tiket ${kode}:`;
        document.getElementById('modal-tolak').classList.remove('hidden');
    }
    function tutupModalTolak() {
        document.getElementById('modal-tolak').classList.add('hidden');
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            tutupModalTolak();
        }
    });
</script>
@endpush
@endsection

