@extends('sysapp.layout')

@section('title', 'Hoteles · HotelDesk Lite')

@section('content')
    <div class="page-head">
        <div>
            <h1>Hoteles</h1>
            <div class="muted">Alta, activación y configuración de hoteles.</div>
        </div>

        <a class="btn btn-primary" href="{{ route('sysapp.hotels.create') }}">Crear hotel</a>
    </div>

    <div class="card">
        <table class="table">
            <thead>
            <tr>
                <th>Hotel</th>
                <th>Estado</th>
                <th>Panel</th>
                <th>QR público</th>
                <th>Taxi</th>
                <th>QRs</th>
                <th>Solicitudes</th>
                <th>Acciones</th>
            </tr>
            </thead>
            <tbody>
            @forelse($hotels as $hotel)
                <tr>
                    <td>
                        <strong>{{ $hotel->name }}</strong><br>
                        <span class="muted">/h/{{ $hotel->slug }}</span>
                    </td>
                    <td>
                        <span class="badge badge-{{ $hotel->status }}">
                            {{ strtoupper($hotel->status) }}
                        </span>
                    </td>
                    <td>{{ $hotel->panel_enabled ? 'Activo' : 'Inactivo' }}</td>
                    <td>{{ $hotel->public_requests_enabled ? 'Activo' : 'Inactivo' }}</td>
                    <td>{{ $hotel->taxi_enabled ? 'Activo' : 'Inactivo' }}</td>
                    <td>{{ $hotel->qr_points_count }}</td>
                    <td>{{ $hotel->requests_count }}</td>
                    <td>
                        <div class="actions">
                            <a class="btn btn-soft" href="{{ route('hotel.login', $hotel) }}" target="_blank">Panel</a>
                            <a class="btn btn-soft" href="{{ route('sysapp.hotels.edit', $hotel) }}">Editar</a>
                            <a class="btn btn-primary" href="{{ route('sysapp.hotels.qr-points.index', $hotel) }}">QRs</a>
                            <a class="btn btn-success" href="{{ route('sysapp.hotels.qr-points.print-all', $hotel) }}" target="_blank">Imprimir</a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">No hay hoteles registrados.</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        <div style="margin-top: 14px;">
            {{ $hotels->links() }}
        </div>
    </div>
@endsection