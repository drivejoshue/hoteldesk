@extends('layouts.hoteldesk-public')

@section('title', 'Servicio no disponible')

@section('content')
    <div class="text-center py-3">
        <span class="avatar avatar-xl bg-yellow-lt text-yellow mb-3">
            <i class="ti ti-alert-triangle" style="font-size: 34px;"></i>
        </span>

        <h1 class="h2 mb-2">Servicio no disponible</h1>

        <p class="text-secondary mb-0">
            Por el momento este servicio no está activo.<br>
            Contacta a recepción directamente.
        </p>
    </div>
@endsection