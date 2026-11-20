<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">

    <title>Masuk Pengurus | Karang Taruna Demangan</title>

    <!-- Favicon Lingkaran Karang Taruna Demangan -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32.png') }}">
    <link rel="icon" type="image/png" sizes="64x64" href="{{ asset('favicon-64.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    <!-- PWA Web App Manifest & Meta Tags -->
    <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
    <meta name="theme-color" content="#CEA042">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Dusun Demangan">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('icon-apple-touch.png') }}">

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
<body class="font-sans text-gray-900 bg-[#F8F7F4] antialiased min-h-screen flex flex-col justify-center items-center p-4 selection:bg-emas-100 selection:text-emas-950">
    <div class="w-full max-w-md">
        <!-- Brand Header -->
        <div class="text-center mb-6">
            <a href="{{ route('beranda') }}" class="inline-flex items-center gap-3.5 group rounded-2xl p-1.5">
                <img src="{{ asset('logo-kartar-dmg-circle.png') }}"
                     alt="Logo Karang Taruna Demangan"
                     width="56" height="56"
                     fetchpriority="high"
                     class="w-14 h-14 rounded-full object-cover shadow-sm border-2 border-emas-300 group-hover:scale-105 transition bg-white shrink-0">
                <div class="text-left">
                    <div class="text-lg font-bold text-gray-900 leading-tight">Inventaris & Kas Dusun</div>
                    <div class="text-xs font-bold text-emas-800 uppercase tracking-wider">Karang Taruna Demangan</div>
                </div>
            </a>
        </div>

        <!-- Auth Card Container -->
        <div class="bg-white rounded-2xl border border-gray-200/90 shadow-sm p-6 sm:p-8">
            {{ $slot }}
        </div>

        <div class="text-center mt-6">
            <a href="{{ route('beranda') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-600 hover:text-emas-800 transition py-2 px-3 rounded-lg">
                <svg class="w-3.5 h-3.5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Kembali ke Beranda Publik</span>
            </a>
        </div>
    </div>
</body>
</html>
