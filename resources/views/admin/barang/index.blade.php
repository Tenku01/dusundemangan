@extends('layouts.admin')

@section('title', 'Manajemen Data Inventaris Barang')

@section('content')
<div class="space-y-4 sm:space-y-6">

    <!-- Header & Action Button -->
    <div class="card-surface p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-lg sm:text-2xl font-bold text-gray-900 tracking-tight leading-tight">
                Data Inventaris Barang Dusun
            </h1>
            <p class="text-xs text-gray-600 mt-0.5">
                Kelola perlengkapan dusun, jumlah unit, kondisi fisik, dan lokasi pos penyimpanan.
            </p>
        </div>

        <button type="button" onclick="bukaModalTambah()" class="btn-primary w-full sm:w-auto px-4 text-xs shrink-0">
            <svg class="w-4 h-4" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Tambah Barang Baru</span>
        </button>
    </div>

    <!-- Filter & Search Bar -->
    <div class="card-surface p-3.5 sm:p-4">
        <form method="GET" action="{{ route('admin.barang.index') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5">
            <div class="flex-1">
                <label for="cari-barang" class="sr-only">Cari nama barang, kode, atau lokasi</label>
                <input type="text" id="cari-barang" name="cari" value="{{ $cari }}" placeholder="Cari nama barang, kode, lokasi..."
                       class="input-field">
            </div>

            <div class="w-full sm:w-48">
                <label for="filter-kategori-barang" class="sr-only">Filter kategori</label>
                <select id="filter-kategori-barang" name="kategori" onchange="this.form.submit()" class="select-field">
                    <option value="Semua">Semua Kategori</option>
                    @foreach($kategoriList as $kat)
                        <option value="{{ $kat }}" {{ $kategori === $kat ? 'selected' : '' }}>{{ $kat }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn-primary w-full sm:w-auto px-5 text-xs shrink-0">
                Filter
            </button>

            @if($cari || ($kategori && $kategori !== 'Semua'))
                <a href="{{ route('admin.barang.index') }}" class="btn-danger-soft text-xs shrink-0">
                    Reset Filter
                </a>
            @endif
        </form>
    </div>

    <!-- TAMPILAN MOBILE: KARTU INVENTARIS -->
    <div class="block md:hidden space-y-3.5">
        @forelse($daftarBarang as $b)
            <div class="card-surface p-4 space-y-3">
                <div class="flex items-start justify-between gap-2 pb-2.5 border-b border-gray-100">
                    <div>
                        <span class="font-mono text-xs font-bold text-emas-800 block">
                            {{ $b->kode_barang }}
                        </span>
                        <h2 class="font-bold text-gray-900 text-sm mt-0.5">{{ $b->nama_barang }}</h2>
                        <span class="text-[11px] text-gray-700 bg-gray-100 px-2 py-0.5 rounded-md font-medium inline-block mt-1">
                            {{ $b->kategori }}
                        </span>
                    </div>

                    <div class="text-right shrink-0">
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold block tabular-money {{ $b->jumlah_tersedia > 0 ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-rose-50 text-rose-800 border border-rose-200' }}">
                            {{ $b->jumlah_tersedia }} <span class="text-[11px] font-medium">tersedia</span>
                        </span>
                        <span class="text-[11px] text-gray-500 mt-1 block tabular-money">Total: {{ $b->jumlah_total }} unit</span>
                    </div>
                </div>

                <div class="space-y-1 text-xs text-gray-600">
                    <div class="flex justify-between">
                        <span>Kondisi Fisik:</span>
                        <span class="font-semibold text-gray-900">{{ $b->kondisi }}</span>
                    </div>
                    @if($b->lokasi_penyimpanan)
                        <div class="flex justify-between">
                            <span>Lokasi Gudang:</span>
                            <span class="text-gray-900 font-medium">{{ $b->lokasi_penyimpanan }}</span>
                        </div>
                    @endif
                    @if($b->keterangan)
                        <p class="text-xs text-gray-600 bg-[#F8F7F4] p-2.5 rounded-xl mt-1.5 border border-gray-100">{{ $b->keterangan }}</p>
                    @endif
                </div>

                <div class="grid grid-cols-2 gap-2 pt-2 border-t border-gray-100">
                    <button type="button" onclick='bukaModalEdit(@json($b))' class="btn-subtle w-full text-xs">
                        Edit Data
                    </button>

                    <form action="{{ route('admin.barang.destroy', $b) }}" method="POST"
                          onsubmit="return confirm('Hapus barang {{ addslashes($b->nama_barang) }} dari inventaris?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-danger-soft w-full text-xs">
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="card-surface p-8 text-center text-gray-500 text-xs">
                Belum ada data barang.
            </div>
        @endforelse
    </div>

    <!-- TAMPILAN DESKTOP: TABEL PENUH -->
    <div class="hidden md:block card-surface overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-left text-xs">
                <thead class="bg-[#F8F7F4] text-gray-600 font-bold uppercase tracking-wider text-[11px]">
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
                        <tr class="hover:bg-emas-50/40 transition">
                            <td class="py-3.5 px-4 font-mono font-bold text-emas-800 whitespace-nowrap">
                                {{ $b->kode_barang }}
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <div class="font-bold text-gray-900">{{ $b->nama_barang }}</div>
                                <div class="text-[11px] text-gray-500 max-w-xs truncate">{{ $b->keterangan ?? '-' }}</div>
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap text-gray-700 font-medium">
                                {{ $b->kategori }}
                            </td>
                            <td class="py-3.5 px-4 text-center font-semibold text-gray-900 whitespace-nowrap tabular-money">
                                {{ $b->jumlah_total }}
                            </td>
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold tabular-money {{ $b->jumlah_tersedia > 0 ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-rose-50 text-rose-800 border border-rose-200' }}">
                                    {{ $b->jumlah_tersedia }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded-md text-[11px] font-semibold {{ $b->kondisi === 'Baik' ? 'bg-emerald-50 text-emerald-800' : 'bg-amber-50 text-amber-800' }}">
                                    {{ $b->kondisi }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-gray-600 whitespace-nowrap">
                                {{ $b->lokasi_penyimpanan ?? '-' }}
                            </td>
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    <button type="button" onclick='bukaModalEdit(@json($b))'
                                            class="px-3 py-1.5 text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition min-h-[36px]">
                                        Edit
                                    </button>

                                    <form action="{{ route('admin.barang.destroy', $b) }}" method="POST"
                                          onsubmit="return confirm('Hapus barang {{ addslashes($b->nama_barang) }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1.5 text-xs font-semibold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 rounded-lg transition min-h-[36px]">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-gray-500">
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

<!-- MODAL TAMBAH BARANG -->
<div id="modal-tambah"
     onclick="if (event.target === this) tutupModalTambah()"
     role="dialog"
     aria-modal="true"
     aria-labelledby="modal-tambah-barang-title"
     class="modal-backdrop hidden">
    <div class="modal-panel max-w-lg p-5 sm:p-6">
        <div class="w-12 h-1.5 bg-gray-300 rounded-full mx-auto mb-3 sm:hidden"></div>

        <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
            <div>
                <h3 id="modal-tambah-barang-title" class="text-base font-bold text-gray-900 leading-tight">Tambah Barang Inventaris Baru</h3>
                <p class="text-xs text-gray-600 mt-0.5">Catat unit baru aset perlengkapan milik dusun.</p>
            </div>
            <button type="button" onclick="tutupModalTambah()" aria-label="Tutup modal tambah barang"
                    class="text-gray-500 hover:text-gray-800 p-2 rounded-xl min-h-[44px] min-w-[44px] flex items-center justify-center bg-gray-100 sm:bg-transparent hover:bg-gray-100 transition">
                <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form action="{{ route('admin.barang.store') }}" method="POST" class="space-y-3.5">
            @csrf
            <div>
                <label for="tambah-nama-barang" class="block text-xs font-semibold text-gray-800 mb-1">Nama Barang <span class="text-rose-600">*</span></label>
                <input type="text" id="tambah-nama-barang" name="nama_barang" required placeholder="Contoh: Genset Listrik 3000 Watt"
                       class="input-field">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label for="tambah-kategori" class="block text-xs font-semibold text-gray-800 mb-1">Kategori <span class="text-rose-600">*</span></label>
                    <input type="text" id="tambah-kategori" name="kategori" required placeholder="Contoh: Tenda & Terpal" list="list-kategori"
                           class="input-field">
                    <datalist id="list-kategori">
                        <option value="Tenda & Terpal">
                        <option value="Meja & Kursi">
                        <option value="Elektronik & Sound">
                        <option value="Alat Kebersihan & Taman">
                        <option value="Perkakas & Pertukangan">
                    </datalist>
                </div>
                <div>
                    <label for="tambah-jumlah" class="block text-xs font-semibold text-gray-800 mb-1">Jumlah Unit Fisik <span class="text-rose-600">*</span></label>
                    <input type="number" id="tambah-jumlah" inputmode="numeric" name="jumlah_total" required min="1" value="1"
                           class="input-field tabular-money">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label for="tambah-kondisi" class="block text-xs font-semibold text-gray-800 mb-1">Kondisi Barang <span class="text-rose-600">*</span></label>
                    <select id="tambah-kondisi" name="kondisi" required class="select-field">
                        <option value="Baik">Baik (Siap Pakai)</option>
                        <option value="Perlu Perbaikan">Perlu Perbaikan</option>
                        <option value="Rusak Ringan">Rusak Ringan</option>
                    </select>
                </div>
                <div>
                    <label for="tambah-lokasi" class="block text-xs font-semibold text-gray-800 mb-1">Lokasi Pos Dusun</label>
                    <input type="text" id="tambah-lokasi" name="lokasi_penyimpanan" placeholder="Contoh: Pos RW 02"
                           class="input-field">
                </div>
            </div>

            <div>
                <label for="tambah-keterangan" class="block text-xs font-semibold text-gray-800 mb-1">Keterangan / Kelengkapan</label>
                <textarea id="tambah-keterangan" name="keterangan" rows="2" placeholder="Spesifikasi ukuran, kelengkapan kabel, dll."
                          class="input-field py-2.5"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-gray-100">
                <button type="button" onclick="tutupModalTambah()" class="btn-subtle px-4 text-xs">
                    Batal
                </button>
                <button type="submit" class="btn-primary px-5 text-xs">
                    Simpan Barang Baru
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT BARANG -->
<div id="modal-edit"
     onclick="if (event.target === this) tutupModalEdit()"
     role="dialog"
     aria-modal="true"
     aria-labelledby="modal-edit-barang-title"
     class="modal-backdrop hidden">
    <div class="modal-panel max-w-lg p-5 sm:p-6">
        <div class="w-12 h-1.5 bg-gray-300 rounded-full mx-auto mb-3 sm:hidden"></div>

        <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
            <div>
                <h3 id="modal-edit-barang-title" class="text-base font-bold text-gray-900 leading-tight">Perbarui Data Barang</h3>
                <p class="text-xs text-gray-600 mt-0.5">Ubah stok atau kondisi barang inventaris.</p>
            </div>
            <button type="button" onclick="tutupModalEdit()" aria-label="Tutup modal edit barang"
                    class="text-gray-500 hover:text-gray-800 p-2 rounded-xl min-h-[44px] min-w-[44px] flex items-center justify-center bg-gray-100 sm:bg-transparent hover:bg-gray-100 transition">
                <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form id="form-edit-barang" method="POST" class="space-y-3.5">
            @csrf
            @method('PUT')
            <div>
                <label for="edit-nama-barang" class="block text-xs font-semibold text-gray-800 mb-1">Nama Barang <span class="text-rose-600">*</span></label>
                <input type="text" name="nama_barang" id="edit-nama-barang" required class="input-field">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label for="edit-kategori" class="block text-xs font-semibold text-gray-800 mb-1">Kategori <span class="text-rose-600">*</span></label>
                    <input type="text" name="kategori" id="edit-kategori" required list="list-kategori" class="input-field">
                </div>
                <div>
                    <label for="edit-jumlah-total" class="block text-xs font-semibold text-gray-800 mb-1">Jumlah Unit Total <span class="text-rose-600">*</span></label>
                    <input type="number" inputmode="numeric" name="jumlah_total" id="edit-jumlah-total" required min="1" class="input-field tabular-money">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label for="edit-kondisi" class="block text-xs font-semibold text-gray-800 mb-1">Kondisi Barang <span class="text-rose-600">*</span></label>
                    <select name="kondisi" id="edit-kondisi" required class="select-field">
                        <option value="Baik">Baik (Siap Pakai)</option>
                        <option value="Perlu Perbaikan">Perlu Perbaikan</option>
                        <option value="Rusak Ringan">Rusak Ringan</option>
                    </select>
                </div>
                <div>
                    <label for="edit-lokasi" class="block text-xs font-semibold text-gray-800 mb-1">Lokasi Pos Dusun</label>
                    <input type="text" name="lokasi_penyimpanan" id="edit-lokasi" class="input-field">
                </div>
            </div>

            <div>
                <label for="edit-keterangan" class="block text-xs font-semibold text-gray-800 mb-1">Keterangan / Kelengkapan</label>
                <textarea name="keterangan" id="edit-keterangan" rows="2" class="input-field py-2.5"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-gray-100">
                <button type="button" onclick="tutupModalEdit()" class="btn-subtle px-4 text-xs">
                    Batal
                </button>
                <button type="submit" class="btn-primary px-5 text-xs">
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
