@extends('sysapp.layout')

@section('title', 'Reset PIN · HotelDesk Lite')

@section('content')
    <div class="page-head">
        <div>
            <h1>Reset de PIN</h1>
            <div class="muted">Solicitudes de hoteles para restablecer el PIN de recepción.</div>
        </div>
    </div>

    @if(session('temporary_pin'))
        <div class="alert alert-success">
            PIN temporal generado para <strong>{{ session('temporary_pin_hotel') }}</strong>:
            <strong style="font-size: 22px;">{{ session('temporary_pin') }}</strong>
            <br>
            Entrega este PIN al responsable del hotel. No se volverá a mostrar.
        </div>
    @endif

    <div class="card">
        <table class="table">
            <thead>
            <tr>
                <th>Hotel</th>
                <th>Solicitante</th>
                <th>Nota</th>
                <th>Estado</th>
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
                        <strong>{{ $item->requester_name ?: '—' }}</strong><br>
                        <span class="muted">{{ $item->requester_phone ?: 'Sin teléfono' }}</span>
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
                        @php
                            $badgeClass = match($item->status) {
                                'completed' => 'badge-active',
                                'rejected' => 'badge-disabled',
                                'canceled' => 'badge-paused',
                                default => 'badge-draft',
                            };
                        @endphp

                        <span class="badge {{ $badgeClass }}">
                            {{ $item->statusLabel() }}
                        </span>
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
                                <form method="POST" action="{{ route('sysapp.pin-reset-requests.complete', $item) }}"
                                      onsubmit="return confirm('Esto generará un nuevo PIN temporal. ¿Continuar?');">
                                    @csrf
                                    <button class="btn btn-success" type="submit">
                                        Generar PIN
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('sysapp.pin-reset-requests.reject', $item) }}"
                                      onsubmit="return confirm('¿Rechazar esta solicitud?');">
                                    @csrf
                                    <input type="hidden" name="reject_reason" value="Solicitud no autorizada por SysApp.">
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
                    <td colspan="6">No hay solicitudes de reset de PIN.</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        <div style="margin-top: 14px;">
            {{ $requests->links() }}
        </div>
    </div>
@endsection