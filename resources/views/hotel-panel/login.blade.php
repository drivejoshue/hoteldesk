@extends('layouts.hoteldesk-public')

@section('title', $hotel->name . ' · Panel recepción')

@section('content')
    <div class="text-center mb-4">
        <div class="text-secondary text-uppercase small fw-bold mb-1">
            Panel de recepción
        </div>

        <h1 class="h2 mb-1">
            {{ $hotel->name }}
        </h1>

        <div class="text-secondary">
            Ingresa el PIN para abrir el panel del hotel.
        </div>
    </div>

    <form method="POST" action="{{ route('hotel.pin.login', $hotel) }}">
        @csrf

        <div class="mb-3">
            <label class="form-label" for="pin">
                PIN de recepción
            </label>

            <input
                class="form-control form-control-lg text-center"
                id="pin"
                name="pin"
                type="password"
                inputmode="numeric"
                maxlength="12"
                autocomplete="off"
                autofocus
                style="letter-spacing: .25em; font-weight: 800;">
        </div>

        <button class="btn btn-primary btn-lg w-100" type="submit">
            <i class="ti ti-lock-open me-2"></i>
            Entrar al panel
        </button>
    </form>

    <div class="text-center text-secondary small mt-4">
        Acceso interno para recepción del hotel.

        <div class="mt-2">
            ¿Olvidaste el PIN?
            <a href="{{ route('hotel.pin-reset.create', $hotel) }}" class="fw-bold">
                Solicitar restablecimiento
            </a>
        </div>
    </div>
@endsection