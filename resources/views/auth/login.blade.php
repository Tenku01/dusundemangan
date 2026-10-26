<x-guest-layout>
    <div class="mb-5 text-center">
        <h2 class="text-xl font-bold text-gray-900 tracking-tight">Masuk Panel Pengurus</h2>
        <p class="text-xs text-gray-500 mt-1">Gunakan akun admin atau superadmin untuk mengelola sistem.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs font-semibold text-gray-700 mb-1">
                Alamat Email <span class="text-rose-500">*</span>
            </label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                   placeholder="nama@demangan.id"
                   class="w-full text-xs rounded-lg border-gray-300 focus:border-[#CEA042] focus:ring-1 focus:ring-[#CEA042] py-2.5 px-3 shadow-xs">
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-xs font-semibold text-gray-700 mb-1">
                Kata Sandi <span class="text-rose-500">*</span>
            </label>
            <input id="password" type="password" name="password" required autocomplete="current-password"
                   placeholder="Masukkan kata sandi..."
                   class="w-full text-xs rounded-lg border-gray-300 focus:border-[#CEA042] focus:ring-1 focus:ring-[#CEA042] py-2.5 px-3 shadow-xs">
            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between text-xs pt-1">
            <label for="remember_me" class="inline-flex items-center text-gray-600">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-[#CEA042] focus:ring-[#CEA042]" name="remember">
                <span class="ms-2">Ingat sesi saya</span>
            </label>
        </div>

        <button type="submit"
                class="w-full flex items-center justify-center py-2.5 px-4 text-xs font-bold text-white bg-[#CEA042] hover:bg-[#BA8832] rounded-lg shadow-xs transition focus:outline-none focus-visible:ring-2 focus-visible:ring-[#CEA042]">
            Masuk ke Panel Pengurus
        </button>
    </form>

    <!-- Info Akun Bawaan (Memudahkan Pengujian & Pengurus Baru) -->
    <div class="mt-6 pt-5 border-t border-gray-100 text-xs text-gray-500 space-y-2">
        <div class="font-semibold text-gray-700">Akun Bawaan Sistem:</div>
        <div class="bg-gray-50 p-2.5 rounded-lg border border-gray-200 text-[11px] space-y-1">
            <div><strong>Superadmin:</strong> <code class="text-purple-700">superadmin@demangan.id</code> / <code class="text-gray-800">password</code></div>
            <div><strong>Admin Pengurus:</strong> <code class="text-amber-800">admin@demangan.id</code> / <code class="text-gray-800">password</code></div>
        </div>
    </div>
</x-guest-layout>
