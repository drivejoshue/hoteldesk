@extends('layouts.hotel-panel')

@section('title', $hotel->name . ' · Solicitudes de QR')
@section('subtitle', 'Solicitudes de nuevos códigos QR')

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

        $statusMeta = function ($status) {
            return match ($status) {
                'approved' => ['class' => 'bg-green-lt text-green', 'icon' => 'ti-circle-check'],
                'rejected' => ['class' => 'bg-red-lt text-red', 'icon' => 'ti-circle-x'],
                'canceled' => ['class' => 'bg-secondary-lt text-secondary', 'icon' => 'ti-ban'],
                default => ['class' => 'bg-yellow-lt text-yellow', 'icon' => 'ti-clock'],
            };
        };
    @endphp

    <div class="page-header d-print-none mb-3">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title mb-1">Solicitudes de QR</h2>
                <div class="text-secondary">
                    Revisa las solicitudes enviadas a SysApp y los QRs aprobados.
                </div>
            </div>

            <div class="col-auto">
                <a class="btn btn-primary" href="{{ route('hotel.qr-requests.create', $hotel) }}">
                    <i class="ti ti-plus me-1"></i>
                    Nueva solicitud
                </a>
            </div>
        </div>
    </div>

    <div class="row row-cards">
        @forelse($requests as $item)
            @php
                $meta = $statusMeta($item->status);
            @endphp

            <div class="col-12 col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between gap-3">
                            <div class="min-width-0">
                                <div class="fw-bold fs-3 text-truncate">
                                    <i class="ti ti-qrcode me-1"></i>
                                    {{ $item->label }}
                                </div>

                                <div class="text-secondary small mt-1">
                                    Tipo: {{ $typeLabels[$item->type] ?? $item->type }}
                                    · Modo: {{ $modeLabels[$item->mode] ?? $item->mode }}
                                    @if($item->floor)
                                        · Zona: {{ $item->floor }}
                                    @endif
                                </div>
                            </div>

                            <span class="badge {{ $meta['class'] }}">
                                <i class="ti {{ $meta['icon'] }} me-1"></i>
                                {{ $item->statusLabel() }}
                            </span>
                        </div>

                        <div class="bg-secondary-lt rounded-3 p-3 mt-3">
                            {{ $item->note ?: 'Sin nota adicional.' }}
                        </div>

                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mt-3">
                            <div class="text-secondary small">
                                Enviada: {{ $item->created_at?->format('d/m/Y H:i') ?? '—' }}

                                @if($item->reviewed_at)
                                    · Revisada: {{ $item->reviewed_at->format('d/m/Y H:i') }}
                                @endif
                            </div>

                            @if($item->createdQrPoint)
                                <a class="btn btn-primary btn-sm"
                                   href="{{ route('hotel.qr-points.print', [$hotel, $item->createdQrPoint]) }}"
                                   target="_blank">
                                    <i class="ti ti-printer me-1"></i>
                                    Imprimir QR
                                </a>
                            @endif
                        </div>

                        @if($item->reject_reason)
                            <div class="alert alert-danger mt-3 mb-0">
                                <i class="ti ti-alert-circle me-1"></i>
                                {{ $item->reject_reason }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="empty">
                    <div class="empty-icon">
                        <i class="ti ti-qrcode"></i>
                    </div>
                    <p class="empty-title">No hay solicitudes de QR.</p>
                    <p class="empty-subtitle text-secondary">
                        Puedes solicitar un nuevo QR para habitación, lobby, área común o recepción.
                    </p>
                    <div class="empty-action">
                        <a class="btn btn-primary" href="{{ route('hotel.qr-requests.create', $hotel) }}">
                            <i class="ti ti-plus me-1"></i>
                            Nueva solicitud
                        </a>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    <div class="mt-3">
        {{ $requests->links() }}
    </div>
@endsection