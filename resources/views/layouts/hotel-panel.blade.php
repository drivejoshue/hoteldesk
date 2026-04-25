<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>@yield('title', $hotel->name ?? 'Panel recepción')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --hd-primary: {{ $hotel->primary_color ?? '#0F6CBD' }};
        }
    </style>
</head>
<body>
<div class="hd-tablet-shell">
    <header class="hd-app-topbar">
        <div class="hd-app-topbar-inner">
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
                    <div class="hd-brand-subtitle">@yield('subtitle', 'Panel de recepción')</div>
                </div>
            </div>

            @yield('topbar-actions')
        </div>
    </header>

    @yield('content')
</div>
</body>
</html>