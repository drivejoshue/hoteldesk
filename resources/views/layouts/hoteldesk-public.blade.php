<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>@yield('title', $hotel->name ?? 'HotelDesk Lite')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --hd-primary: {{ $hotel->primary_color ?? '#0F6CBD' }};
        }
    </style>
</head>
<body>
<div class="hd-mobile-shell">
    <div class="hd-phone-frame">
        <div class="hd-hero">
            <div class="hd-brand">
                <div class="hd-logo-box">
                    @if(!empty($hotel?->logo_path))
                        <img class="hd-logo-img" src="{{ asset('storage/' . $hotel->logo_path) }}" alt="{{ $hotel->name }}">
                    @else
                        <i class="ti ti-building-skyscraper"></i>
                    @endif
                </div>

                <div style="min-width: 0;">
                    <div class="hd-brand-title">{{ $hotel->name ?? 'HotelDesk Lite' }}</div>
                    <div class="hd-brand-subtitle">Solicitud rápida por QR</div>
                </div>
            </div>
        </div>

        <main class="hd-content">
            @yield('content')
        </main>
    </div>
</div>
</body>
</html>