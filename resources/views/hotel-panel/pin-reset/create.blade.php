@extends('layouts.hoteldesk-public')

@section('title', $hotel->name . ' · Recuperar PIN')

@section('content')
    <section class="hd-card hd-card-pad">
        <div class="hd-eyebrow">Recuperación de acceso</div>
        <h1 class="hd-title">Solicitar reset de PIN</h1>

        @if($errors->any())
            <div class="hd-alert hd-alert-danger" style="margin-top: 18px;">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('hotel.pin-reset.store', $hotel) }}" style="margin-top: 20px;">
            @csrf

            <div class="hd-field">
                <label class="hd-label">Nombre de quien solicita</label>
                <input class="hd-input" name="requester_name" value="{{ old('requester_name') }}" required placeholder="Ej. Recepción turno tarde">
            </div>

           <div class="hd-field">
    <label class="hd-label">Teléfono donde enviar el PIN</label>
    <input
        class="hd-input"
        name="requester_phone"
        value="{{ old('requester_phone') }}"
        required
        maxlength="40"
        inputmode="tel"
        placeholder="Ej. 2291234567">
</div>

            <div class="hd-field">
                <label class="hd-label">Nota opcional</label>
                <textarea class="hd-textarea" name="note" maxlength="500" placeholder="Ej. No recordamos el PIN actual">{{ old('note') }}</textarea>
            </div>

            <button class="hd-btn hd-btn-primary hd-btn-full" type="submit">
                <i class="ti ti-send"></i>
                Solicitar restablecimiento
            </button>
        </form>

        <div class="hd-bottom-hint">
            SysApp revisará la solicitud y entregará un nuevo PIN al telefono indicado.
        </div>
    </section>
@endsection