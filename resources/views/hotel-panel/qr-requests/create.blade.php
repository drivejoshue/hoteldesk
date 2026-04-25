@extends('layouts.hotel-panel')

@section('title', $hotel->name . ' · Solicitar QR')
@section('subtitle', 'Solicitar nuevo punto QR')

@section('topbar-actions')
    @include('hotel-panel.partials.topbar-actions', ['showSound' => false])
@endsection

@section('content')
    <div class="page-header d-print-none mb-3">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title mb-1">Solicitar QR</h2>
                <div class="text-secondary">
                    Pide a SysApp la creación de un nuevo punto QR para tu hotel.
                </div>
            </div>

            <div class="col-auto">
                <a class="btn btn-outline-secondary" href="{{ route('hotel.qr-requests.index', $hotel) }}">
                    <i class="ti ti-arrow-left me-1"></i>
                    Volver
                </a>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('hotel.qr-requests.store', $hotel) }}">
        @csrf

        <div class="row row-cards">
            <div class="col-12 col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Datos del punto</h3>
                    </div>

                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12 col-md-7">
                                <label class="form-label">
                                    Nombre del punto
                                    <span class="text-danger">*</span>
                                </label>

                                <input
                                    class="form-control"
                                    name="label"
                                    value="{{ old('label') }}"
                                    required
                                    placeholder="Ej. Gimnasio, Habitación 205, Terraza">
                            </div>

                            <div class="col-12 col-md-5">
                                <label class="form-label">
                                    Tipo
                                    <span class="text-danger">*</span>
                                </label>

                                <select class="form-select" name="type" required>
                                    <option value="room" @selected(old('type') === 'room')>Habitación</option>
                                    <option value="lobby" @selected(old('type') === 'lobby')>Lobby</option>
                                    <option value="area" @selected(old('type') === 'area')>Área</option>
                                    <option value="restaurant" @selected(old('type') === 'restaurant')>Restaurante</option>
                                    <option value="parking" @selected(old('type') === 'parking')>Estacionamiento</option>
                                    <option value="reception" @selected(old('type') === 'reception')>Recepción</option>
                                    <option value="other" @selected(old('type') === 'other')>Otro</option>
                                </select>
                            </div>

                            <div class="col-12 col-md-5">
                                <label class="form-label">Piso / zona</label>

                                <input
                                    class="form-control"
                                    name="floor"
                                    value="{{ old('floor') }}"
                                    placeholder="Ej. PB, Piso 2, Terraza">
                            </div>

                            <div class="col-12 col-md-7">
                                <label class="form-label">
                                    Modo
                                    <span class="text-danger">*</span>
                                </label>

                                <select class="form-select" name="mode" id="qrMode" required>
                                    <option value="menu" @selected(old('mode') === 'menu')>Menú completo</option>
                                    <option value="limited" @selected(old('mode') === 'limited')>Menú limitado</option>
                                    <option value="direct" @selected(old('mode') === 'direct')>Solicitud directa</option>
                                </select>

                                <div class="form-hint">
                                    Menú completo muestra todas las opciones. Menú limitado muestra solo las seleccionadas. Directo crea una solicitud específica.
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Nota para SysApp</label>

                                <textarea
                                    class="form-control"
                                    name="note"
                                    maxlength="500"
                                    rows="3"
                                    placeholder="Ej. Queremos ponerlo en la entrada del gimnasio">{{ old('note') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mt-3" id="limitedOptionsCard">
                    <div class="card-header">
                        <h3 class="card-title">Solicitudes permitidas</h3>
                    </div>

                    <div class="card-body">
                        <div class="text-secondary mb-3">
                            Solo aplica cuando el modo sea <strong>Menú limitado</strong>.
                        </div>

                        <div class="row g-2">
                            @foreach($requestTypes as $key => $type)
                                <div class="col-12 col-md-6">
                                    <label class="form-check border rounded-3 p-3 m-0">
                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            name="allowed_request_types[]"
                                            value="{{ $key }}"
                                            @checked(in_array($key, old('allowed_request_types', []), true))>

                                        <span class="form-check-label fw-bold">
                                            <span class="me-1">{{ $type['icon'] }}</span>
                                            {{ $type['label'] }}
                                        </span>

                                        @if(!empty($type['description']))
                                            <span class="form-hint d-block mt-1">
                                                {{ $type['description'] }}
                                            </span>
                                        @endif
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="card mt-3" id="directOptionsCard">
                    <div class="card-header">
                        <h3 class="card-title">Solicitud directa</h3>
                    </div>

                    <div class="card-body">
                        <label class="form-label">Tipo de solicitud directa</label>

                        <select class="form-select" name="fixed_request_type">
                            <option value="">No aplica</option>
                            @foreach($requestTypes as $key => $type)
                                <option value="{{ $key }}" @selected(old('fixed_request_type') === $key)>
                                    {{ $type['label'] }}
                                </option>
                            @endforeach
                        </select>

                        <div class="form-hint">
                            Si seleccionas “Solicitud directa”, el huésped no verá menú; el QR generará esta solicitud.
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Resumen</h3>
                    </div>

                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="ti ti-info-circle me-1"></i>
                            SysApp revisará la solicitud y, si procede, creará el QR para impresión.
                        </div>

                        <div class="text-secondary small">
                            Recomendación: usa nombres claros como “Habitación 205”, “Lobby” o “Alberca”.
                        </div>
                    </div>

                    <div class="card-footer">
                        <button class="btn btn-primary w-100" type="submit">
                            <i class="ti ti-send me-1"></i>
                            Enviar solicitud
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <script>
        (() => {
            const mode = document.getElementById('qrMode');
            const limitedCard = document.getElementById('limitedOptionsCard');
            const directCard = document.getElementById('directOptionsCard');

            if (!mode || !limitedCard || !directCard) {
                return;
            }

            function syncModeCards() {
                limitedCard.classList.toggle('d-none', mode.value !== 'limited');
                directCard.classList.toggle('d-none', mode.value !== 'direct');
            }

            mode.addEventListener('change', syncModeCards);
            syncModeCards();
        })();
    </script>
@endsection