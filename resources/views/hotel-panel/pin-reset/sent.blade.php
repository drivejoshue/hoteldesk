@extends('layouts.hoteldesk-public')

@section('title', 'Solicitud enviada')

@section('content')
    <section class="hd-card hd-success-screen">
        <div class="hd-success-icon">
            <i class="ti ti-check"></i>
        </div>

        <h1 class="hd-title">Solicitud enviada</h1>

        <p class="hd-text-muted" style="line-height: 1.5;">
            SysApp revisará la solicitud de restablecimiento de PIN.
            Contacta al administrador o espera confirmación.
        </p>

        <a class="hd-btn hd-btn-primary" href="{{ route('hotel.login', $hotel) }}" style="margin-top: 16px;">
            Volver al login
        </a>
    </section>
@endsection