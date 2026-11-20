<!DOCTYPE html>
<html lang="id" class="h-full bg-[#F5F4F0]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">

    <title>@yield('title', 'Panel Pengurus') | Karang Taruna Demangan</title>

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

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

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
<body class="min-h-full flex flex-col font-sans text-gray-900 bg-[#F5F4F0] antialiased selection:bg-emas-100 selection:text-emas-950 pb-20 lg:pb-0 print:bg-white print:pb-0 print:block">

    <!-- Top Admin Bar -->
    <header class="bg-white/95 backdrop-blur-sm border-b border-emas-200/70 sticky top-0 z-30 shadow-sm print:hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Hamburger Button (Mobile) & Brand -->
                <div class="flex items-center gap-2 sm:gap-2.5">
                    <button type="button"
                            onclick="toggleMobileMenu()"
                            aria-label="Buka menu navigasi pengurus"
                            aria-expanded="false"
                            id="btn-hamburger-mobile"
                            class="lg:hidden p-2 text-gray-700 hover:text-gray-900 hover:bg-gray-100 rounded-xl transition min-h-[44px] min-w-[44px] flex items-center justify-center -ml-1">
                        <svg class="w-6 h-6" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>

                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 rounded-xl p-1 -ml-1">
                        <img src="{{ asset('logo-kartar-dmg-circle.png') }}"
                             alt="Logo Karang Taruna Demangan"
                             width="36" height="36"
                             fetchpriority="high"
                             class="w-9 h-9 rounded-full object-cover shadow-sm border border-emas-300 shrink-0 bg-white">
                        <div>
                            <span class="font-bold text-gray-900 text-sm leading-tight block">Panel Pengurus</span>
                            <span class="text-[11px] text-emas-800 font-semibold block leading-tight">Dusun Demangan</span>
                        </div>
                    </a>
                </div>

                <!-- Right Profile & Quick Actions -->
                <div class="flex items-center gap-2 sm:gap-3">
                    <a href="{{ route('beranda') }}" target="_blank"
                       class="btn-gold-soft px-3 py-1.5 text-xs">
                        <svg class="w-3.5 h-3.5 text-emas-800" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        <span><span class="hidden sm:inline">Lihat Web </span>Warga</span>
                    </a>

                    <div class="flex items-center gap-2 pl-2 border-l border-gray-200">
                        <div class="text-right hidden sm:block">
                            <div class="text-xs font-semibold text-gray-900 leading-none">{{ auth()->user()->name }}</div>
                            <span class="inline-block mt-1 px-1.5 py-0.5 text-[10px] font-bold uppercase rounded-md {{ auth()->user()->isSuperadmin() ? 'bg-slate-900 text-emas-300' : 'bg-emas-100 text-emas-900' }}">
                                {{ auth()->user()->peran }}
                            </span>
                        </div>

                        <!-- Logout Form -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                    aria-label="Keluar dari akun"
                                    title="Keluar dari akun"
                                    class="p-2 text-gray-600 hover:text-rose-700 hover:bg-rose-50 rounded-xl transition min-h-[44px] min-w-[44px] flex items-center justify-center">
                                <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- MOBILE DRAWER / SLIDE-OVER NAVIGATION -->
    <div id="mobile-drawer-backdrop"
         onclick="closeMobileMenu()"
         class="fixed inset-0 z-50 bg-gray-900/55 backdrop-blur-sm transition-opacity duration-300 opacity-0 pointer-events-none lg:hidden print:hidden"
         aria-hidden="true"></div>

    <aside id="mobile-drawer-panel"
           class="fixed inset-y-0 left-0 z-50 w-80 max-w-[85vw] bg-white shadow-2xl flex flex-col transform -translate-x-full transition-transform duration-300 ease-in-out lg:hidden print:hidden"
           role="dialog"
           aria-modal="true"
           aria-label="Navigasi Menu Mobile">
        <!-- Drawer Header -->
        <div class="p-4 border-b border-gray-100 flex items-center justify-between bg-[#F8F7F4]">
            <div class="flex items-center gap-2.5">
                <img src="{{ asset('logo-kartar-dmg-circle.png') }}"
                     alt="Logo Karang Taruna Demangan"
                     width="36" height="36"
                     class="w-9 h-9 rounded-full object-cover shadow-sm border border-emas-300 shrink-0 bg-white">
                <div>
                    <span class="font-bold text-gray-900 text-sm leading-tight block">Dusun Demangan</span>
                    <span class="text-[11px] text-gray-500 font-medium block">Menu Lengkap Pengurus</span>
                </div>
            </div>
            <button type="button"
                    onclick="closeMobileMenu()"
                    aria-label="Tutup menu navigasi"
                    class="p-2 text-gray-500 hover:text-gray-800 hover:bg-gray-200/70 rounded-xl transition min-h-[44px] min-w-[44px] flex items-center justify-center">
                <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- User Profile Card in Drawer -->
        <div class="p-4 border-b border-gray-100 bg-gradient-to-br from-emas-50/70 via-white to-slate-50">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl {{ auth()->user()->isSuperadmin() ? 'bg-slate-900 text-emas-300 border border-slate-800' : 'bg-emas-100 text-emas-900 border border-emas-200' }} flex items-center justify-center font-bold text-sm shrink-0 shadow-sm">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <div class="min-w-0 flex-1">
                    <div class="text-xs font-bold text-gray-900 truncate">{{ auth()->user()->name }}</div>
                    <div class="text-[11px] text-gray-500 truncate">{{ auth()->user()->email }}</div>
                    <div class="mt-1 flex items-center gap-1.5">
                        <span class="inline-block px-2 py-0.5 text-[10px] font-bold uppercase rounded-md {{ auth()->user()->isSuperadmin() ? 'bg-slate-900 text-emas-300' : 'bg-emas-100 text-emas-900 border border-emas-200' }}">
                            {{ auth()->user()->isSuperadmin() ? 'Superadmin' : 'Pengurus Admin' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Drawer Menu Items (Scrollable) -->
        <div class="flex-1 overflow-y-auto p-3 space-y-4">
            <!-- Navigasi Dashboard -->
            @if(auth()->user()->hasMenuAkses('beranda'))
                <div>
                    <a href="{{ route('admin.dashboard') }}"
                       onclick="closeMobileMenu()"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs transition min-h-[44px] {{ request()->routeIs('admin.dashboard') ? 'bg-emas-100/90 text-emas-900 font-bold border border-emas-200 shadow-sm' : 'text-gray-700 hover:bg-gray-100 font-semibold' }}">
                        <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.dashboard') ? 'text-emas-700' : 'text-gray-400' }}" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span>Dashboard Ringkasan</span>
                    </a>
                </div>
            @endif

            <!-- Navigasi Khusus Superadmin: CRUD USER / KONTROL PENGURUS -->
            @if(auth()->user()->isSuperadmin())
                <div class="space-y-1.5">
                    <div class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-600">
                        Kontrol Superadmin
                    </div>
                    <a href="{{ route('admin.pengguna.index') }}"
                       onclick="closeMobileMenu()"
                       class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-bold transition min-h-[44px] {{ request()->routeIs('admin.pengguna.*') ? 'bg-slate-900 text-white shadow-sm' : 'bg-slate-100 text-slate-800 border border-slate-200 hover:bg-slate-200/70' }}">
                        <span class="flex items-center gap-3">
                            <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.pengguna.*') ? 'text-emas-400' : 'text-slate-600' }}" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            <span>Kelola Akun Admin</span>
                        </span>
                        <span class="text-[11px] px-2 py-0.5 rounded-full font-bold tabular-money {{ request()->routeIs('admin.pengguna.*') ? 'bg-emas-500 text-[#1F1406]' : 'bg-slate-200 text-slate-800' }}">
                            {{ \App\Models\User::count() }}
                        </span>
                    </a>
                </div>
            @endif

            <!-- Navigasi Kelistrikan Warga -->
            @if(auth()->user()->hasMenuAkses('rekap') || auth()->user()->hasMenuAkses('bayar') || auth()->user()->hasMenuAkses('warga'))
                <div class="space-y-1">
                    <div class="px-3 text-[11px] font-bold uppercase tracking-wider text-gray-500">
                        Kelistrikan Warga
                    </div>

                    @if(auth()->user()->hasMenuAkses('rekap'))
                        <a href="{{ route('admin.rekap-listrik.index') }}"
                           onclick="closeMobileMenu()"
                           class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs transition min-h-[44px] {{ request()->routeIs('admin.rekap-listrik.*') ? 'bg-emas-100/90 text-emas-900 font-bold border border-emas-200' : 'text-gray-700 hover:bg-gray-100 font-semibold' }}">
                            <span class="flex items-center gap-3">
                                <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.rekap-listrik.*') ? 'text-emas-700' : 'text-gray-400' }}" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                                <span>Rekap Tagihan Listrik</span>
                            </span>
                        </a>
                    @endif

                    @if(auth()->user()->hasMenuAkses('bayar'))
                        @php
                            $drawerBelumLunas = \App\Models\TagihanListrik::where('is_lunas', false)->count();
                        @endphp
                        <a href="{{ route('admin.pembayaran-listrik.index') }}"
                           onclick="closeMobileMenu()"
                           class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs transition min-h-[44px] {{ request()->routeIs('admin.pembayaran-listrik.*') ? 'bg-emas-100/90 text-emas-900 font-bold border border-emas-200' : 'text-gray-700 hover:bg-gray-100 font-semibold' }}">
                            <span class="flex items-center gap-3">
                                <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.pembayaran-listrik.*') ? 'text-emas-700' : 'text-gray-400' }}" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                <span>Kasir Bayar Listrik</span>
                            </span>
                            @if($drawerBelumLunas > 0)
                                <span class="text-[11px] bg-rose-600 text-white px-2 py-0.5 rounded-full font-bold tabular-money">
                                    {{ $drawerBelumLunas }}
                                </span>
                            @endif
                        </a>
                    @endif

                    @if(auth()->user()->hasMenuAkses('warga'))
                        <a href="{{ route('admin.warga.index') }}"
                           onclick="closeMobileMenu()"
                           class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs transition min-h-[44px] {{ request()->routeIs('admin.warga.*') ? 'bg-emas-100/90 text-emas-900 font-bold border border-emas-200' : 'text-gray-700 hover:bg-gray-100 font-semibold' }}">
                            <span class="flex items-center gap-3">
                                <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.warga.*') ? 'text-emas-700' : 'text-gray-400' }}" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                <span>Master Warga PLN</span>
                            </span>
                            <span class="text-[11px] bg-gray-100 text-gray-700 px-2 py-0.5 rounded-full font-semibold tabular-money">
                                {{ \App\Models\Warga::count() }}
                            </span>
                        </a>
                    @endif
                </div>
            @endif

            <!-- Navigasi Inventaris -->
            @if(auth()->user()->hasMenuAkses('barang'))
                <div class="space-y-1">
                    <div class="px-3 text-[11px] font-bold uppercase tracking-wider text-gray-500">
                        Inventaris Dusun
                    </div>
                    <a href="{{ route('admin.barang.index') }}"
                       onclick="closeMobileMenu()"
                       class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs transition min-h-[44px] {{ request()->routeIs('admin.barang.*') ? 'bg-emas-100/90 text-emas-900 font-bold border border-emas-200' : 'text-gray-700 hover:bg-gray-100 font-semibold' }}">
                        <span class="flex items-center gap-3">
                            <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.barang.*') ? 'text-emas-700' : 'text-gray-400' }}" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            <span>Data Inventaris</span>
                        </span>
                        <span class="text-[11px] bg-gray-100 text-gray-700 px-2 py-0.5 rounded-full font-semibold tabular-money">
                            {{ \App\Models\Barang::count() }}
                        </span>
                    </a>

                    @php
                        $drawerPendingPinjam = \App\Models\Peminjaman::where('status', 'menunggu')->count();
                    @endphp
                    <a href="{{ route('admin.peminjaman.index') }}"
                       onclick="closeMobileMenu()"
                       class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs transition min-h-[44px] {{ request()->routeIs('admin.peminjaman.*') ? 'bg-emas-100/90 text-emas-900 font-bold border border-emas-200' : 'text-gray-700 hover:bg-gray-100 font-semibold' }}">
                        <span class="flex items-center gap-3">
                            <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.peminjaman.*') ? 'text-emas-700' : 'text-gray-400' }}" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                            <span>Peminjaman Barang</span>
                        </span>
                        @if($drawerPendingPinjam > 0)
                            <span class="text-[11px] bg-emas-500 text-[#1F1406] px-2 py-0.5 rounded-full font-bold tabular-money">
                                {{ $drawerPendingPinjam }}
                            </span>
                        @endif
                    </a>
                </div>
            @endif

            <!-- Navigasi Keuangan -->
            @if(auth()->user()->hasMenuAkses('kas'))
                <div class="space-y-1">
                    <div class="px-3 text-[11px] font-bold uppercase tracking-wider text-gray-500">
                        Keuangan & Kas
                    </div>
                    <a href="{{ route('admin.keuangan.index') }}"
                       onclick="closeMobileMenu()"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs transition min-h-[44px] {{ request()->routeIs('admin.keuangan.index') ? 'bg-emas-100/90 text-emas-900 font-bold border border-emas-200' : 'text-gray-700 hover:bg-gray-100 font-semibold' }}">
                        <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.keuangan.index') ? 'text-emas-700' : 'text-gray-400' }}" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Buku Kas Utama</span>
                    </a>
                    <a href="{{ route('admin.keuangan.laporan') }}"
                       onclick="closeMobileMenu()"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs transition min-h-[44px] {{ request()->routeIs('admin.keuangan.laporan') ? 'bg-emas-100/90 text-emas-900 font-bold border border-emas-200' : 'text-gray-700 hover:bg-gray-100 font-semibold' }}">
                        <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.keuangan.laporan') ? 'text-emas-700' : 'text-gray-400' }}" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Laporan Ringkas Kas</span>
                    </a>
                </div>
            @endif
        </div>

        <!-- Drawer Footer Actions -->
        <div class="p-3 border-t border-gray-100 bg-[#F8F7F4] space-y-2">
            <!-- Tombol Instal Aplikasi PWA -->
            <button type="button"
                    id="btn-install-pwa-drawer"
                    onclick="triggerPwaInstall()"
                    class="w-full hidden items-center justify-center gap-2 px-3.5 py-2 text-xs font-bold text-emerald-800 bg-emerald-50 hover:bg-emerald-100 border border-emerald-300 rounded-xl transition min-h-[44px]">
                <svg class="w-4 h-4 text-emerald-600" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                <span>Instal Aplikasi di HP</span>
            </button>

            <a href="{{ route('beranda') }}" target="_blank"
               class="btn-gold-soft w-full text-xs">
                <svg class="w-4 h-4" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                <span>Buka Web Publik Warga</span>
            </a>

            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <button type="submit" class="btn-danger-soft w-full text-xs">
                    <svg class="w-4 h-4" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    <span>Keluar dari Akun</span>
                </button>
            </form>
        </div>
    </aside>

    <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 py-4 sm:py-6 w-full flex-1 flex flex-col lg:flex-row gap-5 print:max-w-none print:p-0 print:m-0 print:block">
        <!-- Desktop Sidebar Navigation (Hidden on mobile & print) -->
        <aside class="w-64 shrink-0 hidden lg:block print:hidden">
            <nav aria-label="Navigasi Sidebar Pengurus" class="bg-white rounded-2xl border border-gray-200/90 p-3 shadow-sm space-y-1 sticky top-20">
                @if(auth()->user()->hasMenuAkses('beranda'))
                    <a href="{{ route('admin.dashboard') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition {{ request()->routeIs('admin.dashboard') ? 'bg-emas-100/90 text-emas-900 font-bold border border-emas-200/80' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900 font-medium' }}">
                        <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.dashboard') ? 'text-emas-700' : 'text-gray-400' }}" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span>Dashboard Utama</span>
                    </a>
                @endif

                @if(auth()->user()->hasMenuAkses('barang'))
                    <div class="pt-3 pb-1 px-3 text-[11px] font-bold uppercase tracking-wider text-gray-500">
                        Inventaris
                    </div>

                    <a href="{{ route('admin.barang.index') }}"
                       class="flex items-center justify-between px-3 py-2.5 rounded-xl text-sm transition {{ request()->routeIs('admin.barang.*') ? 'bg-emas-100/90 text-emas-900 font-bold border border-emas-200/80' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900 font-medium' }}">
                        <span class="flex items-center gap-3">
                            <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.barang.*') ? 'text-emas-700' : 'text-gray-400' }}" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            <span>Data Inventaris</span>
                        </span>
                        <span class="text-xs bg-gray-100 text-gray-700 px-2 py-0.5 rounded-full font-semibold tabular-money">
                            {{ \App\Models\Barang::count() }}
                        </span>
                    </a>

                    @php
                        $antreanPending = \App\Models\Peminjaman::where('status', 'menunggu')->count();
                    @endphp
                    <a href="{{ route('admin.peminjaman.index') }}"
                       class="flex items-center justify-between px-3 py-2.5 rounded-xl text-sm transition {{ request()->routeIs('admin.peminjaman.*') ? 'bg-emas-100/90 text-emas-900 font-bold border border-emas-200/80' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900 font-medium' }}">
                        <span class="flex items-center gap-3">
                            <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.peminjaman.*') ? 'text-emas-700' : 'text-gray-400' }}" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                            <span>Peminjaman</span>
                        </span>
                        @if($antreanPending > 0)
                            <span class="text-xs bg-emas-500 text-[#1F1406] px-2 py-0.5 rounded-full font-bold tabular-money">
                                {{ $antreanPending }}
                            </span>
                        @endif
                    </a>
                @endif

                @if(auth()->user()->hasMenuAkses('kas'))
                    <div class="pt-3 pb-1 px-3 text-[11px] font-bold uppercase tracking-wider text-gray-500">
                        Keuangan
                    </div>

                    <a href="{{ route('admin.keuangan.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition {{ request()->routeIs('admin.keuangan.index') ? 'bg-emas-100/90 text-emas-900 font-bold border border-emas-200/80' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900 font-medium' }}">
                        <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.keuangan.index') ? 'text-emas-700' : 'text-gray-400' }}" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Buku Kas Utama</span>
                    </a>

                    <a href="{{ route('admin.keuangan.laporan') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition {{ request()->routeIs('admin.keuangan.laporan') ? 'bg-emas-100/90 text-emas-900 font-bold border border-emas-200/80' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900 font-medium' }}">
                        <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.keuangan.laporan') ? 'text-emas-700' : 'text-gray-400' }}" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Laporan Ringkas</span>
                    </a>
                @endif

                @if(auth()->user()->hasMenuAkses('rekap') || auth()->user()->hasMenuAkses('bayar') || auth()->user()->hasMenuAkses('warga'))
                    <div class="pt-3 pb-1 px-3 text-[11px] font-bold uppercase tracking-wider text-gray-500">
                        Listrik Warga
                    </div>

                    @if(auth()->user()->hasMenuAkses('rekap'))
                        <a href="{{ route('admin.rekap-listrik.index') }}"
                           class="flex items-center justify-between px-3 py-2.5 rounded-xl text-sm transition {{ request()->routeIs('admin.rekap-listrik.*') ? 'bg-emas-100/90 text-emas-900 font-bold border border-emas-200/80' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900 font-medium' }}">
                            <span class="flex items-center gap-3">
                                <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.rekap-listrik.*') ? 'text-emas-700' : 'text-gray-400' }}" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                                <span>Rekap Listrik</span>
                            </span>
                            <span class="text-xs bg-emas-100 text-emas-900 px-2 py-0.5 rounded-full font-semibold tabular-money">
                                {{ \App\Models\TagihanListrik::count() }}
                            </span>
                        </a>
                    @endif

                    @if(auth()->user()->hasMenuAkses('bayar'))
                        @php
                            $belumLunasSidebar = \App\Models\TagihanListrik::where('is_lunas', false)->count();
                        @endphp
                        <a href="{{ route('admin.pembayaran-listrik.index') }}"
                           class="flex items-center justify-between px-3 py-2.5 rounded-xl text-sm transition {{ request()->routeIs('admin.pembayaran-listrik.*') ? 'bg-emas-100/90 text-emas-900 font-bold border border-emas-200/80' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900 font-medium' }}">
                            <span class="flex items-center gap-3">
                                <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.pembayaran-listrik.*') ? 'text-emas-700' : 'text-gray-400' }}" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                <span>Pembayaran Listrik</span>
                            </span>
                            @if($belumLunasSidebar > 0)
                                <span class="text-xs bg-rose-600 text-white px-2 py-0.5 rounded-full font-bold tabular-money">
                                    {{ $belumLunasSidebar }}
                                </span>
                            @else
                                <span class="text-xs bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded-full font-semibold">
                                    Lunas
                                </span>
                            @endif
                        </a>
                    @endif

                    @if(auth()->user()->hasMenuAkses('warga'))
                        <a href="{{ route('admin.warga.index') }}"
                           class="flex items-center justify-between px-3 py-2.5 rounded-xl text-sm transition {{ request()->routeIs('admin.warga.*') ? 'bg-emas-100/90 text-emas-900 font-bold border border-emas-200/80' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900 font-medium' }}">
                            <span class="flex items-center gap-3">
                                <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.warga.*') ? 'text-emas-700' : 'text-gray-400' }}" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                <span>Master Warga PLN</span>
                            </span>
                            <span class="text-xs bg-gray-100 text-gray-700 px-2 py-0.5 rounded-full font-semibold tabular-money">
                                {{ \App\Models\Warga::count() }}
                            </span>
                        </a>
                    @endif
                @endif

                @if(auth()->user()->isSuperadmin())
                    <div class="pt-3 pb-1 px-3 text-[11px] font-bold uppercase tracking-wider text-slate-600">
                        Superadmin
                    </div>

                    <a href="{{ route('admin.pengguna.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition {{ request()->routeIs('admin.pengguna.*') ? 'bg-slate-900 text-white font-semibold shadow-sm' : 'text-gray-700 hover:bg-slate-100 hover:text-gray-900 font-medium' }}">
                        <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.pengguna.*') ? 'text-emas-400' : 'text-gray-400' }}" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        <span>Kelola Akun Admin</span>
                    </a>
                @endif
            </nav>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-1 min-w-0">
            @if(session('sukses'))
                <div role="status" class="mb-4 rounded-xl bg-emerald-50 border border-emerald-200 p-3.5 text-emerald-900 flex items-start gap-2.5 shadow-sm">
                    <svg class="w-5 h-5 text-emerald-600 mt-0.5 shrink-0" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div class="text-xs sm:text-sm font-semibold leading-relaxed">{{ session('sukses') }}</div>
                </div>
            @endif

            @if($errors->any())
                <div role="alert" class="mb-4 rounded-xl bg-rose-50 border border-rose-200 p-3.5 text-rose-900 shadow-sm">
                    <div class="flex items-center gap-2 font-bold text-xs sm:text-sm mb-1">
                        <svg class="w-5 h-5 text-rose-600 shrink-0" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Periksa kembali input data:
                    </div>
                    <ul class="list-disc list-inside text-xs space-y-0.5 text-rose-800">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- MOBILE STICKY BOTTOM BAR KHUSUS PENGURUS -->
    @php
        $antreanBadge = \App\Models\Peminjaman::where('status', 'menunggu')->count();
        $belumLunasBadge = \App\Models\TagihanListrik::where('is_lunas', false)->count();
    @endphp
    <nav aria-label="Navigasi Cepat Pengurus" class="lg:hidden fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-sm border-t border-gray-200 shadow-lg px-2 py-1.5 flex justify-around items-center print:hidden">
        @if(auth()->user()->hasMenuAkses('beranda'))
            <a href="{{ route('admin.dashboard') }}"
               class="flex flex-col items-center justify-center flex-1 max-w-[70px] py-1.5 px-1 rounded-xl transition min-h-[44px] {{ request()->routeIs('admin.dashboard') ? 'bg-emas-50 text-emas-800 font-bold' : 'text-gray-600 hover:text-gray-900 font-medium' }}">
                <svg class="w-5 h-5 mb-0.5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span class="text-[11px] leading-tight">Beranda</span>
            </a>
        @endif

        @if(auth()->user()->hasMenuAkses('rekap'))
            <a href="{{ route('admin.rekap-listrik.index') }}"
               class="flex flex-col items-center justify-center flex-1 max-w-[70px] py-1.5 px-1 rounded-xl transition min-h-[44px] {{ request()->routeIs('admin.rekap-listrik.*') ? 'bg-emas-50 text-emas-800 font-bold' : 'text-gray-600 hover:text-gray-900 font-medium' }}">
                <svg class="w-5 h-5 mb-0.5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                <span class="text-[11px] leading-tight">Rekap</span>
            </a>
        @endif

        @if(auth()->user()->hasMenuAkses('bayar'))
            <a href="{{ route('admin.pembayaran-listrik.index') }}"
               class="flex flex-col items-center justify-center flex-1 max-w-[70px] py-1.5 px-1 rounded-xl transition min-h-[44px] relative {{ request()->routeIs('admin.pembayaran-listrik.*') ? 'bg-emas-50 text-emas-800 font-bold' : 'text-gray-600 hover:text-gray-900 font-medium' }}">
                <div class="relative">
                    <svg class="w-5 h-5 mb-0.5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    @if($belumLunasBadge > 0)
                        <span class="absolute -top-1.5 -right-2.5 bg-rose-600 text-white text-[9px] font-bold rounded-full min-w-[16px] h-4 px-1 flex items-center justify-center shadow-sm tabular-money">
                            {{ $belumLunasBadge }}
                        </span>
                    @endif
                </div>
                <span class="text-[11px] leading-tight">Bayar</span>
            </a>
        @endif

        @if(auth()->user()->hasMenuAkses('kas'))
            <a href="{{ route('admin.keuangan.index') }}"
               class="flex flex-col items-center justify-center flex-1 max-w-[70px] py-1.5 px-1 rounded-xl transition min-h-[44px] {{ request()->routeIs('admin.keuangan.*') ? 'bg-emas-50 text-emas-800 font-bold' : 'text-gray-600 hover:text-gray-900 font-medium' }}">
                <svg class="w-5 h-5 mb-0.5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="text-[11px] leading-tight">Kas</span>
            </a>
        @endif

        <!-- Tombol Menu Lengkap (Buka Drawer) -->
        <button type="button"
                onclick="toggleMobileMenu()"
                aria-label="Buka semua menu navigasi"
                class="flex flex-col items-center justify-center flex-1 max-w-[70px] py-1.5 px-1 rounded-xl transition min-h-[44px] relative {{ request()->routeIs('admin.pengguna.*') || request()->routeIs('admin.warga.*') || request()->routeIs('admin.barang.*') || request()->routeIs('admin.peminjaman.*') ? 'bg-slate-100 text-slate-900 font-bold' : 'text-gray-600 hover:text-gray-900 font-medium' }}">
            <div class="relative">
                <svg class="w-5 h-5 mb-0.5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                @if(auth()->user()->isSuperadmin())
                    <span class="absolute -top-0.5 -right-1.5 w-2 h-2 rounded-full bg-emas-500 ring-2 ring-white"></span>
                @elseif($antreanBadge > 0)
                    <span class="absolute -top-0.5 -right-1.5 w-2 h-2 rounded-full bg-amber-500 ring-2 ring-white"></span>
                @endif
            </div>
            <span class="text-[11px] leading-tight">
                {{ auth()->user()->isSuperadmin() ? 'Semua' : 'Menu' }}
            </span>
        </button>
    </nav>

    <script>
        window.toggleMobileMenu = function() {
            const backdrop = document.getElementById('mobile-drawer-backdrop');
            if (!backdrop) return;
            const isOpen = !backdrop.classList.contains('pointer-events-none');
            if (isOpen) {
                closeMobileMenu();
            } else {
                openMobileMenu();
            }
        };

        window.openMobileMenu = function() {
            const backdrop = document.getElementById('mobile-drawer-backdrop');
            const panel = document.getElementById('mobile-drawer-panel');
            const btn = document.getElementById('btn-hamburger-mobile');
            if (!backdrop || !panel) return;

            backdrop.classList.remove('pointer-events-none', 'opacity-0');
            backdrop.classList.add('opacity-100');
            panel.classList.remove('-translate-x-full');
            panel.classList.add('translate-x-0');
            if (btn) btn.setAttribute('aria-expanded', 'true');
            document.body.style.overflow = 'hidden';
        };

        window.closeMobileMenu = function() {
            const backdrop = document.getElementById('mobile-drawer-backdrop');
            const panel = document.getElementById('mobile-drawer-panel');
            const btn = document.getElementById('btn-hamburger-mobile');
            if (!backdrop || !panel) return;

            backdrop.classList.remove('opacity-100');
            backdrop.classList.add('opacity-0', 'pointer-events-none');
            panel.classList.remove('translate-x-0');
            panel.classList.add('-translate-x-full');
            if (btn) btn.setAttribute('aria-expanded', 'false');
            document.body.style.overflow = '';
        };

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeMobileMenu();
            }
        });

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

    <x-pwa-install-ios trigger-button-id="btn-install-pwa-drawer" trigger-display-class="flex" />

    @stack('scripts')
</body>
</html>
