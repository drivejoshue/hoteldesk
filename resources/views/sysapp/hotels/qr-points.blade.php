@extends('sysapp.layout')

@section('title', 'Puntos QR · ' . $hotel->name)

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
        .sys-qr-row-inactive {
            background: #f8fafc;
        }

        .sys-qr-code {
            max-width: 210px;
            white-space: normal;
            word-break: break-all;
        }

        .sys-actions {
            display: flex;
            flex-wrap: nowrap;
            gap: 6px;
        }

        .sys-actions form {
            margin: 0;
        }

        .sys-qr-note {
            font-size: 12px;
            color: #64748b;
        }
    </style>

    <div class="page-header d-print-none mb-3">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title mb-1">Puntos QR</h2>
                <div class="text-secondary">
                    {{ $hotel->name }} · Administración central de habitaciones y áreas comunes.
                </div>
            </div>

            <div class="col-auto">
                <div class="btn-list">
                    <a class="btn btn-outline-secondary" href="{{ route('sysapp.hotels.index') }}">
                        <i class="ti ti-building me-1"></i>
                        Hoteles
                    </a>

                    <a class="btn btn-outline-secondary" href="{{ route('sysapp.hotels.edit', $hotel) }}">
                        <i class="ti ti-edit me-1"></i>
                        Editar hotel
                    </a>

                    <a class="btn btn-success" href="{{ route('sysapp.hotels.qr-points.print-all', $hotel) }}" target="_blank">
                        <i class="ti ti-printer me-1"></i>
                        Imprimir activos
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if(session('info'))
        <div class="alert alert-info">
            <i class="ti ti-info-circle me-1"></i>
            {{ session('info') }}
        </div>
    @endif

    <div class="row row-cards">
        <div class="col-12 col-xl-6">
            <form class="card" method="POST" action="{{ route('sysapp.hotels.qr-points.generate-rooms', $hotel) }}">
                @csrf

                <div class="card-header">
                    <h3 class="card-title">Generar habitaciones</h3>
                </div>

                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label">Desde</label>
                            <input class="form-control" name="from" type="number" min="1" required placeholder="101">
                        </div>

                        <div class="col-6">
                            <label class="form-label">Hasta</label>
                            <input class="form-control" name="to" type="number" min="1" required placeholder="120">
                        </div>

                        <div class="col-6">
                            <label class="form-label">Piso</label>
                            <input class="form-control" name="floor" placeholder="1">
                        </div>

                        <div class="col-6">
                            <label class="form-label">Prefijo</label>
                            <input class="form-control" name="prefix" placeholder="Habitación">
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <button class="btn btn-primary" type="submit">
                        <i class="ti ti-wand me-1"></i>
                        Generar rango
                    </button>
                </div>
            </form>
        </div>

        <div class="col-12 col-xl-6">
            <form class="card" method="POST" action="{{ route('sysapp.hotels.qr-points.store', $hotel) }}">
                @csrf

                <div class="card-header">
                    <h3 class="card-title">Crear punto QR manual</h3>
                </div>

                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Nombre del punto</label>
                            <input class="form-control" name="label" required placeholder="Lobby, Alberca, Habitación 204">
                        </div>

                        <div class="col-6">
                            <label class="form-label">Tipo</label>
                            <select class="form-select" name="type" required>
                                <option value="room">Habitación</option>
                                <option value="lobby">Lobby</option>
                                <option value="area">Área</option>
                                <option value="restaurant">Restaurante</option>
                                <option value="parking">Estacionamiento</option>
                                <option value="reception">Recepción</option>
                                <option value="other">Otro</option>
                            </select>
                        </div>

                        <div class="col-6">
                            <label class="form-label">Piso / zona</label>
                            <input class="form-control" name="floor" placeholder="1, PB, Terraza">
                        </div>

                        <div class="col-12">
                            <label class="form-label">Modo</label>
                            <select class="form-select" name="mode" id="modeSelect" required>
                                <option value="menu">Menú completo</option>
                                <option value="limited">Menú limitado</option>
                                <option value="direct">Solicitud directa</option>
                            </select>
                        </div>

                        <div class="col-12" id="directTypeBox">
                            <label class="form-label">Solicitud directa</label>
                            <select class="form-select" name="fixed_request_type">
                                <option value="">No aplica</option>
                                @foreach($requestTypes as $key => $type)
                                    <option value="{{ $key }}">{{ $type['label'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12" id="limitedTypesBox">
                            <label class="form-label">Solicitudes permitidas para menú limitado</label>

                            <div class="row g-2">
                                @foreach($requestTypes as $key => $type)
                                    <div class="col-12 col-md-6">
                                        <label class="form-check border rounded-3 p-2 m-0">
                                            <input class="form-check-input" type="checkbox" name="allowed_request_types[]" value="{{ $key }}">
                                            <span class="form-check-label fw-bold">
                                                {{ $type['icon'] }} {{ $type['label'] }}
                                            </span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <button class="btn btn-primary" type="submit">
                        <i class="ti ti-plus me-1"></i>
                        Crear punto QR
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header">
            <div>
                <h3 class="card-title mb-0">Puntos configurados</h3>
                <div class="text-secondary small">
                    Regenera el código cuando un QR se pierda, se copie o se use indebidamente.
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead>
                <tr>
                    <th>Punto</th>
                    <th>Tipo</th>
                    <th>Piso</th>
                    <th>Modo</th>
                    <th>Código</th>
                    <th>Estado</th>
                    <th>Historial</th>
                    <th class="w-1">Acciones</th>
                </tr>
                </thead>

                <tbody>
                @forelse($points as $point)
                    <tr class="{{ $point->active ? '' : 'sys-qr-row-inactive' }}">
                        <td>
                            <div class="fw-bold">
                                <i class="ti {{ $typeIcons[$point->type] ?? 'ti-qrcode' }} me-1"></i>
                                {{ $point->label }}
                            </div>
                            <div class="sys-qr-note">
                                <a href="{{ route('public.qr.show', $point->public_code) }}" target="_blank">
                                    Abrir URL pública
                                </a>
                            </div>
                        </td>

                        <td>{{ $typeLabels[$point->type] ?? $point->type }}</td>

                        <td>{{ $point->floor ?: '—' }}</td>

                        <td>{{ $modeLabels[$point->mode] ?? $point->mode }}</td>

                        <td class="sys-qr-code">
                            <code>{{ $point->public_code }}</code>

                            @if($point->previous_public_code)
                                <div class="sys-qr-note mt-1">
                                    Anterior:
                                    <code>{{ $point->previous_public_code }}</code>
                                </div>
                            @endif
                        </td>

                        <td>
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
                        </td>

                        <td>
                            @if($point->regenerated_at)
                                <div class="sys-qr-note">
                                    Regenerado:
                                    {{ $point->regenerated_at->format('d/m/Y H:i') }}
                                </div>
                            @endif

                            @if($point->invalidated_at)
                                <div class="sys-qr-note text-danger">
                                    Invalidado:
                                    {{ $point->invalidated_at->format('d/m/Y H:i') }}
                                </div>
                            @endif

                            @if($point->invalidated_reason)
                                <div class="sys-qr-note">
                                    {{ $point->invalidated_reason }}
                                </div>
                            @endif

                            @if(! $point->regenerated_at && ! $point->invalidated_at)
                                <span class="text-secondary">—</span>
                            @endif
                        </td>

                        <td>
                            <div class="sys-actions">
                                @if($point->active)
                                    <a class="btn btn-success btn-sm"
                                       target="_blank"
                                       href="{{ route('sysapp.hotels.qr-points.print', [$hotel, $point]) }}">
                                        <i class="ti ti-printer me-1"></i>
                                        Imprimir
                                    </a>

                                    <form id="invalidateQrForm{{ $point->id }}"
                                          method="POST"
                                          action="{{ route('sysapp.hotels.qr-points.invalidate', [$hotel, $point]) }}">
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
                                      action="{{ route('sysapp.hotels.qr-points.regenerate', [$hotel, $point]) }}">
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

                                <form method="POST" action="{{ route('sysapp.hotels.qr-points.toggle', [$hotel, $point]) }}">
                                    @csrf
                                    @method('PATCH')

                                    <button class="btn btn-outline-secondary btn-sm" type="submit">
                                        {{ $point->active ? 'Desactivar' : 'Activar' }}
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">
                            <div class="empty">
                                <div class="empty-icon">
                                    <i class="ti ti-qrcode"></i>
                                </div>
                                <p class="empty-title">No hay puntos QR registrados.</p>
                                <p class="empty-subtitle text-secondary">
                                    Genera habitaciones o crea un punto QR manual.
                                </p>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if($points->hasPages())
            <div class="card-footer">
                {{ $points->links() }}
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modeSelect = document.getElementById('modeSelect');
            const directTypeBox = document.getElementById('directTypeBox');
            const limitedTypesBox = document.getElementById('limitedTypesBox');

            if (modeSelect && directTypeBox && limitedTypesBox) {
                function syncMode() {
                    directTypeBox.classList.toggle('d-none', modeSelect.value !== 'direct');
                    limitedTypesBox.classList.toggle('d-none', modeSelect.value !== 'limited');
                }

                modeSelect.addEventListener('change', syncMode);
                syncMode();
            }

            document.querySelectorAll('.js-invalidate-qr').forEach((button) => {
                button.addEventListener('click', async () => {
                    await confirmInvalidateQr(
                        button.dataset.pointId,
                        button.dataset.label || 'este QR'
                    );
                });
            });

            document.querySelectorAll('.js-regenerate-qr').forEach((button) => {
                button.addEventListener('click', async () => {
                    await confirmRegenerateQr(
                        button.dataset.pointId,
                        button.dataset.label || 'este QR',
                        button.dataset.active === '1'
                    );
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