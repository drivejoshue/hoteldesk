<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'HotelDesk Lite · SysApp')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            background: #f4f6f8;
            color: #172033;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }
        header {
            background: #101828;
            color: #fff;
            padding: 14px 18px;
        }
        .topbar {
            max-width: 1180px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            gap: 12px;
            align-items: center;
        }
        .brand {
            font-weight: 900;
            letter-spacing: -.03em;
            font-size: 20px;
        }
        .nav {
            display: flex;
            gap: 8px;
            align-items: center;
        }
        .nav a, .nav button {
            border: 0;
            border-radius: 12px;
            padding: 9px 12px;
            background: rgba(255,255,255,.10);
            color: #fff;
            text-decoration: none;
            font-weight: 800;
            cursor: pointer;
        }
        main {
            max-width: 1180px;
            margin: 0 auto;
            padding: 22px 18px;
        }
        .page-head {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            align-items: center;
            margin-bottom: 18px;
        }
        h1 {
            margin: 0;
            font-size: 28px;
            letter-spacing: -.04em;
        }
        .muted {
            color: #667085;
        }
        .card {
            background: #fff;
            border: 1px solid #e4e7ec;
            border-radius: 20px;
            padding: 18px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, .04);
        }
        .grid {
            display: grid;
            gap: 14px;
        }
        .grid-2 {
            grid-template-columns: repeat(2, 1fr);
        }
        .grid-3 {
            grid-template-columns: repeat(3, 1fr);
        }
        label {
            display: block;
            font-size: 13px;
            font-weight: 800;
            color: #475467;
            margin-bottom: 6px;
        }
        input, select, textarea {
            width: 100%;
            border: 1px solid #d0d5dd;
            border-radius: 13px;
            padding: 11px 12px;
            font: inherit;
            outline: none;
            background: #fff;
        }
        input:focus, select:focus, textarea:focus {
            border-color: #0F6CBD;
            box-shadow: 0 0 0 3px rgba(15,108,189,.12);
        }
        .field {
            margin-bottom: 14px;
        }
        .check-row {
            display: flex;
            gap: 8px;
            align-items: center;
            margin: 6px 0;
        }
        .check-row input {
            width: auto;
        }
        .btn {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            border: 0;
            border-radius: 12px;
            padding: 10px 13px;
            font-weight: 850;
            text-decoration: none;
            cursor: pointer;
            font-size: 14px;
        }
        .btn-primary {
            background: #0F6CBD;
            color: #fff;
        }
        .btn-soft {
            background: #eef2f6;
            color: #344054;
        }
        .btn-danger {
            background: #fff1f1;
            color: #b42318;
        }
        .btn-success {
            background: #ecfdf3;
            color: #027a48;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .table th {
            text-align: left;
            color: #667085;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .04em;
            padding: 12px;
            border-bottom: 1px solid #e4e7ec;
        }
        .table td {
            padding: 12px;
            border-bottom: 1px solid #f0f2f5;
            vertical-align: top;
        }
        .badge {
            display: inline-flex;
            border-radius: 999px;
            padding: 5px 9px;
            font-size: 12px;
            font-weight: 900;
        }
        .badge-active { background: #ecfdf3; color: #027a48; }
        .badge-draft { background: #eef2f6; color: #344054; }
        .badge-paused { background: #fff7e6; color: #b54708; }
        .badge-disabled { background: #fff1f1; color: #b42318; }
        .alert {
            border-radius: 14px;
            padding: 12px 14px;
            margin-bottom: 16px;
            font-weight: 750;
        }
        .alert-success {
            background: #ecfdf3;
            color: #027a48;
            border: 1px solid #abefc6;
        }
        .alert-error {
            background: #fff1f1;
            color: #b42318;
            border: 1px solid #ffd5d5;
        }
        .actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        @media (max-width: 780px) {
            .grid-2, .grid-3 {
                grid-template-columns: 1fr;
            }
            .page-head, .topbar {
                flex-direction: column;
                align-items: flex-start;
            }
            .table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }
        }
    </style>
</head>
<body>
<header>
    <div class="topbar">
        <div class="brand">HotelDesk Lite · SysApp</div>

      
       <div class="nav">
    <span style="font-size: 13px; color: rgba(255,255,255,.75); font-weight: 800;">
        {{ session('hoteldesk.sysapp.admin_name') }}
    </span>

    <a href="{{ route('sysapp.hotels.index') }}">Hoteles</a>

    <a href="{{ route('sysapp.qr-requests.index') }}">Solicitudes QR</a>

    <a href="{{ route('sysapp.pin-reset-requests.index') }}">Reset PIN</a>

    <a href="{{ route('sysapp.audit-logs.index') }}">Logs</a>



    <form method="POST" action="{{ route('sysapp.logout') }}">
        @csrf
        <button type="submit">Salir</button>
    </form>
</div>
           
    </div>
</header>

<main>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-error">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    @yield('content')
</main>
</body>
</html>