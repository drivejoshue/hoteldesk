@if($showSound ?? false)
    <div class="d-none d-md-flex align-items-center gap-2">
        <span class="badge bg-green-lt text-green" id="soundStatus">
            <i class="ti ti-volume-off me-1"></i>
            <span>Sin sonido</span>
        </span>

        <button class="btn btn-primary btn-sm" id="enableSoundBtn" type="button">
            <i class="ti ti-volume me-1"></i>
            <span>Activar</span>
        </button>
    </div>
@endif

<div class="dropdown">
    <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="ti ti-menu-2 me-1"></i>
        Menú
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