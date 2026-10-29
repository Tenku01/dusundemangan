@extends('layouts.admin')

@section('title', 'Kelola Akun Pengurus (Superadmin)')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="bg-white rounded-xl border border-gray-200/90 p-5 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <span class="text-xs font-semibold text-purple-700 uppercase tracking-wider block mb-1">
                Akses Khusus Superadmin
            </span>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">
                Manajemen Akun Pengurus & Admin Dusun
            </h1>
            <p class="text-xs text-gray-500 mt-1">
                Atur akun pengurus harian yang berhak mengelola inventaris barang dan pencatatan kas.
            </p>
        </div>

        <button type="button" onclick="bukaModalPengguna()"
                class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-bold text-white bg-[#CEA042] hover:bg-[#BA8832] rounded-xl shadow-xs transition focus:outline-none focus-visible:ring-2 focus-visible:ring-[#CEA042] self-start sm:self-auto min-h-[44px]">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
            + Tambah Akun Admin Baru
        </button>
    </div>

    <!-- Tabel Pengguna -->
    <!-- TAMPILAN MOBILE: KARTU PENGGUNA RAMAH HP (block md:hidden) -->
    <div class="block md:hidden space-y-3.5">
        @forelse($daftarPengguna as $u)
            <div class="bg-white rounded-2xl border border-gray-200/90 p-4 shadow-xs space-y-3">
                <div class="flex items-start justify-between gap-2 pb-2.5 border-b border-gray-100">
                    <div>
                        <div class="font-bold text-gray-900 text-sm">
                            {{ $u->name }}
                            @if($u->id === auth()->id())
                                <span class="text-[10px] text-[#CEA042] font-semibold">(Akun Anda)</span>
                            @endif
                        </div>
                        <div class="text-xs text-gray-500 font-mono mt-0.5">{{ $u->email }}</div>
                    </div>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase shrink-0 {{ $u->peran === 'superadmin' ? 'bg-purple-100 text-purple-700' : ($u->peran === 'admin' ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-700') }}">
                        {{ $u->peran }}
                    </span>
                </div>

                <div class="flex items-center justify-between text-xs text-gray-600">
                    <div>
                        <span class="text-gray-400">No. WhatsApp:</span>
                        <span class="font-medium text-gray-800">{{ $u->no_hp ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $u->status === 'aktif' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                            {{ $u->status }}
                        </span>
                    </div>
                </div>

                <!-- Hak Akses Menu -->
                <div class="pt-2 border-t border-gray-100">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400 block mb-1.5">Hak Akses Menu:</span>
                    @if($u->isSuperadmin())
                        <span class="inline-flex items-center gap-1 text-[11px] px-2.5 py-1 rounded-lg bg-purple-100 text-purple-800 font-bold">
                            👑 Semua Menu (Akses Penuh)
                        </span>
                    @elseif($u->isAdmin())
                        <div class="flex flex-wrap gap-1">
                            @foreach(\App\Models\User::DAFTAR_MENU as $mKey => $mInfo)
                                @if($u->hasMenuAkses($mKey))
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-semibold bg-amber-50 text-amber-900 border border-amber-200">
                                        {{ $mInfo['label'] }}
                                    </span>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <span class="text-xs text-gray-400 italic">Portal Publik Warga</span>
                    @endif
                </div>

                <!-- Action Button Mobile -->
                @if($u->id !== auth()->id() && $u->peran !== 'superadmin')
                    <div class="grid grid-cols-3 gap-2 pt-2 border-t border-gray-100">
                        <button type="button"
                                onclick="bukaModalHakAkses({{ $u->id }}, '{{ addslashes($u->name) }}', {{ json_encode($u->getDaftarMenuAkses()) }})"
                                class="w-full py-2.5 px-2 text-[11px] font-bold text-[#CEA042] bg-amber-50 hover:bg-amber-100 border border-amber-300 rounded-xl transition min-h-[44px] flex items-center justify-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            Menu
                        </button>

                        <form action="{{ route('admin.pengguna.status', $u) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                    class="w-full py-2.5 px-2 text-[11px] font-semibold rounded-xl transition min-h-[44px] {{ $u->status === 'aktif' ? 'text-amber-800 bg-amber-50 hover:bg-amber-100 border border-amber-200' : 'text-emerald-800 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200' }}">
                                {{ $u->status === 'aktif' ? 'Nonaktif' : 'Aktifkan' }}
                            </button>
                        </form>

                        <form action="{{ route('admin.pengguna.destroy', $u) }}" method="POST"
                              onsubmit="return confirm('Hapus akun {{ addslashes($u->name) }}?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full py-2.5 px-2 text-[11px] font-semibold text-rose-700 bg-rose-50 hover:bg-rose-100 rounded-xl border border-rose-200 transition min-h-[44px]">
                                Hapus
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        @empty
            <div class="bg-white rounded-xl border border-gray-200 p-8 text-center text-gray-400 text-xs">
                Belum ada data pengguna.
            </div>
        @endforelse
    </div>

    <!-- TAMPILAN DESKTOP: TABEL PENGGUNA (hidden md:block) -->
    <div class="hidden md:block bg-white rounded-xl border border-gray-200/90 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-left text-xs">
                <thead class="bg-gray-50 text-gray-600 font-semibold uppercase tracking-wider">
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
                        <tr class="hover:bg-gray-50/80 transition">
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <div class="font-bold text-gray-900">{{ $u->name }}</div>
                                @if($u->id === auth()->id())
                                    <span class="text-[10px] text-[#CEA042] font-semibold">(Akun Anda)</span>
                                @endif
                                <div class="text-[11px] text-gray-400">{{ $u->no_hp ?? '-' }}</div>
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap text-gray-700 font-mono text-[11px]">
                                {{ $u->email }}
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $u->peran === 'superadmin' ? 'bg-purple-100 text-purple-700' : ($u->peran === 'admin' ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-700') }}">
                                    {{ $u->peran }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                @if($u->isSuperadmin())
                                    <span class="inline-flex items-center gap-1 text-[10px] px-2.5 py-0.5 rounded-full bg-purple-100 text-purple-800 font-bold whitespace-nowrap">
                                        👑 Semua Menu (Akses Penuh)
                                    </span>
                                @elseif($u->isAdmin())
                                    <div class="flex flex-wrap gap-1 max-w-xs">
                                        @foreach(\App\Models\User::DAFTAR_MENU as $mKey => $mInfo)
                                            @if($u->hasMenuAkses($mKey))
                                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-semibold bg-amber-50 text-amber-900 border border-amber-200">
                                                    {{ $mInfo['label'] }}
                                                </span>
                                            @endif
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400 italic">Portal Publik</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $u->status === 'aktif' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                    {{ $u->status }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap text-gray-500">
                                {{ $u->created_at->format('d/m/Y') }}
                            </td>
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                @if($u->id !== auth()->id() && $u->peran !== 'superadmin')
                                    <div class="inline-flex items-center gap-1.5">
                                        <!-- Tombol Edit Hak Akses -->
                                        <button type="button"
                                                onclick="bukaModalHakAkses({{ $u->id }}, '{{ addslashes($u->name) }}', {{ json_encode($u->getDaftarMenuAkses()) }})"
                                                class="px-2.5 py-1 text-xs font-bold text-[#CEA042] bg-amber-50 hover:bg-amber-100 border border-amber-300 rounded-md transition flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                            Hak Akses
                                        </button>

                                        <!-- Toggle Status Aktif/Nonaktif -->
                                        <form action="{{ route('admin.pengguna.status', $u) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    class="px-2.5 py-1 text-xs font-medium rounded-md transition {{ $u->status === 'aktif' ? 'text-amber-800 bg-amber-50 hover:bg-amber-100' : 'text-emerald-800 bg-emerald-50 hover:bg-emerald-100' }}">
                                                {{ $u->status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </button>
                                        </form>

                                        <!-- Hapus Akun -->
                                        <form action="{{ route('admin.pengguna.destroy', $u) }}" method="POST"
                                              onsubmit="return confirm('Hapus akun {{ addslashes($u->name) }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-2.5 py-1 text-xs font-medium text-rose-700 bg-rose-50 hover:bg-rose-100 rounded-md transition">
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
                            <td colspan="7" class="py-8 text-center text-gray-400">
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

</div>

<!-- MODAL TAMBAH ADMIN BARU (Bottom Sheet Ramah HP) -->
<div id="modal-pengguna" class="fixed inset-0 z-50 hidden bg-gray-900/60 backdrop-blur-xs flex items-end sm:items-center justify-center p-0 sm:p-4 overflow-y-auto">
    <div class="bg-white rounded-t-3xl sm:rounded-2xl max-w-lg w-full p-5 sm:p-6 shadow-2xl border border-gray-200 max-h-[92vh] overflow-y-auto">
        <!-- Drag Handle Indicator untuk Mobile Phone -->
        <div class="w-12 h-1.5 bg-gray-300 rounded-full mx-auto mb-3 sm:hidden"></div>

        <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
            <div>
                <h3 class="text-base font-bold text-gray-900 leading-tight">Buat Akun Admin Pengurus Baru</h3>
                <p class="text-xs text-gray-500 mt-0.5">Berikan akses manajemen sistem kepada pengurus.</p>
            </div>
            <button type="button" onclick="tutupModalPengguna()" class="text-gray-400 hover:text-gray-700 p-2 rounded-xl focus:outline-none min-h-[44px] min-w-[44px] flex items-center justify-center bg-gray-100 sm:bg-transparent">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form action="{{ route('admin.pengguna.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Lengkap Pengurus <span class="text-rose-500">*</span></label>
                <input type="text" name="name" required placeholder="Contoh: Rian Hendrawan (Seksi Perlengkapan)"
                       class="w-full text-base sm:text-xs rounded-xl border-gray-300 focus:border-[#CEA042] focus:ring-1 focus:ring-[#CEA042] py-2.5 px-3.5 shadow-xs min-h-[46px]">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Alamat Email Login <span class="text-rose-500">*</span></label>
                <input type="email" name="email" required placeholder="Contoh: rian@demangan.id"
                       class="w-full text-base sm:text-xs rounded-xl border-gray-300 focus:border-[#CEA042] focus:ring-1 focus:ring-[#CEA042] py-2.5 px-3.5 shadow-xs min-h-[46px]">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Nomor WhatsApp / HP</label>
                    <input type="tel" inputmode="tel" name="no_hp" placeholder="Contoh: 08123456789"
                           class="w-full text-base sm:text-xs rounded-xl border-gray-300 focus:border-[#CEA042] focus:ring-1 focus:ring-[#CEA042] py-2.5 px-3.5 shadow-xs min-h-[46px]">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Tingkat Hak Akses <span class="text-rose-500">*</span></label>
                    <select name="peran" id="tambah-peran-select" onchange="toggleTambahMenuContainer(this.value)" required class="w-full text-base sm:text-xs rounded-xl border-gray-300 focus:border-[#CEA042] focus:ring-1 focus:ring-[#CEA042] py-2.5 px-3.5 shadow-xs min-h-[46px]">
                        <option value="admin">Admin (Pengurus)</option>
                        <option value="warga">Warga Terdaftar</option>
                    </select>
                </div>
            </div>

            <!-- Checkbox Pilihan Menu Akses Pengurus -->
            <div id="container-tambah-menu-akses" class="space-y-2 p-3 bg-amber-50/50 rounded-2xl border border-amber-200/80">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-gray-800 block">Pilih Menu Yang Dapat Diakses</span>
                        <span class="text-[11px] text-gray-500">Centang menu yang diizinkan untuk admin ini</span>
                    </div>
                </div>

                <!-- Quick Presets -->
                <div class="flex flex-wrap gap-1.5 pt-1">
                    <button type="button" onclick="setPresetTambah('semua')" class="px-2 py-1 text-[10px] font-bold bg-white hover:bg-amber-100 text-gray-700 border border-gray-200 rounded-lg transition">
                        Semua Menu
                    </button>
                    <button type="button" onclick="setPresetTambah('listrik')" class="px-2 py-1 text-[10px] font-bold bg-white hover:bg-amber-100 text-gray-700 border border-gray-200 rounded-lg transition">
                        ⚡ Petugas Listrik
                    </button>
                    <button type="button" onclick="setPresetTambah('kas')" class="px-2 py-1 text-[10px] font-bold bg-white hover:bg-amber-100 text-gray-700 border border-gray-200 rounded-lg transition">
                        💰 Bendahara
                    </button>
                    <button type="button" onclick="setPresetTambah('barang')" class="px-2 py-1 text-[10px] font-bold bg-white hover:bg-amber-100 text-gray-700 border border-gray-200 rounded-lg transition">
                        📦 Inventaris
                    </button>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-1.5">
                    @foreach(\App\Models\User::DAFTAR_MENU as $mKey => $mInfo)
                        <label class="flex items-start gap-2.5 p-2 rounded-xl bg-white border border-gray-200 hover:border-[#CEA042] cursor-pointer transition">
                            <input type="checkbox" name="menu_akses[]" value="{{ $mKey }}" checked
                                   class="checkbox-tambah-menu mt-0.5 rounded text-[#CEA042] focus:ring-[#CEA042] w-4 h-4">
                            <div class="min-w-0">
                                <span class="text-xs font-bold text-gray-900 block leading-tight">{{ $mInfo['label'] }}</span>
                                <span class="text-[10px] text-gray-500 leading-tight block truncate">{{ $mInfo['deskripsi'] }}</span>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Kata Sandi <span class="text-rose-500">*</span></label>
                    <input type="password" name="password" required minlength="8" placeholder="Minimal 8 karakter"
                           class="w-full text-base sm:text-xs rounded-xl border-gray-300 focus:border-[#CEA042] focus:ring-1 focus:ring-[#CEA042] py-2.5 px-3.5 shadow-xs min-h-[46px]">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Ulangi Sandi <span class="text-rose-500">*</span></label>
                    <input type="password" name="password_confirmation" required minlength="8" placeholder="Ulangi sandi"
                           class="w-full text-base sm:text-xs rounded-xl border-gray-300 focus:border-[#CEA042] focus:ring-1 focus:ring-[#CEA042] py-2.5 px-3.5 shadow-xs min-h-[46px]">
                </div>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-gray-100">
                <button type="button" onclick="tutupModalPengguna()" class="px-4 py-2.5 text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition min-h-[44px]">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 text-xs font-bold text-white bg-[#CEA042] hover:bg-[#BA8832] rounded-xl shadow-xs transition min-h-[44px]">
                    Simpan Akun Baru
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL UBAH HAK AKSES MENU PENGURUS (Bottom Sheet Ramah HP) -->
<div id="modal-hak-akses" class="fixed inset-0 z-50 hidden bg-gray-900/60 backdrop-blur-xs flex items-end sm:items-center justify-center p-0 sm:p-4 overflow-y-auto">
    <div class="bg-white rounded-t-3xl sm:rounded-2xl max-w-lg w-full p-5 sm:p-6 shadow-2xl border border-gray-200 max-h-[92vh] overflow-y-auto">
        <!-- Drag Handle Indicator untuk Mobile Phone -->
        <div class="w-12 h-1.5 bg-gray-300 rounded-full mx-auto mb-3 sm:hidden"></div>

        <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-amber-100 text-[#CEA042] flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900 leading-tight">Ubah Hak Akses Menu</h3>
                    <p class="text-xs text-gray-500 mt-0.5" id="label-nama-pengguna-akses">Pengurus: -</p>
                </div>
            </div>
            <button type="button" onclick="tutupModalHakAkses()" class="text-gray-400 hover:text-gray-700 p-2 rounded-xl focus:outline-none min-h-[44px] min-w-[44px] flex items-center justify-center bg-gray-100 sm:bg-transparent">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form id="form-hak-akses" method="POST" action="" class="space-y-4">
            @csrf
            @method('PATCH')

            <!-- Preset Shortcut Cepat -->
            <div class="p-3 bg-gray-50 rounded-2xl border border-gray-200/80 space-y-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-gray-500 block">Pilihan Cepat (Preset):</span>
                <div class="flex flex-wrap gap-1.5">
                    <button type="button" onclick="setPresetEdit('semua')" class="px-2.5 py-1 text-xs font-semibold bg-white hover:bg-amber-50 text-gray-700 hover:text-[#CEA042] border border-gray-200 rounded-lg transition min-h-[36px]">
                        ✨ Semua Menu
                    </button>
                    <button type="button" onclick="setPresetEdit('listrik')" class="px-2.5 py-1 text-xs font-semibold bg-white hover:bg-amber-50 text-gray-700 hover:text-[#CEA042] border border-gray-200 rounded-lg transition min-h-[36px]">
                        ⚡ Petugas Listrik
                    </button>
                    <button type="button" onclick="setPresetEdit('kas')" class="px-2.5 py-1 text-xs font-semibold bg-white hover:bg-amber-50 text-gray-700 hover:text-[#CEA042] border border-gray-200 rounded-lg transition min-h-[36px]">
                        💰 Bendahara (Kas)
                    </button>
                    <button type="button" onclick="setPresetEdit('barang')" class="px-2.5 py-1 text-xs font-semibold bg-white hover:bg-amber-50 text-gray-700 hover:text-[#CEA042] border border-gray-200 rounded-lg transition min-h-[36px]">
                        📦 Inventaris Barang
                    </button>
                </div>
            </div>

            <!-- Daftar Checkbox Menu -->
            <div class="space-y-2">
                <span class="text-xs font-bold text-gray-800 block">Daftar Menu Sistem</span>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                    @foreach(\App\Models\User::DAFTAR_MENU as $mKey => $mInfo)
                        <label class="flex items-start gap-3 p-3 rounded-xl bg-gray-50 hover:bg-amber-50/50 border border-gray-200 hover:border-[#CEA042] cursor-pointer transition">
                            <input type="checkbox" name="menu_akses[]" value="{{ $mKey }}" id="edit-menu-{{ $mKey }}"
                                   class="checkbox-edit-menu mt-0.5 rounded text-[#CEA042] focus:ring-[#CEA042] w-4 h-4">
                            <div class="min-w-0">
                                <span class="text-xs font-bold text-gray-900 block leading-tight">{{ $mInfo['label'] }}</span>
                                <span class="text-[11px] text-gray-500 leading-tight block mt-0.5">{{ $mInfo['deskripsi'] }}</span>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-gray-100">
                <button type="button" onclick="tutupModalHakAkses()" class="px-4 py-2.5 text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition min-h-[44px]">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 text-xs font-bold text-white bg-[#CEA042] hover:bg-[#BA8832] rounded-xl shadow-xs transition min-h-[44px]">
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

