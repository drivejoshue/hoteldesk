@extends('layouts.hotel-panel')

@section('title', $hotel->name . ' · Mis QRs')
@section('subtitle', 'Códigos QR activos del hotel')

@section('topbar-actions')
    @include('hotel-panel.partials.topbar-actions', ['showSound' => false])
@endsection

@section('content')
    <main class="hd-dashboard">
        <div class="hd-toolbar">
            <div>
                <h2 class="hd-toolbar-title">Mis códigos QR</h2>
                <div class="hd-text-muted" style="font-size: 13px; font-weight: 750;">
                    Consulta, abre o reimprime los QRs configurados para tu hotel.
                </div>
            </div>

            <div class="hd-toolbar-actions">
                <a class="hd-btn hd-btn-soft" href="{{ route('hotel.dashboard', $hotel) }}">
                    <i class="ti ti-arrow-left"></i>
                    Volver al inicio
                </a>

                <a class="hd-btn hd-btn-primary" href="{{ route('hotel.qr-requests.create', $hotel) }}">
                    <i class="ti ti-plus"></i>
                    Solicitar QR
                </a>
            </div>
        </div>

        <div class="hd-requests-grid">
            @forelse($points as $point)
                @php
                    $typeLabel = match($point->type) {
                        'room' => 'Habitación',
                        'lobby' => 'Lobby',
                        'area' => 'Área',
                        'restaurant' => 'Restaurante',
                        'parking' => 'Estacionamiento',
                        'reception' => 'Recepción',
                        default => 'Otro',
                    };

                    $modeLabel = match($point->mode) {
                        'menu' => 'Menú completo',
                        'limited' => 'Menú limitado',
                        'direct' => 'Solicitud directa',
                        default => $point->mode,
                    };
                @endphp

                <article class="hd-request-card">
                    <div class="hd-request-head">
                        <div>
                            <div class="hd-request-title">
                                <i class="ti ti-qrcode"></i>
                                {{ $point->label }}
                            </div>

                            <div class="hd-request-meta">
                                {{ $typeLabel }}
                                @if($point->floor)
                                    · {{ $point->floor }}
                                @endif
                                · {{ $modeLabel }}
                            </div>
                        </div>

                        @if($point->active)
                            <span class="hd-status-pill hd-status-progress">
                                <i class="ti ti-check"></i>
                                Activo
                            </span>
                        @else
                            <span class="hd-status-pill hd-status-pending">
                                <i class="ti ti-ban"></i>
                                Inactivo
                            </span>
                        @endif
                    </div>

                    <div class="hd-note">
                        <div style="font-weight: 900; margin-bottom: 4px;">
                            Código: {{ $point->public_code }}
                        </div>

                        Este QR puede reimprimirse cuando se dañe, se pierda o se necesite colocar una copia.
                    </div>

                    <div class="hd-card-actions">
                        @if($point->active)
                            <a class="hd-btn hd-btn-soft"
                               href="{{ route('public.qr.show', $point->public_code) }}"
                               target="_blank">
                                <i class="ti ti-external-link"></i>
                                Abrir
                            </a>

                            <a class="hd-btn hd-btn-primary"
                               href="{{ route('hotel.qr-points.print', [$hotel, $point]) }}"
                               target="_blank">
                                <i class="ti ti-printer"></i>
                                Imprimir
                            </a>
                        @else
                            <button class="hd-btn hd-btn-soft" type="button" disabled>
                                <i class="ti ti-lock"></i>
                                QR inactivo
                            </button>
                        @endif
                    </div>
                </article>
            @empty
                <div class="hd-empty">
                    <i class="ti ti-qrcode" style="font-size: 34px;"></i>
                    <div style="margin-top: 8px;">Aún no hay QRs configurados.</div>

                    <a class="hd-btn hd-btn-primary" href="{{ route('hotel.qr-requests.create', $hotel) }}" style="margin-top: 16px;">
                        <i class="ti ti-plus"></i>
                        Solicitar primer QR
                    </a>
                </div>
            @endforelse
        </div>

        <div style="margin-top: 18px;">
            {{ $points->links() }}
        </div>
    </main>
@endsection