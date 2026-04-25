@extends('sysapp.layout')

@section('title', 'Solicitudes QR · HotelDesk Lite')

@section('content')
    <div class="page-head">
        <div>
            <h1>Solicitudes de QR</h1>
            <div class="muted">Revisa, aprueba o rechaza nuevos puntos QR solicitados por hoteles.</div>
        </div>
    </div>

    @if(session('temporary_pin'))
        <div class="alert alert-success">
            PIN temporal generado para <strong>{{ session('temporary_pin_hotel') }}</strong>:
            <strong style="font-size: 20px;">{{ session('temporary_pin') }}</strong>
            <br>
            Entrega este PIN al hotel. No se volverá a mostrar.
        </div>
    @endif

    <div class="card">
        <table class="table">
            <thead>
            <tr>
                <th>Hotel</th>
                <th>Punto solicitado</th>
                <th>Configuración</th>
                <th>Estado</th>
                <th>Nota</th>
                <th>Revisión</th>
                <th>Acciones</th>
            </tr>
            </thead>

            <tbody>
            @forelse($requests as $item)
                <tr>
                    <td>
                        <strong>{{ $item->hotel?->name ?? 'Hotel eliminado' }}</strong><br>
                        <span class="muted">{{ $item->created_at?->format('d/m/Y H:i') }}</span>
                    </td>

                    <td>
                        <strong>{{ $item->label }}</strong><br>
                        <span class="muted">Tipo: {{ $item->type }} · Piso/zona: {{ $item->floor ?: '—' }}</span>
                    </td>

                    <td>
                        Modo: <strong>{{ $item->mode }}</strong><br>

                        @if($item->mode === 'direct')
                            <span class="muted">Directa: {{ $item->fixed_request_type }}</span>
                        @elseif($item->mode === 'limited')
                            <span class="muted">
                                Permitidas:
                                {{ implode(', ', $item->allowed_request_types ?: []) }}
                            </span>
                        @else
                            <span class="muted">Menú completo</span>
                        @endif
                    </td>

                    <td>
                        @php
                            $badgeClass = match($item->status) {
                                'approved' => 'badge-active',
                                'rejected' => 'badge-disabled',
                                'canceled' => 'badge-paused',
                                default => 'badge-draft',
                            };
                        @endphp

                        <span class="badge {{ $badgeClass }}">
                            {{ $item->statusLabel() }}
                        </span>

                        @if($item->createdQrPoint)
                            <br>
                            <a href="{{ route('sysapp.hotels.qr-points.print', [$item->hotel, $item->createdQrPoint]) }}" target="_blank">
                                Imprimir QR
                            </a>
                        @endif
                    </td>

                    <td>
                        {{ $item->note ?: '—' }}

                        @if($item->reject_reason)
                            <br>
                            <span style="color:#b42318;">
                                Rechazo: {{ $item->reject_reason }}
                            </span>
                        @endif
                    </td>

                    <td>
                        @if($item->reviewed_at)
                            {{ $item->reviewed_at->format('d/m/Y H:i') }}
                        @else
                            <span class="muted">Sin revisar</span>
                        @endif
                    </td>

                    <td>
                        @if($item->status === 'pending')
                            <div class="actions">
                                <form method="POST" action="{{ route('sysapp.qr-requests.approve', $item) }}">
                                    @csrf
                                    <button class="btn btn-success" type="submit">
                                        Aprobar
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('sysapp.qr-requests.reject', $item) }}"
                                      onsubmit="return confirm('¿Rechazar esta solicitud?');">
                                    @csrf
                                    <input type="hidden" name="reject_reason" value="No autorizado por SysApp.">
                                    <button class="btn btn-danger" type="submit">
                                        Rechazar
                                    </button>
                                </form>
                            </div>
                        @else
                            <span class="muted">Revisada</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">No hay solicitudes de QR.</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        <div style="margin-top: 14px;">
            {{ $requests->links() }}
        </div>
    </div>
@endsection