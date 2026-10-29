<x-guest-layout>
    <div class="mb-5 text-center">
        <div class="w-12 h-12 rounded-2xl bg-amber-50 text-[#CEA042] border border-amber-200 mx-auto mb-3 flex items-center justify-center shadow-xs">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
        </div>
        <h2 class="text-xl font-bold text-gray-900 tracking-tight">Lupa Kata Sandi?</h2>
        <p class="text-xs text-gray-500 mt-1 max-w-sm mx-auto">
            Masukkan alamat email akun pengurus Anda. Tautan pemulihan akan dikirimkan ke kotak masuk email Anda.
        </p>
    </div>

    <!-- Session Status Notifikasi Sukses -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs font-semibold text-gray-700 mb-1">
                Alamat Email Terdaftar <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </span>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                       placeholder="nama@demangan.id"
                       class="w-full text-xs rounded-lg border-gray-300 focus:border-[#CEA042] focus:ring-1 focus:ring-[#CEA042] py-2.5 pl-9 pr-3 shadow-xs min-h-[44px]">
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <button type="submit"
                class="w-full flex items-center justify-center gap-2 py-2.5 px-4 text-xs font-bold text-white bg-[#CEA042] hover:bg-[#BA8832] rounded-lg shadow-xs transition focus:outline-none focus-visible:ring-2 focus-visible:ring-[#CEA042] min-h-[44px]">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            Kirim Tautan Reset Sandi
        </button>

        <div class="text-center pt-2 border-t border-gray-100">
            <a href="{{ route('login') }}" class="text-xs font-semibold text-gray-500 hover:text-[#CEA042] transition inline-flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali ke Halaman Masuk
            </a>
        </div>
    </form>
</x-guest-layout>
