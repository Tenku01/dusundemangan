@extends('layouts.admin')

@section('title', 'Dashboard Ringkasan Pengurus')

@section('content')
<div class="space-y-5 sm:space-y-6">

    <!-- Welcome Header -->
    <div class="card-surface p-5 sm:p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <span class="text-xs font-bold text-emas-800 uppercase tracking-wider block mb-1">
                Selamat Datang, {{ auth()->user()->name }}
            </span>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">
                Ringkasan Operasional Inventaris &amp; Keuangan Dusun
            </h1>
            <p class="text-xs sm:text-sm text-gray-600 mt-1">
                Kelola inventaris aset dusun, verifikasi antrean pinjaman warga, dan pantau pembukuan kas.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2 self-stretch sm:self-auto">
            @if(auth()->user()->isSuperadmin())
                <a href="{{ route('status.index') }}" class="btn-secondary flex-1 sm:flex-initial px-4 text-xs">
                    Status &amp; Log
                </a>
            @endif
            <a href="{{ route('admin.barang.index') }}" class="btn-primary flex-1 sm:flex-initial px-4 text-xs">
                + Tambah Barang
            </a>
            <a href="{{ route('admin.keuangan.index') }}" class="btn-gold-soft flex-1 sm:flex-initial px-4 text-xs">
                + Catat Kas
            </a>
        </div>
    </div>

    <!-- KPI Grid Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">
        <div class="card-surface p-5 border-t-2 border-t-emerald-500">
            <span class="text-xs font-bold text-gray-500 uppercase tracking-wider block mb-1">Saldo Kas</span>
            <div class="text-2xl sm:text-3xl font-extrabold text-emerald-700 tracking-tight tabular-money">
                Rp {{ number_format($saldoKasSaatIni, 0, ',', '.') }}
            </div>
            <div class="mt-2.5 pt-2 border-t border-gray-100 text-xs text-gray-600">
                <span>Bulan Ini Masuk: <strong class="text-emerald-800 tabular-money">Rp {{ number_format($pemasukanBulanIni, 0, ',', '.') }}</strong></span>
            </div>
        </div>

        <div class="card-surface p-5 border-t-2 border-t-amber-500">
            <span class="text-xs font-bold text-gray-500 uppercase tracking-wider block mb-1">Permohonan Pinjam Menunggu</span>
            <div class="text-2xl sm:text-3xl font-extrabold text-amber-700 tracking-tight flex items-baseline gap-2 tabular-money">
                {{ $pinjamanMenunggu }} <span class="text-xs font-semibold text-gray-500">permohonan</span>
            </div>
            <div class="mt-2.5 pt-2 border-t border-gray-100 text-xs text-gray-600">
                <span>Sedang dipinjam aktif: <strong class="text-gray-900 tabular-money">{{ $pinjamanAktif }} transaksi</strong></span>
            </div>
        </div>

        <div class="card-surface p-5 border-t-2 border-t-[#CEA042] sm:col-span-2 lg:col-span-1">
            <span class="text-xs font-bold text-gray-500 uppercase tracking-wider block mb-1">Unit Barang Tersedia</span>
            <div class="text-2xl sm:text-3xl font-extrabold text-emas-800 tracking-tight tabular-money">
                {{ $totalUnitTersedia }} <span class="text-xs font-semibold text-gray-500">/ {{ $totalUnitAset }} unit fisik</span>
            </div>
            <div class="mt-2.5 pt-2 border-t border-gray-100 text-xs text-gray-600">
                Tersebar di <strong class="text-gray-900 tabular-money">{{ $totalJenisBarang }}</strong> kategori barang dusun
            </div>
        </div>
    </div>

    <!-- Antrean Pinjaman yang Butuh Tindakan Segera -->
    <div class="card-surface overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-gray-200 flex items-center justify-between gap-3">
            <div>
                <h2 class="text-base font-bold text-gray-900 leading-tight">Antrean Permohonan Pinjaman Terbaru</h2>
                <p class="text-xs text-gray-600 mt-0.5">Permohonan warga yang perlu disetujui atau ditolak.</p>
            </div>
            <a href="{{ route('admin.peminjaman.index') }}" class="text-xs font-bold text-emas-800 hover:text-emas-950 hover:underline shrink-0">
                Lihat Semua Antrean &rarr;
            </a>
        </div>

        <!-- TAMPILAN MOBILE: KARTU ANTREAN -->
        <div class="block md:hidden p-4 space-y-3">
            @forelse($antreanTerbaru as $p)
                <div class="bg-[#F8F7F4] rounded-xl p-3.5 border border-gray-200 space-y-2.5">
                    <div class="flex items-center justify-between pb-2 border-b border-gray-200/70">
                        <span class="font-mono font-bold text-xs text-emas-800">
                            {{ $p->kode_pinjam }}
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-900">
                            Menunggu Approval
                        </span>
                    </div>

                    <div class="text-xs space-y-1">
                        <div>
                            <span class="text-gray-600">Peminjam:</span>
                            <span class="font-bold text-gray-900">{{ $p->nama_peminjam }}</span> ({{ $p->alamat_rt_rw }})
                        </div>
                        <div>
                            <span class="text-gray-600">Barang:</span>
                            <span class="font-bold text-gray-900">{{ $p->barang->nama_barang }}</span>
                            <span class="bg-gray-200 text-gray-800 font-semibold px-1.5 py-0.5 rounded ml-1 tabular-money">{{ $p->jumlah_pinjam }} unit</span>
                        </div>
                        <div class="text-gray-600 tabular-money">
                            <span class="text-gray-600">Jadwal:</span>
                            {{ $p->tanggal_pinjam->format('d/m') }} s/d {{ $p->rencana_tanggal_kembali->format('d/m/Y') }}
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2 pt-2 border-t border-gray-200/70">
                        <form action="{{ route('admin.peminjaman.setujui', $p) }}" method="POST"
                              onsubmit="return confirm('Setujui permohonan pinjam ini? Stok barang akan otomatis berkurang.');">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="w-full py-2.5 px-3 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-sm min-h-[44px] transition">
                                Setujui
                            </button>
                        </form>

                        <button type="button" onclick="bukaModalTolak({{ $p->id }}, '{{ $p->kode_pinjam }}')"
                                class="btn-danger-soft w-full text-xs">
                            Tolak
                        </button>
                    </div>
                </div>
            @empty
                <div class="p-6 text-center text-gray-500 text-xs">
                    Tidak ada permohonan pinjaman yang sedang menunggu persetujuan.
                </div>
            @endforelse
        </div>

        <!-- TAMPILAN DESKTOP: TABEL ANTREAN -->
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-left text-xs">
                <thead class="bg-[#F8F7F4] text-gray-600 font-bold uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="py-3.5 px-4">Tiket</th>
                        <th class="py-3.5 px-4">Peminjam</th>
                        <th class="py-3.5 px-4">Barang</th>
                        <th class="py-3.5 px-4">Jumlah</th>
                        <th class="py-3.5 px-4">Jadwal Pinjam</th>
                        <th class="py-3.5 px-4">Keperluan</th>
                        <th class="py-3.5 px-4 text-center">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($antreanTerbaru as $p)
                        <tr class="hover:bg-emas-50/40 transition">
                            <td class="py-3.5 px-4 font-mono font-bold text-emas-800 whitespace-nowrap">
                                {{ $p->kode_pinjam }}
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <div class="font-bold text-gray-900">{{ $p->nama_peminjam }}</div>
                                <div class="text-[11px] text-gray-500">{{ $p->alamat_rt_rw }} &bull; {{ $p->kontak_peminjam }}</div>
                            </td>
                            <td class="py-3.5 px-4 font-semibold text-gray-900 whitespace-nowrap">
                                {{ $p->barang->nama_barang }}
                            </td>
                            <td class="py-3.5 px-4 font-bold text-gray-900 whitespace-nowrap tabular-money">
                                {{ $p->jumlah_pinjam }} unit
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap text-gray-600 tabular-money">
                                {{ $p->tanggal_pinjam->format('d/m') }} s/d {{ $p->rencana_tanggal_kembali->format('d/m/Y') }}
                            </td>
                            <td class="py-3.5 px-4 text-gray-600 max-w-xs truncate">
                                {{ $p->tujuan_peminjaman }}
                            </td>
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    <form action="{{ route('admin.peminjaman.setujui', $p) }}" method="POST" onsubmit="return confirm('Setujui permohonan pinjam ini? Stok barang akan otomatis berkurang.');">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="px-3 py-1.5 text-xs font-bold text-emerald-900 bg-emerald-100 hover:bg-emerald-200 rounded-lg transition min-h-[36px]">
                                            Setujui
                                        </button>
                                    </form>

                                    <button type="button" onclick="bukaModalTolak({{ $p->id }}, '{{ $p->kode_pinjam }}')"
                                            class="px-3 py-1.5 text-xs font-bold text-rose-800 bg-rose-50 hover:bg-rose-100 border border-rose-200 rounded-lg transition min-h-[36px]">
                                        Tolak
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-10 text-center text-gray-500">
                                Tidak ada permohonan pinjaman yang sedang menunggu persetujuan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Mutasi Kas Terkini -->
    <div class="card-surface overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-gray-200 flex items-center justify-between gap-3">
            <div>
                <h2 class="text-base font-bold text-gray-900 leading-tight">Mutasi Kas Terakhir</h2>
                <p class="text-xs text-gray-600 mt-0.5">5 transaksi penerimaan dan pengeluaran kas terkini.</p>
            </div>
            <a href="{{ route('admin.keuangan.index') }}" class="text-xs font-bold text-emas-800 hover:text-emas-950 hover:underline shrink-0">
                Buka Buku Kas &rarr;
            </a>
        </div>

        <!-- TAMPILAN MOBILE: KARTU KAS TERKINI -->
        <div class="block md:hidden p-4 space-y-3">
            @forelse($kasTerbaru as $kas)
                <div class="bg-[#F8F7F4] rounded-xl p-3.5 border border-gray-200 space-y-2">
                    <div class="flex items-center justify-between pb-1.5 border-b border-gray-200/70">
                        <span class="text-xs text-gray-600 font-medium tabular-money">
                            {{ $kas->tanggal->format('d/m/Y') }}
                        </span>
                        <span class="px-2 py-0.5 rounded-md text-[11px] font-bold {{ $kas->jenis === 'masuk' ? 'bg-emerald-100 text-emerald-900' : 'bg-rose-100 text-rose-900' }}">
                            {{ $kas->jenis === 'masuk' ? 'Kas Masuk' : 'Kas Keluar' }}
                        </span>
                    </div>
                    <div class="flex items-baseline justify-between gap-2">
                        <div class="text-xs font-bold text-gray-900">{{ $kas->kategori }}</div>
                        <div class="text-sm font-extrabold tabular-money shrink-0 {{ $kas->jenis === 'masuk' ? 'text-emerald-700' : 'text-rose-700' }}">
                            {{ $kas->jenis === 'masuk' ? '+' : '-' }} Rp {{ number_format($kas->nominal, 0, ',', '.') }}
                        </div>
                    </div>
                    <p class="text-xs text-gray-600 bg-white p-2.5 rounded-lg border border-gray-100">
                        {{ $kas->keterangan }}
                    </p>
                </div>
            @empty
                <div class="p-6 text-center text-gray-500 text-xs">
                    Belum ada transaksi kas yang dicatat.
                </div>
            @endforelse
        </div>

        <!-- TAMPILAN DESKTOP: TABEL KAS -->
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-left text-xs">
                <thead class="bg-[#F8F7F4] text-gray-600 font-bold uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="py-3.5 px-4">Tanggal</th>
                        <th class="py-3.5 px-4">Kode</th>
                        <th class="py-3.5 px-4">Jenis</th>
                        <th class="py-3.5 px-4">Kategori</th>
                        <th class="py-3.5 px-4">Keterangan</th>
                        <th class="py-3.5 px-4 text-right">Nominal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($kasTerbaru as $kas)
                        <tr class="hover:bg-emas-50/40 transition">
                            <td class="py-3.5 px-4 whitespace-nowrap text-gray-700 font-medium tabular-money">
                                {{ $kas->tanggal->format('d/m/Y') }}
                            </td>
                            <td class="py-3.5 px-4 font-mono text-gray-500 text-[11px] whitespace-nowrap">
                                {{ $kas->kode_transaksi }}
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded-md text-[11px] font-bold {{ $kas->jenis === 'masuk' ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-rose-50 text-rose-800 border border-rose-200' }}">
                                    {{ $kas->jenis === 'masuk' ? 'Masuk' : 'Keluar' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 font-semibold text-gray-900 whitespace-nowrap">
                                {{ $kas->kategori }}
                            </td>
                            <td class="py-3.5 px-4 text-gray-600 max-w-xs truncate">
                                {{ $kas->keterangan }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-bold whitespace-nowrap tabular-money {{ $kas->jenis === 'masuk' ? 'text-emerald-700' : 'text-rose-700' }}">
                                {{ $kas->jenis === 'masuk' ? '+' : '-' }} Rp {{ number_format($kas->nominal, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-gray-500">
                                Belum ada transaksi kas yang dicatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Modal Tolak Pinjaman -->
<div id="modal-tolak"
     onclick="if (event.target === this) tutupModalTolak()"
     role="dialog"
     aria-modal="true"
     aria-labelledby="modal-tolak-title"
     class="modal-backdrop hidden">
    <div class="modal-panel max-w-md p-5 sm:p-6">
        <div class="w-12 h-1.5 bg-gray-300 rounded-full mx-auto mb-3 sm:hidden"></div>

        <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-3">
            <div>
                <h3 id="modal-tolak-title" class="text-base font-bold text-gray-900 leading-tight">Tolak Permohonan Pinjam</h3>
                <p class="text-xs text-gray-600 mt-0.5" id="modal-tolak-info"></p>
            </div>
            <button type="button" onclick="tutupModalTolak()" aria-label="Tutup modal penolakan"
                    class="text-gray-500 hover:text-gray-800 p-2 rounded-xl min-h-[44px] min-w-[44px] flex items-center justify-center bg-gray-100 sm:bg-transparent hover:bg-gray-100 transition">
                <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form id="form-tolak" method="POST" action="">
            @csrf
            @method('PATCH')
            <div class="mb-4">
                <label for="catatan_admin" class="block text-xs font-semibold text-gray-800 mb-1">
                    Alasan Penolakan <span class="text-rose-600">*</span>
                </label>
                <textarea name="catatan_admin" id="catatan_admin" rows="3" required
                          placeholder="Jelaskan alasan penolakan (misal: barang sedang dalam pemeliharaan atau diprioritaskan untuk acara dusun)"
                          class="input-field py-2.5 focus:border-rose-500 focus:ring-rose-500/25"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-gray-100">
                <button type="button" onclick="tutupModalTolak()" class="btn-subtle px-4 text-xs">
                    Batal
                </button>
                <button type="submit" class="btn-danger px-5 text-xs">
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
