<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>QR · {{ $hotel->name }} · {{ $point->label }}</title>
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
        .sheet {
            width: 10cm;
            min-height: 15cm;
            margin: 20px auto;
            background: #fff;
            padding: 18mm 12mm;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 12px 40px rgba(15,23,42,.18);
        }
        .logo {
            max-width: 55mm;
            max-height: 22mm;
            object-fit: contain;
            margin-bottom: 8mm;
        }
        .hotel {
            font-size: 14px;
            color: #667085;
            font-weight: 800;
        }
        h1 {
            margin: 3mm 0 5mm;
            font-size: 24px;
            letter-spacing: -.04em;
        }
        .message {
            font-size: 15px;
            line-height: 1.35;
            color: #344054;
            margin-bottom: 8mm;
        }
        .qr {
            display: inline-block;
            padding: 8px;
            background: #fff;
            border: 1px solid #e4e7ec;
            border-radius: 12px;
        }
        .hint {
            margin-top: 7mm;
            font-size: 12px;
            color: #667085;
            line-height: 1.4;
        }
        .url {
            margin-top: 4mm;
            font-size: 9px;
            color: #98a2b3;
            word-break: break-all;
        }
        @media print {
            body { background: #fff; }
            .toolbar { display: none; }
            .sheet {
                width: auto;
                min-height: auto;
                margin: 0;
                box-shadow: none;
                border-radius: 0;
                page-break-after: always;
            }
        }
    </style>
</head>
<body>
<div class="toolbar">
    <button onclick="window.print()">Imprimir / Guardar PDF</button>
</div>

<section class="sheet">
    @if($hotel->logo_path)
        <img class="logo" src="{{ asset('storage/' . $hotel->logo_path) }}" alt="{{ $hotel->name }}">
    @endif

    <div class="hotel">{{ $hotel->name }}</div>
    <h1>{{ $point->label }}</h1>

    <div class="message">
        ¿Necesitas apoyo?<br>
        Escanea este código para solicitar atención.
    </div>

    <div class="qr">
        {!! $qrSvg !!}
    </div>

    <div class="hint">
        No necesitas instalar ninguna app.<br>
        Recepción recibirá tu solicitud.
    </div>

    <div class="url">{{ $url }}</div>
</section>
</body>
</html>