@extends('layouts.hoteldesk-access')

@section('title', 'Acceso recepción · HotelDesk Lite by Orbana')

@section('content')
<style>
    /* Estilos específicos para el hero y acceso */
    .hd-access-hero {
        position: relative;
        width: 100%;
        min-height: 620px;
        overflow: hidden;
        display: flex;
        align-items: center;
    }

 .hd-access-hero-bg {
    position: absolute;
    inset: 0;
    z-index: 0;
    background:
        linear-gradient(
            90deg,
            rgba(245, 247, 251, .72) 0%,
            rgba(245, 247, 251, .58) 30%,
            rgba(245, 247, 251, .28) 52%,
            rgba(245, 247, 251, .08) 72%,
            rgba(245, 247, 251, .04) 100%
        ),
        var(--hd-access-hero-image) center center / cover no-repeat;
}

    .hd-access-hero-inner {
        position: relative;
        z-index: 1;
        width: 100%;
        max-width: 1320px;
        margin: 0 auto;
        padding: 64px 24px;
    }

    /* Texto más delgado */
    .hd-access-kicker {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 100px;
        background: rgba(15, 108, 189, 0.08);
        backdrop-filter: blur(8px);
        color: var(--hd-access-primary);
        font-weight: 450;
        font-size: 12px;
        letter-spacing: 0.3px;
        margin-bottom: 20px;
    }

    .hd-access-title {
        margin: 0;
        max-width: 700px;
        color: #1a2c3e;
        font-size: clamp(36px, 5vw, 64px);
        line-height: 1.05;
        letter-spacing: -0.04em;
        font-weight: 320;
    }

    .hd-access-title strong {
        font-weight: 550;
        background: linear-gradient(135deg, #0F6CBD, #00A6D6);
        background-clip: text;
        -webkit-background-clip: text;
        color: transparent;
    }

    .hd-access-subtitle {
        margin-top: 20px;
        max-width: 620px;
        color: #3a4e5e;
        font-size: 16px;
        line-height: 1.55;
        font-weight: 350;
    }

    .hd-access-benefits {
        display: flex;
        flex-wrap: wrap;
        gap: 12px 24px;
        margin-top: 32px;
        max-width: 700px;
    }

    .hd-access-benefit {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #2c3f4c;
        font-weight: 420;
        font-size: 13px;
        background: rgba(255, 255, 255, 0.5);
        backdrop-filter: blur(4px);
        padding: 6px 14px;
        border-radius: 40px;
    }

    .hd-access-benefit i {
        width: 28px;
        height: 28px;
        border-radius: 10px;
        display: grid;
        place-items: center;
        background: rgba(15, 108, 189, 0.1);
        color: var(--hd-access-primary);
        flex: 0 0 auto;
        font-size: 14px;
    }

    /* Card más translúcida y elegante */
    .hd-access-card {
        border: 0.5px solid rgba(255, 255, 255, 0.7);
        border-radius: 32px;
        background: rgba(255, 255, 255, 0.55);
        backdrop-filter: blur(24px);
        box-shadow: 0 25px 50px -18px rgba(0, 0, 0, 0.2);
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .hd-access-card:hover {
        background: rgba(255, 255, 255, 0.68);
        border-color: rgba(255, 255, 255, 0.9);
    }

    .hd-access-card .card-body {
        padding: 32px;
    }

    .hd-access-card-icon {
        width: 70px;
        height: 70px;
        border-radius: 20px;
        display: grid;
        place-items: center;
        background: rgba(15, 108, 189, 0.1);
        color: var(--hd-access-primary);
        margin: 0 auto 18px;
    }

    .hd-access-card-icon i {
        font-size: 32px;
    }

    .hd-access-card .h2 {
        font-size: 1.6rem;
        font-weight: 480;
        letter-spacing: -0.02em;
        color: #1a2c3e;
    }

    .hd-access-card .text-secondary {
        color: #5a7282 !important;
        font-weight: 350;
        font-size: 13px;
    }

    .form-label {
        font-size: 12px;
        font-weight: 480;
        color: #2c3f4c;
        margin-bottom: 6px;
    }

    .input-group {
        background: rgba(255, 255, 255, 0.75);
        border-radius: 16px;
        border: 0.5px solid rgba(15, 108, 189, 0.12);
        transition: all 0.2s;
    }

    .input-group:focus-within {
        background: rgba(255, 255, 255, 0.95);
        border-color: var(--hd-access-primary);
        box-shadow: 0 0 0 3px rgba(15, 108, 189, 0.08);
    }

    .input-group-text {
        background: transparent;
        border: none;
        color: #8da0ae;
        padding: 0 0 0 16px;
    }

    .form-control {
        background: transparent;
        border: none;
        padding: 12px 16px 12px 8px;
        font-size: 14px;
        font-weight: 400;
        font-family: inherit;
    }

    .form-control:focus {
        outline: none;
        box-shadow: none;
    }

    .form-hint {
        font-size: 11px;
        font-weight: 350;
        color: #7a8e9e;
        margin-top: 8px;
    }

    .btn-primary {
        background: linear-gradient(135deg, #0F6CBD, #00A6D6);
        border: none;
        border-radius: 16px;
        padding: 12px;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.25s;
    }

    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 20px rgba(15, 108, 189, 0.3);
    }

    .hd-access-secure-note {
        color: #7a8e9e;
        font-size: 11px;
        font-weight: 400;
        text-align: center;
        margin-top: 14px;
    }

    /* Sección de pasos */
    .hd-access-steps {
        width: 100%;
        background: linear-gradient(180deg, rgba(240, 244, 248, 0.6), rgba(240, 244, 248, 0.9));
        padding: 48px 0 56px;
    }

    .hd-access-steps-inner {
        max-width: 1320px;
        margin: 0 auto;
        padding: 0 24px;
    }

    .hd-access-step {
        height: 100%;
        border: 0.5px solid rgba(15, 23, 42, 0.06);
        border-radius: 24px;
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(8px);
        padding: 24px;
        transition: all 0.2s;
    }

    .hd-access-step:hover {
        background: rgba(255, 255, 255, 0.85);
    }

    .hd-access-step-icon {
        width: 48px;
        height: 48px;
        border-radius: 16px;
        display: grid;
        place-items: center;
        background: rgba(15, 108, 189, 0.1);
        color: var(--hd-access-primary);
        margin-bottom: 16px;
    }

    .hd-access-step-icon i {
        font-size: 24px;
    }

    .hd-access-step .h3 {
        font-size: 1.2rem;
        font-weight: 500;
        letter-spacing: -0.02em;
        color: #1a2c3e;
        margin-bottom: 10px;
    }

    .hd-access-step .text-secondary {
        color: #5a7282 !important;
        font-weight: 350;
        font-size: 13px;
        line-height: 1.55;
    }

    @media (max-width: 991px) {
        .hd-access-hero {
            min-height: auto;
        }

        .hd-access-hero-bg {
            background: 
                linear-gradient(
                    180deg,
                    rgba(240, 244, 248, 0.96) 0%,
                    rgba(240, 244, 248, 0.88) 50%,
                    rgba(240, 244, 248, 0.75) 100%
                ),
                var(--hd-access-hero-image) center center / cover no-repeat;
        }

        .hd-access-benefits {
            gap: 10px;
        }

        .hd-access-card {
            margin-top: 28px;
        }
    }

    @media (max-width: 575px) {
        .hd-access-hero-inner,
        .hd-access-steps-inner {
            padding-left: 16px;
            padding-right: 16px;
        }

        .hd-access-title {
            font-size: 34px;
        }

        .hd-access-subtitle {
            font-size: 14px;
        }

        .hd-access-card .card-body {
            padding: 24px;
        }
    }
</style>

<main class="hd-access-hero">
    <div class="hd-access-hero-bg"></div>

    <div class="hd-access-hero-inner">
        <div class="row align-items-center g-4">
            <div class="col-12 col-lg-7">
                <div class="hd-access-kicker">
                    <i class="ti ti-sparkles"></i>
                    Atención rápida por QR para hoteles
                </div>

                <h1 class="hd-access-title">
                    Menos llamadas. <strong>Más orden.</strong><br>
                    Mejor atención.
                </h1>

                <p class="hd-access-subtitle">
                    HotelDesk Lite permite que tus huéspedes soliciten apoyo desde un código QR:
                    toallas, limpieza, mantenimiento, amenidades, taxi y más. Recepción recibe
                    todo en un panel simple, rápido y fácil de usar.
                </p>

                <div class="hd-access-benefits">
                    <div class="hd-access-benefit">
                        <i class="ti ti-qrcode"></i>
                        Solicitudes sin instalar apps
                    </div>

                    <div class="hd-access-benefit">
                        <i class="ti ti-bell"></i>
                        Avisos al instante
                    </div>

                    <div class="hd-access-benefit">
                        <i class="ti ti-history"></i>
                        Historial y seguimiento
                    </div>
                </div>
            </div>

          
        </div>
    </div>
</main>

<section class="hd-access-steps">
    <div class="hd-access-steps-inner">
        <div class="row g-3">
            <div class="col-12 col-md-4">
                <div class="hd-access-step">
                    <div class="hd-access-step-icon">
                        <i class="ti ti-scan"></i>
                    </div>

                    <h3 class="h3 mb-2">1. El huésped escanea</h3>

                    <p class="text-secondary mb-0">
                        Coloca QR en habitaciones, recepción, lobby o áreas comunes.
                        El huésped solo escanea y elige lo que necesita.
                    </p>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="hd-access-step">
                    <div class="hd-access-step-icon">
                        <i class="ti ti-device-tablet"></i>
                    </div>

                    <h3 class="h3 mb-2">2. Recepción controla</h3>

                    <p class="text-secondary mb-0">
                        Las solicitudes llegan a un panel simple para marcar como pendientes,
                        en proceso, resueltas o canceladas.
                    </p>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="hd-access-step">
                    <div class="hd-access-step-icon">
                        <i class="ti ti-chart-bar"></i>
                    </div>

                    <h3 class="h3 mb-2">3. El hotel mejora</h3>

                    <p class="text-secondary mb-0">
                        Reduce llamadas, evita olvidos y mejora la experiencia del huésped
                        con una operación más clara y moderna.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection