<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>QR · {{ $hotel->name }} · {{ $point->label }}</title>
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

        .qr-print-toolbar {
            position: sticky;
            top: 0;
            z-index: 20;
            background: rgba(255, 255, 255, .94);
            backdrop-filter: blur(14px);
            border-bottom: 1px solid #e4e7ec;
        }

        .qr-sheet {
            width: 10cm;
            min-height: 15cm;
            margin: 20px auto;
            background: #fff;
            padding: 18mm 12mm;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 12px 40px rgba(15, 23, 42, .18);
        }

        .qr-logo {
            max-width: 55mm;
            max-height: 22mm;
            object-fit: contain;
            margin-bottom: 8mm;
        }

        .qr-hotel {
            font-size: 14px;
            color: #667085;
            font-weight: 800;
        }

        .qr-title {
            margin: 3mm 0 5mm;
            font-size: 24px;
            letter-spacing: -.04em;
            font-weight: 900;
        }

        .qr-message {
            font-size: 15px;
            line-height: 1.35;
            color: #344054;
            margin-bottom: 8mm;
        }

        .qr-box {
            display: inline-block;
            padding: 8px;
            background: #fff;
            border: 1px solid #e4e7ec;
            border-radius: 12px;
        }

        .qr-hint {
            margin-top: 7mm;
            font-size: 12px;
            color: #667085;
            line-height: 1.4;
        }

        .qr-url {
            margin-top: 4mm;
            font-size: 9px;
            color: #98a2b3;
            word-break: break-all;
        }

        @media print {
            body {
                background: #fff;
            }

            .qr-print-toolbar {
                display: none;
            }

            .qr-sheet {
                width: auto;
                min-height: auto;
                margin: 0;
                box-shadow: none;
                border-radius: 0;
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

            <button class="btn btn-outline-danger" onclick="tryCloseWindow()" type="button">
                <i class="ti ti-x me-1"></i>
                Cerrar
            </button>
        </div>
    </div>
</header>

<section class="qr-sheet">
    @if($hotel->logo_path)
        <img class="qr-logo" src="{{ asset('storage/' . $hotel->logo_path) }}" alt="{{ $hotel->name }}">
    @endif

    <div class="qr-hotel">{{ $hotel->name }}</div>

    <h1 class="qr-title">{{ $point->label }}</h1>

    <div class="qr-message">
        ¿Necesitas apoyo?<br>
        Escanea este código para solicitar atención.
    </div>

    <div class="qr-box">
        {!! $qrSvg !!}
    </div>

    <div class="qr-hint">
        No necesitas instalar ninguna app.<br>
        Recepción recibirá tu solicitud.
    </div>

    <div class="qr-url">{{ $url }}</div>
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