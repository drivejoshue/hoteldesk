@extends('sysapp.layout')

@section('title', 'Hoteles · HotelDesk Lite')

@section('content')
    @php
        $statusMeta = function ($status) {
            return match ($status) {
                'active' => ['label' => 'Activo', 'class' => 'bg-green-lt text-green', 'icon' => 'ti-circle-check'],
                'paused' => ['label' => 'Pausado', 'class' => 'bg-yellow-lt text-yellow', 'icon' => 'ti-player-pause'],
                'disabled' => ['label' => 'Desactivado', 'class' => 'bg-red-lt text-red', 'icon' => 'ti-ban'],
                default => ['label' => 'Borrador', 'class' => 'bg-secondary-lt text-secondary', 'icon' => 'ti-pencil'],
            };
        };

        $boolBadge = function ($enabled) {
            return $enabled
                ? ['label' => 'Activo', 'class' => 'bg-green-lt text-green']
                : ['label' => 'Inactivo', 'class' => 'bg-secondary-lt text-secondary'];
        };
    @endphp

    <div class="page-header d-print-none mb-3">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title mb-1">Hoteles</h2>
                <div class="text-secondary">
                    Alta, activación y configuración de hoteles.
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

    <div class="card">
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead>
                <tr>
                    <th>Hotel</th>
                    <th>Estado</th>
                    <th>Panel</th>
                    <th>QR público</th>
                    <th>Taxi</th>
                    <th class="text-center">QRs</th>
                    <th class="text-center">Solicitudes</th>
                    <th class="w-1">Acciones</th>
                </tr>
                </thead>

                <tbody>
                @forelse($hotels as $hotel)
                    @php
                        $status = $statusMeta($hotel->status);
                        $panel = $boolBadge($hotel->panel_enabled);
                        $publicQr = $boolBadge($hotel->public_requests_enabled);
                        $taxi = $boolBadge($hotel->taxi_enabled);
                    @endphp

                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <span class="avatar bg-primary-lt text-primary">
                                    <i class="ti ti-building"></i>
                                </span>

                                <div>
                                    <div class="fw-bold">{{ $hotel->name }}</div>
                                    <div class="text-secondary small">
                                        /h/{{ $hotel->slug }}
                                    </div>
                                </div>
                            </div>
                        </td>

                        <td>
                            <span class="badge {{ $status['class'] }}">
                                <i class="ti {{ $status['icon'] }} me-1"></i>
                                {{ $status['label'] }}
                            </span>
                        </td>

                        <td>
                            <span class="badge {{ $panel['class'] }}">{{ $panel['label'] }}</span>
                        </td>

                        <td>
                            <span class="badge {{ $publicQr['class'] }}">{{ $publicQr['label'] }}</span>
                        </td>

                        <td>
                            <span class="badge {{ $taxi['class'] }}">{{ $taxi['label'] }}</span>
                        </td>

                        <td class="text-center">
                            <span class="badge bg-blue-lt text-blue">
                                {{ $hotel->qr_points_count }}
                            </span>
                        </td>

                        <td class="text-center">
                            <span class="badge bg-purple-lt text-purple">
                                {{ $hotel->requests_count }}
                            </span>
                        </td>

                        <td>
                          <div class="btn-list flex-nowrap">
    <a class="btn btn-outline-secondary btn-sm"
       href="{{ route('hotel.login', $hotel) }}"
       target="_blank">
        Panel
    </a>

    <a class="btn btn-outline-primary btn-sm"
       href="{{ route('sysapp.hotels.print-access', $hotel) }}"
       target="_blank">
        Acceso
    </a>

    <a class="btn btn-outline-secondary btn-sm"
       href="{{ route('sysapp.hotels.edit', $hotel) }}">
        Editar
    </a>

    <a class="btn btn-primary btn-sm"
       href="{{ route('sysapp.hotels.qr-points.index', $hotel) }}">
        QRs
    </a>

    <a class="btn btn-success btn-sm"
       href="{{ route('sysapp.hotels.qr-points.print-all', $hotel) }}"
       target="_blank">
        Imprimir
    </a>
</div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">
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