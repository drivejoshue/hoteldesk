@extends('layouts.hotel-panel')

@section('title', $hotel->name . ' · Reportes')
@section('subtitle', 'Resumen operativo')

@section('topbar-actions')
    @include('hotel-panel.partials.topbar-actions', ['showSound' => false])
@endsection

@section('content')
    @php
        $formatDurationFromSeconds = function ($seconds) {
            if (! $seconds) {
                return '—';
            }

            $seconds = (int) round($seconds);

            if ($seconds < 60) {
                return $seconds . ' seg';
            }

            $minutes = intdiv($seconds, 60);

            if ($minutes < 60) {
                return $minutes . ' min';
            }

            $hours = intdiv($minutes, 60);
            $remainingMinutes = $minutes % 60;

            return $remainingMinutes > 0
                ? $hours . ' h ' . $remainingMinutes . ' min'
                : $hours . ' h';
        };

        $completed = (int) ($byStatus['completed'] ?? 0);
        $canceled = (int) ($byStatus['canceled'] ?? 0);
        $pending = (int) ($byStatus['pending'] ?? 0);
        $inProgress = (int) ($byStatus['in_progress'] ?? 0);
    @endphp

    <div class="page-header d-print-none mb-3">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title mb-1">Reporte operativo</h2>
                <div class="text-secondary">
                    Resumen de solicitudes del periodo seleccionado.
                </div>
            </div>
        </div>
    </div>

    <form method="GET" class="card mb-3">
        <div class="card-body">
            <div class="row g-2 align-items-end">
                <div class="col-6 col-md-3">
                    <label class="form-label">Periodo</label>
                    <select class="form-select" name="range">
                        <option value="12h" @selected($range === '12h')>Últimas 12 h</option>
                        <option value="24h" @selected($range === '24h')>Últimas 24 h</option>
                        <option value="today" @selected($range === 'today')>Hoy</option>
                        <option value="yesterday" @selected($range === 'yesterday')>Ayer</option>
                        <option value="custom" @selected($range === 'custom')>Rango</option>
                    </select>
                </div>

                <div class="col-6 col-md-2">
                    <label class="form-label">Desde</label>
                    <input
                        class="form-control"
                        type="date"
                        name="from"
                        value="{{ request('from') }}">
                </div>

                <div class="col-6 col-md-2">
                    <label class="form-label">Hasta</label>
                    <input
                        class="form-control"
                        type="date"
                        name="to"
                        value="{{ request('to') }}">
                </div>

                <div class="col-6 col-md-3">
                    <label class="form-label">Resumen</label>
                    <div class="form-control-plaintext text-secondary">
                        {{ $from?->format('d/m/Y H:i') }} → {{ $to?->format('d/m/Y H:i') }}
                    </div>
                </div>

                <div class="col-12 col-md-2">
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary flex-fill" type="submit">
                            <i class="ti ti-filter me-1"></i>
                            Aplicar
                        </button>

                        <a class="btn btn-outline-secondary" href="{{ route('hotel.reports.index', $hotel) }}">
                            Limpiar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="row row-cards mb-3">
        <div class="col-6 col-md-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <span class="bg-primary-lt text-primary avatar me-3">
                            <i class="ti ti-list"></i>
                        </span>
                        <div>
                            <div class="text-secondary">Total</div>
                            <div class="h2 mb-0">{{ $total }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <span class="bg-yellow-lt text-yellow avatar me-3">
                            <i class="ti ti-clock"></i>
                        </span>
                        <div>
                            <div class="text-secondary">Pendientes</div>
                            <div class="h2 mb-0">{{ $pending }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <span class="bg-green-lt text-green avatar me-3">
                            <i class="ti ti-circle-check"></i>
                        </span>
                        <div>
                            <div class="text-secondary">Resueltas</div>
                            <div class="h2 mb-0">{{ $completed }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <span class="bg-red-lt text-red avatar me-3">
                            <i class="ti ti-circle-x"></i>
                        </span>
                        <div>
                            <div class="text-secondary">Canceladas</div>
                            <div class="h2 mb-0">{{ $canceled }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-cards">
        <div class="col-12 col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Solicitudes por tipo</h3>
                </div>

                <div class="list-group list-group-flush">
                    @forelse($byType as $typeKey => $count)
                        <div class="list-group-item">
                            <div class="row align-items-center">
                                <div class="col text-truncate">
                                    <span class="fw-bold">
                                        {{ $types[$typeKey]['label'] ?? $typeKey }}
                                    </span>
                                </div>
                                <div class="col-auto">
                                    <span class="badge bg-primary-lt text-primary">
                                        {{ $count }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="list-group-item text-secondary">
                            Sin datos en este periodo.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Solicitudes por punto</h3>
                </div>

                <div class="list-group list-group-flush">
                    @forelse($byPoint as $point)
                        <div class="list-group-item">
                            <div class="row align-items-center">
                                <div class="col text-truncate">
                                    <span class="fw-bold">
                                        {{ $point->point_label }}
                                    </span>
                                </div>
                                <div class="col-auto">
                                    <span class="badge bg-primary-lt text-primary">
                                        {{ $point->total }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="list-group-item text-secondary">
                            Sin datos en este periodo.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="card-title mb-1">Tiempo promedio de cierre</h3>
                            <div class="text-secondary">
                                Promedio calculado desde que el huésped genera la solicitud hasta que recepción la resuelve o cancela.
                            </div>
                        </div>

                        <div class="col-auto">
                            <div class="h1 mb-0">
                                {{ $formatDurationFromSeconds($avgResolveSeconds) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($inProgress > 0)
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="ti ti-info-circle me-2"></i>
                    Hay {{ $inProgress }} solicitud(es) en proceso dentro de este periodo.
                </div>
            </div>
        @endif
    </div>
@endsection