<div class="hd-panel-actions">
    @if($showSound ?? false)
        <div class="hd-sound-actions">
            <span class="hd-sound-pill hd-chip-compact" id="soundStatus">
                <i class="ti ti-volume-off"></i>
                <span>Sin sonido</span>
            </span>

            <button class="hd-btn hd-btn-primary hd-chip-btn" id="enableSoundBtn" type="button">
                <i class="ti ti-volume"></i>
                <span>Activar</span>
            </button>
        </div>
    @endif

    <nav class="hd-panel-nav">
        <a class="hd-btn hd-btn-soft hd-chip-btn" href="{{ route('hotel.dashboard', $hotel) }}">
            <i class="ti ti-layout-dashboard"></i>
            <span>Inicio</span>
        </a>

        <a class="hd-btn hd-btn-soft hd-chip-btn" href="{{ route('hotel.qr-points.index', $hotel) }}">
            <i class="ti ti-qrcode"></i>
            <span>QRs</span>
        </a>

        <a class="hd-btn hd-btn-soft hd-chip-btn" href="{{ route('hotel.qr-requests.index', $hotel) }}">
            <i class="ti ti-plus"></i>
            <span>Nuevo QR</span>
        </a>

        <a class="hd-btn hd-btn-soft hd-chip-btn" href="{{ route('hotel.settings.pin.edit', $hotel) }}">
            <i class="ti ti-key"></i>
            <span>PIN</span>
        </a>

        <form method="POST" action="{{ route('hotel.logout', $hotel) }}">
            @csrf
            <button class="hd-btn hd-btn-soft hd-chip-btn" type="submit">
                <i class="ti ti-logout"></i>
                <span>Salir</span>
            </button>
        </form>
    </nav>
</div>