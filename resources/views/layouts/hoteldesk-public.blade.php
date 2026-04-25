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
            --tblr-primary: {{ $hotel->primary_color ?? '#0F6CBD' }};
        }
    </style>
</head>

<body>
<div class="hd-public-shell">
    <div class="hd-public-frame">
        <div class="card hd-public-card shadow-sm">
            <div class="card-header border-0 bg-white">
                <div class="d-flex align-items-center gap-2">
                    <div class="hd-logo-box">
                        @if(!empty($hotel?->logo_path))
                            <img class="hd-logo-img" src="{{ asset('storage/' . $hotel->logo_path) }}" alt="{{ $hotel->name }}">
                        @else
                            <i class="ti ti-building-skyscraper"></i>
                        @endif
                    </div>

                    <div class="text-truncate">
                        <div class="hd-brand-title text-truncate">
                            {{ $hotel->name ?? 'HotelDesk Lite' }}
                        </div>
                       <div class="hd-brand-subtitle text-truncate">
    @yield('public-subtitle', 'Solicitud rápida por QR')
</div>
                    </div>
                </div>
            </div>

            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger" role="alert">
                        @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>
</div>
</body>
</html>