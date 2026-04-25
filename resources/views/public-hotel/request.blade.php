@extends('layouts.hoteldesk-public')

@section('title', $hotel->name . ' · ' . $point->label)

@section('content')
    @php
        $secondaryTypeKeys = ['suggestion'];

        $mainTypes = collect($types)
            ->reject(fn ($type, $key) => in_array($key, $secondaryTypeKeys, true))
            ->all();

        $secondaryTypes = collect($types)
            ->only($secondaryTypeKeys)
            ->all();

        $selectedType = old('type_key');

        $iconForType = function ($key) {
            return match ($key) {
                'towels' => 'ti-bath',
                'cleaning' => 'ti-spray',
                'maintenance' => 'ti-tool',
                'amenity' => 'ti-droplet',
                'luggage' => 'ti-luggage',
                'wakeup' => 'ti-alarm',
                'taxi' => 'ti-car',
                'suggestion' => 'ti-message-chatbot',
                'other' => 'ti-message-dots',
                default => 'ti-message',
            };
        };
    @endphp

    <div class="mb-3">
        <h1 class="h2 mb-1">{{ $point->label }}</h1>

        <div class="text-secondary">
            Selecciona una opción. Recepción recibirá tu solicitud.
        </div>
    </div>

    <form id="guestRequestForm"
          method="POST"
          action="{{ route('public.qr.store', $point->public_code) }}">
        @csrf

        <div class="mb-3">
            <label class="form-label fw-bold">
                ¿Qué necesitas?
            </label>

            <div class="row g-2">
                @foreach($mainTypes as $key => $type)
                    <div class="col-6">
                        <input
                            class="btn-check"
                            type="radio"
                            name="type_key"
                            id="type_{{ $key }}"
                            value="{{ $key }}"
                            @checked($selectedType === $key)>

                      <label class="btn btn-outline-primary w-100 h-100 p-3 d-flex align-items-center justify-content-start gap-2"
       for="type_{{ $key }}">
    <span class="avatar avatar-sm bg-primary-lt text-primary flex-shrink-0">
        <i class="ti {{ $iconForType($key) }}"></i>
    </span>

    <span class="fw-bold text-truncate">
        {{ $type['label'] }}
    </span>
</label>
                    </div>
                @endforeach
            </div>
        </div>

        @if(!empty($secondaryTypes))
            <div class="mb-3">
                <label class="form-label fw-bold">
                    Sugerencias
                </label>

                @foreach($secondaryTypes as $key => $type)
                    <input
                        class="btn-check"
                        type="radio"
                        name="type_key"
                        id="type_{{ $key }}"
                        value="{{ $key }}"
                        @checked($selectedType === $key)>
<label class="btn btn-outline-secondary w-100 p-3 d-flex align-items-center justify-content-start gap-2 text-start"
       for="type_{{ $key }}">
    <span class="avatar avatar-sm bg-secondary-lt text-secondary flex-shrink-0">
        <i class="ti {{ $iconForType($key) }}"></i>
    </span>

    <span class="min-width-0">
        <span class="fw-bold d-block">
            {{ $type['label'] }}
        </span>

        <span class="text-secondary small d-block">
            {{ $type['description'] ?? 'Enviar comentario o mejora' }}
        </span>
    </span>
</label>
                @endforeach
            </div>
        @endif

        <div class="mb-3">
            <label class="form-label" for="guest_name">
                Nombre opcional
            </label>

            <input
                class="form-control"
                id="guest_name"
                name="guest_name"
                type="text"
                maxlength="120"
                value="{{ old('guest_name') }}"
                placeholder="Ej. Juan Pérez">
        </div>

        <div class="mb-3">
            <label class="form-label" for="note">
                <span id="noteLabelText">Nota opcional</span>
                <span id="noteRequiredDot" class="text-danger" style="display: none;">*</span>
            </label>

            <textarea
                class="form-control"
                id="note"
                name="note"
                maxlength="500"
                rows="3"
                placeholder="Ej. Necesito dos toallas extra">{{ old('note') }}</textarea>

            <div class="form-hint" id="noteHelper">
                Puedes agregar detalles para que recepción atienda mejor tu solicitud.
            </div>
        </div>

        <button id="guestSubmitBtn" class="btn btn-primary btn-lg w-100" type="submit">
            <i class="ti ti-send me-2"></i>
            Enviar solicitud
        </button>

        <div class="text-center text-secondary small mt-3">
            No necesitas instalar ninguna app.
        </div>
    </form>

    <script>
        const requiredNoteTypes = ['suggestion', 'other'];

        const guestRequestForm = document.getElementById('guestRequestForm');
        const guestSubmitBtn = document.getElementById('guestSubmitBtn');
        const noteInput = document.getElementById('note');
        const noteLabelText = document.getElementById('noteLabelText');
        const noteRequiredDot = document.getElementById('noteRequiredDot');
        const noteHelper = document.getElementById('noteHelper');
        const typeRadios = document.querySelectorAll('input[name="type_key"]');

        function selectedTypeKey() {
            const checked = document.querySelector('input[name="type_key"]:checked');
            return checked ? checked.value : null;
        }

        function applyNoteRules() {
            const typeKey = selectedTypeKey();
            const noteRequired = requiredNoteTypes.includes(typeKey);

            if (!noteInput || !noteLabelText || !noteRequiredDot || !noteHelper) {
                return;
            }

            noteInput.required = noteRequired;

            if (typeKey === 'suggestion') {
                noteLabelText.textContent = 'Escribe tu sugerencia';
                noteRequiredDot.style.display = 'inline';
                noteInput.placeholder = 'Ej. Sería útil agregar más opciones de desayuno';
                noteHelper.textContent = 'La sugerencia necesita texto para que recepción pueda revisarla.';
                return;
            }

            if (typeKey === 'other') {
                noteLabelText.textContent = 'Describe tu solicitud';
                noteRequiredDot.style.display = 'inline';
                noteInput.placeholder = 'Ej. Necesito apoyo con...';
                noteHelper.textContent = 'La opción “Otro” necesita una descripción.';
                return;
            }

            noteLabelText.textContent = 'Nota opcional';
            noteRequiredDot.style.display = 'none';
            noteInput.placeholder = 'Ej. Necesito dos toallas extra';
            noteHelper.textContent = 'Puedes agregar detalles para que recepción atienda mejor tu solicitud.';
        }

        typeRadios.forEach((radio) => {
            radio.addEventListener('change', applyNoteRules);
        });

        applyNoteRules();

        if (guestRequestForm && guestSubmitBtn) {
            guestRequestForm.addEventListener('submit', (event) => {
                const typeKey = selectedTypeKey();

                if (!typeKey) {
                    event.preventDefault();
                    alert('Selecciona una opción para continuar.');
                    return;
                }

                if (requiredNoteTypes.includes(typeKey) && !noteInput.value.trim()) {
                    event.preventDefault();
                    noteInput.focus();
                    alert('Escribe el detalle para enviar esta solicitud.');
                    return;
                }

                guestSubmitBtn.disabled = true;
                guestSubmitBtn.classList.add('disabled');
                guestSubmitBtn.innerHTML = '<i class="ti ti-loader-2 me-2"></i> Enviando...';
            });
        }
    </script>
@endsection