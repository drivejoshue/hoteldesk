@extends('layouts.hotel-panel')

@section('title', $hotel->name . ' · Mis QRs')
@section('subtitle', 'Códigos QR del hotel')

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

    <style>
        .qr-admin-card {
            border-radius: 18px;
            overflow: hidden;
            border: 1px solid #e5e7eb;
            box-shadow: 0 10px 28px rgba(15, 23, 42, 0.06);
            transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
        }

        .qr-admin-card:hover {
            transform: translateY(-1px);
            box-shadow: 0 14px 34px rgba(15, 23, 42, 0.10);
        }

        .qr-admin-card.is-active {
            background: linear-gradient(135deg, #ffffff 0%, #ecfdf5 100%);
            border-color: #bbf7d0;
        }

        .qr-admin-card.is-inactive {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border-color: #cbd5e1;
            opacity: .92;
        }

        .qr-code-box {
            border-radius: 14px;
            border: 1px solid rgba(226, 232, 240, .95);
            background: rgba(255,255,255,.72);
            padding: 12px;
        }

        .qr-security-note {
            border-radius: 14px;
            border: 1px solid #fed7aa;
            background: #fff7ed;
            color: #9a3412;
            padding: 10px 12px;
            font-size: 12px;
            font-weight: 700;
        }

        .qr-inactive-note {
            border-radius: 14px;
            border: 1px solid #e2e8f0;
            background: #f1f5f9;
            color: #475569;
            padding: 10px 12px;
            font-size: 12px;
            font-weight: 700;
        }

        .qr-actions-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
    </style>

    <div class="page-header d-print-none mb-3">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title mb-1">Mis códigos QR</h2>
                <div class="text-secondary">
                    Reimprime, invalida o regenera códigos QR del hotel.
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

    @if (session('success'))
        <div class="alert alert-success">
            <i class="ti ti-circle-check me-1"></i>
            {{ session('success') }}
        </div>
    @endif

    @if (session('info'))
        <div class="alert alert-info">
            <i class="ti ti-info-circle me-1"></i>
            {{ session('info') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <i class="ti ti-alert-triangle me-1"></i>
            {{ $errors->first() }}
        </div>
    @endif

    <div class="row row-cards">
        @forelse($points as $point)
            @php
                $typeLabel = $typeLabels[$point->type] ?? 'Otro';
                $modeLabel = $modeLabels[$point->mode] ?? $point->mode;
                $typeIcon = $typeIcons[$point->type] ?? 'ti-map-2';
            @endphp

            <div class="col-12 col-md-6 col-xl-4">
                <div class="card h-100 qr-admin-card {{ $point->active ? 'is-active' : 'is-inactive' }}">
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
                                <span class="badge bg-green-lt text-green rounded-pill">
                                    <i class="ti ti-check me-1"></i>
                                    Activo
                                </span>
                            @else
                                <span class="badge bg-red-lt text-red rounded-pill">
                                    <i class="ti ti-ban me-1"></i>
                                    Inactivo
                                </span>
                            @endif
                        </div>

                        <div class="qr-code-box mt-3">
                            <div class="fw-bold mb-1">
                                Código actual
                            </div>

                            <div class="font-monospace small text-break">
                                {{ $point->public_code }}
                            </div>

                            @if($point->previous_public_code)
                                <div class="text-secondary small mt-2">
                                    Código anterior:
                                    <span class="font-monospace">{{ $point->previous_public_code }}</span>
                                </div>
                            @endif

                            @if($point->regenerated_at)
                                <div class="text-secondary small mt-1">
                                    Regenerado:
                                    {{ $point->regenerated_at->format('d/m/Y H:i') }}
                                </div>
                            @endif
                        </div>

                        @if(! $point->active)
                            <div class="qr-inactive-note mt-3">
                                <i class="ti ti-lock me-1"></i>
                                Este QR está inactivo y no acepta solicitudes.

                                @if($point->invalidated_reason)
                                    <div class="mt-1">
                                        Motivo: {{ $point->invalidated_reason }}
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="qr-security-note mt-3">
                                <i class="ti ti-shield-lock me-1"></i>
                                Si el QR se perdió, fue fotografiado o se usó mal, regenera el código.
                            </div>
                        @endif

                        <div class="mt-auto pt-3">
                            <div class="qr-actions-grid">
                                @if($point->active)
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
                                        Reimprimir
                                    </a>

                                    <form id="invalidateQrForm{{ $point->id }}"
                                          method="POST"
                                          action="{{ route('hotel.qr-points.invalidate', [$hotel, $point]) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="reason" id="invalidateReason{{ $point->id }}">

                                       <button class="btn btn-outline-danger btn-sm js-invalidate-qr"
        type="button"
        data-point-id="{{ $point->id }}"
        data-label="{{ $point->label }}">
    <i class="ti ti-ban me-1"></i>
    Invalidar
</button>
                                    </form>
                                @endif

                                <form id="regenerateQrForm{{ $point->id }}"
                                      method="POST"
                                      action="{{ route('hotel.qr-points.regenerate', [$hotel, $point]) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="reason" id="regenerateReason{{ $point->id }}">

                                   <button class="btn btn-warning btn-sm js-regenerate-qr"
        type="button"
        data-point-id="{{ $point->id }}"
        data-label="{{ $point->label }}"
        data-active="{{ $point->active ? '1' : '0' }}">
    <i class="ti ti-refresh me-1"></i>
    {{ $point->active ? 'Regenerar' : 'Generar nuevo' }}
</button>
                                </form>
                            </div>
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

  <script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.js-invalidate-qr').forEach((button) => {
        button.addEventListener('click', async () => {
            const pointId = button.dataset.pointId;
            const label = button.dataset.label || 'este QR';

            await confirmInvalidateQr(pointId, label);
        });
    });

    document.querySelectorAll('.js-regenerate-qr').forEach((button) => {
        button.addEventListener('click', async () => {
            const pointId = button.dataset.pointId;
            const label = button.dataset.label || 'este QR';
            const isActive = button.dataset.active === '1';

            await confirmRegenerateQr(pointId, label, isActive);
        });
    });
});

async function confirmInvalidateQr(pointId, label) {
    const form = document.getElementById(`invalidateQrForm${pointId}`);
    const reasonInput = document.getElementById(`invalidateReason${pointId}`);

    if (!form || !reasonInput) {
        console.error('No se encontró el formulario para invalidar QR.', pointId);
        return;
    }

    if (!window.Swal) {
        if (confirm(`¿Invalidar el QR de ${label}?`)) {
            form.submit();
        }

        return;
    }

    const result = await window.Swal.fire({
        title: '¿Invalidar QR?',
        html: `
            <div class="text-start">
                <p>
                    El código QR de <strong>${escapeHtml(label)}</strong> dejará de aceptar solicitudes.
                </p>
                <p class="text-secondary mb-0">
                    Úsalo cuando el QR se perdió, fue retirado o no debe seguir funcionando.
                </p>
            </div>
        `,
        input: 'text',
        inputLabel: 'Motivo opcional',
        inputPlaceholder: 'Ej. QR perdido, dañado o uso indebido',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, invalidar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6c757d',
        reverseButtons: true,
        focusCancel: true,
    });

    if (!result.isConfirmed) {
        return;
    }

    reasonInput.value = result.value || '';
    form.submit();
}

async function confirmRegenerateQr(pointId, label, isActive) {
    const form = document.getElementById(`regenerateQrForm${pointId}`);
    const reasonInput = document.getElementById(`regenerateReason${pointId}`);

    if (!form || !reasonInput) {
        console.error('No se encontró el formulario para regenerar QR.', pointId);
        return;
    }

    const message = isActive
        ? 'Se generará un nuevo código. El QR impreso anteriormente dejará de funcionar.'
        : 'Se generará un nuevo código activo para este punto.';

    if (!window.Swal) {
        if (confirm(`¿Regenerar el QR de ${label}?`)) {
            form.submit();
        }

        return;
    }

    const result = await window.Swal.fire({
        title: isActive ? '¿Regenerar QR?' : '¿Generar nuevo QR?',
        html: `
            <div class="text-start">
                <p>
                    Punto: <strong>${escapeHtml(label)}</strong>
                </p>
                <p class="text-secondary mb-0">
                    ${message}
                </p>
            </div>
        `,
        input: 'text',
        inputLabel: 'Motivo opcional',
        inputPlaceholder: 'Ej. QR perdido, reimpresión segura o cambio preventivo',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: isActive ? 'Sí, regenerar' : 'Sí, generar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#f59e0b',
        cancelButtonColor: '#6c757d',
        reverseButtons: true,
        focusCancel: true,
    });

    if (!result.isConfirmed) {
        return;
    }

    reasonInput.value = result.value || '';
    form.submit();
}

function escapeHtml(value) {
    return String(value ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
}
</script>
@endsection