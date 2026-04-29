@extends('sysapp.layout')

@section('title', 'Hoteles · HotelDesk Lite')

@section('content')
    @php
        $statusMeta = function ($status) {
            return match ($status) {
                'active' => [
                    'label' => 'Activo',
                    'class' => 'bg-green-lt text-green',
                    'softClass' => 'sys-hotel-active',
                    'icon' => 'ti-circle-check',
                ],
                'paused' => [
                    'label' => 'Pausado',
                    'class' => 'bg-yellow-lt text-yellow',
                    'softClass' => 'sys-hotel-paused',
                    'icon' => 'ti-player-pause',
                ],
                'disabled' => [
                    'label' => 'Desactivado',
                    'class' => 'bg-red-lt text-red',
                    'softClass' => 'sys-hotel-disabled',
                    'icon' => 'ti-ban',
                ],
                default => [
                    'label' => 'Borrador',
                    'class' => 'bg-secondary-lt text-secondary',
                    'softClass' => 'sys-hotel-draft',
                    'icon' => 'ti-pencil',
                ],
            };
        };

        $boolBadge = function ($enabled) {
            return $enabled
                ? ['label' => 'Activo', 'class' => 'bg-green-lt text-green', 'icon' => 'ti-check']
                : ['label' => 'Inactivo', 'class' => 'bg-secondary-lt text-secondary', 'icon' => 'ti-minus'];
        };

        $currentStatus = $status ?? '';
        $currentSearch = $search ?? '';
    @endphp

    <style>
        .sys-home-hero {
            border: 1px solid #dbeafe;
            border-radius: 22px;
            background:
                radial-gradient(circle at top right, rgba(0, 204, 255, .16), transparent 34%),
                linear-gradient(135deg, #ffffff 0%, #eff6ff 100%);
            box-shadow: 0 14px 36px rgba(15, 23, 42, .07);
            overflow: hidden;
        }

        .sys-metric-card {
            border-radius: 18px;
            border: 1px solid #e5e7eb;
            background: #ffffff;
            box-shadow: 0 10px 26px rgba(15, 23, 42, .055);
            height: 100%;
        }

        .sys-metric-icon {
            width: 42px;
            height: 42px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }

        .sys-hotels-card {
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid #e5e7eb;
            box-shadow: 0 12px 32px rgba(15, 23, 42, .06);
        }

        .sys-hotel-row {
            transition: background .15s ease;
        }

        .sys-hotel-row:hover {
            background: #f8fafc;
        }

        .sys-hotel-row-marker {
            width: 5px;
            min-width: 5px;
            align-self: stretch;
            border-radius: 999px;
        }

        .sys-hotel-active .sys-hotel-row-marker {
            background: #16a34a;
        }

        .sys-hotel-paused .sys-hotel-row-marker {
            background: #f59e0b;
        }

        .sys-hotel-disabled .sys-hotel-row-marker {
            background: #dc2626;
        }

        .sys-hotel-draft .sys-hotel-row-marker {
            background: #94a3b8;
        }

        .sys-service-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            border-radius: 999px;
            padding: 5px 9px;
            font-size: 12px;
            font-weight: 750;
        }

        .sys-actions {
            display: flex;
            flex-wrap: nowrap;
            gap: 6px;
        }

        .sys-actions .btn {
            white-space: nowrap;
        }

        .sys-code {
            font-size: 12px;
            border-radius: 999px;
            padding: 4px 8px;
            background: #f1f5f9;
            color: #475569;
            font-weight: 750;
        }

        @media (max-width: 991.98px) {
            .sys-actions {
                flex-wrap: wrap;
            }
        }
    </style>

    <div class="page-header d-print-none mb-3">
        <div class="row align-items-center g-2">
            <div class="col">
                <h2 class="page-title mb-1">Panel SysAdmin</h2>
                <div class="text-secondary">
                    Control central de hoteles, accesos, QRs y operación básica.
                </div>
            </div>

            <div class="col-auto">
                <a class="btn btn-primary" href="{{ route('sysapp.hotels.create') }}">
                    <i class="ti ti-plus me-1"></i>
                    Crear hotel
                </a>
            </div>
        </div>
    </div>

    <div class="sys-home-hero card mb-3">
        <div class="card-body">
            <div class="row align-items-center g-3">
                <div class="col-12 col-lg">
                    <div class="d-flex align-items-center gap-3">
                        <span class="avatar avatar-lg bg-primary-lt text-primary">
                            <i class="ti ti-building-skyscraper" style="font-size: 26px;"></i>
                        </span>

                        <div>
                            <h1 class="h2 mb-1">HotelDesk Lite</h1>
                            <div class="text-secondary">
                                Vista principal para administrar hoteles, generar accesos y dar soporte operativo.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-auto">
                    <div class="btn-list">
                        <a class="btn btn-outline-primary" href="{{ route('sysapp.qr-requests.index') }}">
                            <i class="ti ti-qrcode me-1"></i>
                            Solicitudes QR
                        </a>

                        <a class="btn btn-outline-secondary" href="{{ route('sysapp.pin-reset-requests.index') }}">
                            <i class="ti ti-key me-1"></i>
                            Reset PIN
                        </a>

                        <a class="btn btn-outline-secondary" href="{{ route('sysapp.audit-logs.index') }}">
                            <i class="ti ti-history me-1"></i>
                            Logs
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-cards mb-3">
        <div class="col-6 col-lg-3">
            <div class="sys-metric-card card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <span class="sys-metric-icon bg-primary-lt text-primary">
                            <i class="ti ti-building"></i>
                        </span>
                        <span class="badge bg-primary-lt text-primary">Total</span>
                    </div>
                    <div class="h1 mt-3 mb-0">{{ number_format($summary['total'] ?? $hotels->total()) }}</div>
                    <div class="text-secondary">Hoteles registrados</div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="sys-metric-card card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <span class="sys-metric-icon bg-green-lt text-green">
                            <i class="ti ti-circle-check"></i>
                        </span>
                        <span class="badge bg-green-lt text-green">Operando</span>
                    </div>
                    <div class="h1 mt-3 mb-0">{{ number_format($summary['active'] ?? 0) }}</div>
                    <div class="text-secondary">Hoteles activos</div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="sys-metric-card card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <span class="sys-metric-icon bg-blue-lt text-blue">
                            <i class="ti ti-qrcode"></i>
                        </span>
                        <span class="badge bg-blue-lt text-blue">QRs</span>
                    </div>
                    <div class="h1 mt-3 mb-0">{{ number_format($summary['qr_total'] ?? 0) }}</div>
                    <div class="text-secondary">Códigos generados</div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="sys-metric-card card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <span class="sys-metric-icon bg-purple-lt text-purple">
                            <i class="ti ti-inbox"></i>
                        </span>
                        <span class="badge bg-purple-lt text-purple">Uso</span>
                    </div>
                    <div class="h1 mt-3 mb-0">{{ number_format($summary['requests_total'] ?? 0) }}</div>
                    <div class="text-secondary">Solicitudes recibidas</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card sys-hotels-card">
        <div class="card-header">
            <div>
                <h3 class="card-title mb-0">Hoteles</h3>
                <div class="text-secondary small">
                    Alta, activación, accesos, QRs y soporte.
                </div>
            </div>
        </div>

        <div class="card-body border-bottom">
            <form method="GET" action="{{ route('sysapp.hotels.index') }}">
                <div class="row g-2 align-items-end">
                    <div class="col-12 col-md">
                        <label class="form-label">Buscar</label>
                        <input
                            class="form-control"
                            name="q"
                            value="{{ $currentSearch }}"
                            placeholder="Nombre, slug, correo o teléfono">
                    </div>

                    <div class="col-12 col-md-3">
                        <label class="form-label">Estado</label>
                        <select class="form-select" name="status">
                            <option value="">Todos</option>
                            <option value="active" @selected($currentStatus === 'active')>Activos</option>
                            <option value="paused" @selected($currentStatus === 'paused')>Pausados</option>
                            <option value="draft" @selected($currentStatus === 'draft')>Borradores</option>
                            <option value="disabled" @selected($currentStatus === 'disabled')>Desactivados</option>
                        </select>
                    </div>

                    <div class="col-12 col-md-auto">
                        <button class="btn btn-primary w-100" type="submit">
                            <i class="ti ti-search me-1"></i>
                            Filtrar
                        </button>
                    </div>

                    @if($currentSearch !== '' || $currentStatus !== '')
                        <div class="col-12 col-md-auto">
                            <a class="btn btn-outline-secondary w-100" href="{{ route('sysapp.hotels.index') }}">
                                Limpiar
                            </a>
                        </div>
                    @endif
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead>
                <tr>
                    <th>Hotel</th>
                    <th>Estado</th>
                    <th>Servicios</th>
                    <th class="text-center">QRs</th>
                    <th class="text-center">Solicitudes</th>
                    <th class="w-1">Acciones</th>
                </tr>
                </thead>

                <tbody>
                @forelse($hotels as $hotel)
                    @php
                        $statusData = $statusMeta($hotel->status);
                        $panel = $boolBadge($hotel->panel_enabled);
                        $publicQr = $boolBadge($hotel->public_requests_enabled);
                        $taxi = $boolBadge($hotel->taxi_enabled);
                    @endphp

                    <tr class="sys-hotel-row {{ $statusData['softClass'] }}">
                        <td>
                            <div class="d-flex align-items-stretch gap-3">
                                <span class="sys-hotel-row-marker"></span>

                                <span class="avatar bg-primary-lt text-primary mt-1">
                                    @if($hotel->logo_path)
                                        <img src="{{ asset('storage/' . $hotel->logo_path) }}" alt="{{ $hotel->name }}">
                                    @else
                                        <i class="ti ti-building"></i>
                                    @endif
                                </span>

                                <div class="min-width-0">
                                    <div class="fw-bold text-truncate">{{ $hotel->name }}</div>

                                    <div class="d-flex flex-wrap align-items-center gap-1 mt-1">
                                        <span class="sys-code">/h/{{ $hotel->slug }}</span>

                                        @if($hotel->email)
                                            <span class="text-secondary small">
                                                <i class="ti ti-mail me-1"></i>{{ $hotel->email }}
                                            </span>
                                        @endif

                                        @if($hotel->phone)
                                            <span class="text-secondary small">
                                                <i class="ti ti-phone me-1"></i>{{ $hotel->phone }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>

                        <td>
                            <span class="badge {{ $statusData['class'] }} rounded-pill">
                                <i class="ti {{ $statusData['icon'] }} me-1"></i>
                                {{ $statusData['label'] }}
                            </span>
                        </td>

                        <td>
                            <div class="d-flex flex-wrap gap-1">
                                <span class="sys-service-pill {{ $panel['class'] }}">
                                    <i class="ti {{ $panel['icon'] }}"></i>
                                    Panel
                                </span>

                                <span class="sys-service-pill {{ $publicQr['class'] }}">
                                    <i class="ti {{ $publicQr['icon'] }}"></i>
                                    QR
                                </span>

                                <span class="sys-service-pill {{ $taxi['class'] }}">
                                    <i class="ti {{ $taxi['icon'] }}"></i>
                                    Taxi
                                </span>
                            </div>
                        </td>

                        <td class="text-center">
                            <span class="badge bg-blue-lt text-blue rounded-pill">
                                <i class="ti ti-qrcode me-1"></i>
                                {{ $hotel->qr_points_count }}
                            </span>
                        </td>

                        <td class="text-center">
                            <span class="badge bg-purple-lt text-purple rounded-pill">
                                <i class="ti ti-inbox me-1"></i>
                                {{ $hotel->requests_count }}
                            </span>
                        </td>

                        <td>
                            <div class="sys-actions">
                                <a class="btn btn-outline-secondary btn-sm"
                                   href="{{ route('hotel.login', $hotel) }}"
                                   target="_blank"
                                   title="Abrir panel del hotel">
                                    <i class="ti ti-external-link me-1"></i>
                                    Panel
                                </a>

                                <a class="btn btn-outline-primary btn-sm"
                                   href="{{ route('sysapp.hotels.print-access', $hotel) }}"
                                   target="_blank"
                                   title="Imprimir acceso del hotel">
                                    <i class="ti ti-id-badge-2 me-1"></i>
                                    Acceso
                                </a>

                                <a class="btn btn-outline-secondary btn-sm"
                                   href="{{ route('sysapp.hotels.edit', $hotel) }}">
                                    <i class="ti ti-edit me-1"></i>
                                    Editar
                                </a>

                                <a class="btn btn-primary btn-sm"
                                   href="{{ route('sysapp.hotels.qr-points.index', $hotel) }}">
                                    <i class="ti ti-qrcode me-1"></i>
                                    QRs
                                </a>

                                <a class="btn btn-success btn-sm"
                                   href="{{ route('sysapp.hotels.qr-points.print-all', $hotel) }}"
                                   target="_blank">
                                    <i class="ti ti-printer me-1"></i>
                                    Imprimir
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty">
                                <div class="empty-icon">
                                    <i class="ti ti-building"></i>
                                </div>
                                <p class="empty-title">No hay hoteles registrados.</p>
                                <p class="empty-subtitle text-secondary">
                                    Crea el primer hotel para comenzar a generar QRs.
                                </p>
                                <div class="empty-action">
                                    <a class="btn btn-primary" href="{{ route('sysapp.hotels.create') }}">
                                        <i class="ti ti-plus me-1"></i>
                                        Crear hotel
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if($hotels->hasPages())
            <div class="card-footer">
                {{ $hotels->links() }}
            </div>
        @endif
    </div>
@endsection