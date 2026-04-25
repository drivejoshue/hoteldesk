@extends('layouts.hoteldesk-public')

@section('title', $hotel->name . ' · ' . $point->label)

@section('content')
    @php
        /*
         * Solo "suggestion" queda como acción especial abajo.
         * "other" vuelve al grid principal, pero sigue pidiendo nota obligatoria.
         */
        $secondaryTypeKeys = ['suggestion'];

        $mainTypes = collect($types)
            ->reject(fn ($type, $key) => in_array($key, $secondaryTypeKeys, true))
            ->all();

        $secondaryTypes = collect($types)
            ->only($secondaryTypeKeys)
            ->all();

        $selectedType = old('type_key');
    @endphp

    <style>
        .hd-guest-card {
            padding: 17px;
            border-radius: 24px;
        }

        .hd-guest-title {
            font-size: 23px;
            line-height: 1.05;
            letter-spacing: -.055em;
            margin-bottom: 4px;
        }

        .hd-guest-subtitle {
            color: var(--hd-muted);
            font-size: 12.5px;
            font-weight: 750;
            line-height: 1.35;
            margin-top: 6px;
        }

        .hd-guest-section-label {
            margin: 17px 0 9px;
            color: #344054;
            font-size: 12.5px;
            font-weight: 900;
        }

        .hd-request-types-compact {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px;
        }

        .hd-type-option input {
            display: none;
        }

        .hd-type-card-compact {
            min-height: 58px;
            border: 1px solid #dde5ee;
            background: #fff;
            border-radius: 16px;
            padding: 9px 10px;
            display: flex;
            align-items: center;
            gap: 9px;
            cursor: pointer;
            transition: .16s ease;
        }

        .hd-type-option input:checked + .hd-type-card-compact,
        .hd-type-option input:checked + .hd-suggestion-card {
            border-color: var(--hd-primary);
            background: #eef6ff;
            box-shadow: 0 0 0 3px rgba(15, 108, 189, .10);
        }

        .hd-type-icon-compact {
            width: 33px;
            height: 33px;
            border-radius: 12px;
            display: grid;
            place-items: center;
            background: #f2f6fb;
            color: var(--hd-primary);
            font-size: 18px;
            flex: 0 0 auto;
        }

        .hd-type-name-compact {
            font-size: 13px;
            font-weight: 900;
            line-height: 1.1;
        }

        .hd-suggestion-wrap {
            margin-top: 9px;
        }

        .hd-suggestion-card {
            min-height: 54px;
            border: 1px solid #dde5ee;
            background: #fff;
            border-radius: 16px;
            padding: 10px 12px;
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            transition: .16s ease;
        }

        .hd-suggestion-text {
            min-width: 0;
        }

        .hd-suggestion-title {
            font-size: 13px;
            font-weight: 950;
            line-height: 1.1;
        }

        .hd-suggestion-desc {
            color: var(--hd-muted);
            font-size: 11px;
            font-weight: 700;
            margin-top: 3px;
            line-height: 1.2;
        }

        .hd-guest-fields {
            margin-top: 15px;
        }

        .hd-guest-fields .hd-field {
            margin-bottom: 10px;
        }

        .hd-guest-fields .hd-input {
            min-height: 43px;
            border-radius: 14px;
            padding: 10px 12px;
            font-size: 14px;
        }

        .hd-guest-fields .hd-textarea {
            min-height: 80px;
            border-radius: 14px;
            padding: 10px 12px;
            font-size: 14px;
        }

        .hd-note-helper {
            color: var(--hd-muted);
            font-size: 11px;
            font-weight: 700;
            margin-top: 5px;
            line-height: 1.35;
        }

        .hd-required-dot {
            color: #b42318;
            font-weight: 950;
        }

        .hd-submit-compact {
            min-height: 47px;
            border-radius: 16px;
            margin-top: 4px;
        }

        @media (max-width: 380px) {
            .hd-request-types-compact {
                grid-template-columns: 1fr;
            }

            .hd-guest-card {
                padding: 16px;
            }

            .hd-guest-title {
                font-size: 22px;
            }
        }
    </style>

    <section class="hd-card hd-card-pad hd-guest-card">
        <h1 class="hd-title hd-guest-title">{{ $point->label }}</h1>

        <div class="hd-guest-subtitle">
            Selecciona una opción. Recepción recibirá tu solicitud.
        </div>

        @if($errors->any())
            <div class="hd-alert hd-alert-danger" style="margin-top: 16px;">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form id="guestRequestForm"
              method="POST"
              action="{{ route('public.qr.store', $point->public_code) }}"
              style="margin-top: 15px;">
            @csrf

            <div class="hd-guest-section-label">¿Qué necesitas?</div>

            <div class="hd-request-types-compact">
                @foreach($mainTypes as $key => $type)
                    <label class="hd-type-option">
                        <input
                            type="radio"
                            name="type_key"
                            value="{{ $key }}"
                            @checked($selectedType === $key)>

                        <span class="hd-type-card-compact">
                            <span class="hd-type-icon-compact">
                                @switch($key)
                                    @case('towels') <i class="ti ti-bath"></i> @break
                                    @case('cleaning') <i class="ti ti-spray"></i> @break
                                    @case('maintenance') <i class="ti ti-tool"></i> @break
                                    @case('amenity') <i class="ti ti-droplet"></i> @break
                                    @case('luggage') <i class="ti ti-luggage"></i> @break
                                    @case('wakeup') <i class="ti ti-alarm"></i> @break
                                    @case('taxi') <i class="ti ti-car"></i> @break
                                    @case('other') <i class="ti ti-message-dots"></i> @break
                                    @default <i class="ti ti-message"></i>
                                @endswitch
                            </span>

                            <span class="hd-type-name-compact">{{ $type['label'] }}</span>
                        </span>
                    </label>
                @endforeach
            </div>

            @if(!empty($secondaryTypes))
                <div class="hd-guest-section-label" style="margin-top: 14px;">
                    Sugerencias
                </div>

                <div class="hd-suggestion-wrap">
                    @foreach($secondaryTypes as $key => $type)
                        <label class="hd-type-option">
                            <input
                                type="radio"
                                name="type_key"
                                value="{{ $key }}"
                                @checked($selectedType === $key)>

                            <span class="hd-suggestion-card">
                                <span class="hd-type-icon-compact">
                                    <i class="ti ti-message-chatbot"></i>
                                </span>

                                <span class="hd-suggestion-text">
                                    <span class="hd-suggestion-title">{{ $type['label'] }}</span>
                                    <span class="hd-suggestion-desc">
                                        {{ $type['description'] ?? 'Enviar comentario o mejora' }}
                                    </span>
                                </span>
                            </span>
                        </label>
                    @endforeach
                </div>
            @endif

            <div class="hd-guest-fields">
                <div class="hd-field">
                    <label class="hd-label" for="guest_name">Nombre opcional</label>
                    <input
                        class="hd-input"
                        id="guest_name"
                        name="guest_name"
                        type="text"
                        maxlength="120"
                        value="{{ old('guest_name') }}"
                        placeholder="Ej. Juan Pérez">
                </div>

                <div class="hd-field">
                    <label class="hd-label" for="note">
                        <span id="noteLabelText">Nota opcional</span>
                        <span id="noteRequiredDot" class="hd-required-dot" style="display: none;">*</span>
                    </label>

                    <textarea
                        class="hd-textarea"
                        id="note"
                        name="note"
                        maxlength="500"
                        placeholder="Ej. Necesito dos toallas extra">{{ old('note') }}</textarea>

                    <div class="hd-note-helper" id="noteHelper">
                        Puedes agregar detalles para que recepción atienda mejor tu solicitud.
                    </div>
                </div>
            </div>

            <button id="guestSubmitBtn" class="hd-btn hd-btn-primary hd-btn-full hd-submit-compact" type="submit">
                <i class="ti ti-send"></i>
                Enviar solicitud
            </button>

            <div class="hd-bottom-hint">
                No necesitas instalar ninguna app.
            </div>
        </form>
    </section>

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
                guestSubmitBtn.style.opacity = '0.75';
                guestSubmitBtn.innerHTML = '<i class="ti ti-loader-2"></i> Enviando...';
            });
        }
    </script>
@endsection