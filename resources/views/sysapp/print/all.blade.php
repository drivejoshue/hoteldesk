<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>QRs · {{ $hotel->name }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            margin: 0;
            background: #e5e7eb;
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

        .qr-grid {
            width: 21cm;
            margin: 20px auto;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8mm;
        }

        .qr-card {
            background: #fff;
            min-height: 13.2cm;
            padding: 12mm 9mm;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 12px 40px rgba(15, 23, 42, .12);
            page-break-inside: avoid;
        }

        .qr-logo {
            max-width: 45mm;
            max-height: 18mm;
            object-fit: contain;
            margin-bottom: 5mm;
        }

        .qr-hotel {
            font-size: 12px;
            color: #667085;
            font-weight: 800;
        }

        .qr-title {
            margin: 2mm 0 4mm;
            font-size: 20px;
            letter-spacing: -.04em;
            font-weight: 900;
        }

        .qr-message {
            font-size: 13px;
            line-height: 1.35;
            color: #344054;
            margin-bottom: 5mm;
        }

        .qr-box {
            display: inline-block;
            padding: 7px;
            background: #fff;
            border: 1px solid #e4e7ec;
            border-radius: 12px;
        }

        .qr-hint {
            margin-top: 5mm;
            font-size: 11px;
            color: #667085;
            line-height: 1.4;
        }

        .qr-url {
            margin-top: 3mm;
            font-size: 8px;
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

            .qr-grid {
                width: auto;
                margin: 0;
                gap: 6mm;
            }

            .qr-card {
                box-shadow: none;
                border: 1px solid #e4e7ec;
                border-radius: 0;
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

<section class="qr-grid">
    @foreach($qrItems as $item)
        <article class="qr-card">
            @if($hotel->logo_path)
                <img class="qr-logo" src="{{ asset('storage/' . $hotel->logo_path) }}" alt="{{ $hotel->name }}">
            @endif

            <div class="qr-hotel">{{ $hotel->name }}</div>

            <h2 class="qr-title">{{ $item['point']->label }}</h2>

            <div class="qr-message">
                ¿Necesitas apoyo?<br>
                Escanea este código para solicitar atención.
            </div>

            <div class="qr-box">
                {!! $item['qrSvg'] !!}
            </div>

            <div class="qr-hint">
                No necesitas instalar ninguna app.<br>
                Recepción recibirá tu solicitud.
            </div>

            <div class="qr-url">{{ $item['url'] }}</div>
        </article>
    @endforeach
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