<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>QRs · {{ $hotel->name }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            background: #e5e7eb;
            color: #172033;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }
        .toolbar {
            padding: 14px;
            text-align: center;
        }
        .toolbar button {
            border: 0;
            border-radius: 12px;
            padding: 10px 14px;
            background: #0F6CBD;
            color: #fff;
            font-weight: 900;
            cursor: pointer;
        }
        .grid {
            width: 21cm;
            margin: 20px auto;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8mm;
        }
        .card {
            background: #fff;
            min-height: 13.2cm;
            padding: 12mm 9mm;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 12px 40px rgba(15,23,42,.12);
            page-break-inside: avoid;
        }
        .logo {
            max-width: 45mm;
            max-height: 18mm;
            object-fit: contain;
            margin-bottom: 5mm;
        }
        .hotel {
            font-size: 12px;
            color: #667085;
            font-weight: 800;
        }
        h2 {
            margin: 2mm 0 4mm;
            font-size: 20px;
            letter-spacing: -.04em;
        }
        .message {
            font-size: 13px;
            line-height: 1.35;
            color: #344054;
            margin-bottom: 5mm;
        }
        .qr {
            display: inline-block;
            padding: 7px;
            background: #fff;
            border: 1px solid #e4e7ec;
            border-radius: 12px;
        }
        .hint {
            margin-top: 5mm;
            font-size: 11px;
            color: #667085;
            line-height: 1.4;
        }
        .url {
            margin-top: 3mm;
            font-size: 8px;
            color: #98a2b3;
            word-break: break-all;
        }
        @media print {
            body { background: #fff; }
            .toolbar { display: none; }
            .grid {
                width: auto;
                margin: 0;
                gap: 6mm;
            }
            .card {
                box-shadow: none;
                border: 1px solid #e4e7ec;
                border-radius: 0;
            }
        }
    </style>
</head>
<body>
<div class="toolbar">
    <button onclick="window.print()">Imprimir / Guardar PDF</button>
</div>

<section class="grid">
    @foreach($qrItems as $item)
        <article class="card">
            @if($hotel->logo_path)
                <img class="logo" src="{{ asset('storage/' . $hotel->logo_path) }}" alt="{{ $hotel->name }}">
            @endif

            <div class="hotel">{{ $hotel->name }}</div>
            <h2>{{ $item['point']->label }}</h2>

            <div class="message">
                ¿Necesitas apoyo?<br>
                Escanea este código para solicitar atención.
            </div>

            <div class="qr">
                {!! $item['qrSvg'] !!}
            </div>

            <div class="hint">
                No necesitas instalar ninguna app.<br>
                Recepción recibirá tu solicitud.
            </div>

            <div class="url">{{ $item['url'] }}</div>
        </article>
    @endforeach
</section>
</body>
</html>