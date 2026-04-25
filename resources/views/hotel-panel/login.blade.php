@extends('layouts.hoteldesk-public')

@section('title', $hotel->name . ' · Panel recepción')

@section('content')
    <section class="hd-card hd-card-pad">
        <div class="hd-eyebrow">Panel de recepción</div>
        <h1 class="hd-title">{{ $hotel->name }}</h1>

        @if($errors->any())
            <div class="hd-alert hd-alert-danger" style="margin-top: 18px;">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('hotel.pin.login', $hotel) }}" style="margin-top: 20px;">
            @csrf

            <div class="hd-field">
                <label class="hd-label" for="pin">PIN de recepción</label>
                <input class="hd-input"
                       id="pin"
                       name="pin"
                       type="password"
                       inputmode="numeric"
                       maxlength="12"
                       autocomplete="off"
                       autofocus
                       style="font-size: 24px; text-align: center; letter-spacing: .25em;">
            </div>

            <button class="hd-btn hd-btn-primary hd-btn-full" type="submit">
                <i class="ti ti-lock-open"></i>
                Entrar al panel
            </button>
        </form>

        <div class="hd-bottom-hint">
            Acceso interno para recepción del hotel.<br>
            ¿Olvidaste el PIN?
            <a href="{{ route('hotel.pin-reset.create', $hotel) }}"
               style="font-weight: 900; color: var(--hd-primary); text-decoration: none;">
                Solicitar restablecimiento
            </a>
        </div>
    </section>
@endsection