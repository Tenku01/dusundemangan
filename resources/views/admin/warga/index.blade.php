@extends('layouts.admin')

@section('title', 'Master Data Warga Pelanggan Listrik')

@section('content')
<div class="space-y-4 sm:space-y-6"
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
    <div class="bg-white rounded-xl border border-gray-200/90 p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-amber-500 text-white font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </span>
                <h1 class="text-lg sm:text-2xl font-bold text-gray-900 tracking-tight leading-tight">
                    Master Data Warga Pelanggan Listrik
                </h1>
            </div>
            <p class="text-xs text-gray-500 mt-1">
                Basis data warga Dusun Demangan: pemisahan antara nama warga yang dikenal dengan nama atas rekening pendaftaran PLN.
            </p>
        </div>

        <div class="flex items-center gap-2 self-stretch sm:self-auto">
            <a href="{{ route('admin.rekap-listrik.index') }}"
               class="px-3 py-2 text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition min-h-[44px] flex items-center">
                Lihat Rekap Tagihan
            </a>
            <button type="button" @click="modalTambah = true"
                    class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-2 px-4 py-2.5 text-xs font-bold text-white bg-[#CEA042] hover:bg-[#BA8832] focus-visible:ring-2 focus-visible:ring-[#CEA042] rounded-xl shadow-xs transition min-h-[46px]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                + Tambah Warga
            </button>
        </div>
    </div>

    <!-- Filter & Pencarian Instan -->
    <div class="bg-white rounded-xl border border-gray-200/90 p-3.5 sm:p-4 shadow-xs">
        <form method="GET" action="{{ route('admin.warga.index') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5">
            <div class="relative flex-1">
                <input type="text" name="cari" x-model="cari" placeholder="Langsung ketik nama warga, nama rekening PLN, nomor struk..."
                       class="w-full text-base sm:text-xs rounded-lg border-gray-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 py-2.5 pl-9 pr-8 shadow-xs min-h-[44px]">
                <svg class="w-4 h-4 text-amber-500 absolute left-3 top-3.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <button type="button" x-show="cari.length > 0" @click="cari = ''" class="absolute right-2.5 top-3 p-0.5 text-gray-400 hover:text-gray-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="w-full sm:w-44">
                <select name="rt" onchange="this.form.submit()"
                        class="w-full text-base sm:text-xs rounded-lg border-gray-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 py-2 px-2.5 shadow-xs min-h-[44px]">
                    <option value="">Semua RT (1 - 3)</option>
                    @for($i = 1; $i <= 3; $i++)
                        <option value="{{ $i }}" {{ (string)$rt === (string)$i ? 'selected' : '' }}>RT {{ sprintf('%02d', $i) }}</option>
                    @endfor
                </select>
            </div>

            @if($cari || $rt)
                <a href="{{ route('admin.warga.index') }}" class="text-xs text-rose-600 hover:underline text-center py-2 px-2">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Tabel Master Warga -->
    <div class="bg-white rounded-xl border border-gray-200/90 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-600 font-bold uppercase tracking-wider text-[11px]">
                        <th class="py-3 px-3.5 text-center w-12">No</th>
                        <th class="py-3 px-3.5">Wilayah RT</th>
                        <th class="py-3 px-3.5">Nama Warga (Penghuni)</th>
                        <th class="py-3 px-3.5">Atas Nama Rekening PLN</th>
                        <th class="py-3 px-3.5">Nomor Struk</th>
                        <th class="py-3 px-3.5">Kontak & Alamat</th>
                        <th class="py-3 px-3.5 text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($daftarWarga as $idx => $warga)
                        <tr class="hover:bg-amber-50/40 transition"
                            x-show="cocokSearch('{{ addslashes($warga->nama) }}', '{{ addslashes($warga->nama_pelanggan_pln) }}', '{{ addslashes($warga->nomor_meter ?? '') }}')">
                            <td class="py-3 px-3.5 text-center text-gray-500 font-medium">
                                {{ $daftarWarga->firstItem() + $idx }}
                            </td>

                            <td class="py-3 px-3.5">
                                <span class="inline-block px-2 py-0.5 text-xs font-bold bg-amber-100 text-amber-800 rounded">
                                    RT {{ sprintf('%02d', $warga->rt) }}
                                </span>
                            </td>

                            <td class="py-3 px-3.5">
                                <span class="font-bold text-gray-900 text-sm block">{{ $warga->nama }}</span>
                            </td>

                            <td class="py-3 px-3.5">
                                <div class="flex items-center gap-1.5 font-semibold text-gray-800">
                                    <svg class="w-3.5 h-3.5 text-amber-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4zm3 1h6v4H7V5zm8 8v2h1v-2h-1zm-2-2H7v4h6v-4zm2 0h1V9h-1v2zm1-4V5h-1v2h1zM5 5v2H4V5h1zm0 4H4v2h1V9zm-1 4h1v2H4v-2z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="text-xs">{{ $warga->nama_pelanggan_pln ?: '(Sama dengan nama warga)' }}</span>
                                </div>
                                <span class="text-[10px] text-gray-400">Sesuai nama pendaftaran PLN</span>
                            </td>

                            <td class="py-3 px-3.5">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-amber-50 text-amber-900 font-mono font-bold text-xs border border-amber-200">
                                    #{{ $warga->nomor_meter ?: '-' }}
                                </span>
                            </td>

                            <td class="py-3 px-3.5 text-gray-600">
                                @if($warga->no_telepon)
                                    <div class="text-[11px] font-medium">{{ $warga->no_telepon }}</div>
                                @endif
                                <div class="text-[10px] text-gray-400">{{ $warga->alamat ?: 'Dusun Demangan' }}</div>
                            </td>

                            <td class="py-3 px-3.5 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <button type="button" @click="bukaEdit({{ $warga->toJson() }})"
                                            class="p-1.5 text-gray-600 hover:text-amber-600 hover:bg-amber-50 rounded-md transition"
                                            title="Ubah data warga">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>

                                    <form method="POST" action="{{ route('admin.warga.destroy', $warga) }}"
                                          onsubmit="return confirm('Hapus data warga {{ $warga->nama }}?');"
                                          class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="p-1.5 text-gray-400 hover:text-rose-600 hover:bg-rose-50 rounded-md transition"
                                                title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                <div class="max-w-xs mx-auto text-center space-y-2">
                                    <p class="text-xs">Belum ada master data warga pelanggan listrik.</p>
                                    <button type="button" @click="modalTambah = true"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-bold text-white bg-[#CEA042] hover:bg-[#BA8832] rounded-lg shadow-xs transition">
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
            <div class="p-3.5 border-t border-gray-100 bg-gray-50">
                {{ $daftarWarga->links() }}
            </div>
        @endif
    </div>

    <!-- MODAL TAMBAH WARGA -->
    <div x-show="modalTambah" x-cloak
         class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
        <div @click.away="modalTambah = false"
             class="bg-white rounded-2xl max-w-lg w-full p-5 sm:p-6 shadow-xl border border-gray-200 space-y-4">
            <div class="flex items-start justify-between border-b border-gray-100 pb-3">
                <div class="space-y-0.5">
                    <h3 class="text-base sm:text-lg font-black text-gray-900 tracking-tight">Tambah Master Warga</h3>
                    <p class="text-xs text-gray-500 font-normal">Daftarkan warga baru, tentukan nomor struk, dan data rekening PLN.</p>
                </div>
                <button type="button" @click="modalTambah = false"
                        class="text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-xl p-1.5 transition text-xl leading-none">&times;</button>
            </div>

            <form method="POST" action="{{ route('admin.warga.store') }}" class="space-y-4 text-xs">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label class="block text-xs font-bold text-gray-800 mb-1.5">
                            Rukun Tetangga (RT) <span class="text-rose-500">*</span>
                        </label>
                        <select name="rt" required
                                class="w-full text-sm font-semibold rounded-xl border-gray-300 focus:border-[#CEA042] focus:ring-2 focus:ring-[#CEA042]/20 py-2.5 px-3 min-h-[46px] shadow-xs">
                            @for($i = 1; $i <= 3; $i++)
                                <option value="{{ $i }}">RT {{ sprintf('%02d', $i) }}</option>
                            @endfor
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-800 mb-1.5">
                            Nomor Struk [ # ] <span class="text-rose-500">*</span>
                        </label>
                        <div class="flex items-stretch rounded-xl border border-gray-300 bg-white shadow-xs focus-within:border-[#CEA042] focus-within:ring-2 focus-within:ring-[#CEA042]/20 overflow-hidden transition-all min-h-[46px]">
                            <span class="flex items-center justify-center bg-gray-100/90 px-3.5 text-gray-500 font-black text-sm select-none border-r border-gray-200 shrink-0">
                                #
                            </span>
                            <input type="text"
                                   name="nomor_meter"
                                   required
                                   placeholder="Contoh: 21 atau 28"
                                   class="w-full text-sm font-bold text-gray-900 border-0 focus:ring-0 focus:outline-none py-2.5 px-3 min-h-[46px] bg-transparent">
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-1.5">
                        Nama Warga (Penghuni) <span class="text-rose-500">*</span>
                    </label>
                    <input type="text"
                           name="nama"
                           required
                           placeholder="Contoh: SUGIYANTO (P. TEBEK)"
                           class="w-full text-sm font-bold text-gray-900 rounded-xl border-gray-300 focus:border-[#CEA042] focus:ring-2 focus:ring-[#CEA042]/20 py-2.5 px-3.5 shadow-xs min-h-[46px]">
                    <span class="text-[11px] text-gray-400 mt-1 block">Nama orang yang sehari-hari tinggal dan membayar tagihan</span>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-1.5">
                        Atas Nama Rekening PLN <span class="text-rose-500">*</span>
                    </label>
                    <input type="text"
                           name="nama_pelanggan_pln"
                           required
                           placeholder="Contoh: SUGIYANTO"
                           class="w-full text-sm font-semibold text-gray-900 rounded-xl border-gray-300 focus:border-[#CEA042] focus:ring-2 focus:ring-[#CEA042]/20 py-2.5 px-3.5 shadow-xs min-h-[46px]">
                    <span class="text-[11px] text-gray-400 mt-1 block">Sesuai nama pendaftaran PLN yang tertera di struk fisik</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label class="block text-xs font-bold text-gray-800 mb-1.5">
                            No. HP / WhatsApp <span class="text-gray-400 font-normal lowercase">(opsional)</span>
                        </label>
                        <input type="text"
                               name="no_telepon"
                               placeholder="08xxxxxxxxxx"
                               class="w-full text-sm font-normal text-gray-900 rounded-xl border-gray-300 focus:border-[#CEA042] focus:ring-2 focus:ring-[#CEA042]/20 py-2.5 px-3.5 shadow-xs min-h-[46px]">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-800 mb-1.5">
                            Keterangan Alamat <span class="text-gray-400 font-normal lowercase">(opsional)</span>
                        </label>
                        <input type="text"
                               name="alamat"
                               placeholder="Dusun Demangan RT 01"
                               class="w-full text-sm font-normal text-gray-900 rounded-xl border-gray-300 focus:border-[#CEA042] focus:ring-2 focus:ring-[#CEA042]/20 py-2.5 px-3.5 shadow-xs min-h-[46px]">
                    </div>
                </div>

                <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2.5">
                    <button type="button" @click="modalTambah = false"
                            class="px-4 py-2.5 text-xs font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition min-h-[46px]">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2.5 text-xs font-bold text-white bg-[#CEA042] hover:bg-[#BA8832] rounded-xl shadow-xs transition min-h-[46px]">
                        Simpan Data Warga
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL EDIT WARGA -->
    <div x-show="modalEdit" x-cloak
         class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
        <div @click.away="modalEdit = false"
             class="bg-white rounded-2xl max-w-lg w-full p-5 sm:p-6 shadow-xl border border-gray-200 space-y-4">
            <div class="flex items-start justify-between border-b border-gray-100 pb-3">
                <div class="space-y-0.5">
                    <h3 class="text-base sm:text-lg font-black text-gray-900 tracking-tight">Ubah Data Master Warga</h3>
                    <p class="text-xs text-gray-500 font-normal">Perbarui informasi rukun tetangga, nomor struk, dan data rekening PLN.</p>
                </div>
                <button type="button" @click="modalEdit = false"
                        class="text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-xl p-1.5 transition text-xl leading-none">&times;</button>
            </div>

            <form method="POST" :action="'/admin/warga/' + wargaEdit.id" class="space-y-4 text-xs">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label class="block text-xs font-bold text-gray-800 mb-1.5">
                            Rukun Tetangga (RT) <span class="text-rose-500">*</span>
                        </label>
                        <select name="rt" x-model="wargaEdit.rt" required
                                class="w-full text-sm font-semibold rounded-xl border-gray-300 focus:border-[#CEA042] focus:ring-2 focus:ring-[#CEA042]/20 py-2.5 px-3 min-h-[46px] shadow-xs">
                            @for($i = 1; $i <= 3; $i++)
                                <option value="{{ $i }}">RT {{ sprintf('%02d', $i) }}</option>
                            @endfor
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-800 mb-1.5">
                            Nomor Struk [ # ] <span class="text-rose-500">*</span>
                        </label>
                        <div class="flex items-stretch rounded-xl border border-gray-300 bg-white shadow-xs focus-within:border-[#CEA042] focus-within:ring-2 focus-within:ring-[#CEA042]/20 overflow-hidden transition-all min-h-[46px]">
                            <span class="flex items-center justify-center bg-gray-100/90 px-3.5 text-gray-500 font-black text-sm select-none border-r border-gray-200 shrink-0">
                                #
                            </span>
                            <input type="text"
                                   name="nomor_meter"
                                   x-model="wargaEdit.nomor_meter"
                                   required
                                   placeholder="Contoh: 21 atau 28"
                                   class="w-full text-sm font-bold text-gray-900 border-0 focus:ring-0 focus:outline-none py-2.5 px-3 min-h-[46px] bg-transparent">
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-1.5">
                        Nama Warga (Penghuni) <span class="text-rose-500">*</span>
                    </label>
                    <input type="text"
                           name="nama"
                           x-model="wargaEdit.nama"
                           required
                           placeholder="Contoh: AM'AD NGASRI"
                           class="w-full text-sm font-bold text-gray-900 rounded-xl border-gray-300 focus:border-[#CEA042] focus:ring-2 focus:ring-[#CEA042]/20 py-2.5 px-3.5 shadow-xs min-h-[46px]">
                    <span class="text-[11px] text-gray-400 mt-1 block">Nama warga yang sehari-hari tinggal dan membayar tagihan</span>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-800 mb-1.5">
                        Atas Nama Rekening PLN <span class="text-rose-500">*</span>
                    </label>
                    <input type="text"
                           name="nama_pelanggan_pln"
                           x-model="wargaEdit.nama_pelanggan_pln"
                           required
                           placeholder="Contoh: AM'AD NGASRI"
                           class="w-full text-sm font-semibold text-gray-900 rounded-xl border-gray-300 focus:border-[#CEA042] focus:ring-2 focus:ring-[#CEA042]/20 py-2.5 px-3.5 shadow-xs min-h-[46px]">
                    <span class="text-[11px] text-gray-400 mt-1 block">Sesuai nama pendaftaran PLN yang tertera di struk fisik</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label class="block text-xs font-bold text-gray-800 mb-1.5">
                            No. HP / WhatsApp <span class="text-gray-400 font-normal lowercase">(opsional)</span>
                        </label>
                        <input type="text"
                               name="no_telepon"
                               x-model="wargaEdit.no_telepon"
                               placeholder="08xxxxxxxxxx"
                               class="w-full text-sm font-normal text-gray-900 rounded-xl border-gray-300 focus:border-[#CEA042] focus:ring-2 focus:ring-[#CEA042]/20 py-2.5 px-3.5 shadow-xs min-h-[46px]">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-800 mb-1.5">
                            Keterangan Alamat <span class="text-gray-400 font-normal lowercase">(opsional)</span>
                        </label>
                        <input type="text"
                               name="alamat"
                               x-model="wargaEdit.alamat"
                               placeholder="Dusun Demangan RT 01"
                               class="w-full text-sm font-normal text-gray-900 rounded-xl border-gray-300 focus:border-[#CEA042] focus:ring-2 focus:ring-[#CEA042]/20 py-2.5 px-3.5 shadow-xs min-h-[46px]">
                    </div>
                </div>

                <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2.5">
                    <button type="button" @click="modalEdit = false"
                            class="px-4 py-2.5 text-xs font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition min-h-[46px]">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2.5 text-xs font-bold text-white bg-[#CEA042] hover:bg-[#BA8832] rounded-xl shadow-xs transition min-h-[46px]">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
