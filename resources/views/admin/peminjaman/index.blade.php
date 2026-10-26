@extends('layouts.admin')

@section('title', 'Manajemen Permohonan & Approval Peminjaman')

@section('content')
<div class="space-y-4 sm:space-y-6">

    <!-- Header -->
    <div class="bg-white rounded-xl border border-gray-200/90 p-4 sm:p-5 shadow-xs">
        <h1 class="text-lg sm:text-2xl font-bold text-gray-900 tracking-tight leading-tight">
            Persetujuan & Pengembalian Barang
        </h1>
        <p class="text-xs text-gray-500 mt-1">
            Verifikasi peminjaman warga lewat HP, kurangi stok otomatis saat disetujui, dan konfirmasi pengembalian barang.
        </p>
    </div>

    <!-- Status Tabs (Horizontal Scroll on Mobile) -->
    <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-xs no-scrollbar -mx-1 px-1">
        <a href="{{ route('admin.peminjaman.index', ['status' => 'menunggu']) }}"
           class="whitespace-nowrap px-3 py-2 rounded-lg font-semibold transition flex items-center gap-1.5 min-h-[44px] {{ $status === 'menunggu' ? 'bg-[#CEA042] text-white shadow-xs' : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-200' }}">
            <span>Menunggu Approval</span>
            <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $status === 'menunggu' ? 'bg-amber-700 text-white' : 'bg-amber-100 text-amber-800' }}">
                {{ $jumlahMenunggu }}
            </span>
        </a>

        <a href="{{ route('admin.peminjaman.index', ['status' => 'disetujui']) }}"
           class="whitespace-nowrap px-3 py-2 rounded-lg font-semibold transition flex items-center gap-1.5 min-h-[44px] {{ $status === 'disetujui' ? 'bg-[#CEA042] text-white shadow-xs' : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-200' }}">
            <span>Sedang Dipinjam</span>
            <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $status === 'disetujui' ? 'bg-amber-700 text-white' : 'bg-emerald-100 text-emerald-800' }}">
                {{ $jumlahDisetujui }}
            </span>
        </a>

        <a href="{{ route('admin.peminjaman.index', ['status' => 'dikembalikan']) }}"
           class="whitespace-nowrap px-3 py-2 rounded-lg font-semibold transition flex items-center gap-1.5 min-h-[44px] {{ $status === 'dikembalikan' ? 'bg-[#CEA042] text-white shadow-xs' : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-200' }}">
            <span>Selesai (Kembali)</span>
            <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $status === 'dikembalikan' ? 'bg-amber-700 text-white' : 'bg-blue-100 text-blue-800' }}">
                {{ $jumlahDikembalikan }}
            </span>
        </a>

        <a href="{{ route('admin.peminjaman.index', ['status' => 'ditolak']) }}"
           class="whitespace-nowrap px-3 py-2 rounded-lg font-semibold transition flex items-center gap-1.5 min-h-[44px] {{ $status === 'ditolak' ? 'bg-[#CEA042] text-white shadow-xs' : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-200' }}">
            <span>Ditolak</span>
            <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $status === 'ditolak' ? 'bg-amber-700 text-white' : 'bg-rose-100 text-rose-800' }}">
                {{ $jumlahDitolak }}
            </span>
        </a>

        <a href="{{ route('admin.peminjaman.index', ['status' => 'semua']) }}"
           class="whitespace-nowrap px-3 py-2 rounded-lg font-semibold transition min-h-[44px] {{ $status === 'semua' ? 'bg-[#CEA042] text-white shadow-xs' : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-200' }}">
            Semua
        </a>
    </div>

    <!-- TAMPILAN MOBILE: KARTU VERTIKAL RAMAH HP (block md:hidden) -->
    <div class="block md:hidden space-y-3.5">
        @forelse($daftarPinjam as $p)
            <div class="bg-white rounded-xl border border-gray-200/90 p-4 shadow-xs space-y-3">
                <div class="flex items-center justify-between pb-2.5 border-b border-gray-100">
                    <span class="font-mono font-bold text-sm text-[#CEA042]">
                        {{ $p->kode_pinjam }}
                    </span>
                    @if($p->status === 'menunggu')
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                            Menunggu Persetujuan
                        </span>
                    @elseif($p->status === 'disetujui')
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            Sedang Dipinjam
                        </span>
                    @elseif($p->status === 'dikembalikan')
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                            Sudah Dikembalikan
                        </span>
                    @elseif($p->status === 'ditolak')
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                            Ditolak
                        </span>
                    @endif
                </div>

                <!-- Informasi Peminjam & Barang -->
                <div class="space-y-1.5 text-xs">
                    <div>
                        <span class="text-gray-500">Peminjam:</span>
                        <span class="font-bold text-gray-900">{{ $p->nama_peminjam }}</span> ({{ $p->alamat_rt_rw }})
                    </div>
                    <div>
                        <span class="text-gray-500">Barang:</span>
                        <span class="font-bold text-gray-900">{{ $p->barang->nama_barang }}</span>
                        <span class="bg-gray-100 text-gray-800 font-semibold px-1.5 py-0.5 rounded ml-1">{{ $p->jumlah_pinjam }} unit</span>
                    </div>
                    <div class="text-gray-600">
                        <span class="text-gray-500">Jadwal:</span>
                        {{ $p->tanggal_pinjam->format('d/m/Y') }} s/d {{ $p->rencana_tanggal_kembali->format('d/m/Y') }}
                    </div>
                    <div class="text-gray-600 bg-gray-50 p-2 rounded-lg">
                        <strong class="text-gray-700 block text-[11px]">Keperluan:</strong>
                        {{ $p->tujuan_peminjaman }}
                    </div>

                    @if($p->catatan_admin)
                        <div class="text-[11px] text-gray-600 bg-amber-50/70 p-2 rounded-lg border border-amber-100">
                            <strong>Catatan Pengurus:</strong> {{ $p->catatan_admin }}
                        </div>
                    @endif
                </div>

                <!-- Tombol Hubungi WhatsApp Peminjam (Sangat Ramah HP) -->
                @php
                    $waClean = preg_replace('/[^0-9]/', '', $p->kontak_peminjam);
                    if (str_starts_with($waClean, '0')) {
                        $waClean = '62' . substr($waClean, 1);
                    }
                @endphp
                <div class="pt-1">
                    <a href="https://wa.me/{{ $waClean }}?text=Halo%20{{ rawurlencode($p->nama_peminjam) }},%20saya%20pengurus%20inventaris%20Dusun%20Demangan%20terkait%20pinjaman%20{{ rawurlencode($p->kode_pinjam) }}"
                       target="_blank"
                       class="w-full flex items-center justify-center gap-1.5 py-2 px-3 text-xs font-semibold text-emerald-800 bg-emerald-50 hover:bg-emerald-100 rounded-lg border border-emerald-200 transition min-h-[44px]">
                        <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                        Hubungi WhatsApp ({{ $p->kontak_peminjam }})
                    </a>
                </div>

                <!-- Tombol Aksi di Layar HP (Tinggi min 44px) -->
                @if($p->status === 'menunggu')
                    <div class="grid grid-cols-2 gap-2 pt-1 border-t border-gray-100">
                        <form action="{{ route('admin.peminjaman.setujui', $p) }}" method="POST"
                              onsubmit="return confirm('Setujui peminjaman {{ addslashes($p->barang->nama_barang) }}? Stok tersedia otomatis berkurang {{ $p->jumlah_pinjam }} unit.');">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="w-full py-2.5 px-3 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg shadow-xs min-h-[44px]">
                                Setujui
                            </button>
                        </form>

                        <button type="button" onclick="bukaModalTolak({{ $p->id }}, '{{ $p->kode_pinjam }}')"
                                class="w-full py-2.5 px-3 text-xs font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 rounded-lg border border-rose-200 min-h-[44px]">
                            Tolak
                        </button>
                    </div>
                @elseif($p->status === 'disetujui')
                    <div class="pt-1 border-t border-gray-100">
                        <form action="{{ route('admin.peminjaman.kembalikan', $p) }}" method="POST"
                              onsubmit="return confirm('Konfirmasi bahwa barang fisik telah dikembalikan ke pos/gudang?');">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="w-full py-2.5 px-3 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-xs min-h-[44px]">
                                Konfirmasi Barang Kembali
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        @empty
            <div class="bg-white rounded-xl border border-gray-200 p-8 text-center text-gray-400 text-xs">
                Tidak ada data permohonan pinjam pada tab ini.
            </div>
        @endforelse
    </div>

    <!-- TAMPILAN DESKTOP: TABEL PENUH (hidden md:block) -->
    <div class="hidden md:block bg-white rounded-xl border border-gray-200/90 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-left text-xs">
                <thead class="bg-gray-50 text-gray-600 font-semibold uppercase tracking-wider">
                    <tr>
                        <th class="py-3.5 px-4">Tiket</th>
                        <th class="py-3.5 px-4">Peminjam</th>
                        <th class="py-3.5 px-4">Barang</th>
                        <th class="py-3.5 px-4 text-center">Jumlah</th>
                        <th class="py-3.5 px-4">Jadwal Pinjam</th>
                        <th class="py-3.5 px-4">Keperluan</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($daftarPinjam as $p)
                        <tr class="hover:bg-gray-50/80 transition">
                            <td class="py-3.5 px-4 font-mono font-bold text-[#CEA042] whitespace-nowrap">
                                {{ $p->kode_pinjam }}
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <div class="font-bold text-gray-900">{{ $p->nama_peminjam }}</div>
                                <div class="text-[11px] text-gray-500">{{ $p->alamat_rt_rw }}</div>
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $p->kontak_peminjam) }}" target="_blank"
                                   class="text-[11px] font-semibold text-emerald-700 hover:underline">
                                    WA: {{ $p->kontak_peminjam }}
                                </a>
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <div class="font-semibold text-gray-900">{{ $p->barang->nama_barang }}</div>
                                <div class="text-[11px] text-gray-500">Stok: {{ $p->barang->jumlah_tersedia }} unit</div>
                            </td>
                            <td class="py-3.5 px-4 text-center font-bold text-gray-900 whitespace-nowrap">
                                {{ $p->jumlah_pinjam }} unit
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap text-gray-600">
                                <div>{{ $p->tanggal_pinjam->format('d/m/Y') }}</div>
                                <div class="text-gray-400">s/d {{ $p->rencana_tanggal_kembali->format('d/m/Y') }}</div>
                            </td>
                            <td class="py-3.5 px-4 text-gray-600 max-w-xs">
                                <div>{{ $p->tujuan_peminjaman }}</div>
                                @if($p->catatan_admin)
                                    <div class="text-[10px] text-gray-500 italic mt-0.5">Catatan: {{ $p->catatan_admin }}</div>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                @if($p->status === 'menunggu')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                        Menunggu
                                    </span>
                                @elseif($p->status === 'disetujui')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Dipinjam
                                    </span>
                                @elseif($p->status === 'dikembalikan')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                        Selesai
                                    </span>
                                @elseif($p->status === 'ditolak')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                        Ditolak
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                @if($p->status === 'menunggu')
                                    <div class="inline-flex items-center gap-1.5">
                                        <form action="{{ route('admin.peminjaman.setujui', $p) }}" method="POST"
                                              onsubmit="return confirm('Setujui peminjaman ini?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="px-3 py-1.5 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg min-h-[36px]">
                                                Setujui
                                            </button>
                                        </form>

                                        <button type="button" onclick="bukaModalTolak({{ $p->id }}, '{{ $p->kode_pinjam }}')"
                                                class="px-2.5 py-1.5 text-xs font-medium text-rose-700 bg-rose-50 hover:bg-rose-100 rounded-lg min-h-[36px]">
                                            Tolak
                                        </button>
                                    </div>
                                @elseif($p->status === 'disetujui')
                                    <form action="{{ route('admin.peminjaman.kembalikan', $p) }}" method="POST"
                                          onsubmit="return confirm('Konfirmasi barang fisik telah kembali ke gudang?');">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="px-3 py-1.5 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg min-h-[36px]">
                                            Barang Kembali
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-gray-400">
                                Tidak ada data permohonan pinjaman.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="pt-2">
        {{ $daftarPinjam->links() }}
    </div>

</div>

<!-- Modal Tolak Peminjaman (Bottom Sheet Ramah HP) -->
<div id="modal-tolak" class="fixed inset-0 z-50 hidden bg-gray-900/60 backdrop-blur-xs flex items-end sm:items-center justify-center p-0 sm:p-4 overflow-y-auto">
    <div class="bg-white rounded-t-3xl sm:rounded-2xl max-w-md w-full p-5 sm:p-6 shadow-2xl border border-gray-200 max-h-[90vh] overflow-y-auto">
        <!-- Drag Handle Indicator untuk Mobile Phone -->
        <div class="w-12 h-1.5 bg-gray-300 rounded-full mx-auto mb-3 sm:hidden"></div>

        <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-3">
            <div>
                <h3 class="text-base font-bold text-gray-900 leading-tight">Tolak Permohonan Pinjaman</h3>
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
                          placeholder="Jelaskan alasan penolakan agar peminjam dapat memahami..."
                          class="w-full text-base sm:text-xs rounded-xl border-gray-300 focus:border-rose-500 focus:ring-1 focus:ring-rose-500 py-2.5 px-3.5 shadow-xs"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-2 border-t border-gray-100">
                <button type="button" onclick="tutupModalTolak()" class="px-4 py-2.5 text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition min-h-[44px]">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 active:bg-rose-800 rounded-xl shadow-xs min-h-[44px]">
                    Kirim Penolakan
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

