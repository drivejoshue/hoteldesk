@extends('layouts.hotel-panel')

@section('title', $hotel->name . ' · Mis QRs')
@section('subtitle', 'Códigos QR activos del hotel')

@section('topbar-actions')
    @include('hotel-panel.partials.topbar-actions', ['showSound' => false])
@endsection

@section('content')
    @php
        $typeLabels = [
            'room' => 'Habitación',
            'lobby' => 'Lobby',
            'area' => 'Área',
            'restaurant' => 'Restaurante',
            'parking' => 'Estacionamiento',
            'reception' => 'Recepción',
            'other' => 'Otro',
        ];

        $modeLabels = [
            'menu' => 'Menú completo',
            'limited' => 'Menú limitado',
            'direct' => 'Solicitud directa',
        ];

        $typeIcons = [
            'room' => 'ti-bed',
            'lobby' => 'ti-building',
            'area' => 'ti-map-pin',
            'restaurant' => 'ti-tools-kitchen-2',
            'parking' => 'ti-parking',
            'reception' => 'ti-desk',
            'other' => 'ti-map-2',
        ];
    @endphp

    <div class="page-header d-print-none mb-3">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title mb-1">Mis códigos QR</h2>
                <div class="text-secondary">
                    Consulta, abre o reimprime los QRs configurados para tu hotel.
                </div>
            </div>

            <div class="col-auto d-flex gap-2">
                <a class="btn btn-outline-secondary" href="{{ route('hotel.dashboard', $hotel) }}">
                    <i class="ti ti-arrow-left me-1"></i>
                    Inicio
                </a>

                <a class="btn btn-primary" href="{{ route('hotel.qr-requests.create', $hotel) }}">
                    <i class="ti ti-plus me-1"></i>
                    Solicitar QR
                </a>
            </div>
        </div>
    </div>

    <div class="row row-cards">
        @forelse($points as $point)
            @php
                $typeLabel = $typeLabels[$point->type] ?? 'Otro';
                $modeLabel = $modeLabels[$point->mode] ?? $point->mode;
                $typeIcon = $typeIcons[$point->type] ?? 'ti-map-2';
            @endphp

            <div class="col-12 col-md-6 col-xl-4">
                <div class="card h-100">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex align-items-start justify-content-between gap-3">
                            <div class="min-width-0">
                                <div class="fw-bold fs-3 text-truncate">
                                    <i class="ti ti-qrcode me-1"></i>
                                    {{ $point->label }}
                                </div>

                                <div class="text-secondary small mt-1">
                                    <i class="ti {{ $typeIcon }} me-1"></i>
                                    {{ $typeLabel }}

                                    @if($point->floor)
                                        · {{ $point->floor }}
                                    @endif

                                    · {{ $modeLabel }}
                                </div>
                            </div>

                            @if($point->active)
                                <span class="badge bg-green-lt text-green">
                                    <i class="ti ti-check me-1"></i>
                                    Activo
                                </span>
                            @else
                                <span class="badge bg-secondary-lt text-secondary">
                                    <i class="ti ti-ban me-1"></i>
                                    Inactivo
                                </span>
                            @endif
                        </div>

                        <div class="bg-secondary-lt rounded-3 p-3 mt-3">
                            <div class="fw-bold mb-1">
                                Código: {{ $point->public_code }}
                            </div>

                            <div class="text-secondary small">
                                Este QR puede reimprimirse cuando se dañe, se pierda o se necesite colocar una copia.
                            </div>
                        </div>

                        <div class="mt-auto pt-3">
                            @if($point->active)
                                <div class="d-flex flex-wrap gap-2">
                                    <a class="btn btn-outline-secondary btn-sm"
                                       href="{{ route('public.qr.show', $point->public_code) }}"
                                       target="_blank"
                                       rel="noopener">
                                        <i class="ti ti-external-link me-1"></i>
                                        Abrir
                                    </a>

                                    <a class="btn btn-primary btn-sm"
                                       href="{{ route('hotel.qr-points.print', [$hotel, $point]) }}"
                                       target="_blank">
                                        <i class="ti ti-printer me-1"></i>
                                        Imprimir
                                    </a>
                                </div>
                            @else
                                <button class="btn btn-outline-secondary btn-sm" type="button" disabled>
                                    <i class="ti ti-lock me-1"></i>
                                    QR inactivo
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="empty">
                    <div class="empty-icon">
                        <i class="ti ti-qrcode"></i>
                    </div>

                    <p class="empty-title">
                        Aún no hay QRs configurados.
                    </p>

                    <p class="empty-subtitle text-secondary">
                        Solicita el primer QR para una habitación, área común o recepción.
                    </p>

                    <div class="empty-action">
                        <a class="btn btn-primary" href="{{ route('hotel.qr-requests.create', $hotel) }}">
                            <i class="ti ti-plus me-1"></i>
                            Solicitar primer QR
                        </a>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    <div class="mt-3">
        {{ $points->links() }}
    </div>
@endsection