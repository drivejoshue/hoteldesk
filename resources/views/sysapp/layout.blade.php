<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'HotelDesk Lite · SysApp')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
<div class="page">
   <header class="navbar navbar-expand-md navbar-dark bg-dark d-print-none">
    <div class="container-xl">
        <a class="navbar-brand fw-bold py-2" href="{{ route('sysapp.hotels.index') }}">
            <i class="ti ti-building-skyscraper me-2"></i>
            HotelDesk Lite · SysApp
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sysappNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="sysappNavbar">
            <div class="navbar-nav ms-auto">
                <a class="nav-link py-2" href="{{ route('sysapp.hotels.index') }}">
                    <i class="ti ti-building me-1"></i>
                    Hoteles
                </a>

                <a class="nav-link py-2" href="{{ route('sysapp.qr-requests.index') }}">
                    <i class="ti ti-qrcode me-1"></i>
                    Solicitudes QR
                </a>

                <a class="nav-link py-2" href="{{ route('sysapp.pin-reset-requests.index') }}">
                    <i class="ti ti-key me-1"></i>
                    Reset PIN
                </a>

                <a class="nav-link py-2" href="{{ route('sysapp.audit-logs.index') }}">
                    <i class="ti ti-history me-1"></i>
                    Logs
                </a>
            </div>

            <div class="navbar-nav ms-md-3">
                <div class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle py-2" data-bs-toggle="dropdown">
                        <i class="ti ti-user-circle me-1"></i>
                        {{ session('hoteldesk.sysapp.admin_name') ?: 'SysApp' }}
                    </a>

                    <div class="dropdown-menu dropdown-menu-end">
                        <div class="dropdown-header">Administración</div>

                        <form method="POST" action="{{ route('sysapp.logout') }}">
                            @csrf
                            <button class="dropdown-item text-danger" type="submit">
                                <i class="ti ti-logout me-2"></i>
                                Salir
                            </button>
                        </form>
                    </div>
                </div>
            </div>
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