<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>@yield('title', ($hotel ?? null)?->name ?? 'Panel recepción')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --hd-primary: {{ ($hotel ?? null)?->primary_color ?? '#0F6CBD' }};
            --tblr-primary: {{ ($hotel ?? null)?->primary_color ?? '#0F6CBD' }};
        }

        .hd-navbar-inner {
            display: flex;
            align-items: center;
            gap: .75rem;
            min-width: 0;
        }

        .hd-brand {
            min-width: 0;
            flex: 1 1 auto;
            max-width: 100%;
        }

        .hd-logo-box {
            width: 38px;
            height: 38px;
            min-width: 38px;
            border-radius: 12px;
            background: color-mix(in srgb, var(--hd-primary) 10%, #ffffff);
            color: var(--hd-primary);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border: 1px solid rgba(15, 108, 189, .12);
        }

        .hd-logo-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .hd-brand-title {
            font-size: .95rem;
            font-weight: 700;
            line-height: 1.15;
            max-width: 100%;
        }

        .hd-brand-subtitle {
            font-size: .76rem;
            color: #667085;
            line-height: 1.15;
            max-width: 100%;
        }

        .hd-topbar-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: .5rem;
            flex: 0 0 auto;
            min-width: max-content;
        }

        @media (max-width: 767.98px) {
            .hd-navbar-inner {
                gap: .5rem;
            }

            .hd-logo-box {
                width: 34px;
                height: 34px;
                min-width: 34px;
                border-radius: 10px;
            }

            .hd-brand-title {
                font-size: .88rem;
            }

            .hd-brand-subtitle {
                display: none;
            }

            .hd-topbar-actions {
                gap: .35rem;
            }
        }

        @media (max-width: 420px) {
            .hd-brand-title {
                max-width: 145px;
            }
        }
    </style>
</head>

<body>
<div class="page">
    <header class="navbar navbar-expand-md d-print-none bg-white border-bottom">
        <div class="container-xl hd-navbar-inner">

            <div class="navbar-brand hd-brand d-flex align-items-center gap-2 m-0">
                <div class="hd-logo-box">
                    @if(!empty(($hotel ?? null)?->logo_path))
                        <img
                            class="hd-logo-img"
                            src="{{ asset('storage/' . $hotel->logo_path) }}"
                            alt="{{ $hotel->name }}"
                        >
                    @else
                        <i class="ti ti-building-skyscraper"></i>
                    @endif
                </div>

                <div class="text-truncate min-w-0">
                    <div class="hd-brand-title text-truncate">
                        {{ ($hotel ?? null)?->name ?? 'HotelDesk Lite' }}
                    </div>

                    <div class="hd-brand-subtitle text-truncate">
                        @yield('subtitle', 'Panel de recepción')
                    </div>
                </div>
            </div>

            <div class="hd-topbar-actions">
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