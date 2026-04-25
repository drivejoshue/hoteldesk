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
    @endphp

    <div class="page-header d-print-none mb-3">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title mb-1">Puntos QR</h2>
                <div class="text-secondary">
                    {{ $hotel->name }} · Habitaciones y áreas comunes.
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
                        Imprimir todos
                    </a>
                </div>
            </div>
        </div>
    </div>

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
            <h3 class="card-title">Puntos configurados</h3>
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
                    <th>URL pública</th>
                    <th class="w-1">Acciones</th>
                </tr>
                </thead>

                <tbody>
                @forelse($points as $point)
                    <tr>
                        <td>
                            <strong>{{ $point->label }}</strong>
                        </td>

                        <td>{{ $typeLabels[$point->type] ?? $point->type }}</td>

                        <td>{{ $point->floor ?: '—' }}</td>

                        <td>{{ $modeLabels[$point->mode] ?? $point->mode }}</td>

                        <td>
                            <code>{{ $point->public_code }}</code>
                        </td>

                        <td>
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
                        </td>

                        <td>
                            <a href="{{ route('public.qr.show', $point->public_code) }}" target="_blank">
                                Abrir QR
                            </a>
                        </td>

                        <td>
                            <div class="btn-list flex-nowrap">
                                <a class="btn btn-success btn-sm"
                                   target="_blank"
                                   href="{{ route('sysapp.hotels.qr-points.print', [$hotel, $point]) }}">
                                    Imprimir
                                </a>

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
        (() => {
            const modeSelect = document.getElementById('modeSelect');
            const directTypeBox = document.getElementById('directTypeBox');
            const limitedTypesBox = document.getElementById('limitedTypesBox');

            if (!modeSelect || !directTypeBox || !limitedTypesBox) {
                return;
            }

            function syncMode() {
                directTypeBox.classList.toggle('d-none', modeSelect.value !== 'direct');
                limitedTypesBox.classList.toggle('d-none', modeSelect.value !== 'limited');
            }

            modeSelect.addEventListener('change', syncMode);
            syncMode();
        })();
    </script>
@endsection