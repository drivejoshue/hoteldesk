@extends('sysapp.layout')

@section('title', 'Reset PIN · HotelDesk Lite')

@section('content')
    @php
        $statusMeta = function ($status) {
            return match ($status) {
                'completed' => ['class' => 'bg-green-lt text-green', 'icon' => 'ti-circle-check'],
                'rejected' => ['class' => 'bg-red-lt text-red', 'icon' => 'ti-circle-x'],
                'canceled' => ['class' => 'bg-yellow-lt text-yellow', 'icon' => 'ti-ban'],
                default => ['class' => 'bg-secondary-lt text-secondary', 'icon' => 'ti-clock'],
            };
        };
    @endphp

    <div class="page-header d-print-none mb-3">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title mb-1">Reset de PIN</h2>
                <div class="text-secondary">
                    Solicitudes de hoteles para restablecer el PIN de recepción.
                </div>
            </div>
        </div>
    </div>

    @if(session('temporary_pin'))
        <div class="alert alert-success">
            <div class="d-flex">
                <div>
                    <i class="ti ti-key me-2"></i>
                </div>
                <div>
                    <strong>PIN temporal generado para {{ session('temporary_pin_hotel') }}:</strong>
                    <span class="badge bg-green text-white ms-2" style="font-size: 18px;">
                        {{ session('temporary_pin') }}
                    </span>
                    <div class="mt-1">
                        Entrega este PIN al responsable del hotel. No se volverá a mostrar.
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Solicitudes recibidas</h3>
        </div>

        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead>
                <tr>
                    <th>Hotel</th>
                    <th>Solicitante</th>
                    <th>Nota</th>
                    <th>Estado</th>
                    <th>Revisión</th>
                    <th class="w-1">Acciones</th>
                </tr>
                </thead>

                <tbody>
                @forelse($requests as $item)
                    @php
                        $meta = $statusMeta($item->status);
                    @endphp

                    <tr>
                        <td>
                            <div class="fw-bold">
                                {{ $item->hotel?->name ?? 'Hotel eliminado' }}
                            </div>
                            <div class="text-secondary small">
                                {{ $item->created_at?->format('d/m/Y H:i') }}
                            </div>
                        </td>

                        <td>
                            <div class="fw-bold">
                                {{ $item->requester_name ?: '—' }}
                            </div>
                            <div class="text-secondary small">
                                {{ $item->requester_phone ?: 'Sin teléfono' }}
                            </div>
                        </td>

                        <td style="max-width: 360px;">
                            <div class="text-wrap">
                                {{ $item->note ?: '—' }}
                            </div>

                            @if($item->reject_reason)
                                <div class="text-danger small mt-1">
                                    <i class="ti ti-alert-circle me-1"></i>
                                    Rechazo: {{ $item->reject_reason }}
                                </div>
                            @endif
                        </td>

                        <td>
                            <span class="badge {{ $meta['class'] }}">
                                <i class="ti {{ $meta['icon'] }} me-1"></i>
                                {{ $item->statusLabel() }}
                            </span>
                        </td>

                        <td>
                            @if($item->reviewed_at)
                                <div>{{ $item->reviewed_at->format('d/m/Y H:i') }}</div>
                            @else
                                <span class="text-secondary">Sin revisar</span>
                            @endif
                        </td>

                        <td>
                            @if($item->status === 'pending')
                                <div class="btn-list flex-nowrap">
                                    <form method="POST"
                                          action="{{ route('sysapp.pin-reset-requests.complete', $item) }}"
                                          onsubmit="return confirm('Esto generará un nuevo PIN temporal. ¿Continuar?');">
                                        @csrf
                                        <button class="btn btn-success btn-sm" type="submit">
                                            <i class="ti ti-key me-1"></i>
                                            Generar PIN
                                        </button>
                                    </form>

                                    <form method="POST"
                                          action="{{ route('sysapp.pin-reset-requests.reject', $item) }}"
                                          onsubmit="return confirm('¿Rechazar esta solicitud?');">
                                        @csrf
                                        <input type="hidden" name="reject_reason" value="Solicitud no autorizada por SysApp.">

                                        <button class="btn btn-outline-danger btn-sm" type="submit">
                                            <i class="ti ti-x me-1"></i>
                                            Rechazar
                                        </button>
                                    </form>
                                </div>
                            @else
                                <span class="text-secondary small">Revisada</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty">
                                <div class="empty-icon">
                                    <i class="ti ti-key"></i>
                                </div>
                                <p class="empty-title">No hay solicitudes de reset de PIN.</p>
                                <p class="empty-subtitle text-secondary">
                                    Cuando un hotel solicite recuperar su acceso aparecerá aquí.
                                </p>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if($requests->hasPages())
            <div class="card-footer">
                {{ $requests->links() }}
            </div>
        @endif
    </div>
@endsection