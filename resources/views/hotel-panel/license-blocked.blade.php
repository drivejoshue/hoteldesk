<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Servicio no activo | HotelDesk Lite</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        body {
            margin: 0;
            min-height: 100vh;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: #f5f7fb;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #1f2937;
        }

        .card {
            width: min(92vw, 460px);
            background: #fff;
            border-radius: 22px;
            padding: 28px;
            box-shadow: 0 18px 45px rgba(15, 23, 42, .12);
            text-align: center;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 54px;
            height: 54px;
            border-radius: 18px;
            background: #fff4e5;
            color: #b45309;
            font-size: 28px;
            font-weight: 800;
            margin-bottom: 16px;
        }

        h1 {
            font-size: 22px;
            margin: 0 0 10px;
        }

        p {
            font-size: 15px;
            line-height: 1.5;
            color: #64748b;
            margin: 0 0 18px;
        }

        .small {
            font-size: 13px;
            color: #94a3b8;
            margin-bottom: 0;
        }
    </style>
</head>
<body>
<div class="card">
    <div class="badge">!</div>

    <h1>Servicio no activo</h1>

    <p>
        El acceso de <strong>{{ $hotel->name }}</strong> a HotelDesk Lite no se encuentra activo en este momento.
    </p>

    <p class="small">
        Si considera que esto es un error, contacte a SysApp para revisar la licencia o el periodo de prueba.
    </p>
</div>
</body>
</html>