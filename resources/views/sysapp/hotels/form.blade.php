@extends('sysapp.layout')

@section('title', ($hotel->exists ? 'Editar hotel' : 'Crear hotel') . ' · HotelDesk Lite')

@section('content')
    <div class="page-head">
        <div>
            <h1>{{ $hotel->exists ? 'Editar hotel' : 'Crear hotel' }}</h1>
            <div class="muted">Configuración multi-tenant del hotel.</div>
        </div>

        <a class="btn btn-soft" href="{{ route('sysapp.hotels.index') }}">Volver</a>
    </div>

    <form class="card" method="POST"
          enctype="multipart/form-data"
          action="{{ $hotel->exists ? route('sysapp.hotels.update', $hotel) : route('sysapp.hotels.store') }}">
        @csrf

        @if($hotel->exists)
            @method('PUT')
        @endif

        <div class="grid grid-2">
            <div class="field">
                <label>Nombre del hotel</label>
                <input name="name" value="{{ old('name', $hotel->name) }}" required>
            </div>

            <div class="field">
                <label>Slug</label>
                <input name="slug" value="{{ old('slug', $hotel->slug) }}" placeholder="la-central">
            </div>

            <div class="field">
                <label>PIN recepción {{ $hotel->exists ? '(dejar vacío para no cambiar)' : '' }}</label>
                <input name="pin" type="text" inputmode="numeric" value="{{ old('pin') }}" {{ $hotel->exists ? '' : 'required' }}>
            </div>

            <div class="field">
                <label>Estado</label>
                <select name="status" required>
                    @foreach(['draft' => 'Draft', 'active' => 'Activo', 'paused' => 'Pausado', 'disabled' => 'Desactivado'] as $key => $label)
                        <option value="{{ $key }}" @selected(old('status', $hotel->status) === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="field">
                <label>Teléfono</label>
                <input name="phone" value="{{ old('phone', $hotel->phone) }}">
            </div>

            <div class="field">
                <label>Correo</label>
                <input name="email" type="email" value="{{ old('email', $hotel->email) }}">
            </div>

            <div class="field">
    <label>URL Service Point Orbana</label>
    <input
        name="service_point_url"
        value="{{ old('service_point_url', $hotel->service_point_url) }}"
        placeholder="https://dispatch.orbana.mx/sp/....">
    <div class="muted" style="margin-top: 6px;">
        Opcional. Se usará como acceso rápido cuando recepción reciba una solicitud de taxi.
    </div>
</div>


            <div class="field">
                <label>Color principal</label>
                <input name="primary_color" value="{{ old('primary_color', $hotel->primary_color ?: '#0F6CBD') }}">
            </div>

            <div class="field">
                <label>Logo</label>
                <input name="logo" type="file" accept="image/*">
                @if($hotel->logo_path)
                    <div class="muted" style="margin-top: 8px;">Logo actual: {{ $hotel->logo_path }}</div>
                @endif
            </div>
        </div>

        <div class="field">
            <label>Dirección</label>
            <textarea name="address" rows="2">{{ old('address', $hotel->address) }}</textarea>
        </div>

        <div class="grid grid-3">
            <label class="check-row">
                <input type="checkbox" name="public_requests_enabled" value="1" @checked(old('public_requests_enabled', $hotel->public_requests_enabled))>
                QR público activo
            </label>

            <label class="check-row">
                <input type="checkbox" name="panel_enabled" value="1" @checked(old('panel_enabled', $hotel->panel_enabled))>
                Panel recepción activo
            </label>

            <label class="check-row">
                <input type="checkbox" name="taxi_enabled" value="1" @checked(old('taxi_enabled', $hotel->taxi_enabled))>
                Taxi activo
            </label>
        </div>

        <div class="actions" style="margin-top: 18px;">
            <button class="btn btn-primary" type="submit">
                {{ $hotel->exists ? 'Guardar cambios' : 'Crear hotel' }}
            </button>

            @if($hotel->exists)
                <a class="btn btn-soft" href="{{ route('sysapp.hotels.qr-points.index', $hotel) }}">Configurar QRs</a>
            @endif
        </div>
    </form>
@endsection