@extends('sysapp.layout')

@section('title', ($hotel->exists ? 'Editar hotel' : 'Crear hotel') . ' · HotelDesk Lite')

@section('content')
    <div class="page-header d-print-none mb-3">
        <div class="row align-items-center">
            <div class="col">
                <div class="page-pretitle">SysApp · HotelDesk Lite</div>

                <h2 class="page-title mb-1">
                    {{ $hotel->exists ? 'Editar hotel' : 'Crear hotel' }}
                </h2>

                <div class="text-secondary">
                    Configuración del hotel, accesos, servicios activos y licencia.
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

    @if(session('success'))
        <div class="alert alert-success">
            <i class="ti ti-check me-2"></i>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <div class="fw-bold mb-1">
                Revise la información capturada.
            </div>

            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Licencia / prueba --}}
    @if($hotel->exists)
        <div class="card mb-3">
            <div class="card-header">
                <div>
                    <h3 class="card-title mb-0">
                        <i class="ti ti-shield-check me-2 text-secondary"></i>
                        Licencia / prueba
                    </h3>
                    <div class="text-secondary small">
                        Administración del periodo de prueba, mensualidad o licencia anual.
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <div class="subheader">Plan</div>
                        <div class="fw-bold">{{ $hotel->planLabel() }}</div>
                    </div>

                    <div class="col-md-3">
                        <div class="subheader">Estado</div>
                        <span class="badge {{ $hotel->licenseStatusClass() }}">
                            {{ $hotel->licenseLabel() }}
                        </span>
                    </div>

                    <div class="col-md-3">
                        <div class="subheader">Ciclo</div>
                        <div class="fw-bold">{{ $hotel->billingCycleLabel() }}</div>
                    </div>

                    <div class="col-md-3">
                        <div class="subheader">Vence</div>
                        <div class="fw-bold">
                            {{ $hotel->licenseEndsAt() ? $hotel->licenseEndsAt()->format('d/m/Y H:i') : 'Sin fecha' }}
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <div class="subheader">Mensual Lite</div>
                        <div class="fw-bold">{{ $hotel->formattedMonthlyPrice() }}</div>
                    </div>

                    <div class="col-md-3">
                        <div class="subheader">Anual Lite</div>
                        <div class="fw-bold">{{ $hotel->formattedAnnualPrice() }}</div>
                    </div>

                    <div class="col-md-6">
                        <div class="alert alert-info mb-0 py-2">
                            <i class="ti ti-info-circle me-1"></i>
                            La prueba no inicia al prepararla. El conteo inicia cuando el hotel acepta los términos al entrar por primera vez.
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    <form method="POST" action="{{ route('sysapp.hotels.license.trial', $hotel) }}">
                        @csrf
                        <button class="btn btn-primary" type="submit">
                            <i class="ti ti-player-play me-1"></i>
                            Preparar prueba + PIN iniciales
                        </button>
                    </form>

                    <form method="POST" action="{{ route('sysapp.hotels.license.monthly-lite', $hotel) }}">
                        @csrf
                        <button class="btn btn-outline-success" type="submit">
                            <i class="ti ti-calendar-dollar me-1"></i>
                            Activar mensual Lite
                        </button>
                    </form>

                    <form method="POST" action="{{ route('sysapp.hotels.license.annual-lite', $hotel) }}">
                        @csrf
                        <button class="btn btn-outline-success" type="submit">
                            <i class="ti ti-calendar-stats me-1"></i>
                            Activar anual Lite
                        </button>
                    </form>

                    <form method="POST"
                          action="{{ route('sysapp.hotels.license.suspend', $hotel) }}"
                          onsubmit="return confirm('¿Suspender la licencia de este hotel?');">
                        @csrf
                        <button class="btn btn-outline-danger" type="submit">
                            <i class="ti ti-ban me-1"></i>
                            Suspender
                        </button>
                    </form>

                    <a class="btn btn-outline-secondary ms-md-auto"
                       href="{{ route('hotel.license.index', $hotel) }}"
                       target="_blank">
                        <i class="ti ti-external-link me-1"></i>
                        Ver licencia pública
                    </a>
                </div>

                <div class="text-secondary small mt-3">
                    Al preparar la prueba se asignan PIN iniciales: recepción <strong>1234</strong> y admin <strong>4321</strong>.
                    El hotel podrá cambiarlos después desde el menú del panel.
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-info mb-3">
            <div class="d-flex">
                <div>
                    <i class="ti ti-info-circle me-2"></i>
                </div>
                <div>
                    Primero crea el hotel. Después podrás preparar la prueba de 14 días, asignar PIN iniciales y configurar sus QRs.
                </div>
            </div>
        </div>
    @endif

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
                        <div>
                            <h3 class="card-title mb-0">
                                <i class="ti ti-building me-2 text-secondary"></i>
                                Datos generales
                            </h3>
                            <div class="text-secondary small">
                                Información principal del hotel y acceso al panel.
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12 col-md-7">
                                <label class="form-label">
                                    Nombre del hotel
                                    <span class="text-danger">*</span>
                                </label>

                                <input
                                    class="form-control @error('name') is-invalid @enderror"
                                    name="name"
                                    value="{{ old('name', $hotel->name) }}"
                                    required
                                    placeholder="Ej. AutoHotel Ilussion">

                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-5">
                                <label class="form-label">Slug</label>

                                <div class="input-group">
                                    <span class="input-group-text">/h/</span>
                                    <input
                                        class="form-control @error('slug') is-invalid @enderror"
                                        name="slug"
                                        value="{{ old('slug', $hotel->slug) }}"
                                        placeholder="autohotel-ilussion">
                                </div>

                                <div class="form-hint">
                                    Se usa para el acceso del panel del hotel. Si se deja vacío, se genera desde el nombre.
                                </div>

                                @error('slug')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="form-label">
                                    PIN recepción
                                    @if($hotel->exists)
                                        <span class="text-secondary">(opcional)</span>
                                    @else
                                        <span class="text-danger">*</span>
                                    @endif
                                </label>

                                <input
                                    class="form-control @error('pin') is-invalid @enderror"
                                    name="pin"
                                    type="text"
                                    inputmode="numeric"
                                    value="{{ old('pin', $hotel->exists ? '' : '1234') }}"
                                    {{ $hotel->exists ? '' : 'required' }}
                                    placeholder="{{ $hotel->exists ? 'No cambiar' : '1234' }}">

                                <div class="form-hint">
                                    Uso diario de recepción.
                                </div>

                                @error('pin')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="form-label">
                                    PIN admin
                                    <span class="text-secondary">(opcional)</span>
                                </label>

                                <input
                                    class="form-control @error('admin_pin') is-invalid @enderror"
                                    name="admin_pin"
                                    type="text"
                                    inputmode="numeric"
                                    value="{{ old('admin_pin', $hotel->exists ? '' : '4321') }}"
                                    placeholder="{{ $hotel->exists ? 'No cambiar' : '4321' }}">

                                <div class="form-hint">
                                    Protege reportes, QRs y configuración avanzada.
                                </div>

                                @error('admin_pin')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="form-label">
                                    Estado
                                    <span class="text-danger">*</span>
                                </label>

                                <select class="form-select @error('status') is-invalid @enderror" name="status" required>
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

                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-3">
                                <label class="form-label">Color principal</label>

                                <input
                                    class="form-control form-control-color @error('primary_color') is-invalid @enderror"
                                    name="primary_color"
                                    type="color"
                                    value="{{ old('primary_color', $hotel->primary_color ?: '#0F6CBD') }}">

                                @error('primary_color')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="form-label">Teléfono</label>

                                <input
                                    class="form-control @error('phone') is-invalid @enderror"
                                    name="phone"
                                    value="{{ old('phone', $hotel->phone) }}"
                                    placeholder="Ej. 229...">

                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-5">
                                <label class="form-label">Correo</label>

                                <input
                                    class="form-control @error('email') is-invalid @enderror"
                                    name="email"
                                    type="email"
                                    value="{{ old('email', $hotel->email) }}"
                                    placeholder="recepcion@hotel.com">

                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">URL Service Point Orbana</label>

                                <input
                                    class="form-control @error('service_point_url') is-invalid @enderror"
                                    name="service_point_url"
                                    value="{{ old('service_point_url', $hotel->service_point_url) }}"
                                    placeholder="https://dispatch.orbana.mx/sp/...">

                                <div class="form-hint">
                                    Opcional. Se usará como acceso rápido cuando recepción reciba una solicitud de taxi.
                                </div>

                                @error('service_point_url')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">Dirección</label>

                                <textarea
                                    class="form-control @error('address') is-invalid @enderror"
                                    name="address"
                                    rows="2"
                                    placeholder="Dirección del hotel">{{ old('address', $hotel->address) }}</textarea>

                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        <div>
                            <h3 class="card-title mb-0">
                                <i class="ti ti-photo me-2 text-secondary"></i>
                                Logo
                            </h3>
                            <div class="text-secondary small">
                                Imagen del establecimiento para el panel y materiales internos.
                            </div>
                        </div>
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
                                    class="form-control @error('logo') is-invalid @enderror"
                                    name="logo"
                                    type="file"
                                    accept="image/*">

                                @if($hotel->logo_path)
                                    <div class="form-hint">
                                        Logo actual: {{ $hotel->logo_path }}
                                    </div>
                                @endif

                                @error('logo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-4">
                <div class="card">
                    <div class="card-header">
                        <div>
                            <h3 class="card-title mb-0">
                                <i class="ti ti-adjustments me-2 text-secondary"></i>
                                Servicios activos
                            </h3>
                            <div class="text-secondary small">
                                Controla qué módulos quedan visibles para el hotel.
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <input type="hidden" name="public_requests_enabled" value="0">
                        <label class="form-check form-switch mb-3">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                name="public_requests_enabled"
                                value="1"
                                @checked((string) old('public_requests_enabled', $hotel->public_requests_enabled ? '1' : '0') === '1')>

                            <span class="form-check-label">
                                <strong>QR público activo</strong>
                                <span class="form-hint d-block">
                                    Permite que los huéspedes generen solicitudes desde QR.
                                </span>
                            </span>
                        </label>

                        <input type="hidden" name="panel_enabled" value="0">
                        <label class="form-check form-switch mb-3">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                name="panel_enabled"
                                value="1"
                                @checked((string) old('panel_enabled', $hotel->panel_enabled ? '1' : '0') === '1')>

                            <span class="form-check-label">
                                <strong>Panel recepción activo</strong>
                                <span class="form-hint d-block">
                                    Permite que recepción entre al panel del hotel.
                                </span>
                            </span>
                        </label>

                        <input type="hidden" name="taxi_enabled" value="0">
                        <label class="form-check form-switch mb-0">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                name="taxi_enabled"
                                value="1"
                                @checked((string) old('taxi_enabled', $hotel->taxi_enabled ? '1' : '0') === '1')>

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
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="ti ti-checkup-list me-2 text-secondary"></i>
                            Acciones
                        </h3>
                    </div>

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
                               href="{{ route('sysapp.hotels.print-access', $hotel) }}"
                               target="_blank">
                                <i class="ti ti-printer me-1"></i>
                                Imprimir acceso
                            </a>

                            <a class="btn btn-outline-secondary w-100 mt-2"
                               href="{{ route('hotel.login', $hotel) }}"
                               target="_blank">
                                <i class="ti ti-external-link me-1"></i>
                                Abrir panel hotel
                            </a>
                        @else
                            <div class="text-secondary small mt-3">
                                Después de crear el hotel podrás configurar QRs y preparar su prueba de 14 días.
                            </div>
                        @endif
                    </div>
                </div>

                @if($hotel->exists)
                    <div class="card mt-3">
                        <div class="card-body">
                            <div class="subheader">Acceso del hotel</div>
                            <div class="text-truncate">
                                <a href="{{ route('hotel.login', $hotel) }}" target="_blank">
                                    {{ route('hotel.login', $hotel) }}
                                </a>
                            </div>

                            <div class="text-secondary small mt-2">
                                Usa este enlace para enviarlo al establecimiento después de preparar la prueba.
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </form>
@endsection