@if($showSound ?? false)
    <div class="d-flex align-items-center gap-2 flex-shrink-0">
        <span class="badge bg-green-lt text-green d-none d-lg-inline-flex align-items-center" id="soundStatus">
            <i class="ti ti-volume-off me-1"></i>
            <span>Sin sonido</span>
        </span>

        <button
            class="btn btn-primary btn-sm d-inline-flex align-items-center justify-content-center flex-shrink-0"
            id="enableSoundBtn"
            type="button"
            title="Activar sonido"
            aria-label="Activar sonido"
        >
            <i class="ti ti-volume me-lg-1"></i>
            <span class="d-none d-lg-inline">Activar</span>
        </button>
    </div>
@endif

<div class="dropdown flex-shrink-0">
    <button
        class="btn btn-outline-secondary btn-sm dropdown-toggle d-inline-flex align-items-center"
        type="button"
        data-bs-toggle="dropdown"
        aria-expanded="false"
    >
        <i class="ti ti-menu-2 me-lg-1"></i>
        <span class="d-none d-sm-inline">Menú</span>
    </button>

    <div class="dropdown-menu dropdown-menu-end shadow">
        <div class="dropdown-header">
            {{ $hotel->name }}
        </div>

        <a class="dropdown-item" href="{{ route('hotel.dashboard', $hotel) }}">
            <i class="ti ti-layout-dashboard me-2"></i>
            Inicio
        </a>

        <a class="dropdown-item" href="{{ route('hotel.requests.history', $hotel) }}">
            <i class="ti ti-history me-2"></i>
            Historial
        </a>

        <a class="dropdown-item" href="{{ route('hotel.reports.index', $hotel) }}">
            <i class="ti ti-chart-bar me-2"></i>
            Reportes
        </a>

        <a class="dropdown-item" href="{{ route('hotel.qr-points.index', $hotel) }}">
            <i class="ti ti-qrcode me-2"></i>
            Mis QRs
        </a>

        <a class="dropdown-item" href="{{ route('hotel.qr-requests.index', $hotel) }}">
            <i class="ti ti-plus me-2"></i>
            Solicitar QR
        </a>

        <a class="dropdown-item" href="{{ route('hotel.settings.pin.edit', $hotel) }}">
            <i class="ti ti-key me-2"></i>
            Cambiar PIN
        </a>

        <div class="dropdown-divider"></div>

        <form method="POST" action="{{ route('hotel.logout', $hotel) }}">
            @csrf
            <button class="dropdown-item text-danger" type="submit">
                <i class="ti ti-logout me-2"></i>
                Salir del panel
            </button>
        </form>
    </div>
</div>