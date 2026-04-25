@extends('sysapp.layout')

@section('title', ($hotel->exists ? 'Editar hotel' : 'Crear hotel') . ' · HotelDesk Lite')

@section('content')
    <div class="page-header d-print-none mb-3">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title mb-1">
                    {{ $hotel->exists ? 'Editar hotel' : 'Crear hotel' }}
                </h2>

                <div class="text-secondary">
                    Configuración multi-tenant del hotel.
                </div>
            </div>

            <div class="col-auto">
                <a class="btn btn-outline-secondary" href="{{ route('sysapp.hotels.index') }}">
                    <i class="ti ti-arrow-left me-1"></i>
                    Volver
                </a>
            </div>
        </div>
    </div>

    <form method="POST"
          enctype="multipart/form-data"
          action="{{ $hotel->exists ? route('sysapp.hotels.update', $hotel) : route('sysapp.hotels.store') }}">
        @csrf

        @if($hotel->exists)
            @method('PUT')
        @endif

        <div class="row row-cards">
            <div class="col-12 col-xl-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Datos generales</h3>
                    </div>

                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12 col-md-7">
                                <label class="form-label">
                                    Nombre del hotel
                                    <span class="text-danger">*</span>
                                </label>

                                <input
                                    class="form-control"
                                    name="name"
                                    value="{{ old('name', $hotel->name) }}"
                                    required
                                    placeholder="Ej. Hotel La Central">
                            </div>

                            <div class="col-12 col-md-5">
                                <label class="form-label">Slug</label>

                                <div class="input-group">
                                    <span class="input-group-text">/h/</span>
                                    <input
                                        class="form-control"
                                        name="slug"
                                        value="{{ old('slug', $hotel->slug) }}"
                                        placeholder="la-central">
                                </div>

                                <div class="form-hint">
                                    Se usa para el acceso del panel del hotel.
                                </div>
                            </div>

                            <div class="col-12 col-md-5">
                                <label class="form-label">
                                    PIN recepción
                                    @if($hotel->exists)
                                        <span class="text-secondary">(opcional)</span>
                                    @else
                                        <span class="text-danger">*</span>
                                    @endif
                                </label>

                                <input
                                    class="form-control"
                                    name="pin"
                                    type="text"
                                    inputmode="numeric"
                                    value="{{ old('pin') }}"
                                    {{ $hotel->exists ? '' : 'required' }}
                                    placeholder="{{ $hotel->exists ? 'Dejar vacío para no cambiar' : 'Ej. 2468' }}">
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="form-label">
                                    Estado
                                    <span class="text-danger">*</span>
                                </label>

                                <select class="form-select" name="status" required>
                                    @foreach([
                                        'draft' => 'Borrador',
                                        'active' => 'Activo',
                                        'paused' => 'Pausado',
                                        'disabled' => 'Desactivado',
                                    ] as $key => $label)
                                        <option value="{{ $key }}" @selected(old('status', $hotel->status) === $key)>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12 col-md-3">
                                <label class="form-label">Color principal</label>

                                <input
                                    class="form-control form-control-color"
                                    name="primary_color"
                                    type="color"
                                    value="{{ old('primary_color', $hotel->primary_color ?: '#0F6CBD') }}">
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label">Teléfono</label>

                                <input
                                    class="form-control"
                                    name="phone"
                                    value="{{ old('phone', $hotel->phone) }}"
                                    placeholder="Ej. 229...">
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label">Correo</label>

                                <input
                                    class="form-control"
                                    name="email"
                                    type="email"
                                    value="{{ old('email', $hotel->email) }}"
                                    placeholder="recepcion@hotel.com">
                            </div>

                            <div class="col-12">
                                <label class="form-label">URL Service Point Orbana</label>

                                <input
                                    class="form-control"
                                    name="service_point_url"
                                    value="{{ old('service_point_url', $hotel->service_point_url) }}"
                                    placeholder="https://dispatch.orbana.mx/sp/...">

                                <div class="form-hint">
                                    Opcional. Se usará como acceso rápido cuando recepción reciba una solicitud de taxi.
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Dirección</label>

                                <textarea
                                    class="form-control"
                                    name="address"
                                    rows="2"
                                    placeholder="Dirección del hotel">{{ old('address', $hotel->address) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        <h3 class="card-title">Logo</h3>
                    </div>

                    <div class="card-body">
                        <div class="row g-3 align-items-center">
                            <div class="col-auto">
                                @if($hotel->logo_path)
                                    <span class="avatar avatar-xl bg-white border">
                                        <img src="{{ asset('storage/' . $hotel->logo_path) }}" alt="{{ $hotel->name }}">
                                    </span>
                                @else
                                    <span class="avatar avatar-xl bg-primary-lt text-primary">
                                        <i class="ti ti-building"></i>
                                    </span>
                                @endif
                            </div>

                            <div class="col">
                                <label class="form-label">Subir nuevo logo</label>

                                <input
                                    class="form-control"
                                    name="logo"
                                    type="file"
                                    accept="image/*">

                                @if($hotel->logo_path)
                                    <div class="form-hint">
                                        Logo actual: {{ $hotel->logo_path }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Servicios activos</h3>
                    </div>

                    <div class="card-body">
                        <label class="form-check form-switch mb-3">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                name="public_requests_enabled"
                                value="1"
                                @checked(old('public_requests_enabled', $hotel->public_requests_enabled))>

                            <span class="form-check-label">
                                <strong>QR público activo</strong>
                                <span class="form-hint d-block">
                                    Permite que los huéspedes generen solicitudes desde QR.
                                </span>
                            </span>
                        </label>

                        <label class="form-check form-switch mb-3">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                name="panel_enabled"
                                value="1"
                                @checked(old('panel_enabled', $hotel->panel_enabled))>

                            <span class="form-check-label">
                                <strong>Panel recepción activo</strong>
                                <span class="form-hint d-block">
                                    Permite que recepción entre al panel del hotel.
                                </span>
                            </span>
                        </label>

                        <label class="form-check form-switch mb-0">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                name="taxi_enabled"
                                value="1"
                                @checked(old('taxi_enabled', $hotel->taxi_enabled))>

                            <span class="form-check-label">
                                <strong>Taxi activo</strong>
                                <span class="form-hint d-block">
                                    Muestra la opción taxi en los QRs permitidos.
                                </span>
                            </span>
                        </label>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-body">
                        <button class="btn btn-primary w-100" type="submit">
                            <i class="ti ti-device-floppy me-1"></i>
                            {{ $hotel->exists ? 'Guardar cambios' : 'Crear hotel' }}
                        </button>

                        @if($hotel->exists)
                            <a class="btn btn-outline-secondary w-100 mt-2"
                               href="{{ route('sysapp.hotels.qr-points.index', $hotel) }}">
                                <i class="ti ti-qrcode me-1"></i>
                                Configurar QRs
                            </a>

                            <a class="btn btn-outline-secondary w-100 mt-2"
                               href="{{ route('hotel.login', $hotel) }}"
                               target="_blank">
                                <i class="ti ti-external-link me-1"></i>
                                Abrir panel hotel
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection