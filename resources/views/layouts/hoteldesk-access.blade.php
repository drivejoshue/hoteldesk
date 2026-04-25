<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'HotelDesk Lite by Orbana')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --hd-access-primary: #0F6CBD;
            --hd-access-cyan: #00A6D6;
            --hd-access-dark: #0F172A;
            --hd-access-muted: #667085;
            --hd-access-hero-image: url("{{ asset('images/hoteldesk-landing.png') }}");
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            margin: 0;
            min-height: 100%;
            background: #f0f4f8;
            overflow-x: hidden;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }

        .hd-access-page {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background: #f0f4f8;
        }

        /* Navbar más sutil */
        .hd-access-navbar {
            width: 100%;
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(20px);
            border-bottom: 0.5px solid rgba(255, 255, 255, 0.4);
            position: relative;
            z-index: 20;
        }

        .hd-access-navbar-inner {
            max-width: 1320px;
            margin: 0 auto;
            padding: 14px 24px;
        }

        .hd-access-brand-mark {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            display: grid;
            place-items: center;
            background: linear-gradient(135deg, var(--hd-access-primary), var(--hd-access-cyan));
            color: #fff;
            box-shadow: 0 12px 28px rgba(15, 108, 189, 0.2);
            flex: 0 0 auto;
        }

        .hd-access-brand-title {
            color: #1a2c3e;
            font-size: 18px;
            font-weight: 600;
            letter-spacing: -0.02em;
            line-height: 1;
        }

        .hd-access-brand-subtitle {
            margin-top: 3px;
            color: var(--hd-access-muted);
            font-size: 11px;
            font-weight: 400;
        }

        .hd-access-nav-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 12px;
            border-radius: 100px;
            background: rgba(15, 108, 189, 0.1);
            backdrop-filter: blur(4px);
            color: #0F6CBD;
            font-size: 12px;
            font-weight: 450;
        }

        /* Footer */
        .hd-access-footer {
            width: 100%;
            margin-top: auto;
            background: #0f1729;
            color: rgba(255, 255, 255, 0.6);
            padding: 22px 0;
        }

        .hd-access-footer-inner {
            max-width: 1320px;
            margin: 0 auto;
            padding: 0 24px;
            font-size: 12px;
            font-weight: 350;
        }

        .hd-access-footer strong {
            color: #fff;
            font-weight: 500;
        }

        @media (max-width: 575px) {
            .hd-access-navbar-inner {
                padding-left: 16px;
                padding-right: 16px;
            }

            .hd-access-brand-title {
                font-size: 16px;
            }

            .hd-access-nav-pill {
                display: none;
            }
        }
    </style>
</head>

<body>
<div class="hd-access-page">
    <header class="hd-access-navbar">
        <div class="hd-access-navbar-inner">
            <div class="d-flex align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="hd-access-brand-mark">
                        <i class="ti ti-qrcode" style="font-size: 22px;"></i>
                    </div>

                    <div>
                        <div class="hd-access-brand-title">HotelDesk Lite</div>
                        <div class="hd-access-brand-subtitle">by Orbana</div>
                    </div>
                </div>

              <div class="d-flex align-items-center gap-2">
    <div class="d-none d-lg-inline-flex hd-access-nav-pill">
        <i class="ti ti-building"></i>
        Solicitudes por QR para hoteles
    </div>

    <div class="dropdown">
        <button
            class="btn btn-primary dropdown-toggle"
            type="button"
            id="hotelAccessDropdown"
            data-bs-toggle="dropdown"
            data-bs-auto-close="outside"
            aria-expanded="false">
            <i class="ti ti-door-enter me-1"></i>
            Acceso hotel
        </button>

        <div class="dropdown-menu dropdown-menu-end p-3 shadow-lg border-0"
             style="width: min(380px, calc(100vw - 24px)); border-radius: 20px;">

            <div class="text-center mb-3">
                <span class="avatar avatar-lg bg-primary-lt text-primary mb-2">
                    <i class="ti ti-door-enter" style="font-size: 26px;"></i>
                </span>

                <div class="fw-bold fs-3">
                    Acceso del hotel
                </div>

                <div class="text-secondary small">
                    Escribe el código para abrir el panel de recepción.
                </div>
            </div>

            @if($errors->any())
                <div class="alert alert-danger py-2 small" role="alert">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('public.access.store') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label" for="code">
                        Código del hotel
                    </label>

                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="ti ti-building"></i>
                        </span>

                        <input
                            class="form-control"
                            id="code"
                            name="code"
                            value="{{ old('code') }}"
                            autocomplete="off"
                            placeholder="Ej. la-central">
                    </div>

                    <div class="form-hint">
                        Aparece en la hoja de acceso entregada al hotel.
                    </div>
                </div>

                <button class="btn btn-primary w-100" type="submit">
                    <i class="ti ti-arrow-right me-1"></i>
                    Entrar al panel
                </button>
            </form>
        </div>
    </div>
</div>
            </div>
        </div>
    </header>

    @yield('content')

    <footer class="hd-access-footer">
        <div class="hd-access-footer-inner">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
                <div>
                    <strong>HotelDesk Lite by Orbana</strong>
                    · Tecnología práctica para recepción, atención en sitio y operación hotelera.
                </div>

                <div class="d-flex gap-3">
                    <span>SysApp</span>
                    <span>Orbana</span>
                    <span>Soporte</span>
                </div>
            </div>
        </div>
    </footer>
</div>

@if($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const button = document.getElementById('hotelAccessDropdown');

            if (!button || !window.bootstrap) {
                return;
            }

            const dropdown = bootstrap.Dropdown.getOrCreateInstance(button);
            dropdown.show();
        });
    </script>
@endif
</body>
</html>