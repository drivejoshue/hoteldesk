@extends('layouts.hoteldesk-public')

@section('title', 'Servicio no disponible')

@section('content')
    <section class="hd-card hd-success-screen">
        <div class="hd-success-icon" style="background: var(--hd-warning-bg); color: var(--hd-warning);">
            <i class="ti ti-alert-triangle"></i>
        </div>

        <h1 class="hd-title">Servicio no disponible</h1>

        <p class="hd-text-muted" style="line-height: 1.5;">
            Por el momento este servicio no está activo.
            Contacta a recepción directamente.
        </p>
    </section>
@endsection