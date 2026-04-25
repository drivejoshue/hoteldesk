@extends('sysapp.layout')

@section('title', 'Puntos QR · ' . $hotel->name)

@section('content')
    <div class="page-head">
        <div>
            <h1>Puntos QR</h1>
            <div class="muted">{{ $hotel->name }} · Habitaciones y áreas comunes.</div>
        </div>

        <div class="actions">
            <a class="btn btn-soft" href="{{ route('sysapp.hotels.index') }}">Hoteles</a>
            <a class="btn btn-soft" href="{{ route('sysapp.hotels.edit', $hotel) }}">Editar hotel</a>
            <a class="btn btn-success" href="{{ route('sysapp.hotels.qr-points.print-all', $hotel) }}" target="_blank">Imprimir todos</a>
        </div>
    </div>

    <div class="grid grid-2">
        <form class="card" method="POST" action="{{ route('sysapp.hotels.qr-points.generate-rooms', $hotel) }}">
            @csrf

            <h2 style="margin-top: 0;">Generar habitaciones</h2>

            <div class="grid grid-2">
                <div class="field">
                    <label>Desde</label>
                    <input name="from" type="number" min="1" required placeholder="101">
                </div>

                <div class="field">
                    <label>Hasta</label>
                    <input name="to" type="number" min="1" required placeholder="120">
                </div>

                <div class="field">
                    <label>Piso</label>
                    <input name="floor" placeholder="1">
                </div>

                <div class="field">
                    <label>Prefijo</label>
                    <input name="prefix" placeholder="Habitación">
                </div>
            </div>

            <button class="btn btn-primary" type="submit">Generar rango</button>
        </form>

        <form class="card" method="POST" action="{{ route('sysapp.hotels.qr-points.store', $hotel) }}">
            @csrf

            <h2 style="margin-top: 0;">Crear punto QR manual</h2>

            <div class="field">
                <label>Nombre del punto</label>
                <input name="label" required placeholder="Lobby, Alberca, Habitación 204">
            </div>

            <div class="grid grid-2">
                <div class="field">
                    <label>Tipo</label>
                    <select name="type" required>
                        <option value="room">Habitación</option>
                        <option value="lobby">Lobby</option>
                        <option value="area">Área</option>
                        <option value="restaurant">Restaurante</option>
                        <option value="parking">Estacionamiento</option>
                        <option value="reception">Recepción</option>
                        <option value="other">Otro</option>
                    </select>
                </div>

                <div class="field">
                    <label>Piso / zona</label>
                    <input name="floor" placeholder="1, PB, Terraza">
                </div>
            </div>

            <div class="field">
                <label>Modo</label>
                <select name="mode" id="modeSelect" required>
                    <option value="menu">Menú completo</option>
                    <option value="limited">Menú limitado</option>
                    <option value="direct">Solicitud directa</option>
                </select>
            </div>

            <div class="field">
                <label>Solicitud directa</label>
                <select name="fixed_request_type">
                    <option value="">No aplica</option>
                    @foreach($requestTypes as $key => $type)
                        <option value="{{ $key }}">{{ $type['label'] }}</option>
                    @endforeach
                </select>
            </div>

            <div class="field">
                <label>Solicitudes permitidas para menú limitado</label>
                <div class="grid grid-2">
                    @foreach($requestTypes as $key => $type)
                        <label class="check-row">
                            <input type="checkbox" name="allowed_request_types[]" value="{{ $key }}">
                            {{ $type['icon'] }} {{ $type['label'] }}
                        </label>
                    @endforeach
                </div>
            </div>

            <button class="btn btn-primary" type="submit">Crear punto QR</button>
        </form>
    </div>

    <div class="card" style="margin-top: 18px;">
        <table class="table">
            <thead>
            <tr>
                <th>Punto</th>
                <th>Tipo</th>
                <th>Piso</th>
                <th>Modo</th>
                <th>Código</th>
                <th>Estado</th>
                <th>URL pública</th>
                <th>Acciones</th>
            </tr>
            </thead>
            <tbody>
            @forelse($points as $point)
                <tr>
                    <td><strong>{{ $point->label }}</strong></td>
                    <td>{{ $point->type }}</td>
                    <td>{{ $point->floor ?: '—' }}</td>
                    <td>{{ $point->mode }}</td>
                    <td><code>{{ $point->public_code }}</code></td>
                    <td>
                        @if($point->active)
                            <span class="badge badge-active">Activo</span>
                        @else
                            <span class="badge badge-disabled">Inactivo</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('public.qr.show', $point->public_code) }}" target="_blank">
                            Abrir QR
                        </a>
                    </td>
                    <td>
                        <div class="actions">
                            <a class="btn btn-success" target="_blank" href="{{ route('sysapp.hotels.qr-points.print', [$hotel, $point]) }}">
                                Imprimir
                            </a>

                            <form method="POST" action="{{ route('sysapp.hotels.qr-points.toggle', [$hotel, $point]) }}">
                                @csrf
                                @method('PATCH')
                                <button class="btn btn-soft" type="submit">
                                    {{ $point->active ? 'Desactivar' : 'Activar' }}
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">No hay puntos QR registrados.</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        <div style="margin-top: 14px;">
            {{ $points->links() }}
        </div>
    </div>
@endsection