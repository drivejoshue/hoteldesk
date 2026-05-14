<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Licencia | HotelDesk Lite</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Usa tu Tabler local/layout si ya lo tienes cargado en el proyecto.
         Esto es solo fallback si la vista se abre standalone. --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta20/dist/css/tabler.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">

    <style>
        body {
            background: #f6f8fb;
        }

        .page-wrapper {
            padding-bottom: 32px;
        }

        .metric-card {
            min-height: 120px;
        }

        .price-big {
            font-size: 2rem;
            line-height: 1.1;
            font-weight: 700;
            letter-spacing: -.02em;
        }

        .section-muted {
            color: #6c7a91;
        }

        .empty-soft {
            border: 1px dashed #d9dee7;
            border-radius: 12px;
            background: #fbfcfe;
            padding: 32px 24px;
        }
    </style>
</head>
<body>
<div class="page">
    <div class="page-wrapper">
        @php
            $days = $hotel->licenseDaysRemaining();
            $endsAt = $hotel->licenseEndsAt();

            $isTrialActive = $hotel->license_status === 'trial' && $hotel->isLicenseActive();
            $isActive = $hotel->license_status === 'active' && $hotel->isLicenseActive();
            $isBlocked = ! $hotel->isLicenseActive();

            $statusText = $isTrialActive
                ? 'Prueba activa'
                : ($isActive ? 'Licencia activa' : 'Licencia no activa');

            $statusAlertClass = $isTrialActive
                ? 'bg-blue-lt text-blue'
                : ($isActive ? 'bg-green-lt text-green' : 'bg-yellow-lt text-yellow');
        @endphp

        <div class="page-header d-print-none">
            <div class="container-xl">
                <div class="row g-3 align-items-center">
                    <div class="col">
                        <div class="page-pretitle">HotelDesk Lite</div>
                        <h2 class="page-title">Licencia y servicio</h2>
                        <div class="text-secondary">
                            {{ $hotel->name }}
                        </div>
                    </div>

                    <div class="col-auto ms-auto d-print-none">
                        <div class="btn-list">
                            <a href="{{ route('hotel.license.index', $hotel) }}" class="btn btn-outline-secondary">
                                <i class="ti ti-shield-check me-1"></i>
                                Licencia
                            </a>

                            <a href="{{ route('hotel.docs.index', $hotel) }}" class="btn btn-outline-secondary">
                                <i class="ti ti-book me-1"></i>
                                Guía de uso
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

                {{-- Estado general --}}
                <div class="alert {{ $statusAlertClass }} mb-4">
                    <div class="d-flex">
                        <div>
                            <i class="ti ti-info-circle me-2"></i>
                        </div>
                        <div>
                            <strong>{{ $statusText }}.</strong>

                            @if($isTrialActive)
                                Puede usar HotelDesk Lite durante su periodo de prueba.
                            @elseif($isActive)
                                Su servicio está vigente y disponible para operación.
                            @else
                                El servicio requiere reactivación o renovación para continuar operando.
                            @endif

                            @if($endsAt)
                                Vence el <strong>{{ $endsAt->format('d/m/Y') }}</strong>.
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Resumen superior --}}
                <div class="row row-cards mb-4">
                    <div class="col-sm-6 col-lg-3">
                        <div class="card metric-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="subheader">Estado</div>
                                        <div class="h2 mb-0">{{ $hotel->licenseLabel() }}</div>
                                    </div>
                                    <div class="avatar bg-blue-lt text-blue">
                                        <i class="ti ti-shield-check"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg-3">
                        <div class="card metric-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="subheader">Plan</div>
                                        <div class="h2 mb-0">{{ $hotel->planLabel() }}</div>
                                    </div>
                                    <div class="avatar bg-secondary-lt text-secondary">
                                        <i class="ti ti-package"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg-3">
                        <div class="card metric-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="subheader">Vencimiento</div>
                                        <div class="h2 mb-0">
                                            {{ $endsAt ? $endsAt->format('d/m/Y') : '—' }}
                                        </div>
                                    </div>
                                    <div class="avatar bg-yellow-lt text-yellow">
                                        <i class="ti ti-calendar-event"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg-3">
                        <div class="card metric-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="subheader">Días restantes</div>
                                        <div class="h2 mb-0">{{ $days ?? '—' }}</div>
                                    </div>
                                    <div class="avatar bg-green-lt text-green">
                                        <i class="ti ti-clock-hour-4"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row row-cards">

                    {{-- Estado actual --}}
                    <div class="col-lg-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="ti ti-report-money me-2 text-secondary"></i>
                                    Estado actual
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <div class="subheader">Plan contratado</div>
                                    <div class="fw-bold fs-3">{{ $hotel->planLabel() }}</div>
                                </div>

                                <div class="mb-3">
                                    <div class="subheader">Estado de licencia</div>
                                    <span class="badge {{ $hotel->licenseStatusClass() }}">
                                        {{ $hotel->licenseLabel() }}
                                    </span>
                                </div>

                                <div class="mb-3">
                                    <div class="subheader">Tipo de ciclo</div>
                                    <div class="fw-medium">{{ $hotel->billingCycleLabel() }}</div>
                                </div>

                                <div class="mb-3">
                                    <div class="subheader">Fecha de vencimiento</div>
                                    <div class="fw-medium">
                                        {{ $endsAt ? $endsAt->format('d/m/Y H:i') : 'Sin fecha definida' }}
                                    </div>
                                </div>

                                @if($days !== null)
                                    <div class="card bg-light-lt border-0">
                                        <div class="card-body py-3">
                                            <div class="subheader">Tiempo disponible</div>
                                            <div class="d-flex align-items-end gap-2">
                                                <div class="display-6 fw-bold mb-0">{{ $days }}</div>
                                                <div class="section-muted mb-1">día(s)</div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Precio --}}
                    <div class="col-lg-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="ti ti-wallet me-2 text-secondary"></i>
                                    Plan Lite
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="mb-4">
                                    <div class="subheader">Mensual</div>
                                    <div class="price-big">$249.00 MXN</div>
                                    <div class="section-muted">
                                        Opción flexible para operar mes a mes.
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <div class="subheader">Anual</div>
                                    <div class="price-big">$2,499.00 MXN</div>
                                    <div class="section-muted">
                                        Precio preferente para operación continua.
                                    </div>
                                </div>

                                <div class="d-flex flex-wrap gap-2">
                                    <span class="badge bg-green-lt text-green">Actualizaciones incluidas</span>
                                    <span class="badge bg-blue-lt text-blue">Respaldo incluido</span>
                                    <span class="badge bg-secondary-lt text-secondary">Soporte básico</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Qué incluye --}}
                    <div class="col-lg-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="ti ti-list-check me-2 text-secondary"></i>
                                    Qué incluye
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="divide-y">
                                    <div class="py-2 d-flex">
                                        <div class="me-2 text-green"><i class="ti ti-check"></i></div>
                                        <div>Panel de recepción para ver solicitudes en tiempo real.</div>
                                    </div>
                                    <div class="py-2 d-flex">
                                        <div class="me-2 text-green"><i class="ti ti-check"></i></div>
                                        <div>Códigos QR por habitación, lobby o área común.</div>
                                    </div>
                                    <div class="py-2 d-flex">
                                        <div class="me-2 text-green"><i class="ti ti-check"></i></div>
                                        <div>Solicitudes básicas: limpieza, toallas, mantenimiento y atención.</div>
                                    </div>
                                    <div class="py-2 d-flex">
                                        <div class="me-2 text-green"><i class="ti ti-check"></i></div>
                                        <div>Estados: pendiente, en proceso, resuelto o cancelado.</div>
                                    </div>
                                    <div class="py-2 d-flex">
                                        <div class="me-2 text-green"><i class="ti ti-check"></i></div>
                                        <div>Servidor, respaldo, actualizaciones y soporte básico.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Renovaciones --}}
                    <div class="col-lg-8">
                        <div class="card h-100">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="ti ti-refresh me-2 text-secondary"></i>
                                    Renovaciones
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="row g-3 mb-4">
                                    <div class="col-md-4">
                                        <div class="card bg-light-lt border-0">
                                            <div class="card-body py-3">
                                                <div class="subheader">Última renovación</div>
                                                <div class="fw-medium">Pendiente</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="card bg-light-lt border-0">
                                            <div class="card-body py-3">
                                                <div class="subheader">Próxima renovación</div>
                                                <div class="fw-medium">
                                                    {{ $endsAt ? $endsAt->format('d/m/Y') : 'Sin fecha' }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="card bg-light-lt border-0">
                                            <div class="card-body py-3">
                                                <div class="subheader">Canal de pago</div>
                                                <div class="fw-medium">Directo con SysApp</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="empty-soft text-center">
                                    <div class="h3 mb-2">Aún no hay renovaciones registradas</div>
                                    <div class="section-muted">
                                        Más adelante aquí aparecerán pagos, renovaciones,
                                        comprobantes, facturas o historial de licencia.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Ecosistema --}}
                    <div class="col-lg-4">
                        <div class="card h-100 border-dashed">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="ti ti-layout-grid me-2 text-secondary"></i>
                                    Ecosistema SysApp
                                </h3>
                            </div>
                            <div class="card-body">
                                <p class="section-muted mb-3">
                                    HotelDesk Lite forma parte de una familia de herramientas para negocios locales.
                                </p>

                                <p class="section-muted mb-4">
                                    Más adelante se podrán habilitar beneficios, integraciones o herramientas adicionales
                                    para establecimientos participantes.
                                </p>

                                <a href="{{ route('hotel.docs.index', $hotel) }}" class="btn btn-outline-secondary w-100">
                                    <i class="ti ti-book me-1"></i>
                                    Ver documentación
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center text-secondary mt-4">
                    HotelDesk Lite · SysApp
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>