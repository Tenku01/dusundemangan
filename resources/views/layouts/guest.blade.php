<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Masuk Pengurus | Karang Taruna Demangan</title>

    <!-- Favicon Lingkaran Karang Taruna Demangan -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32.png') }}">
    <link rel="icon" type="image/png" sizes="64x64" href="{{ asset('favicon-64.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('logo-kartar-dmg-circle.png') }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-gray-900 bg-gray-50 antialiased min-h-screen flex flex-col justify-center items-center p-4">
    <div class="w-full max-w-md">
        <!-- Brand Header -->
        <div class="text-center mb-6">
            <a href="{{ route('beranda') }}" class="inline-flex items-center gap-3.5 group focus:outline-none">
                <img src="{{ asset('logo-kartar-dmg-circle.png') }}"
                     alt="Logo Karang Taruna Demangan"
                     class="w-14 h-14 rounded-full object-cover shadow-xs border border-amber-200/90 group-hover:scale-105 transition bg-white shrink-0">
                <div class="text-left">
                    <div class="text-lg font-bold text-gray-900 leading-tight">Inventaris & Kas Dusun</div>
                    <div class="text-xs font-semibold text-[#CEA042] uppercase tracking-wider">Karang Taruna Demangan</div>
                </div>
            </a>
        </div>

        <!-- Auth Card Container -->
        <div class="bg-white rounded-2xl border border-gray-200/90 shadow-sm p-6 sm:p-8">
            {{ $slot }}
        </div>

        <div class="text-center mt-6">
            <a href="{{ route('beranda') }}" class="text-xs font-medium text-gray-500 hover:text-gray-800 transition">
                &larr; Kembali ke Beranda Publik
            </a>
        </div>
    </div>
</body>
</html>
