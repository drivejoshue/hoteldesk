<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Guía de uso | HotelDesk Lite</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Si ya cargas Tabler desde tu layout, después quitamos estos CDN. --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta20/dist/css/tabler.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">

    <style>
        body {
            background: #f6f8fb;
        }

        .page-wrapper {
            padding-bottom: 32px;
        }

        .doc-section {
            scroll-margin-top: 24px;
        }

        .sticky-index {
            position: sticky;
            top: 16px;
        }

        .section-number {
            width: 32px;
            height: 32px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
        }

        .doc-muted {
            color: #6c7a91;
        }

        .doc-card-soft {
            background: #fbfcfe;
            border: 1px solid #e6eaf0;
        }

        .glossary-term {
            font-weight: 700;
        }

        .list-clean {
            margin-bottom: 0;
        }

        .list-clean li {
            margin-bottom: .5rem;
        }

        .list-clean li:last-child {
            margin-bottom: 0;
        }
    </style>
</head>
<body>
<div class="page">
    <div class="page-wrapper">

        {{-- Header Tabler --}}
        <div class="page-header d-print-none">
            <div class="container-xl">
                <div class="row g-3 align-items-center">
                    <div class="col">
                        <div class="page-pretitle">HotelDesk Lite</div>
                        <h2 class="page-title">Guía de uso</h2>
                        <div class="text-secondary">
                            Manual rápido para recepción y administración · {{ $hotel->name }}
                        </div>
                    </div>

                    <div class="col-auto ms-auto d-print-none">
                        <div class="btn-list">
                            <a href="{{ route('hotel.license.index', $hotel) }}" class="btn btn-outline-secondary">
                                <i class="ti ti-shield-check me-1"></i>
                                Licencia
                            </a>

                            @if($hotel->isLicenseActive())
                                <a href="{{ route('hotel.dashboard', $hotel) }}" class="btn btn-primary">
                                    <i class="ti ti-layout-dashboard me-1"></i>
                                    Volver al panel
                                </a>
                            @else
                                <a href="{{ route('hotel.login', $hotel) }}" class="btn btn-primary">
                                    <i class="ti ti-login me-1"></i>
                                    Ir al acceso
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="page-body">
            <div class="container-xl">

                {{-- Intro --}}
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row g-3 align-items-center">
                            <div class="col-auto">
                                <span class="avatar bg-blue-lt text-blue">
                                    <i class="ti ti-book"></i>
                                </span>
                            </div>

                            <div class="col">
                                <h3 class="card-title mb-1">Documentación rápida del sistema</h3>
                                <div class="text-secondary">
                                    Esta guía explica cómo usar HotelDesk Lite: recepción, códigos QR, solicitudes,
                                    estados, PIN administrador, licencia y buenas prácticas.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row row-cards">

                    {{-- Índice --}}
                    <div class="col-lg-3">
                        <div class="card sticky-index">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="ti ti-list me-2 text-secondary"></i>
                                    Índice
                                </h3>
                            </div>

                            <div class="list-group list-group-flush">
                                <a href="#que-es" class="list-group-item list-group-item-action">
                                    1. Qué es HotelDesk Lite
                                </a>
                                <a href="#recepcion" class="list-group-item list-group-item-action">
                                    2. Panel de recepción
                                </a>
                                <a href="#qr" class="list-group-item list-group-item-action">
                                    3. Códigos QR
                                </a>
                                <a href="#solicitudes" class="list-group-item list-group-item-action">
                                    4. Solicitudes
                                </a>
                                <a href="#estados" class="list-group-item list-group-item-action">
                                    5. Estados de atención
                                </a>
                                <a href="#admin" class="list-group-item list-group-item-action">
                                    6. PIN administrador
                                </a>
                                <a href="#licencia" class="list-group-item list-group-item-action">
                                    7. Licencia y renovaciones
                                </a>
                                <a href="#buenas-practicas" class="list-group-item list-group-item-action">
                                    8. Buenas prácticas
                                </a>
                                <a href="#glosario" class="list-group-item list-group-item-action">
                                    9. Glosario
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Contenido --}}
                    <div class="col-lg-9">

                        {{-- 1 --}}
                        <div id="que-es" class="card doc-section mb-3">
                            <div class="card-header">
                                <h3 class="card-title d-flex align-items-center gap-2 mb-0">
                                    <span class="section-number bg-blue-lt text-blue">1</span>
                                    Qué es HotelDesk Lite
                                </h3>
                            </div>

                            <div class="card-body">
                                <p class="mb-3">
                                    HotelDesk Lite es una herramienta web para que los huéspedes soliciten atención
                                    desde su celular escaneando un código QR. Recepción recibe cada solicitud en una
                                    pantalla y puede darle seguimiento.
                                </p>

                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="card card-sm doc-card-soft">
                                            <div class="card-body">
                                                <div class="d-flex gap-2">
                                                    <i class="ti ti-browser text-secondary mt-1"></i>
                                                    <div>
                                                        <strong>Sin instalación</strong>
                                                        <div class="text-secondary small">
                                                            Funciona desde navegador.
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="card card-sm doc-card-soft">
                                            <div class="card-body">
                                                <div class="d-flex gap-2">
                                                    <i class="ti ti-qrcode text-secondary mt-1"></i>
                                                    <div>
                                                        <strong>QR por área</strong>
                                                        <div class="text-secondary small">
                                                            Habitaciones, lobby o zonas comunes.
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="card card-sm doc-card-soft">
                                            <div class="card-body">
                                                <div class="d-flex gap-2">
                                                    <i class="ti ti-list-check text-secondary mt-1"></i>
                                                    <div>
                                                        <strong>Seguimiento</strong>
                                                        <div class="text-secondary small">
                                                            Pendiente, en proceso y resuelto.
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- 2 --}}
                        <div id="recepcion" class="card doc-section mb-3">
                            <div class="card-header">
                                <h3 class="card-title d-flex align-items-center gap-2 mb-0">
                                    <span class="section-number bg-secondary-lt text-secondary">2</span>
                                    Panel de recepción
                                </h3>
                            </div>

                            <div class="card-body">
                                <p>
                                    El panel de recepción muestra solicitudes nuevas y permite cambiar su estado.
                                    Se recomienda mantenerlo abierto durante el turno.
                                </p>

                                <ol class="list-clean">
                                    <li>Ingrese al enlace del hotel.</li>
                                    <li>Capture el PIN de recepción.</li>
                                    <li>Abra el dashboard.</li>
                                    <li>Revise solicitudes pendientes.</li>
                                    <li>Marque como “en proceso” o “resuelto” según corresponda.</li>
                                </ol>
                            </div>
                        </div>

                        {{-- 3 --}}
                        <div id="qr" class="card doc-section mb-3">
                            <div class="card-header">
                                <h3 class="card-title d-flex align-items-center gap-2 mb-0">
                                    <span class="section-number bg-secondary-lt text-secondary">3</span>
                                    Códigos QR
                                </h3>
                            </div>

                            <div class="card-body">
                                <p>
                                    Cada QR representa una habitación o área. Cuando el huésped lo escanea,
                                    el sistema identifica el punto de origen de la solicitud.
                                </p>

                                <div class="table-responsive">
                                    <table class="table table-vcenter card-table">
                                        <thead>
                                        <tr>
                                            <th>Tipo</th>
                                            <th>Uso</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td><span class="badge bg-blue-lt text-blue">Habitación</span></td>
                                            <td>QR colocado dentro de una habitación.</td>
                                        </tr>
                                        <tr>
                                            <td><span class="badge bg-secondary-lt text-secondary">Lobby</span></td>
                                            <td>QR para recepción, sala de espera o entrada.</td>
                                        </tr>
                                        <tr>
                                            <td><span class="badge bg-secondary-lt text-secondary">Área</span></td>
                                            <td>QR para alberca, gimnasio, terraza, restaurante o estacionamiento.</td>
                                        </tr>
                                        <tr>
                                            <td><span class="badge bg-yellow-lt text-yellow">Limitado</span></td>
                                            <td>QR que muestra solo ciertos tipos de solicitud.</td>
                                        </tr>
                                        <tr>
                                            <td><span class="badge bg-green-lt text-green">Directo</span></td>
                                            <td>QR que genera una solicitud específica de forma más rápida.</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        {{-- 4 --}}
                        <div id="solicitudes" class="card doc-section mb-3">
                            <div class="card-header">
                                <h3 class="card-title d-flex align-items-center gap-2 mb-0">
                                    <span class="section-number bg-secondary-lt text-secondary">4</span>
                                    Solicitudes disponibles
                                </h3>
                            </div>

                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="card card-sm">
                                            <div class="card-body d-flex gap-3">
                                                <span class="avatar bg-blue-lt text-blue">
                                                    <i class="ti ti-sparkles"></i>
                                                </span>
                                                <div>
                                                    <strong>Limpieza</strong>
                                                    <div class="text-secondary">Solicitud de aseo o revisión de habitación.</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="card card-sm">
                                            <div class="card-body d-flex gap-3">
                                                <span class="avatar bg-blue-lt text-blue">
                                                    <i class="ti ti-hanger"></i>
                                                </span>
                                                <div>
                                                    <strong>Toallas</strong>
                                                    <div class="text-secondary">Pedido rápido de toallas o amenidades.</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="card card-sm">
                                            <div class="card-body d-flex gap-3">
                                                <span class="avatar bg-yellow-lt text-yellow">
                                                    <i class="ti ti-tool"></i>
                                                </span>
                                                <div>
                                                    <strong>Mantenimiento</strong>
                                                    <div class="text-secondary">Reporte de falla en habitación o área.</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="card card-sm">
                                            <div class="card-body d-flex gap-3">
                                                <span class="avatar bg-secondary-lt text-secondary">
                                                    <i class="ti ti-message-2"></i>
                                                </span>
                                                <div>
                                                    <strong>Atención general</strong>
                                                    <div class="text-secondary">Solicitud libre con nota del huésped.</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- 5 --}}
                        <div id="estados" class="card doc-section mb-3">
                            <div class="card-header">
                                <h3 class="card-title d-flex align-items-center gap-2 mb-0">
                                    <span class="section-number bg-secondary-lt text-secondary">5</span>
                                    Estados de atención
                                </h3>
                            </div>

                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-vcenter card-table">
                                        <thead>
                                        <tr>
                                            <th>Estado</th>
                                            <th>Significado</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td><span class="badge bg-yellow-lt text-yellow">Pendiente</span></td>
                                            <td>Nueva solicitud recibida, aún no atendida.</td>
                                        </tr>
                                        <tr>
                                            <td><span class="badge bg-blue-lt text-blue">En proceso</span></td>
                                            <td>El personal ya tomó la solicitud.</td>
                                        </tr>
                                        <tr>
                                            <td><span class="badge bg-green-lt text-green">Resuelta</span></td>
                                            <td>La solicitud ya fue atendida.</td>
                                        </tr>
                                        <tr>
                                            <td><span class="badge bg-red-lt text-red">Cancelada</span></td>
                                            <td>La solicitud fue cancelada por recepción.</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        {{-- 6 --}}
                        <div id="admin" class="card doc-section mb-3">
                            <div class="card-header">
                                <h3 class="card-title d-flex align-items-center gap-2 mb-0">
                                    <span class="section-number bg-secondary-lt text-secondary">6</span>
                                    PIN administrador
                                </h3>
                            </div>

                            <div class="card-body">
                                <p>
                                    El PIN administrador permite acceder a funciones sensibles del hotel:
                                    reportes, impresión de QRs, regeneración de códigos y solicitudes de nuevos puntos.
                                </p>

                                <div class="alert alert-warning mb-0">
                                    <i class="ti ti-alert-triangle me-2"></i>
                                    Mantenga el PIN administrador solo con personal autorizado.
                                </div>
                            </div>
                        </div>

                        {{-- 7 --}}
                        <div id="licencia" class="card doc-section mb-3">
                            <div class="card-header">
                                <h3 class="card-title d-flex align-items-center gap-2 mb-0">
                                    <span class="section-number bg-secondary-lt text-secondary">7</span>
                                    Licencia y renovaciones
                                </h3>
                            </div>

                            <div class="card-body">
                                <p>
                                    La sección de licencia muestra plan actual, estado, días restantes,
                                    fecha de vencimiento y costos de referencia.
                                </p>

                                <a href="{{ route('hotel.license.index', $hotel) }}" class="btn btn-primary">
                                    <i class="ti ti-shield-check me-1"></i>
                                    Ver licencia
                                </a>
                            </div>
                        </div>

                        {{-- 8 --}}
                        <div id="buenas-practicas" class="card doc-section mb-3">
                            <div class="card-header">
                                <h3 class="card-title d-flex align-items-center gap-2 mb-0">
                                    <span class="section-number bg-secondary-lt text-secondary">8</span>
                                    Buenas prácticas
                                </h3>
                            </div>

                            <div class="card-body">
                                <ul class="list-clean">
                                    <li>Mantener el panel abierto durante el turno de recepción.</li>
                                    <li>Marcar solicitudes en proceso cuando alguien ya las esté atendiendo.</li>
                                    <li>Marcar como resuelto solo cuando el huésped ya fue atendido.</li>
                                    <li>Colocar los QR en lugares visibles y limpios.</li>
                                    <li>No compartir el PIN administrador con personal no autorizado.</li>
                                    <li>Revisar el historial para detectar solicitudes repetidas o fallas frecuentes.</li>
                                </ul>
                            </div>
                        </div>

                        {{-- 9 --}}
                        <div id="glosario" class="card doc-section">
                            <div class="card-header">
                                <h3 class="card-title d-flex align-items-center gap-2 mb-0">
                                    <span class="section-number bg-secondary-lt text-secondary">9</span>
                                    Glosario
                                </h3>
                            </div>

                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="card card-sm doc-card-soft">
                                            <div class="card-body">
                                                <div class="glossary-term">QR</div>
                                                <div class="text-secondary">Código que el huésped escanea para abrir el menú de solicitudes.</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="card card-sm doc-card-soft">
                                            <div class="card-body">
                                                <div class="glossary-term">Punto QR</div>
                                                <div class="text-secondary">Habitación o área vinculada a un código QR.</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="card card-sm doc-card-soft">
                                            <div class="card-body">
                                                <div class="glossary-term">Solicitud</div>
                                                <div class="text-secondary">Petición enviada por el huésped o creada desde el panel.</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="card card-sm doc-card-soft">
                                            <div class="card-body">
                                                <div class="glossary-term">Panel de recepción</div>
                                                <div class="text-secondary">Pantalla donde el personal revisa y atiende solicitudes.</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="card card-sm doc-card-soft">
                                            <div class="card-body">
                                                <div class="glossary-term">PIN de recepción</div>
                                                <div class="text-secondary">Clave para entrar al panel principal del hotel.</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="card card-sm doc-card-soft">
                                            <div class="card-body">
                                                <div class="glossary-term">PIN administrador</div>
                                                <div class="text-secondary">Clave para funciones avanzadas como reportes, QR e impresión.</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="card card-sm doc-card-soft">
                                            <div class="card-body">
                                                <div class="glossary-term">Licencia</div>
                                                <div class="text-secondary">Periodo de uso activo de HotelDesk Lite: prueba, mensual o anual.</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-center text-secondary mt-4 mb-3">
                            HotelDesk Lite · SysApp
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('a[href^="#"]').forEach(function (link) {
        link.addEventListener('click', function (event) {
            const target = document.querySelector(this.getAttribute('href'));

            if (!target) {
                return;
            }

            event.preventDefault();

            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        });
    });
</script>
</body>
</html>