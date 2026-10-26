<!DOCTYPE html>
<html lang="id" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">

    <title>@yield('title', 'Sistem Manajemen Inventaris & Keuangan Dusun') | Karang Taruna Demangan</title>

    <!-- Favicon Lingkaran Karang Taruna Demangan -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32.png') }}">
    <link rel="icon" type="image/png" sizes="64x64" href="{{ asset('favicon-64.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('logo-kartar-dmg-circle.png') }}">

    <!-- Font Figtree -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
        /* Prevent iOS zoom on input focus */
        @media screen and (max-width: 768px) {
            input, select, textarea {
                font-size: 16px !important;
            }
        }
    </style>
</head>
<body class="min-h-full flex flex-col font-sans text-gray-900 bg-gray-50 antialiased selection:bg-amber-100 selection:text-amber-900 pb-20 md:pb-0">

    <!-- Top Identity Bar -->
    <div class="bg-[#CEA042] text-white text-xs py-1.5 px-4">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <span class="font-medium tracking-wide truncate">Karang Taruna Dusun Demangan</span>
            <span class="text-amber-100 text-[11px] shrink-0 font-medium">Layanan Warga</span>
        </div>
    </div>

    <!-- Main Navigation Header -->
    <header class="sticky top-0 z-30 bg-white border-b border-gray-200/80 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16 sm:h-20">
                <!-- Brand & Logo -->
                <a href="{{ route('beranda') }}" class="flex items-center gap-2.5 sm:gap-3.5 group focus:outline-none focus-visible:ring-2 focus-visible:ring-[#CEA042] rounded-lg p-1">
                    <img src="{{ asset('logo-kartar-dmg-circle.png') }}"
                         alt="Logo Karang Taruna Demangan"
                         class="w-10 h-10 sm:w-11 sm:h-11 rounded-full object-cover shadow-xs border border-amber-200/90 group-hover:scale-105 transition shrink-0 bg-white">
                    <div>
                        <div class="text-sm sm:text-lg font-bold tracking-tight text-gray-900 group-hover:text-[#CEA042] transition leading-tight">
                            Inventaris & Kas Dusun
                        </div>
                        <div class="text-[10px] sm:text-xs font-semibold text-gray-500 tracking-wide uppercase">
                            Dusun Demangan
                        </div>
                    </div>
                </a>

                <!-- Desktop Navigation Menu -->
                <nav class="hidden md:flex items-center gap-1 sm:gap-2">
                    <a href="{{ route('beranda') }}"
                       class="px-3.5 py-2 text-sm font-medium rounded-lg transition {{ request()->routeIs('beranda') ? 'text-[#CEA042] bg-amber-50 font-semibold' : 'text-gray-700 hover:text-gray-900 hover:bg-gray-100' }}">
                        Katalog Barang
                    </a>
                    <a href="{{ route('publik.buku-kas') }}"
                       class="px-3.5 py-2 text-sm font-medium rounded-lg transition {{ request()->routeIs('publik.buku-kas') ? 'text-[#CEA042] bg-amber-50 font-semibold' : 'text-gray-700 hover:text-gray-900 hover:bg-gray-100' }}">
                        Transparansi Kas
                    </a>
                    <a href="{{ route('peminjaman.cek-status') }}"
                       class="px-3.5 py-2 text-sm font-medium rounded-lg transition {{ request()->routeIs('peminjaman.cek-status') ? 'text-[#CEA042] bg-amber-50 font-semibold' : 'text-gray-700 hover:text-gray-900 hover:bg-gray-100' }}">
                        Cek Status Pinjam
                    </a>
                </nav>

                <!-- Auth Action -->
                <div class="flex items-center gap-2">
                    @auth
                        <a href="{{ route('admin.dashboard') }}"
                           class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 sm:px-4 sm:py-2 text-xs sm:text-sm font-semibold text-white bg-[#CEA042] hover:bg-[#BA8832] rounded-lg shadow-xs transition min-h-[44px]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                            <span class="hidden sm:inline">Panel</span> Pengurus
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                           class="inline-flex items-center justify-center px-3 py-1.5 sm:px-4 sm:py-2 text-xs sm:text-sm font-medium text-gray-700 hover:text-gray-900 bg-gray-100 hover:bg-gray-200 rounded-lg transition min-h-[44px]">
                            Masuk <span class="hidden sm:inline">&nbsp;Pengurus</span>
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="flex-1">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
            @if(session('sukses'))
                <div class="mb-5 rounded-xl bg-emerald-50 border border-emerald-200 p-4 text-emerald-800 flex items-start gap-3 shadow-xs">
                    <svg class="w-5 h-5 text-emerald-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div class="text-xs sm:text-sm font-semibold leading-relaxed">{{ session('sukses') }}</div>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-5 rounded-xl bg-rose-50 border border-rose-200 p-4 text-rose-800 shadow-xs">
                    <div class="flex items-center gap-2 font-bold text-xs sm:text-sm mb-1.5">
                        <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Mohon lengkapi isian formulir:
                    </div>
                    <ul class="list-disc list-inside text-xs space-y-1 text-rose-700">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        @yield('content')
    </main>

    <!-- Footer Publik -->
    <footer class="mt-12 bg-white border-t border-gray-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 text-center sm:text-left">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('logo-kartar-dmg-circle.png') }}"
                         alt="Logo Karang Taruna Demangan"
                         class="w-9 h-9 rounded-full object-cover shadow-xs border border-amber-200 shrink-0 bg-white">
                    <div class="text-xs text-gray-700">
                        <strong class="block text-gray-900 font-bold">Karang Taruna Dusun Demangan</strong>
                        Sistem Inventaris & Kas Transparan Warga
                    </div>
                </div>

                <div class="text-xs text-gray-500">
                    &copy; {{ date('Y') }} Dusun Demangan. Dioptimalkan untuk Smartphone Warga.
                </div>
            </div>
        </div>
    </footer>

    <!-- MOBILE STICKY BOTTOM NAVIGATION BAR (Khusus Pengguna HP) -->
    <nav class="md:hidden fixed bottom-0 left-0 right-0 z-40 bg-white border-t border-gray-200 shadow-lg px-2 py-1.5 flex justify-around items-center">
        <!-- Tab 1: Katalog -->
        <a href="{{ route('beranda') }}"
           class="flex flex-col items-center justify-center w-16 py-1 rounded-lg transition min-h-[44px] {{ request()->routeIs('beranda') ? 'text-[#CEA042] font-bold' : 'text-gray-500 hover:text-gray-800' }}">
            <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            <span class="text-[10px] leading-tight">Katalog</span>
        </a>

        <!-- Tab 2: Lacak Status Tiket -->
        <a href="{{ route('peminjaman.cek-status') }}"
           class="flex flex-col items-center justify-center w-16 py-1 rounded-lg transition min-h-[44px] {{ request()->routeIs('peminjaman.cek-status') ? 'text-[#CEA042] font-bold' : 'text-gray-500 hover:text-gray-800' }}">
            <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            <span class="text-[10px] leading-tight">Cek Resi</span>
        </a>

        <!-- Tab 3: Transparansi Kas -->
        <a href="{{ route('publik.buku-kas') }}"
           class="flex flex-col items-center justify-center w-16 py-1 rounded-lg transition min-h-[44px] {{ request()->routeIs('publik.buku-kas') ? 'text-[#CEA042] font-bold' : 'text-gray-500 hover:text-gray-800' }}">
            <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span class="text-[10px] leading-tight">Buku Kas</span>
        </a>

        <!-- Tab 4: Pengurus / Bantuan -->
        <a href="{{ route('login') }}"
           class="flex flex-col items-center justify-center w-16 py-1 rounded-lg transition min-h-[44px] text-gray-500 hover:text-gray-800">
            <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            <span class="text-[10px] leading-tight">Pengurus</span>
        </a>
    </nav>

    @stack('scripts')
</body>
</html>
