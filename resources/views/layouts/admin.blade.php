<!DOCTYPE html>
<html lang="id" class="h-full bg-gray-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">

    <title>@yield('title', 'Panel Pengurus') | Karang Taruna Demangan</title>

    <!-- Favicon Lingkaran Karang Taruna Demangan -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32.png') }}">
    <link rel="icon" type="image/png" sizes="64x64" href="{{ asset('favicon-64.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('logo-kartar-dmg-circle.png') }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
        @media screen and (max-width: 768px) {
            input, select, textarea {
                font-size: 16px !important;
            }
        }
    </style>
</head>
<body class="min-h-full flex flex-col font-sans text-gray-900 bg-gray-100 antialiased pb-20 lg:pb-0">

    <!-- Top Admin Bar -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Brand -->
                <div class="flex items-center gap-2.5">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 focus:outline-none">
                        <img src="{{ asset('logo-kartar-dmg-circle.png') }}"
                             alt="Logo Karang Taruna Demangan"
                             width="36" height="36"
                             fetchpriority="high"
                             class="w-9 h-9 rounded-full object-cover shadow-xs border border-amber-200/90 shrink-0 bg-white">
                        <div>
                            <span class="font-bold text-gray-900 text-sm leading-tight block">Panel Pengurus</span>
                            <span class="text-[10px] text-gray-500 font-semibold uppercase">Dusun Demangan</span>
                        </div>
                    </a>
                </div>

                <!-- Right Profile & Quick Actions -->
                <div class="flex items-center gap-2 sm:gap-3">
                    <a href="{{ route('beranda') }}" target="_blank"
                       class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-semibold text-[#CEA042] bg-amber-50 hover:bg-amber-100 rounded-lg border border-amber-200 transition min-h-[44px]">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        <span class="hidden sm:inline">Lihat Web</span> Warga
                    </a>

                    <div class="flex items-center gap-2 pl-2 border-l border-gray-200">
                        <div class="text-right hidden sm:block">
                            <div class="text-xs font-semibold text-gray-900 leading-none">{{ auth()->user()->name }}</div>
                            <span class="inline-block mt-0.5 px-1.5 py-0.5 text-[9px] font-bold uppercase rounded {{ auth()->user()->isSuperadmin() ? 'bg-purple-100 text-purple-700' : 'bg-amber-100 text-amber-800' }}">
                                {{ auth()->user()->peran }}
                            </span>
                        </div>

                        <!-- Logout Form -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                    title="Keluar dari akun"
                                    class="p-2 text-gray-500 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition min-h-[44px] min-w-[44px] flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 py-4 sm:py-6 w-full flex-1 flex flex-col lg:flex-row gap-5">
        <!-- Desktop Sidebar Navigation (Hidden on mobile) -->
        <aside class="w-64 shrink-0 hidden lg:block">
            <nav class="bg-white rounded-xl border border-gray-200/80 p-3 shadow-xs space-y-1 sticky top-20">
                <a href="{{ route('admin.dashboard') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('admin.dashboard') ? 'bg-amber-50 text-[#CEA042] font-semibold' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                    <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.dashboard') ? 'text-[#CEA042]' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Dashboard Utama
                </a>

                <div class="pt-2 pb-1 px-3 text-[11px] font-bold uppercase tracking-wider text-gray-400">
                    Inventaris
                </div>

                <a href="{{ route('admin.barang.index') }}"
                   class="flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('admin.barang.*') ? 'bg-amber-50 text-[#CEA042] font-semibold' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                    <span class="flex items-center gap-3">
                        <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.barang.*') ? 'text-[#CEA042]' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        Data Inventaris
                    </span>
                    <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full font-semibold">
                        {{ \App\Models\Barang::count() }}
                    </span>
                </a>

                @php
                    $antreanPending = \App\Models\Peminjaman::where('status', 'menunggu')->count();
                @endphp
                <a href="{{ route('admin.peminjaman.index') }}"
                   class="flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('admin.peminjaman.*') ? 'bg-amber-50 text-[#CEA042] font-semibold' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                    <span class="flex items-center gap-3">
                        <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.peminjaman.*') ? 'text-[#CEA042]' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        Peminjaman
                    </span>
                    @if($antreanPending > 0)
                        <span class="text-xs bg-amber-500 text-white px-2 py-0.5 rounded-full font-bold">
                            {{ $antreanPending }}
                        </span>
                    @endif
                </a>

                <div class="pt-2 pb-1 px-3 text-[11px] font-bold uppercase tracking-wider text-gray-400">
                    Keuangan
                </div>

                <a href="{{ route('admin.keuangan.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('admin.keuangan.index') ? 'bg-amber-50 text-[#CEA042] font-semibold' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                    <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.keuangan.index') ? 'text-[#CEA042]' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Buku Kas Utama
                </a>

                <a href="{{ route('admin.keuangan.laporan') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('admin.keuangan.laporan') ? 'bg-amber-50 text-[#CEA042] font-semibold' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                    <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.keuangan.laporan') ? 'text-[#CEA042]' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Laporan Ringkas
                </a>

                <div class="pt-2 pb-1 px-3 text-[11px] font-bold uppercase tracking-wider text-gray-400">
                    Listrik Warga
                </div>

                <a href="{{ route('admin.rekap-listrik.index') }}"
                   class="flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('admin.rekap-listrik.*') ? 'bg-amber-50 text-[#CEA042] font-semibold' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                    <span class="flex items-center gap-3">
                        <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.rekap-listrik.*') ? 'text-[#CEA042]' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                        Rekap Listrik
                    </span>
                    <span class="text-xs bg-amber-100 text-amber-800 px-2 py-0.5 rounded-full font-semibold">
                        {{ \App\Models\TagihanListrik::count() }}
                    </span>
                </a>

                @php
                    $belumLunasSidebar = \App\Models\TagihanListrik::where('is_lunas', false)->count();
                @endphp
                <a href="{{ route('admin.pembayaran-listrik.index') }}"
                   class="flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('admin.pembayaran-listrik.*') ? 'bg-amber-50 text-[#CEA042] font-semibold' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                    <span class="flex items-center gap-3">
                        <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.pembayaran-listrik.*') ? 'text-[#CEA042]' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        Pembayaran Listrik
                    </span>
                    @if($belumLunasSidebar > 0)
                        <span class="text-xs bg-rose-500 text-white px-2 py-0.5 rounded-full font-bold">
                            {{ $belumLunasSidebar }}
                        </span>
                    @else
                        <span class="text-xs bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded-full font-semibold">
                            Lunas
                        </span>
                    @endif
                </a>

                <a href="{{ route('admin.warga.index') }}"
                   class="flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('admin.warga.*') ? 'bg-amber-50 text-[#CEA042] font-semibold' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                    <span class="flex items-center gap-3">
                        <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.warga.*') ? 'text-[#CEA042]' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        Master Warga PLN
                    </span>
                    <span class="text-xs bg-gray-100 text-gray-700 px-2 py-0.5 rounded-full font-semibold">
                        {{ \App\Models\Warga::count() }}
                    </span>
                </a>

                @if(auth()->user()->isSuperadmin())
                    <div class="pt-2 pb-1 px-3 text-[11px] font-bold uppercase tracking-wider text-purple-600">
                        Superadmin
                    </div>

                    <a href="{{ route('admin.pengguna.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('admin.pengguna.*') ? 'bg-purple-50 text-purple-700 font-semibold' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                        <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.pengguna.*') ? 'text-purple-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        Kelola Akun Admin
                    </a>
                @endif
            </nav>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-1 min-w-0">
            @if(session('sukses'))
                <div class="mb-4 rounded-xl bg-emerald-50 border border-emerald-200 p-3.5 text-emerald-800 flex items-start gap-2.5 shadow-xs">
                    <svg class="w-5 h-5 text-emerald-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div class="text-xs sm:text-sm font-semibold leading-relaxed">{{ session('sukses') }}</div>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 rounded-xl bg-rose-50 border border-rose-200 p-3.5 text-rose-800 shadow-xs">
                    <div class="flex items-center gap-2 font-bold text-xs sm:text-sm mb-1">
                        <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Periksa kembali input data:
                    </div>
                    <ul class="list-disc list-inside text-xs space-y-0.5 text-rose-700">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- MOBILE STICKY BOTTOM BAR KHUSUS PENGURUS (Jempol Pengurus di HP) -->
    @php
        $antreanBadge = \App\Models\Peminjaman::where('status', 'menunggu')->count();
        $belumLunasBadge = \App\Models\TagihanListrik::where('is_lunas', false)->count();
    @endphp
    <nav class="lg:hidden fixed bottom-0 left-0 right-0 z-40 bg-white border-t border-gray-200 shadow-lg px-2 py-1.5 flex justify-around items-center">
        <!-- Dashboard -->
        <a href="{{ route('admin.dashboard') }}"
           class="flex flex-col items-center justify-center w-14 py-1 rounded-lg transition min-h-[44px] {{ request()->routeIs('admin.dashboard') ? 'text-[#CEA042] font-bold' : 'text-gray-500 hover:text-gray-800' }}">
            <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            <span class="text-[9px] leading-tight">Beranda</span>
        </a>

        <!-- Rekap Listrik (Khusus Input di HP) -->
        <a href="{{ route('admin.rekap-listrik.index') }}"
           class="flex flex-col items-center justify-center w-14 py-1 rounded-lg transition min-h-[44px] {{ request()->routeIs('admin.rekap-listrik.*') ? 'text-[#CEA042] font-bold' : 'text-gray-500 hover:text-gray-800' }}">
            <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
            <span class="text-[9px] leading-tight">Rekap</span>
        </a>

        <!-- Kasir Bayar Listrik (Tombol Utama Kasir di HP) -->
        <a href="{{ route('admin.pembayaran-listrik.index') }}"
           class="flex flex-col items-center justify-center w-14 py-1 rounded-lg transition min-h-[44px] relative {{ request()->routeIs('admin.pembayaran-listrik.*') ? 'text-[#CEA042] font-bold' : 'text-gray-500 hover:text-gray-800' }}">
            <div class="relative">
                <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                @if($belumLunasBadge > 0)
                    <span class="absolute -top-1.5 -right-2.5 bg-rose-500 text-white text-[9px] font-bold rounded-full min-w-4 h-4 px-1 flex items-center justify-center shadow-xs">
                        {{ $belumLunasBadge }}
                    </span>
                @endif
            </div>
            <span class="text-[9px] leading-tight font-semibold">Bayar</span>
        </a>

        <!-- Barang / Inventaris -->
        <a href="{{ route('admin.barang.index') }}"
           class="flex flex-col items-center justify-center w-14 py-1 rounded-lg transition min-h-[44px] {{ request()->routeIs('admin.barang.*') ? 'text-[#CEA042] font-bold' : 'text-gray-500 hover:text-gray-800' }}">
            <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            <span class="text-[9px] leading-tight">Barang</span>
        </a>

        <!-- Kas Dusun -->
        <a href="{{ route('admin.keuangan.index') }}"
           class="flex flex-col items-center justify-center w-14 py-1 rounded-lg transition min-h-[44px] {{ request()->routeIs('admin.keuangan.*') ? 'text-[#CEA042] font-bold' : 'text-gray-500 hover:text-gray-800' }}">
            <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span class="text-[9px] leading-tight">Kas</span>
        </a>
    </nav>

    <script>
        // Global Format Rupiah Real-time untuk semua input nominal
        window.formatRupiahString = function(angka) {
            if (angka === null || angka === undefined || angka === '') return '';
            let clean = angka.toString().replace(/[^0-9]/g, '');
            if (!clean) return '';
            return new Intl.NumberFormat('id-ID').format(clean);
        };

        window.parseRupiahNumber = function(str) {
            if (!str) return 0;
            return parseInt(str.toString().replace(/[^0-9]/g, '')) || 0;
        };

        document.addEventListener('input', function(e) {
            if (e.target.matches('.input-rupiah, [data-rupiah]')) {
                let el = e.target;
                let cursor = el.selectionStart;
                let prevLen = el.value.length;
                let clean = el.value.replace(/[^0-9]/g, '');

                if (!clean) {
                    el.value = '';
                } else {
                    let formatted = new Intl.NumberFormat('id-ID').format(clean);
                    el.value = formatted;
                    let diff = formatted.length - prevLen;
                    let newCursor = Math.max(0, cursor + diff);
                    el.setSelectionRange(newCursor, newCursor);
                }
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
