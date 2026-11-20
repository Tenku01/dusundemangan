@extends('layouts.admin')

@section('title', 'Kelola Akun Pengurus (Superadmin)')

@section('content')
<div class="space-y-5 sm:space-y-6">

    <!-- Header -->
    <div class="card-surface p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <span class="text-xs font-bold text-slate-700 uppercase tracking-wider block mb-1">
                Akses Khusus Superadmin
            </span>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">
                Manajemen Akun Pengurus &amp; Admin Dusun
            </h1>
            <p class="text-xs text-gray-600 mt-1">
                Atur akun pengurus harian yang berhak mengelola inventaris barang dan pencatatan kas.
            </p>
        </div>

        <button type="button" onclick="bukaModalPengguna()" class="btn-primary self-start sm:self-auto px-4 text-xs shrink-0">
            <svg class="w-4 h-4" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
            <span>Tambah Akun Admin Baru</span>
        </button>
    </div>

    <!-- TAMPILAN MOBILE: KARTU PENGGUNA -->
    <div class="block md:hidden space-y-3.5">
        @forelse($daftarPengguna as $u)
            <div class="card-surface p-4 space-y-3">
                <div class="flex items-start justify-between gap-2 pb-2.5 border-b border-gray-100">
                    <div>
                        <div class="font-bold text-gray-900 text-sm">
                            {{ $u->name }}
                            @if($u->id === auth()->id())
                                <span class="text-xs text-emas-800 font-semibold">(Akun Anda)</span>
                            @endif
                        </div>
                        <div class="text-xs text-gray-500 font-mono mt-0.5">{{ $u->email }}</div>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-md text-[11px] font-bold uppercase shrink-0 {{ $u->peran === 'superadmin' ? 'bg-slate-900 text-emas-300' : ($u->peran === 'admin' ? 'bg-emas-100 text-emas-900 border border-emas-200' : 'bg-gray-100 text-gray-700') }}">
                        {{ $u->peran }}
                    </span>
                </div>

                <div class="flex items-center justify-between text-xs text-gray-600">
                    <div>
                        <span class="text-gray-500">No. WhatsApp:</span>
                        <span class="font-semibold text-gray-900 tabular-money">{{ $u->no_hp ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="px-2 py-0.5 rounded-md text-[11px] font-bold uppercase {{ $u->status === 'aktif' ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-rose-50 text-rose-800 border border-rose-200' }}">
                            {{ $u->status }}
                        </span>
                    </div>
                </div>

                <div class="pt-2 border-t border-gray-100">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-gray-500 block mb-1.5">Hak Akses Menu:</span>
                    @if($u->isSuperadmin())
                        <span class="inline-flex items-center gap-1.5 text-xs px-2.5 py-1 rounded-lg bg-slate-100 text-slate-900 border border-slate-300 font-bold">
                            <svg class="w-3.5 h-3.5 text-emas-700" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            Semua Menu (Akses Penuh)
                        </span>
                    @elseif($u->isAdmin())
                        <div class="flex flex-wrap gap-1.5">
                            @foreach(\App\Models\User::DAFTAR_MENU as $mKey => $mInfo)
                                @if($u->hasMenuAkses($mKey))
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-emas-50 text-emas-900 border border-emas-200">
                                        {{ $mInfo['label'] }}
                                    </span>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <span class="text-xs text-gray-500 italic">Portal Publik Warga</span>
                    @endif
                </div>

                @if($u->id !== auth()->id() && $u->peran !== 'superadmin')
                    <div class="grid grid-cols-3 gap-2 pt-2 border-t border-gray-100">
                        <button type="button"
                                onclick="bukaModalHakAkses({{ $u->id }}, '{{ addslashes($u->name) }}', {{ json_encode($u->getDaftarMenuAkses()) }})"
                                class="btn-gold-soft w-full px-2 text-xs">
                            <svg class="w-3.5 h-3.5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            <span>Menu</span>
                        </button>

                        <form action="{{ route('admin.pengguna.status', $u) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                    class="w-full py-2.5 px-2 text-xs font-semibold rounded-xl transition min-h-[44px] {{ $u->status === 'aktif' ? 'text-amber-900 bg-amber-50 hover:bg-amber-100 border border-amber-200' : 'text-emerald-900 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200' }}">
                                {{ $u->status === 'aktif' ? 'Nonaktif' : 'Aktifkan' }}
                            </button>
                        </form>

                        <form action="{{ route('admin.pengguna.destroy', $u) }}" method="POST"
                              onsubmit="return confirm('Hapus akun {{ addslashes($u->name) }}?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-danger-soft w-full px-2 text-xs">
                                Hapus
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        @empty
            <div class="card-surface p-8 text-center text-gray-500 text-xs">
                Belum ada data pengguna.
            </div>
        @endforelse
    </div>

    <!-- TAMPILAN DESKTOP: TABEL PENGGUNA -->
    <div class="hidden md:block card-surface overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-left text-xs">
                <thead class="bg-[#F8F7F4] text-gray-600 font-bold uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="py-3.5 px-4">Nama Lengkap</th>
                        <th class="py-3.5 px-4">Email Login</th>
                        <th class="py-3.5 px-4">Peran (Role)</th>
                        <th class="py-3.5 px-4">Hak Akses Menu</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4">Terdaftar</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($daftarPengguna as $u)
                        <tr class="hover:bg-emas-50/40 transition">
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <div class="font-bold text-gray-900">{{ $u->name }}</div>
                                @if($u->id === auth()->id())
                                    <span class="text-[11px] text-emas-800 font-semibold">(Akun Anda)</span>
                                @endif
                                <div class="text-[11px] text-gray-500 tabular-money">{{ $u->no_hp ?? '-' }}</div>
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap text-gray-700 font-mono text-[11px]">
                                {{ $u->email }}
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="px-2.5 py-0.5 rounded-md text-[11px] font-bold uppercase {{ $u->peran === 'superadmin' ? 'bg-slate-900 text-emas-300' : ($u->peran === 'admin' ? 'bg-emas-100 text-emas-900 border border-emas-200' : 'bg-gray-100 text-gray-700') }}">
                                    {{ $u->peran }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                @if($u->isSuperadmin())
                                    <span class="inline-flex items-center gap-1.5 text-[11px] px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-900 border border-slate-300 font-bold whitespace-nowrap">
                                        Semua Menu (Akses Penuh)
                                    </span>
                                @elseif($u->isAdmin())
                                    <div class="flex flex-wrap gap-1 max-w-xs">
                                        @foreach(\App\Models\User::DAFTAR_MENU as $mKey => $mInfo)
                                            @if($u->hasMenuAkses($mKey))
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-emas-50 text-emas-900 border border-emas-200">
                                                    {{ $mInfo['label'] }}
                                                </span>
                                            @endif
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-xs text-gray-500 italic">Portal Publik</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded-md text-[11px] font-bold uppercase {{ $u->status === 'aktif' ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-rose-50 text-rose-800 border border-rose-200' }}">
                                    {{ $u->status }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap text-gray-600 tabular-money">
                                {{ $u->created_at->format('d/m/Y') }}
                            </td>
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                @if($u->id !== auth()->id() && $u->peran !== 'superadmin')
                                    <div class="inline-flex items-center gap-1.5">
                                        <button type="button"
                                                onclick="bukaModalHakAkses({{ $u->id }}, '{{ addslashes($u->name) }}', {{ json_encode($u->getDaftarMenuAkses()) }})"
                                                class="px-2.5 py-1.5 text-xs font-bold text-emas-900 bg-emas-50 hover:bg-emas-100 border border-emas-300 rounded-lg transition inline-flex items-center gap-1 min-h-[36px]">
                                            <svg class="w-3.5 h-3.5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                            <span>Hak Akses</span>
                                        </button>

                                        <form action="{{ route('admin.pengguna.status', $u) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    class="px-2.5 py-1.5 text-xs font-semibold rounded-lg transition min-h-[36px] {{ $u->status === 'aktif' ? 'text-amber-900 bg-amber-50 hover:bg-amber-100 border border-amber-200' : 'text-emerald-900 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200' }}">
                                                {{ $u->status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.pengguna.destroy', $u) }}" method="POST"
                                              onsubmit="return confirm('Hapus akun {{ addslashes($u->name) }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-2.5 py-1.5 text-xs font-semibold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 rounded-lg transition min-h-[36px]">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400">Terkunci</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-10 text-center text-gray-500">
                                Belum ada data pengguna.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-gray-100">
            {{ $daftarPengguna->links() }}
        </div>
    </div>

    <div class="md:hidden">
        {{ $daftarPengguna->links() }}
    </div>

</div>

<!-- MODAL TAMBAH ADMIN BARU -->
<div id="modal-pengguna"
     onclick="if (event.target === this) tutupModalPengguna()"
     role="dialog"
     aria-modal="true"
     aria-labelledby="modal-pengguna-title"
     class="modal-backdrop hidden">
    <div class="modal-panel max-w-lg p-5 sm:p-6">
        <div class="w-12 h-1.5 bg-gray-300 rounded-full mx-auto mb-3 sm:hidden"></div>

        <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
            <div>
                <h3 id="modal-pengguna-title" class="text-base font-bold text-gray-900 leading-tight">Buat Akun Admin Pengurus Baru</h3>
                <p class="text-xs text-gray-600 mt-0.5">Berikan akses manajemen sistem kepada pengurus.</p>
            </div>
            <button type="button" onclick="tutupModalPengguna()" aria-label="Tutup modal tambah admin"
                    class="text-gray-500 hover:text-gray-800 p-2 rounded-xl min-h-[44px] min-w-[44px] flex items-center justify-center bg-gray-100 sm:bg-transparent hover:bg-gray-100 transition">
                <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form action="{{ route('admin.pengguna.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label for="pengguna-nama" class="block text-xs font-semibold text-gray-800 mb-1">Nama Lengkap Pengurus <span class="text-rose-600">*</span></label>
                <input type="text" id="pengguna-nama" name="name" required placeholder="Contoh: Rian Hendrawan (Seksi Perlengkapan)"
                       class="input-field">
            </div>

            <div>
                <label for="pengguna-email" class="block text-xs font-semibold text-gray-800 mb-1">Alamat Email Login <span class="text-rose-600">*</span></label>
                <input type="email" id="pengguna-email" name="email" required placeholder="Contoh: rian@demangan.id"
                       class="input-field">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label for="pengguna-hp" class="block text-xs font-semibold text-gray-800 mb-1">Nomor WhatsApp / HP</label>
                    <input type="tel" id="pengguna-hp" inputmode="tel" name="no_hp" placeholder="Contoh: 08123456789"
                           class="input-field">
                </div>
                <div>
                    <label for="tambah-peran-select" class="block text-xs font-semibold text-gray-800 mb-1">Tingkat Hak Akses <span class="text-rose-600">*</span></label>
                    <select name="peran" id="tambah-peran-select" onchange="toggleTambahMenuContainer(this.value)" required class="select-field">
                        <option value="admin">Admin (Pengurus)</option>
                        <option value="warga">Warga Terdaftar</option>
                    </select>
                </div>
            </div>

            <div id="container-tambah-menu-akses" class="space-y-2.5 p-3.5 bg-emas-50/70 rounded-2xl border border-emas-200">
                <div>
                    <span class="text-xs font-bold text-gray-900 block">Pilih Menu Yang Dapat Diakses</span>
                    <span class="text-[11px] text-gray-600">Centang menu yang diizinkan untuk admin ini</span>
                </div>

                <div class="flex flex-wrap gap-1.5">
                    <button type="button" onclick="setPresetTambah('semua')" class="px-2.5 py-1.5 text-[11px] font-bold bg-white hover:bg-emas-100 text-gray-800 border border-gray-200 rounded-lg transition min-h-[36px]">
                        Semua Menu
                    </button>
                    <button type="button" onclick="setPresetTambah('listrik')" class="px-2.5 py-1.5 text-[11px] font-bold bg-white hover:bg-emas-100 text-gray-800 border border-gray-200 rounded-lg transition min-h-[36px]">
                        Petugas Listrik
                    </button>
                    <button type="button" onclick="setPresetTambah('kas')" class="px-2.5 py-1.5 text-[11px] font-bold bg-white hover:bg-emas-100 text-gray-800 border border-gray-200 rounded-lg transition min-h-[36px]">
                        Bendahara
                    </button>
                    <button type="button" onclick="setPresetTambah('barang')" class="px-2.5 py-1.5 text-[11px] font-bold bg-white hover:bg-emas-100 text-gray-800 border border-gray-200 rounded-lg transition min-h-[36px]">
                        Inventaris
                    </button>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-1">
                    @foreach(\App\Models\User::DAFTAR_MENU as $mKey => $mInfo)
                        <label class="flex items-start gap-2.5 p-2.5 rounded-xl bg-white border border-gray-200 hover:border-[#CEA042] cursor-pointer transition">
                            <input type="checkbox" name="menu_akses[]" value="{{ $mKey }}" checked
                                   class="checkbox-tambah-menu mt-0.5 rounded text-[#CEA042] focus:ring-[#CEA042] w-4 h-4">
                            <div class="min-w-0">
                                <span class="text-xs font-bold text-gray-900 block leading-tight">{{ $mInfo['label'] }}</span>
                                <span class="text-[11px] text-gray-500 leading-tight block truncate">{{ $mInfo['deskripsi'] }}</span>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label for="pengguna-pass" class="block text-xs font-semibold text-gray-800 mb-1">Kata Sandi <span class="text-rose-600">*</span></label>
                    <input type="password" id="pengguna-pass" name="password" required minlength="8" placeholder="Minimal 8 karakter"
                           class="input-field">
                </div>
                <div>
                    <label for="pengguna-pass-confirm" class="block text-xs font-semibold text-gray-800 mb-1">Ulangi Sandi <span class="text-rose-600">*</span></label>
                    <input type="password" id="pengguna-pass-confirm" name="password_confirmation" required minlength="8" placeholder="Ulangi sandi"
                           class="input-field">
                </div>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-gray-100">
                <button type="button" onclick="tutupModalPengguna()" class="btn-subtle px-4 text-xs">
                    Batal
                </button>
                <button type="submit" class="btn-primary px-5 text-xs">
                    Simpan Akun Baru
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL UBAH HAK AKSES MENU PENGURUS -->
<div id="modal-hak-akses"
     onclick="if (event.target === this) tutupModalHakAkses()"
     role="dialog"
     aria-modal="true"
     aria-labelledby="modal-hak-akses-title"
     class="modal-backdrop hidden">
    <div class="modal-panel max-w-lg p-5 sm:p-6">
        <div class="w-12 h-1.5 bg-gray-300 rounded-full mx-auto mb-3 sm:hidden"></div>

        <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-emas-100 text-emas-800 border border-emas-200 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
                <div>
                    <h3 id="modal-hak-akses-title" class="text-base font-bold text-gray-900 leading-tight">Ubah Hak Akses Menu</h3>
                    <p class="text-xs text-gray-600 mt-0.5" id="label-nama-pengguna-akses">Pengurus: -</p>
                </div>
            </div>
            <button type="button" onclick="tutupModalHakAkses()" aria-label="Tutup modal hak akses"
                    class="text-gray-500 hover:text-gray-800 p-2 rounded-xl min-h-[44px] min-w-[44px] flex items-center justify-center bg-gray-100 sm:bg-transparent hover:bg-gray-100 transition">
                <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form id="form-hak-akses" method="POST" action="" class="space-y-4">
            @csrf
            @method('PATCH')

            <div class="p-3 bg-[#F8F7F4] rounded-2xl border border-gray-200 space-y-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-gray-600 block">Pilihan Cepat (Preset):</span>
                <div class="flex flex-wrap gap-1.5">
                    <button type="button" onclick="setPresetEdit('semua')" class="px-2.5 py-1.5 text-xs font-semibold bg-white hover:bg-emas-50 text-gray-800 hover:text-emas-900 border border-gray-200 rounded-lg transition min-h-[38px]">
                        Semua Menu
                    </button>
                    <button type="button" onclick="setPresetEdit('listrik')" class="px-2.5 py-1.5 text-xs font-semibold bg-white hover:bg-emas-50 text-gray-800 hover:text-emas-900 border border-gray-200 rounded-lg transition min-h-[38px]">
                        Petugas Listrik
                    </button>
                    <button type="button" onclick="setPresetEdit('kas')" class="px-2.5 py-1.5 text-xs font-semibold bg-white hover:bg-emas-50 text-gray-800 hover:text-emas-900 border border-gray-200 rounded-lg transition min-h-[38px]">
                        Bendahara (Kas)
                    </button>
                    <button type="button" onclick="setPresetEdit('barang')" class="px-2.5 py-1.5 text-xs font-semibold bg-white hover:bg-emas-50 text-gray-800 hover:text-emas-900 border border-gray-200 rounded-lg transition min-h-[38px]">
                        Inventaris Barang
                    </button>
                </div>
            </div>

            <div class="space-y-2">
                <span class="text-xs font-bold text-gray-800 block">Daftar Menu Sistem</span>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                    @foreach(\App\Models\User::DAFTAR_MENU as $mKey => $mInfo)
                        <label class="flex items-start gap-3 p-3 rounded-xl bg-[#F8F7F4] hover:bg-emas-50/70 border border-gray-200 hover:border-[#CEA042] cursor-pointer transition">
                            <input type="checkbox" name="menu_akses[]" value="{{ $mKey }}" id="edit-menu-{{ $mKey }}"
                                   class="checkbox-edit-menu mt-0.5 rounded text-[#CEA042] focus:ring-[#CEA042] w-4 h-4">
                            <div class="min-w-0">
                                <span class="text-xs font-bold text-gray-900 block leading-tight">{{ $mInfo['label'] }}</span>
                                <span class="text-[11px] text-gray-600 leading-tight block mt-0.5">{{ $mInfo['deskripsi'] }}</span>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-gray-100">
                <button type="button" onclick="tutupModalHakAkses()" class="btn-subtle px-4 text-xs">
                    Batal
                </button>
                <button type="submit" class="btn-primary px-5 text-xs">
                    Simpan Hak Akses
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function bukaModalPengguna() {
        document.getElementById('modal-pengguna').classList.remove('hidden');
    }
    function tutupModalPengguna() {
        document.getElementById('modal-pengguna').classList.add('hidden');
    }

    function toggleTambahMenuContainer(peran) {
        const c = document.getElementById('container-tambah-menu-akses');
        if (c) {
            c.style.display = (peran === 'admin') ? 'block' : 'none';
        }
    }

    function setPresetTambah(preset) {
        const boxes = document.querySelectorAll('.checkbox-tambah-menu');
        boxes.forEach(b => {
            if (preset === 'semua') {
                b.checked = true;
            } else if (preset === 'listrik') {
                b.checked = (b.value === 'rekap' || b.value === 'bayar' || b.value === 'beranda');
            } else if (preset === 'kas') {
                b.checked = (b.value === 'kas' || b.value === 'beranda');
            } else if (preset === 'barang') {
                b.checked = (b.value === 'barang' || b.value === 'beranda');
            }
        });
    }

    function bukaModalHakAkses(userId, userName, userMenus) {
        const modal = document.getElementById('modal-hak-akses');
        const form = document.getElementById('form-hak-akses');
        const label = document.getElementById('label-nama-pengguna-akses');

        form.action = '/admin/pengguna/' + userId + '/menu-akses';
        label.textContent = 'Pengurus: ' + userName;

        const boxes = document.querySelectorAll('.checkbox-edit-menu');
        boxes.forEach(b => {
            b.checked = Array.isArray(userMenus) && userMenus.includes(b.value);
        });

        modal.classList.remove('hidden');
    }

    function tutupModalHakAkses() {
        document.getElementById('modal-hak-akses').classList.add('hidden');
    }

    function setPresetEdit(preset) {
        const boxes = document.querySelectorAll('.checkbox-edit-menu');
        boxes.forEach(b => {
            if (preset === 'semua') {
                b.checked = true;
            } else if (preset === 'listrik') {
                b.checked = (b.value === 'rekap' || b.value === 'bayar' || b.value === 'beranda');
            } else if (preset === 'kas') {
                b.checked = (b.value === 'kas' || b.value === 'beranda');
            } else if (preset === 'barang') {
                b.checked = (b.value === 'barang' || b.value === 'beranda');
            }
        });
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            tutupModalPengguna();
            tutupModalHakAkses();
        }
    });
</script>
@endpush
@endsection
