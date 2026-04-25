@extends('layouts.hotel-panel')

@section('title', $hotel->name . ' · Solicitudes de QR')
@section('subtitle', 'Solicitudes de nuevos códigos QR')

@section('topbar-actions')
    @include('hotel-panel.partials.topbar-actions', ['showSound' => false])
@endsection

@section('content')
    <main class="hd-dashboard">
        @if(session('success'))
            <div class="hd-alert hd-alert-success">{{ session('success') }}</div>
        @endif

        <div class="hd-toolbar">
            <div>
                <h2 class="hd-toolbar-title">Solicitudes de QR</h2>
                <div class="hd-text-muted" style="font-size: 13px; font-weight: 750;">
                    Aquí puedes revisar las solicitudes enviadas a SysApp.
                </div>
            </div>

            <a class="hd-btn hd-btn-primary" href="{{ route('hotel.qr-requests.create', $hotel) }}">
                <i class="ti ti-plus"></i>
                Nueva solicitud
            </a>
        </div>

        <div class="hd-requests-grid">
            @forelse($requests as $item)
                <article class="hd-request-card">
                    <div class="hd-request-head">
                        <div>
                            <div class="hd-request-title">
                                <i class="ti ti-qrcode"></i>
                                {{ $item->label }}
                            </div>
                            <div class="hd-request-meta">
                                Tipo: {{ $item->type }} · Modo: {{ $item->mode }}
                            </div>
                        </div>

                        <span class="hd-status-pill {{ $item->status === 'approved' ? 'hd-status-progress' : 'hd-status-pending' }}">
                            {{ $item->statusLabel() }}
                        </span>
                    </div>

                    <div class="hd-note">
                        {{ $item->note ?: 'Sin nota adicional.' }}
                    </div>

                    @if($item->createdQrPoint)
                        <div class="hd-card-actions">
                            <a class="hd-btn hd-btn-primary" href="{{ route('hotel.qr-points.print', [$hotel, $item->createdQrPoint]) }}" target="_blank">
                                <i class="ti ti-printer"></i>
                                Imprimir QR aprobado
                            </a>
                        </div>
                    @endif
                </article>
            @empty
                <div class="hd-empty">
                    <i class="ti ti-plus" style="font-size: 34px;"></i>
                    <div style="margin-top: 8px;">No hay solicitudes de QR.</div>
                </div>
            @endforelse
        </div>

        <div style="margin-top: 18px;">
            {{ $requests->links() }}
        </div>
    </main>
@endsection