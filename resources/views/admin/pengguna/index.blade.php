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
            <div class="bg-white rounded-xl border border-gray-200/90 p-4 shadow-xs space-y-3">
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

                <!-- Action Button Mobile -->
                @if($u->id !== auth()->id() && $u->peran !== 'superadmin')
                    <div class="grid grid-cols-2 gap-2 pt-2 border-t border-gray-100">
                        <form action="{{ route('admin.pengguna.status', $u) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                    class="w-full py-2.5 px-3 text-xs font-semibold rounded-xl transition min-h-[44px] {{ $u->status === 'aktif' ? 'text-amber-800 bg-amber-50 hover:bg-amber-100 border border-amber-200' : 'text-emerald-800 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200' }}">
                                {{ $u->status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' }}
                            </button>
                        </form>

                        <form action="{{ route('admin.pengguna.destroy', $u) }}" method="POST"
                              onsubmit="return confirm('Hapus akun {{ addslashes($u->name) }}?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full py-2.5 px-3 text-xs font-semibold text-rose-700 bg-rose-50 hover:bg-rose-100 rounded-xl border border-rose-200 transition min-h-[44px]">
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
                        <th class="py-3.5 px-4">No. HP</th>
                        <th class="py-3.5 px-4">Peran (Role)</th>
                        <th class="py-3.5 px-4">Status Akun</th>
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
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap text-gray-700 font-mono text-[11px]">
                                {{ $u->email }}
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap text-gray-600">
                                {{ $u->no_hp ?? '-' }}
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $u->peran === 'superadmin' ? 'bg-purple-100 text-purple-700' : ($u->peran === 'admin' ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-700') }}">
                                    {{ $u->peran }}
                                </span>
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
    <div class="bg-white rounded-t-3xl sm:rounded-2xl max-w-md w-full p-5 sm:p-6 shadow-2xl border border-gray-200 max-h-[92vh] overflow-y-auto">
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

        <form action="{{ route('admin.pengguna.store') }}" method="POST" class="space-y-3.5">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Lengkap Pengurus <span class="text-rose-500">*</span></label>
                <input type="text" name="name" required placeholder="Contoh: Rian Hendrawan (Seksi Perlengkapan)"
                       class="w-full text-base sm:text-xs rounded-xl border-gray-300 focus:border-purple-500 focus:ring-1 focus:ring-purple-500 py-2.5 px-3.5 shadow-xs min-h-[46px]">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Alamat Email Login <span class="text-rose-500">*</span></label>
                <input type="email" name="email" required placeholder="Contoh: rian@demangan.id"
                       class="w-full text-base sm:text-xs rounded-xl border-gray-300 focus:border-purple-500 focus:ring-1 focus:ring-purple-500 py-2.5 px-3.5 shadow-xs min-h-[46px]">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Nomor WhatsApp / HP</label>
                    <input type="tel" inputmode="tel" name="no_hp" placeholder="Contoh: 08123456789"
                           class="w-full text-base sm:text-xs rounded-xl border-gray-300 focus:border-purple-500 focus:ring-1 focus:ring-purple-500 py-2.5 px-3.5 shadow-xs min-h-[46px]">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Tingkat Hak Akses <span class="text-rose-500">*</span></label>
                    <select name="peran" required class="w-full text-base sm:text-xs rounded-xl border-gray-300 focus:border-purple-500 focus:ring-1 focus:ring-purple-500 py-2.5 px-3.5 shadow-xs min-h-[46px]">
                        <option value="admin">Admin (Pengurus)</option>
                        <option value="warga">Warga Terdaftar</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Kata Sandi <span class="text-rose-500">*</span></label>
                    <input type="password" name="password" required minlength="8" placeholder="Minimal 8 karakter"
                           class="w-full text-base sm:text-xs rounded-xl border-gray-300 focus:border-purple-500 focus:ring-1 focus:ring-purple-500 py-2.5 px-3.5 shadow-xs min-h-[46px]">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Ulangi Sandi <span class="text-rose-500">*</span></label>
                    <input type="password" name="password_confirmation" required minlength="8" placeholder="Ulangi sandi"
                           class="w-full text-base sm:text-xs rounded-xl border-gray-300 focus:border-purple-500 focus:ring-1 focus:ring-purple-500 py-2.5 px-3.5 shadow-xs min-h-[46px]">
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

@push('scripts')
<script>
    function bukaModalPengguna() {
        document.getElementById('modal-pengguna').classList.remove('hidden');
    }
    function tutupModalPengguna() {
        document.getElementById('modal-pengguna').classList.add('hidden');
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            tutupModalPengguna();
        }
    });
</script>
@endpush
@endsection

