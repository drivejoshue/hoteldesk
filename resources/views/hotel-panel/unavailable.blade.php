@extends('layouts.hoteldesk-public')

@section('title', 'Panel no disponible')

@section('content')
    <section class="hd-card hd-success-screen">
        <div class="hd-success-icon" style="background: var(--hd-warning-bg); color: var(--hd-warning);">
            <i class="ti ti-lock-cancel"></i>
        </div>

        <h1 class="hd-title">Panel no disponible</h1>

        <p class="hd-text-muted" style="line-height: 1.5;">
            El panel de este hotel no está activo por el momento.
        </p>
    </section>
@endsection