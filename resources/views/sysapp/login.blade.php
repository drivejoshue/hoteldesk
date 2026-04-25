<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>HotelDesk Lite · SysApp</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 18px;
            background:
                radial-gradient(circle at top left, rgba(15,108,189,.22), transparent 34%),
                linear-gradient(135deg, #101828, #182230);
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .sys-login-card {
            width: 100%;
            max-width: 430px;
            background: #fff;
            border-radius: 28px;
            padding: 28px;
            box-shadow: 0 28px 80px rgba(0,0,0,.35);
        }

        .sys-logo {
            width: 52px;
            height: 52px;
            border-radius: 18px;
            display: grid;
            place-items: center;
            background: linear-gradient(135deg, #0F6CBD, #00A6D6);
            color: #fff;
            font-size: 28px;
            margin-bottom: 16px;
        }

        .sys-eyebrow {
            color: #667085;
            font-size: 13px;
            font-weight: 850;
        }

        h1 {
            margin: 4px 0 20px;
            color: #111827;
            font-size: 29px;
            letter-spacing: -.05em;
            line-height: 1.05;
        }

        .field {
            margin-bottom: 14px;
        }

        label {
            display: block;
            color: #475467;
            font-size: 13px;
            font-weight: 850;
            margin-bottom: 7px;
        }

        input {
            width: 100%;
            border: 1px solid #D0D5DD;
            border-radius: 16px;
            padding: 14px;
            font: inherit;
            outline: none;
        }

        input:focus {
            border-color: #0F6CBD;
            box-shadow: 0 0 0 4px rgba(15,108,189,.12);
        }

        button {
            width: 100%;
            border: 0;
            border-radius: 16px;
            padding: 14px 16px;
            background: #0F6CBD;
            color: #fff;
            font-size: 15px;
            font-weight: 900;
            cursor: pointer;
            margin-top: 6px;
        }

        .alert {
            border-radius: 16px;
            padding: 12px 14px;
            margin-bottom: 16px;
            background: #FFF1F1;
            color: #B42318;
            border: 1px solid #FFD5D5;
            font-size: 14px;
            font-weight: 750;
        }

        .hint {
            margin-top: 14px;
            text-align: center;
            color: #667085;
            font-size: 12px;
            line-height: 1.4;
        }
    </style>
</head>
<body>
<main class="sys-login-card">
    <div class="sys-logo">
        <i class="ti ti-shield-lock"></i>
    </div>

    <div class="sys-eyebrow">SysApp Admin</div>
    <h1>HotelDesk Lite</h1>

    @if($errors->any())
        <div class="alert">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('sysapp.login.post') }}">
        @csrf

        <div class="field">
            <label for="email">Correo</label>
            <input id="email"
                   name="email"
                   type="email"
                   value="{{ old('email') }}"
                   autocomplete="username"
                   required
                   autofocus>
        </div>

        <div class="field">
            <label for="password">Contraseña</label>
            <input id="password"
                   name="password"
                   type="password"
                   autocomplete="current-password"
                   required>
        </div>

        <button type="submit">
            <i class="ti ti-login"></i>
            Entrar
        </button>
    </form>

    <div class="hint">
        Acceso restringido para administración interna de SysApp.
    </div>
</main>
</body>
</html>