@extends('layouts.hotel-panel')

@section('title', $hotel->name . ' · Historial')
@section('subtitle', 'Solicitudes resueltas')

@section('topbar-actions')
    @include('hotel-panel.partials.topbar-actions', ['showSound' => false])
@endsection

@section('content')
    @php
        $formatDateTime = function ($date) {
            return $date ? $date->format('d/m/Y H:i') : '—';
        };

        $formatClock = function ($date) {
            return $date ? $date->format('H:i') : '—';
        };

        $formatDuration = function ($start, $end) {
            if (! $start || ! $end) {
                return '—';
            }

            $seconds = (int) round($start->diffInSeconds($end));

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
    @endphp

    <div class="page-header d-print-none mb-3">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title mb-1">Historial</h2>
                <div class="text-secondary">
                    Solicitudes resueltas o canceladas.
                </div>
            </div>
        </div>
    </div>

    <form method="GET" class="card mb-3">
        <div class="card-body">
            <div class="row g-2 align-items-end">
                <div class="col-6 col-md-2">
                    <label class="form-label">Rango</label>
                    <select class="form-select" name="range">
                        <option value="12h" @selected($range === '12h')>Últimas 12 h</option>
                        <option value="24h" @selected($range === '24h')>Últimas 24 h</option>
                        <option value="today" @selected($range === 'today')>Hoy</option>
                        <option value="yesterday" @selected($range === 'yesterday')>Ayer</option>
                        <option value="custom" @selected($range === 'custom')>Rango</option>
                    </select>
                </div>

                <div class="col-6 col-md-2">
                    <label class="form-label">Estado</label>
                    <select class="form-select" name="status">
                        <option value="">Todos</option>
                        <option value="completed" @selected(request('status') === 'completed')>Resueltas</option>
                        <option value="canceled" @selected(request('status') === 'canceled')>Canceladas</option>
                    </select>
                </div>

                <div class="col-6 col-md-2">
                    <label class="form-label">Tipo</label>
                    <select class="form-select" name="type_key">
                        <option value="">Todos</option>
                        @foreach($types as $key => $type)
                            <option value="{{ $key }}" @selected(request('type_key') === $key)>
                                {{ $type['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label">Buscar</label>
                    <input
                        class="form-control"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Habitación, nota, nombre...">
                </div>

                <div class="col-12 col-md-2">
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary flex-fill" type="submit">
                            <i class="ti ti-filter me-1"></i>
                            Filtrar
                        </button>

                        <a class="btn btn-outline-secondary" href="{{ route('hotel.requests.history', $hotel) }}">
                            Limpiar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="row row-cards">
        @forelse($requests as $item)
            @php
                $endAt = $item->completed_at ?: $item->canceled_at ?: $item->updated_at;
                $isCompleted = $item->status === 'completed';
                $statusClass = $isCompleted ? 'bg-green-lt text-green' : 'bg-red-lt text-red';
                $statusIcon = $isCompleted ? 'ti-circle-check' : 'ti-circle-x';
                $endLabel = $isCompleted ? 'Resuelta' : 'Cancelada';
            @endphp

            <div class="col-12 col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between gap-3">
                            <div class="min-width-0">
                                <div class="fw-bold fs-3 text-truncate">
                                    <i class="ti {{ $statusIcon }} me-1"></i>
                                    {{ $item->point_label }} — {{ $item->typeLabel() }}
                                </div>

                                <div class="text-secondary small mt-1">
                                    Pedido: {{ $formatDateTime($item->created_at) }}
                                    @if($endAt)
                                        · {{ $endLabel }}: {{ $formatClock($endAt) }}
                                    @endif
                                </div>
                            </div>

                            <span class="badge {{ $statusClass }}">
                                {{ $item->statusLabel() }}
                            </span>
                        </div>

                        <div class="bg-secondary-lt rounded-3 p-3 mt-3">
                            {{ $item->note ?: 'Sin nota adicional.' }}
                        </div>

                        <div class="d-flex flex-wrap gap-2 mt-3 text-secondary small">
                            <span>
                                <i class="ti ti-clock me-1"></i>
                                Tiempo total: {{ $formatDuration($item->created_at, $endAt) }}
                            </span>

                            @if($item->guest_name)
                                <span>
                                    <i class="ti ti-user me-1"></i>
                                    Huésped: {{ $item->guest_name }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="empty">
                    <div class="empty-icon">
                        <i class="ti ti-history"></i>
                    </div>
                    <p class="empty-title">No hay solicitudes en este rango.</p>
                    <p class="empty-subtitle text-secondary">
                        Cambia los filtros o revisa otro periodo.
                    </p>
                </div>
            </div>
        @endforelse
    </div>

    <div class="mt-3">
        {{ $requests->links() }}
    </div>
@endsection