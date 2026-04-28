@extends('layouts.hotel-panel')

@section('title', $hotel->name . ' · Administrador')
@section('subtitle', 'Acceso administrativo')

@section('content')
    <main class="container-tight py-4">
        <div class="card card-md shadow-sm">
            <div class="card-body">
                <div class="text-center mb-4">
                    <span class="avatar avatar-xl bg-primary-lt text-primary mb-3">
                        <i class="ti ti-shield-lock" style="font-size: 34px;"></i>
                    </span>

                    <h1 class="h2 mb-1">Acceso de administrador</h1>

                    <p class="text-secondary mb-0">
                        Ingresa el PIN admin para acceder a reportes, QRs y configuración avanzada.
                    </p>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('hotel.admin-pin.verify', $hotel) }}">
                    @csrf

                    <input type="hidden" name="intended" value="{{ session('hotel_admin_intended_url') }}">

                    <div class="mb-3">
                        <label class="form-label" for="pin">PIN administrativo</label>
                        <input
                            id="pin"
                            name="pin"
                            type="password"
                            inputmode="numeric"
                            maxlength="12"
                            class="form-control form-control-lg text-center"
                            autocomplete="off"
                            autofocus
                            required
                        >
                    </div>

                    <button class="btn btn-primary btn-lg w-100" type="submit">
                        <i class="ti ti-lock-open me-2"></i>
                        Entrar como administrador
                    </button>
                </form>

                <div class="text-center mt-3">
                    <a href="{{ route('hotel.dashboard', $hotel) }}" class="text-secondary">
                        Volver a recepción
                    </a>
                </div>
            </div>
        </div>
    </main>
@endsection