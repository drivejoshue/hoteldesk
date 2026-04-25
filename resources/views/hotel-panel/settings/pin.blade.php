@extends('layouts.hotel-panel')

@section('title', $hotel->name . ' · Cambiar PIN')
@section('subtitle', 'Seguridad del panel')

@section('topbar-actions')
    @include('hotel-panel.partials.topbar-actions', ['showSound' => false])
@endsection

@section('content')
    <main class="hd-dashboard">
        @if(session('success'))
            <div class="hd-alert hd-alert-success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="hd-alert hd-alert-danger">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <section class="hd-card hd-card-pad">
            <div class="hd-eyebrow">Panel del hotel</div>
            <h1 class="hd-title">Cambiar PIN</h1>

            <form method="POST" action="{{ route('hotel.settings.pin.update', $hotel) }}" style="margin-top: 20px;">
                @csrf
                @method('PUT')

                <div class="hd-field">
                    <label class="hd-label">PIN actual</label>
                    <input class="hd-input" name="current_pin" type="password" inputmode="numeric" maxlength="12" required>
                </div>

                <div class="hd-field">
                    <label class="hd-label">Nuevo PIN</label>
                    <input class="hd-input" name="pin" type="password" inputmode="numeric" maxlength="12" required>
                </div>

                <div class="hd-field">
                    <label class="hd-label">Confirmar nuevo PIN</label>
                    <input class="hd-input" name="pin_confirmation" type="password" inputmode="numeric" maxlength="12" required>
                </div>

                <button class="hd-btn hd-btn-primary hd-btn-full" type="submit">
                    <i class="ti ti-key"></i>
                    Actualizar PIN
                </button>
            </form>
        </section>
    </main>
@endsection