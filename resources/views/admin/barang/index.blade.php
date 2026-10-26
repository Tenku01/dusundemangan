@extends('layouts.admin')

@section('title', 'Manajemen Data Inventaris Barang')

@section('content')
<div class="space-y-4 sm:space-y-6">

    <!-- Header & Action Button -->
    <div class="bg-white rounded-xl border border-gray-200/90 p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-lg sm:text-2xl font-bold text-gray-900 tracking-tight leading-tight">
                Data Inventaris Barang Dusun
            </h1>
            <p class="text-xs text-gray-500 mt-0.5">
                Kelola perlengkapan dusun, jumlah unit, kondisi fisik, dan lokasi pos penyimpanan.
            </p>
        </div>

        <button type="button" onclick="bukaModalTambah()"
                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 text-xs font-bold text-white bg-[#CEA042] hover:bg-[#BA8832] rounded-lg shadow-xs transition focus:outline-none min-h-[44px]">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            + Tambah Barang Baru
        </button>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white rounded-xl border border-gray-200/90 p-3.5 shadow-xs">
        <form method="GET" action="{{ route('admin.barang.index') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5">
            <div class="flex-1">
                <input type="text" name="cari" value="{{ $cari }}" placeholder="Cari nama barang, kode, lokasi..."
                       class="w-full text-base sm:text-xs rounded-lg border-gray-300 focus:border-[#CEA042] focus:ring-1 focus:ring-[#CEA042] py-2 px-3 shadow-xs min-h-[44px] sm:min-h-0">
            </div>

            <div class="w-full sm:w-44">
                <select name="kategori" onchange="this.form.submit()"
                        class="w-full text-base sm:text-xs rounded-lg border-gray-300 focus:border-[#CEA042] focus:ring-1 focus:ring-[#CEA042] py-2 px-2.5 shadow-xs min-h-[44px] sm:min-h-0">
                    <option value="Semua">Semua Kategori</option>
                    @foreach($kategoriList as $kat)
                        <option value="{{ $kat }}" {{ $kategori === $kat ? 'selected' : '' }}>{{ $kat }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="w-full sm:w-auto px-4 py-2 text-xs font-semibold text-white bg-[#CEA042] hover:bg-[#BA8832] rounded-lg shadow-xs min-h-[44px]">
                Filter
            </button>

            @if($cari || ($kategori && $kategori !== 'Semua'))
                <a href="{{ route('admin.barang.index') }}" class="text-xs text-rose-600 hover:underline text-center py-1">
                    Reset Filter
                </a>
            @endif
        </form>
    </div>

    <!-- TAMPILAN MOBILE: KARTU INVENTARIS RAMAH HP (block md:hidden) -->
    <div class="block md:hidden space-y-3.5">
        @forelse($daftarBarang as $b)
            <div class="bg-white rounded-xl border border-gray-200/90 p-4 shadow-xs space-y-3">
                <div class="flex items-start justify-between gap-2 pb-2.5 border-b border-gray-100">
                    <div>
                        <span class="font-mono text-xs font-bold text-[#CEA042] block">
                            {{ $b->kode_barang }}
                        </span>
                        <h3 class="font-bold text-gray-900 text-sm mt-0.5">{{ $b->nama_barang }}</h3>
                        <span class="text-[10px] text-gray-500 bg-gray-100 px-2 py-0.5 rounded font-medium inline-block mt-1">
                            {{ $b->kategori }}
                        </span>
                    </div>

                    <div class="text-right shrink-0">
                        <span class="px-2.5 py-1 rounded-full text-xs font-extrabold block {{ $b->jumlah_tersedia > 0 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                            {{ $b->jumlah_tersedia }} <span class="text-[10px] font-normal">tersedia</span>
                        </span>
                        <span class="text-[10px] text-gray-500 mt-0.5 block">Total: {{ $b->jumlah_total }} unit</span>
                    </div>
                </div>

                <div class="space-y-1 text-xs text-gray-600">
                    <div class="flex justify-between">
                        <span>Kondisi Fisik:</span>
                        <span class="font-semibold text-gray-800">{{ $b->kondisi }}</span>
                    </div>
                    @if($b->lokasi_penyimpanan)
                        <div class="flex justify-between">
                            <span>Lokasi Gudang:</span>
                            <span class="text-gray-800 font-medium">{{ $b->lokasi_penyimpanan }}</span>
                        </div>
                    @endif
                    @if($b->keterangan)
                        <p class="text-[11px] text-gray-500 bg-gray-50 p-2 rounded mt-1">{{ $b->keterangan }}</p>
                    @endif
                </div>

                <!-- Action Buttons Mobile -->
                <div class="grid grid-cols-2 gap-2 pt-2 border-t border-gray-100">
                    <button type="button" onclick='bukaModalEdit(@json($b))'
                            class="w-full py-2 px-3 text-xs font-bold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg min-h-[44px]">
                        Edit Data
                    </button>

                    <form action="{{ route('admin.barang.destroy', $b) }}" method="POST"
                          onsubmit="return confirm('Hapus barang {{ addslashes($b->nama_barang) }} dari inventaris?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full py-2 px-3 text-xs font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 rounded-lg border border-rose-200 min-h-[44px]">
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl border border-gray-200 p-8 text-center text-gray-400 text-xs">
                Belum ada data barang.
            </div>
        @endforelse
    </div>

    <!-- TAMPILAN DESKTOP: TABEL PENUH (hidden md:block) -->
    <div class="hidden md:block bg-white rounded-xl border border-gray-200/90 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-left text-xs">
                <thead class="bg-gray-50 text-gray-600 font-semibold uppercase tracking-wider">
                    <tr>
                        <th class="py-3.5 px-4">Kode</th>
                        <th class="py-3.5 px-4">Nama Barang</th>
                        <th class="py-3.5 px-4">Kategori</th>
                        <th class="py-3.5 px-4 text-center">Total</th>
                        <th class="py-3.5 px-4 text-center">Tersedia</th>
                        <th class="py-3.5 px-4">Kondisi</th>
                        <th class="py-3.5 px-4">Lokasi Pos</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($daftarBarang as $b)
                        <tr class="hover:bg-gray-50/80 transition">
                            <td class="py-3.5 px-4 font-mono font-bold text-[#CEA042] whitespace-nowrap">
                                {{ $b->kode_barang }}
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <div class="font-bold text-gray-900">{{ $b->nama_barang }}</div>
                                <div class="text-[11px] text-gray-500 max-w-xs truncate">{{ $b->keterangan ?? '-' }}</div>
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap text-gray-700">
                                {{ $b->kategori }}
                            </td>
                            <td class="py-3.5 px-4 text-center font-semibold text-gray-900 whitespace-nowrap">
                                {{ $b->jumlah_total }}
                            </td>
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $b->jumlah_tersedia > 0 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                    {{ $b->jumlah_tersedia }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold {{ $b->kondisi === 'Baik' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                                    {{ $b->kondisi }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-gray-600 whitespace-nowrap">
                                {{ $b->lokasi_penyimpanan ?? '-' }}
                            </td>
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    <button type="button" onclick='bukaModalEdit(@json($b))'
                                            class="px-2.5 py-1 text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md transition min-h-[32px]">
                                        Edit
                                    </button>

                                    <form action="{{ route('admin.barang.destroy', $b) }}" method="POST"
                                          onsubmit="return confirm('Hapus barang {{ addslashes($b->nama_barang) }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2.5 py-1 text-xs font-medium text-rose-700 bg-rose-50 hover:bg-rose-100 rounded-md transition min-h-[32px]">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-gray-400">
                                Belum ada data inventaris barang.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="pt-2">
        {{ $daftarBarang->links() }}
    </div>

</div>

<!-- MODAL TAMBAH BARANG (Bottom Sheet Ramah HP) -->
<div id="modal-tambah" class="fixed inset-0 z-50 hidden bg-gray-900/60 backdrop-blur-xs flex items-end sm:items-center justify-center p-0 sm:p-4 overflow-y-auto">
    <div class="bg-white rounded-t-3xl sm:rounded-2xl max-w-lg w-full p-5 sm:p-6 shadow-2xl border border-gray-200 max-h-[92vh] overflow-y-auto">
        <!-- Drag Handle Indicator untuk Mobile Phone -->
        <div class="w-12 h-1.5 bg-gray-300 rounded-full mx-auto mb-3 sm:hidden"></div>

        <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
            <div>
                <h3 class="text-base font-bold text-gray-900 leading-tight">Tambah Barang Inventaris Baru</h3>
                <p class="text-xs text-gray-500 mt-0.5">Catat unit baru aset perlengkapan milik dusun.</p>
            </div>
            <button type="button" onclick="tutupModalTambah()" class="text-gray-400 hover:text-gray-700 p-2 rounded-xl focus:outline-none min-h-[44px] min-w-[44px] flex items-center justify-center bg-gray-100 sm:bg-transparent">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form action="{{ route('admin.barang.store') }}" method="POST" class="space-y-3.5">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Barang <span class="text-rose-500">*</span></label>
                <input type="text" name="nama_barang" required placeholder="Contoh: Genset Listrik 3000 Watt"
                       class="w-full text-base sm:text-xs rounded-xl border-gray-300 focus:border-[#CEA042] focus:ring-1 focus:ring-[#CEA042] py-2.5 px-3.5 shadow-xs min-h-[46px]">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Kategori <span class="text-rose-500">*</span></label>
                    <input type="text" name="kategori" required placeholder="Contoh: Tenda & Terpal" list="list-kategori"
                           class="w-full text-base sm:text-xs rounded-xl border-gray-300 focus:border-[#CEA042] focus:ring-1 focus:ring-[#CEA042] py-2.5 px-3.5 shadow-xs min-h-[46px]">
                    <datalist id="list-kategori">
                        <option value="Tenda & Terpal">
                        <option value="Meja & Kursi">
                        <option value="Elektronik & Sound">
                        <option value="Alat Kebersihan & Taman">
                        <option value="Perkakas & Pertukangan">
                    </datalist>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Jumlah Unit Fisik <span class="text-rose-500">*</span></label>
                    <input type="number" inputmode="numeric" name="jumlah_total" required min="1" value="1"
                           class="w-full text-base sm:text-xs rounded-xl border-gray-300 focus:border-[#CEA042] focus:ring-1 focus:ring-[#CEA042] py-2.5 px-3.5 shadow-xs min-h-[46px]">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Kondisi Barang <span class="text-rose-500">*</span></label>
                    <select name="kondisi" required class="w-full text-base sm:text-xs rounded-xl border-gray-300 focus:border-[#CEA042] focus:ring-1 focus:ring-[#CEA042] py-2.5 px-3.5 shadow-xs min-h-[46px]">
                        <option value="Baik">Baik (Siap Pakai)</option>
                        <option value="Perlu Perbaikan">Perlu Perbaikan</option>
                        <option value="Rusak Ringan">Rusak Ringan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Lokasi Pos Dusun</label>
                    <input type="text" name="lokasi_penyimpanan" placeholder="Contoh: Pos RW 02"
                           class="w-full text-base sm:text-xs rounded-xl border-gray-300 focus:border-[#CEA042] focus:ring-1 focus:ring-[#CEA042] py-2.5 px-3.5 shadow-xs min-h-[46px]">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Keterangan / Kelengkapan</label>
                <textarea name="keterangan" rows="2" placeholder="Spesifikasi ukuran, kelengkapan kabel, dll."
                          class="w-full text-base sm:text-xs rounded-xl border-gray-300 focus:border-[#CEA042] focus:ring-1 focus:ring-[#CEA042] py-2.5 px-3.5 shadow-xs"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-gray-100">
                <button type="button" onclick="tutupModalTambah()" class="px-4 py-2.5 text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition min-h-[44px]">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 text-xs font-bold text-white bg-[#CEA042] hover:bg-[#BA8832] rounded-xl shadow-xs transition min-h-[44px]">
                    Simpan Barang Baru
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT BARANG (Bottom Sheet Ramah HP) -->
<div id="modal-edit" class="fixed inset-0 z-50 hidden bg-gray-900/60 backdrop-blur-xs flex items-end sm:items-center justify-center p-0 sm:p-4 overflow-y-auto">
    <div class="bg-white rounded-t-3xl sm:rounded-2xl max-w-lg w-full p-5 sm:p-6 shadow-2xl border border-gray-200 max-h-[92vh] overflow-y-auto">
        <!-- Drag Handle Indicator untuk Mobile Phone -->
        <div class="w-12 h-1.5 bg-gray-300 rounded-full mx-auto mb-3 sm:hidden"></div>

        <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
            <div>
                <h3 class="text-base font-bold text-gray-900 leading-tight">Perbarui Data Barang</h3>
                <p class="text-xs text-gray-500 mt-0.5">Ubah stok atau kondisi barang inventaris.</p>
            </div>
            <button type="button" onclick="tutupModalEdit()" class="text-gray-400 hover:text-gray-700 p-2 rounded-xl focus:outline-none min-h-[44px] min-w-[44px] flex items-center justify-center bg-gray-100 sm:bg-transparent">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form id="form-edit-barang" method="POST" class="space-y-3.5">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Barang <span class="text-rose-500">*</span></label>
                <input type="text" name="nama_barang" id="edit-nama-barang" required
                       class="w-full text-base sm:text-xs rounded-xl border-gray-300 focus:border-[#CEA042] focus:ring-1 focus:ring-[#CEA042] py-2.5 px-3.5 shadow-xs min-h-[46px]">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Kategori <span class="text-rose-500">*</span></label>
                    <input type="text" name="kategori" id="edit-kategori" required list="list-kategori"
                           class="w-full text-base sm:text-xs rounded-xl border-gray-300 focus:border-[#CEA042] focus:ring-1 focus:ring-[#CEA042] py-2.5 px-3.5 shadow-xs min-h-[46px]">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Jumlah Unit Total <span class="text-rose-500">*</span></label>
                    <input type="number" inputmode="numeric" name="jumlah_total" id="edit-jumlah-total" required min="1"
                           class="w-full text-base sm:text-xs rounded-xl border-gray-300 focus:border-[#CEA042] focus:ring-1 focus:ring-[#CEA042] py-2.5 px-3.5 shadow-xs min-h-[46px]">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Kondisi Barang <span class="text-rose-500">*</span></label>
                    <select name="kondisi" id="edit-kondisi" required class="w-full text-base sm:text-xs rounded-xl border-gray-300 focus:border-[#CEA042] focus:ring-1 focus:ring-[#CEA042] py-2.5 px-3.5 shadow-xs min-h-[46px]">
                        <option value="Baik">Baik (Siap Pakai)</option>
                        <option value="Perlu Perbaikan">Perlu Perbaikan</option>
                        <option value="Rusak Ringan">Rusak Ringan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Lokasi Pos Dusun</label>
                    <input type="text" name="lokasi_penyimpanan" id="edit-lokasi"
                           class="w-full text-base sm:text-xs rounded-xl border-gray-300 focus:border-[#CEA042] focus:ring-1 focus:ring-[#CEA042] py-2.5 px-3.5 shadow-xs min-h-[46px]">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Keterangan / Kelengkapan</label>
                <textarea name="keterangan" id="edit-keterangan" rows="2"
                          class="w-full text-base sm:text-xs rounded-xl border-gray-300 focus:border-[#CEA042] focus:ring-1 focus:ring-[#CEA042] py-2.5 px-3.5 shadow-xs"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-gray-100">
                <button type="button" onclick="tutupModalEdit()" class="px-4 py-2.5 text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition min-h-[44px]">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 text-xs font-bold text-white bg-[#CEA042] hover:bg-[#BA8832] rounded-xl shadow-xs transition min-h-[44px]">
                    Perbarui Data Barang
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function bukaModalTambah() {
        document.getElementById('modal-tambah').classList.remove('hidden');
    }
    function tutupModalTambah() {
        document.getElementById('modal-tambah').classList.add('hidden');
    }

    function bukaModalEdit(barang) {
        document.getElementById('form-edit-barang').action = `/admin/barang/${barang.id}`;
        document.getElementById('edit-nama-barang').value = barang.nama_barang;
        document.getElementById('edit-kategori').value = barang.kategori;
        document.getElementById('edit-jumlah-total').value = barang.jumlah_total;
        document.getElementById('edit-kondisi').value = barang.kondisi;
        document.getElementById('edit-lokasi').value = barang.lokasi_penyimpanan || '';
        document.getElementById('edit-keterangan').value = barang.keterangan || '';
        document.getElementById('modal-edit').classList.remove('hidden');
    }
    function tutupModalEdit() {
        document.getElementById('modal-edit').classList.add('hidden');
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            tutupModalTambah();
            tutupModalEdit();
        }
    });
</script>
@endpush
@endsection

