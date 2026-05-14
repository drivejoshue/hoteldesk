<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Términos de prueba | HotelDesk Lite</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta20/dist/css/tabler.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">

    <style>
        body {
            background: #f6f8fb;
        }

        .trial-shell {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .trial-card {
            max-width: 760px;
            width: 100%;
        }

        .terms-box {
            max-height: 280px;
            overflow-y: auto;
            border: 1px solid #e6eaf0;
            border-radius: 10px;
            background: #fbfcfe;
            padding: 16px;
        }
    </style>
</head>
<body>
<div class="trial-shell">
    <div class="card trial-card shadow-sm">
        <div class="card-header">
            <div class="d-flex align-items-center gap-3">
                <span class="avatar bg-blue-lt text-blue">
                    <i class="ti ti-shield-check"></i>
                </span>

                <div>
                    <h3 class="card-title mb-0">Activar periodo de prueba</h3>
                    <div class="text-secondary">
                        {{ $hotel->name }} · HotelDesk Lite
                    </div>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('hotel.terms.accept', $hotel) }}">
            @csrf

            <div class="card-body">
                <div class="alert bg-blue-lt text-blue">
                    <i class="ti ti-info-circle me-2"></i>
                    Al aceptar, se activará el periodo de prueba de
                    <strong>{{ $hotel->trial_days ?: 14 }} días</strong>.
                    El conteo inicia a partir de este momento.
                </div>

                <div class="terms-box mb-3">
                    <h4>Términos básicos de uso</h4>

                    <p>
                        HotelDesk Lite es una herramienta web para apoyar la operación interna del hotel,
                        permitiendo recibir solicitudes mediante códigos QR y dar seguimiento desde un panel de recepción.
                    </p>

                    <p>
                        Durante el periodo de prueba, el hotel podrá evaluar el funcionamiento del sistema
                        sin obligación de contratación al finalizar dicho periodo.
                    </p>

                    <p>
                        El sistema no debe utilizarse para capturar información sensible de huéspedes.
                        Las notas o comentarios deben limitarse a información operativa necesaria para atender solicitudes.
                    </p>

                    <p>
                        El hotel es responsable del uso interno del panel, del resguardo de sus PIN de acceso
                        y de la atención que brinde a las solicitudes recibidas.
                    </p>

                    <p>
                        SysApp podrá registrar datos técnicos mínimos para operación y seguridad, como fecha de aceptación,
                        dirección IP, navegador utilizado y movimientos administrativos del sistema.
                    </p>

                    <p>
                        Al finalizar la prueba, el acceso operativo puede quedar limitado hasta que se active
                        una licencia mensual, anual o acuerdo comercial vigente.
                    </p>

                    <p class="mb-0">
                        Este texto corresponde a condiciones básicas para prueba operativa. En caso de contratación formal,
                        podrán establecerse condiciones comerciales adicionales.
                    </p>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nombre de quien acepta</label>
                    <input
                        type="text"
                        name="accepted_by"
                        class="form-control @error('accepted_by') is-invalid @enderror"
                        maxlength="120"
                        placeholder="Ej. Recepción, gerente, encargado"
                        value="{{ old('accepted_by') }}"
                    >

                    @error('accepted_by')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <label class="form-check">
                    <input
                        class="form-check-input @error('accept_terms') is-invalid @enderror"
                        type="checkbox"
                        name="accept_terms"
                        value="1"
                        required
                    >
                    <span class="form-check-label">
                        Acepto los términos básicos de uso y deseo activar el periodo de prueba.
                    </span>

                    @error('accept_terms')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </label>
            </div>

            <div class="card-footer d-flex justify-content-between align-items-center">
                <a href="{{ route('hotel.license.index', $hotel) }}" class="btn btn-outline-secondary">
                    Ver licencia
                </a>

                <button class="btn btn-primary" type="submit">
                    <i class="ti ti-check me-1"></i>
                    Aceptar y activar prueba
                </button>
            </div>
        </form>
    </div>
</div>
</body>
</html>