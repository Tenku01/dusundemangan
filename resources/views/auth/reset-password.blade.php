<x-guest-layout>
    <div class="mb-5 text-center">
        <div class="w-12 h-12 rounded-2xl bg-emas-100 text-emas-800 border border-emas-200 mx-auto mb-3 flex items-center justify-center shadow-sm">
            <svg class="w-6 h-6" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
        </div>
        <h1 class="text-xl font-bold text-gray-900 tracking-tight">Atur Kata Sandi Baru</h1>
        <p class="text-xs text-gray-600 mt-1 max-w-sm mx-auto">
            Masukkan kata sandi baru Anda minimal 8 karakter.
        </p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div>
            <label for="email" class="block text-xs font-semibold text-gray-800 mb-1">
                Alamat Email <span class="text-rose-600">*</span>
            </label>
            <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus
                   class="input-field bg-[#F8F7F4]">
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <div>
            <label for="password" class="block text-xs font-semibold text-gray-800 mb-1">
                Kata Sandi Baru <span class="text-rose-600">*</span>
            </label>
            <input id="password" type="password" name="password" required minlength="8" autocomplete="new-password"
                   placeholder="Minimal 8 karakter"
                   class="input-field">
            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
        </div>

        <div>
            <label for="password_confirmation" class="block text-xs font-semibold text-gray-800 mb-1">
                Konfirmasi Kata Sandi Baru <span class="text-rose-600">*</span>
            </label>
            <input id="password_confirmation" type="password" name="password_confirmation" required minlength="8" autocomplete="new-password"
                   placeholder="Ulangi kata sandi baru"
                   class="input-field">
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5" />
        </div>

        <button type="submit" class="btn-primary w-full text-xs sm:text-sm">
            Simpan Kata Sandi Baru
        </button>
    </form>
</x-guest-layout>
