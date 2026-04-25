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
            --tblr-primary: {{ $hotel->primary_color ?? '#0F6CBD' }};
        }
    </style>
</head>

<body>
<div class="page">
    <header class="navbar navbar-expand-md d-print-none bg-white border-bottom">
        <div class="container-xl">
            <div class="navbar-brand d-flex align-items-center gap-2 me-auto">
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
                        @yield('subtitle', 'Panel de recepción')
                    </div>
                </div>
            </div>

            <div class="navbar-nav flex-row align-items-center gap-2">
                @yield('topbar-actions')
            </div>
        </div>
    </header>

    <div class="page-wrapper">
        <div class="page-body">
            <div class="container-xl">
                @if(session('success'))
                    <div class="alert alert-success" role="alert">
                        {{ session('success') }}
                    </div>
                @endif

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