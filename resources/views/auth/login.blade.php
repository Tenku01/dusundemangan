<x-guest-layout>
    <div class="mb-5 text-center">
        <h1 class="text-xl font-bold text-gray-900 tracking-tight">Masuk Panel Pengurus</h1>
        <p class="text-xs text-gray-600 mt-1">Gunakan akun admin atau superadmin untuk mengelola sistem.</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div>
            <label for="email" class="block text-xs font-semibold text-gray-800 mb-1">
                Alamat Email <span class="text-rose-600">*</span>
            </label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                   placeholder="nama@demangan.id"
                   class="input-field">
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <div>
            <label for="password" class="block text-xs font-semibold text-gray-800 mb-1">
                Kata Sandi <span class="text-rose-600">*</span>
            </label>
            <input id="password" type="password" name="password" required autocomplete="current-password"
                   placeholder="Masukkan kata sandi..."
                   class="input-field">
            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
        </div>

        <div class="flex items-center justify-between text-xs pt-1">
            <label for="remember_me" class="inline-flex items-center text-gray-700 py-1">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-[#CEA042] focus:ring-[#CEA042] w-4 h-4" name="remember">
                <span class="ms-2 font-medium">Ingat sesi saya</span>
            </label>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}"
                   class="font-semibold text-emas-800 hover:text-emas-950 transition underline underline-offset-2 py-1">
                    Lupa sandi?
                </a>
            @endif
        </div>

        <button type="submit" class="btn-primary w-full text-xs sm:text-sm">
            Masuk ke Panel Pengurus
        </button>
    </form>
</x-guest-layout>
