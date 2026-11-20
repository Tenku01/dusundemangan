<x-guest-layout>
    <div class="mb-5 text-center">
        <div class="w-12 h-12 rounded-2xl bg-emas-100 text-emas-800 border border-emas-200 mx-auto mb-3 flex items-center justify-center shadow-sm">
            <svg class="w-6 h-6" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
        </div>
        <h1 class="text-xl font-bold text-gray-900 tracking-tight">Lupa Kata Sandi?</h1>
        <p class="text-xs text-gray-600 mt-1 max-w-sm mx-auto leading-relaxed">
            Masukkan alamat email akun pengurus Anda. Tautan pemulihan akan dikirimkan ke kotak masuk email Anda.
        </p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <div>
            <label for="email" class="block text-xs font-semibold text-gray-800 mb-1">
                Alamat Email Terdaftar <span class="text-rose-600">*</span>
            </label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                    <svg class="w-4 h-4" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </span>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                       placeholder="nama@demangan.id"
                       class="input-field pl-10 pr-3.5">
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <button type="submit" class="btn-primary w-full text-xs sm:text-sm">
            <svg class="w-4 h-4" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            <span>Kirim Tautan Reset Sandi</span>
        </button>

        <div class="text-center pt-2 border-t border-gray-100">
            <a href="{{ route('login') }}" class="text-xs font-semibold text-gray-600 hover:text-emas-800 transition inline-flex items-center gap-1.5 py-2 px-3 rounded-lg">
                <svg class="w-3.5 h-3.5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Kembali ke Halaman Masuk</span>
            </a>
        </div>
    </form>
</x-guest-layout>
