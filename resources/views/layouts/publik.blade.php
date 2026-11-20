<!DOCTYPE html>
<html lang="id" class="h-full bg-[#F8F7F4]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">

    <title>@yield('title', 'Dusun Demangan')</title>

    <!-- Favicon Lingkaran Karang Taruna Demangan -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32.png') }}">
    <link rel="icon" type="image/png" sizes="64x64" href="{{ asset('favicon-64.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('icon-apple-touch.png') }}">

    <!-- PWA Web App Manifest & Meta Tags -->
    <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
    <meta name="theme-color" content="#CEA042">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Dusun Demangan">

    <!-- Font Figtree -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
        /* Mencegah auto-zoom iOS saat input difokuskan tanpa membatasi pinch-zoom aksesibilitas */
        @media screen and (max-width: 768px) {
            input, select, textarea {
                font-size: 16px !important;
            }
        }
    </style>
</head>
<body class="min-h-full flex flex-col font-sans text-gray-900 bg-[#F8F7F4] antialiased selection:bg-emas-100 selection:text-emas-950 pb-20 md:pb-0">

    <!-- Main Navigation Header -->
    <header class="sticky top-0 z-30 bg-white/95 backdrop-blur-sm border-b border-emas-200/70 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16 sm:h-[72px] gap-3">
                <!-- Brand & Logo -->
                <a href="{{ route('beranda') }}" class="flex items-center gap-2.5 sm:gap-3 group rounded-xl p-1 -ml-1 transition">
                    <img src="{{ asset('logo-kartar-dmg-circle.png') }}"
                         alt="Logo Dusun Demangan"
                         width="44" height="44"
                         fetchpriority="high"
                         class="w-10 h-10 sm:w-11 sm:h-11 rounded-full object-cover shadow-sm border border-emas-300 group-hover:scale-105 transition bg-white shrink-0">
                    <div>
                        <div class="text-base sm:text-lg font-bold tracking-tight text-gray-900 group-hover:text-emas-800 transition leading-tight">
                            Dusun Demangan
                        </div>
                        <div class="hidden sm:block text-[11px] font-medium text-gray-500 leading-tight">
                            Karang Taruna &bull; Layanan Warga
                        </div>
                    </div>
                </a>

                <!-- Desktop Navigation Menu -->
                <nav aria-label="Navigasi Utama" class="hidden md:flex items-center gap-1">
                    <a href="{{ route('publik.cek-listrik') }}"
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 text-sm rounded-xl transition min-h-[44px] {{ request()->routeIs('publik.cek-listrik') ? 'bg-emas-100/90 text-emas-900 border border-emas-200 font-semibold' : 'text-gray-700 hover:text-gray-900 hover:bg-gray-100 font-medium' }}">
                        <svg class="w-4 h-4 {{ request()->routeIs('publik.cek-listrik') ? 'text-emas-700' : 'text-gray-500' }}" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <span>Cek Tagihan Listrik</span>
                    </a>
                    <a href="{{ route('publik.buku-kas') }}"
                       class="inline-flex items-center px-3.5 py-2 text-sm rounded-xl transition min-h-[44px] {{ request()->routeIs('publik.buku-kas') ? 'bg-emas-100/90 text-emas-900 border border-emas-200 font-semibold' : 'text-gray-700 hover:text-gray-900 hover:bg-gray-100 font-medium' }}">
                        Transparansi Kas
                    </a>
                    <a href="{{ route('beranda') }}"
                       class="inline-flex items-center px-3.5 py-2 text-sm rounded-xl transition min-h-[44px] {{ request()->routeIs('beranda') ? 'bg-emas-100/90 text-emas-900 border border-emas-200 font-semibold' : 'text-gray-700 hover:text-gray-900 hover:bg-gray-100 font-medium' }}">
                        Katalog Barang
                    </a>
                    <a href="{{ route('peminjaman.cek-status') }}"
                       class="inline-flex items-center px-3.5 py-2 text-sm rounded-xl transition min-h-[44px] {{ request()->routeIs('peminjaman.cek-status') ? 'bg-emas-100/90 text-emas-900 border border-emas-200 font-semibold' : 'text-gray-700 hover:text-gray-900 hover:bg-gray-100 font-medium' }}">
                        Cek Status Pinjam
                    </a>
                </nav>

                <!-- Auth Action & PWA -->
                <div class="flex items-center gap-2">
                    <!-- PWA Install Button (Dynamic) -->
                    <button type="button"
                            id="btn-install-pwa-publik"
                            onclick="triggerPwaInstallPublik()"
                            class="hidden btn-gold-soft px-3 py-1.5 text-xs">
                        <svg class="w-4 h-4 text-emas-700" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        <span>Pasang App</span>
                    </button>

                    @auth
                        <a href="{{ route('admin.dashboard') }}"
                           class="btn-primary px-3.5 sm:px-4 py-2 text-xs sm:text-sm">
                            <svg class="w-4 h-4" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                            <span><span class="hidden sm:inline">Panel </span>Pengurus</span>
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                           class="btn-secondary px-3.5 sm:px-4 py-2 text-xs sm:text-sm">
                            <span>Masuk <span class="hidden sm:inline">Pengurus</span></span>
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="flex-1">
        @if(session('sukses') || $errors->any())
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
                @if(session('sukses'))
                    <div role="status" class="mb-4 rounded-xl bg-emerald-50 border border-emerald-200 p-4 text-emerald-900 flex items-start gap-3 shadow-sm">
                        <svg class="w-5 h-5 text-emerald-600 mt-0.5 shrink-0" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div class="text-xs sm:text-sm font-semibold leading-relaxed">{{ session('sukses') }}</div>
                    </div>
                @endif

                @if($errors->any())
                    <div role="alert" class="mb-4 rounded-xl bg-rose-50 border border-rose-200 p-4 text-rose-900 shadow-sm">
                        <div class="flex items-center gap-2 font-bold text-xs sm:text-sm mb-1.5">
                            <svg class="w-5 h-5 text-rose-600 shrink-0" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Mohon lengkapi isian formulir:
                        </div>
                        <ul class="list-disc list-inside text-xs space-y-1 text-rose-800">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer Publik -->
    <footer class="mt-12 bg-white border-t border-gray-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 text-center sm:text-left">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('logo-kartar-dmg-circle.png') }}"
                         alt="Logo Dusun Demangan"
                         width="36" height="36"
                         class="w-9 h-9 rounded-full object-cover shadow-sm border border-emas-300 shrink-0 bg-white">
                    <div class="text-xs text-gray-600">
                        <strong class="block text-gray-900 font-bold">Dusun Demangan</strong>
                        Layanan dan Informasi Transparan Warga
                    </div>
                </div>

                <div class="text-xs text-gray-500">
                    &copy; {{ date('Y') }} Karang Taruna Dusun Demangan, Desa Candimulyo.
                </div>
            </div>
        </div>
    </footer>

    <!-- MOBILE STICKY BOTTOM NAVIGATION BAR (Khusus Pengguna HP) -->
    <nav aria-label="Navigasi Mobile" class="md:hidden fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-sm border-t border-gray-200 shadow-lg px-1.5 py-1.5 flex justify-around items-center">
        <!-- Tab 1: Cek Tagihan Listrik -->
        <a href="{{ route('publik.cek-listrik') }}"
           class="flex flex-col items-center justify-center flex-1 max-w-[74px] py-1.5 px-1 rounded-xl transition min-h-[44px] {{ request()->routeIs('publik.cek-listrik') ? 'bg-emas-50 text-emas-800 font-bold' : 'text-gray-600 hover:text-gray-900 font-medium' }}">
            <svg class="w-5 h-5 mb-0.5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            <span class="text-[11px] leading-tight">Listrik</span>
        </a>

        <!-- Tab 2: Transparansi Kas -->
        <a href="{{ route('publik.buku-kas') }}"
           class="flex flex-col items-center justify-center flex-1 max-w-[74px] py-1.5 px-1 rounded-xl transition min-h-[44px] {{ request()->routeIs('publik.buku-kas') ? 'bg-emas-50 text-emas-800 font-bold' : 'text-gray-600 hover:text-gray-900 font-medium' }}">
            <svg class="w-5 h-5 mb-0.5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span class="text-[11px] leading-tight">Buku Kas</span>
        </a>

        <!-- Tab 3: Katalog -->
        <a href="{{ route('beranda') }}"
           class="flex flex-col items-center justify-center flex-1 max-w-[74px] py-1.5 px-1 rounded-xl transition min-h-[44px] {{ request()->routeIs('beranda') ? 'bg-emas-50 text-emas-800 font-bold' : 'text-gray-600 hover:text-gray-900 font-medium' }}">
            <svg class="w-5 h-5 mb-0.5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            <span class="text-[11px] leading-tight">Katalog</span>
        </a>

        <!-- Tab 4: Lacak Status Tiket -->
        <a href="{{ route('peminjaman.cek-status') }}"
           class="flex flex-col items-center justify-center flex-1 max-w-[74px] py-1.5 px-1 rounded-xl transition min-h-[44px] {{ request()->routeIs('peminjaman.cek-status') ? 'bg-emas-50 text-emas-800 font-bold' : 'text-gray-600 hover:text-gray-900 font-medium' }}">
            <svg class="w-5 h-5 mb-0.5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            <span class="text-[11px] leading-tight">Cek Resi</span>
        </a>

        <!-- Tab 5: Pengurus / Masuk -->
        <a href="{{ auth()->check() ? route('admin.dashboard') : route('login') }}"
           class="flex flex-col items-center justify-center flex-1 max-w-[74px] py-1.5 px-1 rounded-xl transition min-h-[44px] {{ request()->routeIs('admin.*') || request()->routeIs('login') ? 'bg-emas-50 text-emas-800 font-bold' : 'text-gray-600 hover:text-gray-900 font-medium' }}">
            <svg class="w-5 h-5 mb-0.5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            <span class="text-[11px] leading-tight">{{ auth()->check() ? 'Admin' : 'Masuk' }}</span>
        </a>
    </nav>

    <x-pwa-install-ios trigger-button-id="btn-install-pwa-publik" trigger-display-class="inline-flex" />

    @stack('scripts')
</body>
</html>
