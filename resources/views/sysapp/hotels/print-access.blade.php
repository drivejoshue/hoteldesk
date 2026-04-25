<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Acceso recepción · {{ $hotel->name }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            margin: 0;
            background:
                radial-gradient(circle at top left, rgba(15, 108, 189, .12), transparent 34%),
                #e5e7eb;
            color: #172033;
        }

        .access-toolbar {
            position: sticky;
            top: 0;
            z-index: 20;
            background: rgba(255, 255, 255, .94);
            backdrop-filter: blur(14px);
            border-bottom: 1px solid #e4e7ec;
        }

        .access-sheet {
            width: 13.95cm;
            min-height: 21.6cm;
            margin: 24px auto;
            background: #fff;
            padding: 16mm 13mm;
            border-radius: 18px;
            text-align: center;
            box-shadow: 0 16px 48px rgba(15, 23, 42, .18);
            border: 1px solid #eef2f6;
        }

        .access-logo {
            max-width: 55mm;
            max-height: 24mm;
            object-fit: contain;
            margin: 0 auto 8mm;
        }

        .access-logo-placeholder {
            width: 20mm;
            height: 20mm;
            border-radius: 7mm;
            background: linear-gradient(135deg, #0F6CBD, #00A6D6);
            color: #fff;
            display: grid;
            place-items: center;
            margin: 0 auto 8mm;
            font-size: 28px;
            font-weight: 900;
        }

        .access-eyebrow {
            color: #667085;
            font-size: 12px;
            font-weight: 850;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .access-title {
            margin: 3mm 0 3mm;
            font-size: 30px;
            line-height: 1.05;
            letter-spacing: -.045em;
            font-weight: 900;
        }

        .access-subtitle {
            color: #344054;
            font-size: 15px;
            line-height: 1.45;
            margin-bottom: 9mm;
        }

        .access-qr {
            display: inline-block;
            padding: 9px;
            background: #fff;
            border: 1px solid #d0d5dd;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(15, 23, 42, .08);
            margin: 0 auto 8mm;
        }

        .access-code-box {
            border: 1px solid #e4e7ec;
            background: #f8fafc;
            border-radius: 16px;
            padding: 12px;
            margin-top: 5mm;
            text-align: left;
        }

        .access-label {
            color: #667085;
            font-size: 11px;
            font-weight: 850;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .access-value {
            color: #111827;
            font-size: 16px;
            font-weight: 900;
            word-break: break-all;
            margin-top: 2mm;
        }

        .access-note {
            margin-top: 8mm;
            color: #667085;
            font-size: 12px;
            line-height: 1.45;
        }

        .access-warning {
            margin-top: 7mm;
            border: 1px solid #fde68a;
            background: #fffbeb;
            color: #92400e;
            border-radius: 14px;
            padding: 10px 12px;
            font-size: 12px;
            font-weight: 750;
            line-height: 1.4;
        }

        @media print {
            body {
                background: #fff;
            }

            .access-toolbar {
                display: none;
            }

            .access-sheet {
                width: auto;
                min-height: auto;
                margin: 0 auto;
                border: 0;
                border-radius: 0;
                box-shadow: none;
            }

            @page {
                margin: 8mm;
            }
        }
    </style>
</head>

<body>
<header class="access-toolbar d-print-none">
    <div class="container-xl py-2">
        <div class="d-flex flex-wrap align-items-center justify-content-center gap-2">
            <button class="btn btn-primary" onclick="window.print()" type="button">
                <i class="ti ti-printer me-1"></i>
                Imprimir / Guardar PDF
            </button>

            <a class="btn btn-outline-secondary" href="{{ route('sysapp.hotels.index') }}">
                <i class="ti ti-building me-1"></i>
                Hoteles
            </a>

            <button class="btn btn-outline-danger" onclick="tryCloseWindow()" type="button">
                <i class="ti ti-x me-1"></i>
                Cerrar
            </button>
        </div>
    </div>
</header>

<section class="access-sheet">
    @if($hotel->logo_path)
        <img class="access-logo" src="{{ asset('storage/' . $hotel->logo_path) }}" alt="{{ $hotel->name }}">
    @else
        <div class="access-logo-placeholder">
            {{ mb_substr($hotel->name, 0, 1) }}
        </div>
    @endif

    <div class="access-eyebrow">
        HotelDesk Lite
    </div>

    <h1 class="access-title">
        Acceso recepción
    </h1>

    <div class="access-subtitle">
        {{ $hotel->name }}<br>
        Escanea este código para abrir el panel del hotel.
    </div>

    <div class="access-qr">
        {!! $qrSvg !!}
    </div>

    <div class="access-code-box">
        <div class="access-label">Código del hotel</div>
        <div class="access-value">{{ $accessCode }}</div>
    </div>

    <div class="access-code-box">
        <div class="access-label">URL directa del panel</div>
        <div class="access-value">{{ $panelUrl }}</div>
    </div>

    <div class="access-code-box">
        <div class="access-label">Acceso general</div>
        <div class="access-value">{{ $accessUrl }}</div>
    </div>

    <div class="access-warning">
        Esta hoja es para uso interno de recepción. No contiene el PIN.
        El PIN debe entregarse por separado al responsable autorizado.
    </div>

    <div class="access-note">
        Si se cierra la página del panel, puedes volver a entrar escaneando este QR
        o escribiendo el código del hotel en la página de acceso.
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