<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('code', 'Error') - @yield('title', 'Terjadi Kesalahan') | Dusun Demangan</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <meta name="theme-color" content="#CEA042">
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            font-family: Figtree, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: #F8F7F4;
            color: #111827;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 1.25rem;
            -webkit-font-smoothing: antialiased;
        }
        .error-card {
            width: 100%;
            max-width: 32rem;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 1.25rem;
            box-shadow: 0 10px 25px -5px rgba(31, 20, 6, 0.08), 0 4px 10px -4px rgba(31, 20, 6, 0.05);
            padding: 2rem 1.75rem;
            text-align: center;
        }
        .brand-row {
            display: inline-flex;
            align-items: center;
            gap: 0.625rem;
            margin-bottom: 1.5rem;
            text-decoration: none;
            color: inherit;
        }
        .brand-logo {
            width: 40px;
            height: 40px;
            border-radius: 9999px;
            object-fit: cover;
            border: 1px solid #ead199;
            background: #fff;
        }
        .brand-text {
            text-align: left;
            line-height: 1.2;
        }
        .brand-name {
            font-size: 0.875rem;
            font-weight: 700;
            color: #111827;
        }
        .brand-sub {
            font-size: 0.7rem;
            font-weight: 600;
            color: #7E5522;
        }
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.3rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.03em;
            text-transform: uppercase;
            margin-bottom: 0.75rem;
        }
        .badge-3xx { background: #e0f2fe; color: #075985; border: 1px solid #bae6fd; }
        .badge-4xx { background: #FAF4E4; color: #674620; border: 1px solid #ead199; }
        .badge-5xx { background: #ffe4e6; color: #9f1239; border: 1px solid #fecdd3; }
        .error-code {
            font-size: 3.25rem;
            line-height: 1;
            font-weight: 800;
            font-variant-numeric: tabular-nums;
            letter-spacing: -0.03em;
            color: #111827;
            margin: 0.25rem 0 0.75rem;
        }
        .error-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #111827;
            margin: 0 0 0.5rem;
        }
        .error-desc {
            font-size: 0.875rem;
            line-height: 1.6;
            color: #4b5563;
            margin: 0 0 1.25rem;
        }
        .error-detail {
            font-size: 0.75rem;
            color: #374151;
            background: #F8F7F4;
            border: 1px solid #e5e7eb;
            border-radius: 0.75rem;
            padding: 0.625rem 0.875rem;
            margin-bottom: 1.25rem;
            word-break: break-word;
        }
        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.625rem;
            justify-content: center;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.45rem;
            min-height: 44px;
            padding: 0.625rem 1.125rem;
            border-radius: 0.75rem;
            font-size: 0.8125rem;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 150ms ease, border-color 150ms ease;
        }
        .btn-primary {
            background-color: #CEA042;
            color: #1F1406;
            border: 1px solid rgba(186, 136, 50, 0.55);
        }
        .btn-primary:hover { background-color: #BA8832; }
        .btn-secondary {
            background-color: #ffffff;
            color: #1f2937;
            border: 1px solid #d1d5db;
        }
        .btn-secondary:hover { background-color: #FDFBF5; border-color: #dcb96d; }
        .btn:focus-visible { outline: 2px solid #CEA042; outline-offset: 2px; }
        .footer-note {
            margin-top: 1.25rem;
            font-size: 0.75rem;
            color: #6b7280;
            text-align: center;
        }
    </style>
</head>
<body>
    <main class="error-card" role="main">
        <a href="{{ url('/') }}" class="brand-row">
            <img src="{{ asset('logo-kartar-dmg-circle.png') }}" alt="Logo Karang Taruna Dusun Demangan" class="brand-logo">
            <span class="brand-text">
                <span class="brand-name" style="display:block;">Dusun Demangan</span>
                <span class="brand-sub" style="display:block;">Karang Taruna Desa Candimulyo</span>
            </span>
        </a>

        <div>
            @php
                $family = trim($__env->yieldContent('family', '4xx'));
                $badgeClass = match ($family) {
                    '3xx' => 'badge-3xx',
                    '5xx' => 'badge-5xx',
                    default => 'badge-4xx',
                };
                $familyLabel = match ($family) {
                    '3xx' => 'Pengalihan Halaman (3xx)',
                    '5xx' => 'Gangguan Server (5xx)',
                    default => 'Permintaan Klien (4xx)',
                };
            @endphp
            <span class="status-badge {{ $badgeClass }}">{{ $familyLabel }}</span>
        </div>

        <div class="error-code">@yield('code', 'Error')</div>
        <h1 class="error-title">@yield('heading', 'Terjadi Kesalahan')</h1>
        <p class="error-desc">@yield('message')</p>

        @if(isset($exception) && method_exists($exception, 'getMessage') && trim((string) $exception->getMessage()) !== '')
            <div class="error-detail">
                <strong>Catatan Sistem:</strong> {{ $exception->getMessage() }}
            </div>
        @endif

        <div class="actions">
            <a href="{{ url('/') }}" class="btn btn-primary">
                Kembali ke Beranda
            </a>
            <button type="button" onclick="window.history.back()" class="btn btn-secondary">
                Halaman Sebelumnya
            </button>
            @yield('extra-action')
        </div>
    </main>

    <p class="footer-note">
        &copy; {{ date('Y') }} Karang Taruna Dusun Demangan, Desa Candimulyo.
    </p>
</body>
</html>
