@extends('layouts.admin')

@section('title', 'Master Data Warga Pelanggan Listrik')

@section('content')
<div class="space-y-4 sm:space-y-6"
     @keydown.escape.window="modalTambah = false; modalEdit = false"
     x-data="{
        cari: '{{ addslashes($cari ?? '') }}',
        modalTambah: false,
        modalEdit: false,
        wargaEdit: {
            id: null,
            nama: '',
            nama_pelanggan_pln: '',
            rt: 1,
            nomor_meter: '',
            no_telepon: '',
            alamat: ''
        },
        cocokSearch(nama, pln, meter) {
            if (!this.cari || this.cari.trim() === '') return true;
            const q = this.cari.toLowerCase().trim();
            const n = (nama || '').toLowerCase();
            const p = (pln || '').toLowerCase();
            const m = (meter || '').toLowerCase();
            return n.includes(q) || p.includes(q) || m.includes(q);
        },
        bukaEdit(item) {
            this.wargaEdit = {
                id: item.id,
                nama: item.nama,
                nama_pelanggan_pln: item.nama_pelanggan_pln || '',
                rt: item.rt,
                nomor_meter: item.nomor_meter || '',
                no_telepon: item.no_telepon || '',
                alamat: item.alamat || ''
            };
            this.modalEdit = true;
        }
     }">

    <!-- Header & Action -->
    <div class="card-surface p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <div class="flex items-center gap-2.5">
                <span class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-[#CEA042] text-[#FAF4E4CC] font-bold shrink-0 shadow-sm">
                    <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </span>
                <h1 class="text-lg sm:text-2xl font-bold text-gray-900 tracking-tight leading-tight">
                    Master Data Warga Pelanggan Listrik
                </h1>
            </div>
            <p class="text-xs text-gray-600 mt-1">
                Basis data warga Dusun Demangan: pemisahan antara nama warga yang dikenal dengan nama atas rekening pendaftaran PLN.
            </p>
        </div>

        <div class="flex items-center gap-2 self-stretch sm:self-auto">
            <a href="{{ route('admin.rekap-listrik.index') }}" class="btn-secondary px-3.5 text-xs">
                Lihat Rekap Tagihan
            </a>
            <button type="button" @click="modalTambah = true" class="btn-primary flex-1 sm:flex-initial px-4 text-xs">
                <svg class="w-4 h-4" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Tambah Warga</span>
            </button>
        </div>
    </div>

    <!-- Filter & Pencarian Instan -->
    <div class="card-surface p-3.5 sm:p-4">
        <form method="GET" action="{{ route('admin.warga.index') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5">
            <div class="relative flex-1">
                <label for="cari-warga" class="sr-only">Cari warga atau nomor struk</label>
                <input type="text" id="cari-warga" name="cari" x-model="cari" placeholder="Langsung ketik nama warga, nama rekening PLN, nomor struk..."
                       class="input-field pl-9 pr-9">
                <svg class="w-4 h-4 text-emas-700 absolute left-3 top-3.5 pointer-events-none" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <button type="button" x-show="cari.length > 0" @click="cari = ''" aria-label="Bersihkan pencarian"
                        class="absolute right-2.5 top-2.5 p-1 text-gray-400 hover:text-gray-700 rounded-lg">
                    <svg class="w-4 h-4" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="w-full sm:w-44">
                <label for="filter-rt-warga" class="sr-only">Filter Wilayah RT</label>
                <select id="filter-rt-warga" name="rt" onchange="this.form.submit()" class="select-field">
                    <option value="">Semua RT (1 - 3)</option>
                    @for($i = 1; $i <= 3; $i++)
                        <option value="{{ $i }}" {{ (string)$rt === (string)$i ? 'selected' : '' }}>RT {{ sprintf('%02d', $i) }}</option>
                    @endfor
                </select>
            </div>

            @if($cari || $rt)
                <a href="{{ route('admin.warga.index') }}" class="btn-danger-soft px-3.5 text-xs">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- TAMPILAN MOBILE: KARTU MASTER WARGA -->
    <div class="block md:hidden space-y-3">
        @forelse($daftarWarga as $warga)
            <div class="card-surface p-4 space-y-2.5"
                 x-show="cocokSearch('{{ addslashes($warga->nama) }}', '{{ addslashes($warga->nama_pelanggan_pln) }}', '{{ addslashes($warga->nomor_meter ?? '') }}')">
                <div class="flex items-start justify-between gap-2 pb-2 border-b border-gray-100">
                    <div>
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-emas-100 text-emas-900 font-mono font-bold text-xs border border-emas-200 tabular-money">
                                #{{ $warga->nomor_meter ?: '-' }}
                            </span>
                            <span class="inline-block px-2 py-0.5 text-[11px] font-bold bg-gray-100 text-gray-800 rounded-md">
                                RT {{ sprintf('%02d', $warga->rt) }}
                            </span>
                        </div>
                        <h2 class="font-bold text-gray-900 text-sm mt-1.5">{{ $warga->nama }}</h2>
                        <p class="text-xs text-gray-600 mt-0.5">
                            PLN: <span class="font-semibold text-gray-800">{{ $warga->nama_pelanggan_pln ?: '(Sama dengan nama warga)' }}</span>
                        </p>
                    </div>
                </div>

                @if($warga->no_telepon || $warga->alamat)
                    <div class="text-xs text-gray-600 flex items-center justify-between gap-2">
                        <span class="tabular-money">{{ $warga->no_telepon ?? '-' }}</span>
                        <span class="text-gray-500 truncate">{{ $warga->alamat ?: 'Dusun Demangan' }}</span>
                    </div>
                @endif

                <div class="grid grid-cols-2 gap-2 pt-2 border-t border-gray-100">
                    <button type="button" @click="bukaEdit({{ $warga->toJson() }})" class="btn-subtle w-full text-xs">
                        Ubah Data
                    </button>
                    <form method="POST" action="{{ route('admin.warga.destroy', $warga) }}"
                          onsubmit="return confirm('Hapus data warga {{ addslashes($warga->nama) }}?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-danger-soft w-full text-xs">
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="card-surface p-8 text-center text-gray-500 space-y-3">
                <p class="text-xs">Belum ada master data warga pelanggan listrik.</p>
                <button type="button" @click="modalTambah = true" class="btn-primary px-4 text-xs">
                    + Daftarkan Warga Pertama
                </button>
            </div>
        @endforelse
    </div>

    <!-- TAMPILAN DESKTOP: TABEL MASTER WARGA -->
    <div class="hidden md:block card-surface overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-[#F8F7F4] border-b border-gray-200 text-gray-600 font-bold uppercase tracking-wider text-[11px]">
                        <th class="py-3.5 px-3.5 text-center w-12">No</th>
                        <th class="py-3.5 px-3.5">Wilayah RT</th>
                        <th class="py-3.5 px-3.5">Nama Warga (Penghuni)</th>
                        <th class="py-3.5 px-3.5">Atas Nama Rekening PLN</th>
                        <th class="py-3.5 px-3.5">Nomor Struk</th>
                        <th class="py-3.5 px-3.5">Kontak &amp; Alamat</th>
                        <th class="py-3.5 px-3.5 text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($daftarWarga as $idx => $warga)
                        <tr class="hover:bg-emas-50/40 transition"
                            x-show="cocokSearch('{{ addslashes($warga->nama) }}', '{{ addslashes($warga->nama_pelanggan_pln) }}', '{{ addslashes($warga->nomor_meter ?? '') }}')">
                            <td class="py-3.5 px-3.5 text-center text-gray-500 font-medium tabular-money">
                                {{ $daftarWarga->firstItem() + $idx }}
                            </td>

                            <td class="py-3.5 px-3.5">
                                <span class="inline-block px-2.5 py-0.5 text-xs font-bold bg-emas-100 text-emas-900 border border-emas-200 rounded-md">
                                    RT {{ sprintf('%02d', $warga->rt) }}
                                </span>
                            </td>

                            <td class="py-3.5 px-3.5">
                                <span class="font-bold text-gray-900 text-sm block">{{ $warga->nama }}</span>
                            </td>

                            <td class="py-3.5 px-3.5">
                                <div class="flex items-center gap-1.5 font-semibold text-gray-800">
                                    <svg class="w-3.5 h-3.5 text-emas-700 shrink-0" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4zm3 1h6v4H7V5zm8 8v2h1v-2h-1zm-2-2H7v4h6v-4zm2 0h1V9h-1v2zm1-4V5h-1v2h1zM5 5v2H4V5h1zm0 4H4v2h1V9zm-1 4h1v2H4v-2z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="text-xs">{{ $warga->nama_pelanggan_pln ?: '(Sama dengan nama warga)' }}</span>
                                </div>
                                <span class="text-[11px] text-gray-500">Sesuai nama pendaftaran PLN</span>
                            </td>

                            <td class="py-3.5 px-3.5">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-emas-50 text-emas-900 font-mono font-bold text-xs border border-emas-200 tabular-money">
                                    #{{ $warga->nomor_meter ?: '-' }}
                                </span>
                            </td>

                            <td class="py-3.5 px-3.5 text-gray-600">
                                @if($warga->no_telepon)
                                    <div class="text-xs font-medium text-gray-800 tabular-money">{{ $warga->no_telepon }}</div>
                                @endif
                                <div class="text-[11px] text-gray-500">{{ $warga->alamat ?: 'Dusun Demangan' }}</div>
                            </td>

                            <td class="py-3.5 px-3.5 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <button type="button" @click="bukaEdit({{ $warga->toJson() }})"
                                            aria-label="Ubah data {{ $warga->nama }}"
                                            class="p-2 text-gray-600 hover:text-emas-800 hover:bg-emas-50 rounded-lg transition min-h-[36px] min-w-[36px] flex items-center justify-center"
                                            title="Ubah data warga">
                                        <svg class="w-4 h-4" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>

                                    <form method="POST" action="{{ route('admin.warga.destroy', $warga) }}"
                                          onsubmit="return confirm('Hapus data warga {{ addslashes($warga->nama) }}?');"
                                          class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                aria-label="Hapus data {{ $warga->nama }}"
                                                class="p-2 text-gray-500 hover:text-rose-700 hover:bg-rose-50 rounded-lg transition min-h-[36px] min-w-[36px] flex items-center justify-center"
                                                title="Hapus">
                                            <svg class="w-4 h-4" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-10 text-center text-gray-500">
                                <div class="max-w-xs mx-auto text-center space-y-2.5">
                                    <p class="text-xs">Belum ada master data warga pelanggan listrik.</p>
                                    <button type="button" @click="modalTambah = true" class="btn-primary px-4 text-xs">
                                        + Daftarkan Warga Pertama
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($daftarWarga->hasPages())
            <div class="p-3.5 border-t border-gray-100 bg-[#F8F7F4]">
                {{ $daftarWarga->links() }}
            </div>
        @endif
    </div>

    @if($daftarWarga->hasPages())
        <div class="md:hidden">
            {{ $daftarWarga->links() }}
        </div>
    @endif

    <!-- MODAL TAMBAH WARGA -->
    <div x-show="modalTambah" x-cloak
         @click.self="modalTambah = false"
         role="dialog"
         aria-modal="true"
         aria-labelledby="modal-tambah-warga-title"
         class="modal-backdrop">
        <div class="modal-panel max-w-lg p-5 sm:p-6 space-y-4">
            <div class="w-12 h-1.5 bg-gray-300 rounded-full mx-auto mb-1 sm:hidden"></div>
            <div class="flex items-start justify-between border-b border-gray-100 pb-3">
                <div class="space-y-0.5">
                    <h3 id="modal-tambah-warga-title" class="text-base sm:text-lg font-bold text-gray-900 tracking-tight">Tambah Master Warga</h3>
                    <p class="text-xs text-gray-600 font-normal">Daftarkan warga baru, tentukan nomor struk, dan data rekening PLN.</p>
                </div>
                <button type="button" @click="modalTambah = false" aria-label="Tutup modal tambah warga"
                        class="text-gray-500 hover:text-gray-800 hover:bg-gray-100 rounded-xl p-2 transition min-h-[44px] min-w-[44px] flex items-center justify-center">
                    <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.warga.store') }}" class="space-y-4 text-xs">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label for="tambah-warga-rt" class="block text-xs font-bold text-gray-800 mb-1.5">
                            Rukun Tetangga (RT) <span class="text-rose-600">*</span>
                        </label>
                        <select id="tambah-warga-rt" name="rt" required class="select-field font-semibold">
                            @for($i = 1; $i <= 3; $i++)
                                <option value="{{ $i }}">RT {{ sprintf('%02d', $i) }}</option>
                            @endfor
                        </select>
                    </div>

                    <div>
                        <label for="tambah-warga-struk" class="block text-xs font-bold text-gray-800 mb-1.5">
                            Nomor Struk [ # ] <span class="text-rose-600">*</span>
                        </label>
                        <div class="input-addon-group">
                            <span class="flex items-center justify-center bg-gray-100 px-3.5 text-gray-600 font-bold text-sm select-none border-r border-gray-200 shrink-0">
                                #
                            </span>
                            <input type="text"
                                   id="tambah-warga-struk"
                                   name="nomor_meter"
                                   required
                                   placeholder="Contoh: 21 atau 28"
                                   class="w-full text-sm font-bold text-gray-900 border-0 focus:ring-0 focus:outline-none py-2.5 px-3 bg-transparent tabular-money">
                        </div>
                    </div>
                </div>

                <div>
                    <label for="tambah-warga-nama" class="block text-xs font-bold text-gray-800 mb-1.5">
                        Nama Warga (Penghuni) <span class="text-rose-600">*</span>
                    </label>
                    <input type="text"
                           id="tambah-warga-nama"
                           name="nama"
                           required
                           placeholder="Contoh: Sugiyanto (P. Tebek)"
                           class="input-field font-semibold">
                    <span class="text-[11px] text-gray-500 mt-1 block">Nama orang yang sehari-hari tinggal dan membayar tagihan</span>
                </div>

                <div>
                    <label for="tambah-warga-pln" class="block text-xs font-bold text-gray-800 mb-1.5">
                        Atas Nama Rekening PLN <span class="text-rose-600">*</span>
                    </label>
                    <input type="text"
                           id="tambah-warga-pln"
                           name="nama_pelanggan_pln"
                           required
                           placeholder="Contoh: SUGIYANTO"
                           class="input-field font-semibold">
                    <span class="text-[11px] text-gray-500 mt-1 block">Sesuai nama pendaftaran PLN yang tertera di struk fisik</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label for="tambah-warga-hp" class="block text-xs font-bold text-gray-800 mb-1.5">
                            No. HP / WhatsApp <span class="text-gray-500 font-normal">(opsional)</span>
                        </label>
                        <input type="text" id="tambah-warga-hp" name="no_telepon" placeholder="08xxxxxxxxxx" class="input-field">
                    </div>
                    <div>
                        <label for="tambah-warga-alamat" class="block text-xs font-bold text-gray-800 mb-1.5">
                            Keterangan Alamat <span class="text-gray-500 font-normal">(opsional)</span>
                        </label>
                        <input type="text" id="tambah-warga-alamat" name="alamat" placeholder="Dusun Demangan RT 01" class="input-field">
                    </div>
                </div>

                <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2.5">
                    <button type="button" @click="modalTambah = false" class="btn-subtle px-4 text-xs">
                        Batal
                    </button>
                    <button type="submit" class="btn-primary px-5 text-xs">
                        Simpan Data Warga
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL EDIT WARGA -->
    <div x-show="modalEdit" x-cloak
         @click.self="modalEdit = false"
         role="dialog"
         aria-modal="true"
         aria-labelledby="modal-edit-warga-title"
         class="modal-backdrop">
        <div class="modal-panel max-w-lg p-5 sm:p-6 space-y-4">
            <div class="w-12 h-1.5 bg-gray-300 rounded-full mx-auto mb-1 sm:hidden"></div>
            <div class="flex items-start justify-between border-b border-gray-100 pb-3">
                <div class="space-y-0.5">
                    <h3 id="modal-edit-warga-title" class="text-base sm:text-lg font-bold text-gray-900 tracking-tight">Ubah Data Master Warga</h3>
                    <p class="text-xs text-gray-600 font-normal">Perbarui informasi rukun tetangga, nomor struk, dan data rekening PLN.</p>
                </div>
                <button type="button" @click="modalEdit = false" aria-label="Tutup modal edit warga"
                        class="text-gray-500 hover:text-gray-800 hover:bg-gray-100 rounded-xl p-2 transition min-h-[44px] min-w-[44px] flex items-center justify-center">
                    <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form method="POST" :action="'/admin/warga/' + wargaEdit.id" class="space-y-4 text-xs">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label for="edit-warga-rt" class="block text-xs font-bold text-gray-800 mb-1.5">
                            Rukun Tetangga (RT) <span class="text-rose-600">*</span>
                        </label>
                        <select id="edit-warga-rt" name="rt" x-model="wargaEdit.rt" required class="select-field font-semibold">
                            @for($i = 1; $i <= 3; $i++)
                                <option value="{{ $i }}">RT {{ sprintf('%02d', $i) }}</option>
                            @endfor
                        </select>
                    </div>

                    <div>
                        <label for="edit-warga-struk" class="block text-xs font-bold text-gray-800 mb-1.5">
                            Nomor Struk [ # ] <span class="text-rose-600">*</span>
                        </label>
                        <div class="input-addon-group">
                            <span class="flex items-center justify-center bg-gray-100 px-3.5 text-gray-600 font-bold text-sm select-none border-r border-gray-200 shrink-0">
                                #
                            </span>
                            <input type="text"
                                   id="edit-warga-struk"
                                   name="nomor_meter"
                                   x-model="wargaEdit.nomor_meter"
                                   required
                                   placeholder="Contoh: 21 atau 28"
                                   class="w-full text-sm font-bold text-gray-900 border-0 focus:ring-0 focus:outline-none py-2.5 px-3 bg-transparent tabular-money">
                        </div>
                    </div>
                </div>

                <div>
                    <label for="edit-warga-nama" class="block text-xs font-bold text-gray-800 mb-1.5">
                        Nama Warga (Penghuni) <span class="text-rose-600">*</span>
                    </label>
                    <input type="text"
                           id="edit-warga-nama"
                           name="nama"
                           x-model="wargaEdit.nama"
                           required
                           placeholder="Contoh: Am'ad Ngasri"
                           class="input-field font-semibold">
                    <span class="text-[11px] text-gray-500 mt-1 block">Nama warga yang sehari-hari tinggal dan membayar tagihan</span>
                </div>

                <div>
                    <label for="edit-warga-pln" class="block text-xs font-bold text-gray-800 mb-1.5">
                        Atas Nama Rekening PLN <span class="text-rose-600">*</span>
                    </label>
                    <input type="text"
                           id="edit-warga-pln"
                           name="nama_pelanggan_pln"
                           x-model="wargaEdit.nama_pelanggan_pln"
                           required
                           placeholder="Contoh: AM'AD NGASRI"
                           class="input-field font-semibold">
                    <span class="text-[11px] text-gray-500 mt-1 block">Sesuai nama pendaftaran PLN yang tertera di struk fisik</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label for="edit-warga-hp" class="block text-xs font-bold text-gray-800 mb-1.5">
                            No. HP / WhatsApp <span class="text-gray-500 font-normal">(opsional)</span>
                        </label>
                        <input type="text" id="edit-warga-hp" name="no_telepon" x-model="wargaEdit.no_telepon" placeholder="08xxxxxxxxxx" class="input-field">
                    </div>
                    <div>
                        <label for="edit-warga-alamat" class="block text-xs font-bold text-gray-800 mb-1.5">
                            Keterangan Alamat <span class="text-gray-500 font-normal">(opsional)</span>
                        </label>
                        <input type="text" id="edit-warga-alamat" name="alamat" x-model="wargaEdit.alamat" placeholder="Dusun Demangan RT 01" class="input-field">
                    </div>
                </div>

                <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2.5">
                    <button type="button" @click="modalEdit = false" class="btn-subtle px-4 text-xs">
                        Batal
                    </button>
                    <button type="submit" class="btn-primary px-5 text-xs">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
