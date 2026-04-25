<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>QR · {{ $hotel->name }} · {{ $point->label }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            background:
                radial-gradient(circle at top left, rgba(15, 108, 189, .12), transparent 34%),
                #e5e7eb;
        }

        .qr-print-toolbar {
            position: sticky;
            top: 0;
            z-index: 20;
            background: rgba(255, 255, 255, .94);
            backdrop-filter: blur(14px);
            border-bottom: 1px solid #e4e7ec;
        }

        .qr-print-sheet {
            margin: 24px auto;
            background: #fff;
            text-align: center;
            box-shadow: 0 16px 48px rgba(15, 23, 42, .20);
            border: 1px solid #eef2f6;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .qr-print-sheet.sheet-quarter {
            width: 10.8cm;
            min-height: 13.95cm;
            padding: 10mm 8mm;
            border-radius: 13px;
        }

        .qr-print-sheet.sheet-half {
            width: 13.95cm;
            min-height: 21.6cm;
            padding: 16mm 11mm;
            border-radius: 16px;
        }

        .qr-print-sheet.sheet-letter {
            width: 21.6cm;
            min-height: 27.9cm;
            padding: 22mm 18mm;
            border-radius: 18px;
        }

        .qr-print-logo {
            max-width: 58mm;
            max-height: 24mm;
            object-fit: contain;
            margin: 0 auto 7mm;
        }

        .sheet-quarter .qr-print-logo {
            max-width: 44mm;
            max-height: 17mm;
            margin-bottom: 5mm;
        }

        .sheet-letter .qr-print-logo {
            max-width: 75mm;
            max-height: 30mm;
            margin-bottom: 10mm;
        }

        .qr-logo-placeholder {
            width: 18mm;
            height: 18mm;
            border-radius: 6mm;
            background: linear-gradient(135deg, #0F6CBD, #00A6D6);
            color: #fff;
            display: grid;
            place-items: center;
            margin: 0 auto 7mm;
            font-size: 24px;
            font-weight: 900;
        }

        .qr-print-hotel {
            font-size: 13px;
            color: #667085;
            font-weight: 850;
            margin-bottom: 2mm;
        }

        .sheet-quarter .qr-print-hotel {
            font-size: 11px;
        }

        .qr-print-title {
            margin: 0 0 5mm;
            font-size: 25px;
            line-height: 1.05;
            letter-spacing: -.045em;
            color: #111827;
            font-weight: 900;
        }

        .sheet-quarter .qr-print-title {
            font-size: 20px;
            margin-bottom: 4mm;
        }

        .sheet-letter .qr-print-title {
            font-size: 36px;
            margin-bottom: 8mm;
        }

        .qr-print-message {
            font-size: 15px;
            line-height: 1.35;
            color: #344054;
            margin-bottom: 7mm;
        }

        .sheet-quarter .qr-print-message {
            font-size: 12px;
            margin-bottom: 5mm;
        }

        .sheet-letter .qr-print-message {
            font-size: 20px;
            margin-bottom: 10mm;
        }

        .qr-wrap {
            display: inline-block;
            padding: 9px;
            background: #fff;
            border: 1px solid #d0d5dd;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(15, 23, 42, .08);
            margin: 0 auto;
        }

        .sheet-quarter .qr-wrap {
            padding: 6px;
            border-radius: 13px;
        }

        .qr-print-hint {
            margin-top: 7mm;
            font-size: 12px;
            color: #667085;
            line-height: 1.4;
        }

        .sheet-quarter .qr-print-hint {
            margin-top: 5mm;
            font-size: 10px;
        }

        .sheet-letter .qr-print-hint {
            margin-top: 10mm;
            font-size: 16px;
        }

        .qr-print-tagline {
            margin-top: 5mm;
            padding-top: 4mm;
            border-top: 1px solid #eef2f6;
            font-size: 11px;
            color: #98a2b3;
            font-weight: 750;
        }

        .sheet-quarter .qr-print-tagline {
            margin-top: 4mm;
            padding-top: 3mm;
            font-size: 9px;
        }

        .qr-print-url {
            margin-top: 3mm;
            font-size: 8px;
            color: #98a2b3;
            word-break: break-all;
        }

        .sheet-quarter .qr-print-url {
            font-size: 7px;
        }

        @media print {
            body {
                background: #fff;
            }

            .qr-print-toolbar {
                display: none;
            }

            .qr-print-sheet {
                margin: 0 auto;
                box-shadow: none;
                border-radius: 0;
                border: 0;
                page-break-after: always;
            }

            @page {
                margin: 8mm;
            }
        }
    </style>
</head>

<body>
<header class="qr-print-toolbar d-print-none">
    <div class="container-xl py-2">
        <div class="d-flex flex-wrap align-items-center justify-content-center gap-2">
            <button class="btn btn-primary" onclick="window.print()" type="button">
                <i class="ti ti-printer me-1"></i>
                Imprimir / Guardar PDF
            </button>

            <div class="btn-list">
                <a class="btn {{ $size === 'quarter' ? 'btn-dark' : 'btn-outline-secondary' }}"
                   href="{{ route('hotel.qr-points.print', ['hotel' => $hotel, 'point' => $point, 'size' => 'quarter']) }}">
                    Cuarto carta
                </a>

                <a class="btn {{ $size === 'half' ? 'btn-dark' : 'btn-outline-secondary' }}"
                   href="{{ route('hotel.qr-points.print', ['hotel' => $hotel, 'point' => $point, 'size' => 'half']) }}">
                    Media carta
                </a>

                <a class="btn {{ $size === 'letter' ? 'btn-dark' : 'btn-outline-secondary' }}"
                   href="{{ route('hotel.qr-points.print', ['hotel' => $hotel, 'point' => $point, 'size' => 'letter']) }}">
                    Carta
                </a>
            </div>

            <a class="btn btn-outline-secondary" href="{{ route('hotel.qr-points.index', $hotel) }}">
                <i class="ti ti-arrow-left me-1"></i>
                Mis QRs
            </a>

            <button class="btn btn-outline-danger" onclick="tryCloseWindow()" type="button">
                <i class="ti ti-x me-1"></i>
                Cerrar
            </button>
        </div>
    </div>
</header>

<section class="qr-print-sheet sheet-{{ $size }}">
    @if($hotel->logo_path)
        <img class="qr-print-logo" src="{{ asset('storage/' . $hotel->logo_path) }}" alt="{{ $hotel->name }}">
    @else
        <div class="qr-logo-placeholder">
            {{ mb_substr($hotel->name, 0, 1) }}
        </div>
    @endif

    <div class="qr-print-hotel">
        {{ $hotel->name }}
    </div>

    <h1 class="qr-print-title">
        {{ $point->label }}
    </h1>

    <div class="qr-print-message">
        ¿Necesitas apoyo?<br>
        Escanea este código para solicitar atención.
    </div>

    <div class="qr-wrap">
        {!! $qrSvg !!}
    </div>

    <div class="qr-print-hint">
        No necesitas instalar ninguna app.<br>
        Recepción recibirá tu solicitud.
    </div>

    <div class="qr-print-tagline">
        HotelDesk Lite · Powered by SysApp
    </div>

    <div class="qr-print-url">
        {{ $url }}
    </div>
</section>

<script>
    function tryCloseWindow() {
        window.close();

        setTimeout(() => {
            alert('Si la ventana no se cerró automáticamente, puedes cerrarla manualmente.');
        }, 300);
    }
</script>
</body>
</html>