@extends('layouts.hotel-panel')

@section('title', $hotel->name . ' · Solicitar QR')
@section('subtitle', 'Solicitar nuevo punto QR')

@section('topbar-actions')
    @include('hotel-panel.partials.topbar-actions', ['showSound' => false])
@endsection

@section('content')
    <main class="hd-dashboard">
        @if($errors->any())
            <div class="hd-alert hd-alert-danger">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <section class="hd-card hd-card-pad">
            <div class="hd-eyebrow">Nuevo punto QR</div>
            <h1 class="hd-title">Solicitar QR</h1>

            <form method="POST" action="{{ route('hotel.qr-requests.store', $hotel) }}" style="margin-top: 20px;">
                @csrf

                <div class="hd-grid hd-grid-2">
                    <div class="hd-field">
                        <label class="hd-label">Nombre del punto</label>
                        <input class="hd-input" name="label" value="{{ old('label') }}" required placeholder="Ej. Gimnasio, Habitación 205, Terraza">
                    </div>

                    <div class="hd-field">
                        <label class="hd-label">Tipo</label>
                        <select class="hd-select" name="type" required>
                            <option value="room">Habitación</option>
                            <option value="lobby">Lobby</option>
                            <option value="area">Área</option>
                            <option value="restaurant">Restaurante</option>
                            <option value="parking">Estacionamiento</option>
                            <option value="reception">Recepción</option>
                            <option value="other">Otro</option>
                        </select>
                    </div>

                    <div class="hd-field">
                        <label class="hd-label">Piso / zona</label>
                        <input class="hd-input" name="floor" value="{{ old('floor') }}" placeholder="Ej. PB, Piso 2, Terraza">
                    </div>

                    <div class="hd-field">
                        <label class="hd-label">Modo</label>
                        <select class="hd-select" name="mode" required>
                            <option value="menu">Menú completo</option>
                            <option value="limited">Menú limitado</option>
                            <option value="direct">Solicitud directa</option>
                        </select>
                    </div>
                </div>

                <div class="hd-field">
                    <label class="hd-label">Solicitud directa</label>
                    <select class="hd-select" name="fixed_request_type">
                        <option value="">No aplica</option>
                        @foreach($requestTypes as $key => $type)
                            <option value="{{ $key }}">{{ $type['label'] }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="hd-field">
                    <label class="hd-label">Solicitudes permitidas para menú limitado</label>

                    <div class="hd-grid hd-grid-2">
                        @foreach($requestTypes as $key => $type)
                            <label style="display:flex; align-items:center; gap:8px; font-weight:800;">
                                <input type="checkbox" name="allowed_request_types[]" value="{{ $key }}" style="width:auto;">
                                {{ $type['icon'] }} {{ $type['label'] }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="hd-field">
                    <label class="hd-label">Nota para SysApp</label>
                    <textarea class="hd-textarea" name="note" maxlength="500" placeholder="Ej. Queremos ponerlo en la entrada del gimnasio">{{ old('note') }}</textarea>
                </div>

                <button class="hd-btn hd-btn-primary hd-btn-full" type="submit">
                    <i class="ti ti-send"></i>
                    Enviar solicitud
                </button>
            </form>
        </section>
    </main>
@endsection