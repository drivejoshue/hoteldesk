@extends('layouts.hotel-panel')

@section('title', $hotel->name . ' · Recepción')
@section('subtitle', 'Panel de solicitudes')

@section('topbar-actions')
    @include('hotel-panel.partials.topbar-actions', ['showSound' => true])
@endsection

@section('content')
    <style>
        .hd-request-card {
            position: relative;
            overflow: hidden;
            border-radius: 18px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 10px 28px rgba(15, 23, 42, 0.06);
            transition:
                transform .18s ease,
                box-shadow .18s ease,
                border-color .18s ease,
                background .18s ease;
        }

        .hd-request-card:hover {
            transform: translateY(-1px);
            box-shadow: 0 14px 34px rgba(15, 23, 42, 0.10);
        }

        .hd-request-card::before {
            content: "";
            position: absolute;
            inset: 0 auto 0 0;
            width: 5px;
            opacity: .95;
        }

        .hd-request-pending {
            background: linear-gradient(135deg, #ffffff 0%, #fff8ed 100%);
            border-color: #fed7aa;
        }

        .hd-request-pending::before {
            background: #f59e0b;
        }

        .hd-request-in_progress {
            background: linear-gradient(135deg, #ffffff 0%, #eff6ff 100%);
            border-color: #bfdbfe;
        }

        .hd-request-in_progress::before {
            background: #0f6cbd;
        }

        .hd-request-completed {
            background: linear-gradient(135deg, #ffffff 0%, #ecfdf5 100%);
            border-color: #bbf7d0;
        }

        .hd-request-completed::before {
            background: #16a34a;
        }

        .hd-request-canceled {
            background: linear-gradient(135deg, #ffffff 0%, #fef2f2 100%);
            border-color: #fecaca;
        }

        .hd-request-canceled::before {
            background: #dc2626;
        }

        .hd-request-neutral {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border-color: #e2e8f0;
        }

        .hd-request-neutral::before {
            background: #64748b;
        }

        .hd-status-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-radius: 999px;
            padding: 7px 11px;
            font-size: 12px;
            font-weight: 800;
            line-height: 1;
            white-space: nowrap;
            border: 1px solid transparent;
        }

        .hd-status-pending {
            color: #92400e;
            background: #fffbeb;
            border-color: #fde68a;
        }

        .hd-status-progress {
            color: #075985;
            background: #e0f2fe;
            border-color: #bae6fd;
        }

        .hd-status-done {
            color: #166534;
            background: #dcfce7;
            border-color: #bbf7d0;
        }

        .hd-status-canceled {
            color: #991b1b;
            background: #fee2e2;
            border-color: #fecaca;
        }

        .hd-status-neutral {
            color: #334155;
            background: #f1f5f9;
            border-color: #e2e8f0;
        }

        .hd-request-main-icon {
            box-shadow: 0 6px 16px rgba(15, 23, 42, 0.08);
        }

        .hd-request-pending .hd-request-main-icon {
            background: #fffbeb !important;
            color: #d97706 !important;
        }

        .hd-request-in_progress .hd-request-main-icon {
            background: #e0f2fe !important;
            color: #0f6cbd !important;
        }

        .hd-soft-note {
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.72);
            border: 1px solid rgba(226, 232, 240, 0.86);
            padding: 9px 11px;
        }

        .hd-btn-finalize {
            background: #16a34a;
            border-color: #16a34a;
            color: #ffffff;
            box-shadow: 0 8px 18px rgba(22, 163, 74, 0.18);
        }

        .hd-btn-finalize:hover {
            background: #15803d;
            border-color: #15803d;
            color: #ffffff;
        }

        .hd-orbana-progress-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-radius: 999px;
            padding: 6px 10px;
            font-size: 12px;
            font-weight: 800;
            color: #075985;
            background: #e0f2fe;
            border: 1px solid #bae6fd;
        }
    </style>

    <main class="hd-dashboard">
        <section class="hd-kpi-bar">
            <div class="hd-kpi-item">
                <span class="hd-kpi-icon pending">
                    <i class="ti ti-bell"></i>
                </span>
                <span class="hd-kpi-label">Pendientes</span>
                <strong class="hd-kpi-value" id="countPending">{{ $counts['pending'] }}</strong>
            </div>

            <div class="hd-kpi-item">
                <span class="hd-kpi-icon progress">
                    <i class="ti ti-progress-check"></i>
                </span>
                <span class="hd-kpi-label">Proceso</span>
                <strong class="hd-kpi-value" id="countProgress">{{ $counts['in_progress'] }}</strong>
            </div>

            <div class="hd-kpi-item">
                <span class="hd-kpi-icon done">
                    <i class="ti ti-circle-check"></i>
                </span>
                <span class="hd-kpi-label">Resueltas 24h</span>
                <strong class="hd-kpi-value" id="countCompleted">{{ $counts['completed_today'] }}</strong>
            </div>

            <div class="hd-kpi-item">
                <span class="hd-kpi-icon canceled">
                    <i class="ti ti-circle-x"></i>
                </span>
                <span class="hd-kpi-label">Canceladas</span>
                <strong class="hd-kpi-value" id="countCanceled">{{ $counts['canceled_today'] }}</strong>
            </div>
        </section>

        <section>
            <div class="hd-toolbar">
                <div>
                    <h2 class="hd-toolbar-title">Solicitudes activas</h2>
                    <div class="hd-text-muted" style="font-size: 13px; font-weight: 750;">
                        Actualización automática cada 8 segundos.
                    </div>
                </div>

                <div class="hd-toolbar-actions">
                    <button class="hd-btn hd-btn-soft" type="button" onclick="loadFeed()">
                        <i class="ti ti-refresh"></i>
                        Actualizar
                    </button>
                </div>
            </div>

            <div id="requestsContainer" class="hd-requests-grid"></div>
        </section>
    </main>

    <audio id="alertSound" preload="auto">
        <source src="{{ asset('sounds/hotel-alert.mp3') }}" type="audio/mpeg">
    </audio>

    <script>
        const feedUrl = @json(route('hotel.requests.feed', $hotel));
        const takeBaseUrl = @json(url('/h/' . $hotel->slug . '/requests'));
        const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const hotelServicePointUrl = @json($hotel->service_point_url);
        const soundStorageKey = @json('hoteldesk.sound.enabled.' . $hotel->id);

        let soundEnabled = false;
        let soundUnlocked = false;
        let knownIds = new Set();
        let firstLoad = true;
        let orbanaProcessingIds = new Set();
        let requestsById = new Map();

        const alertSound = document.getElementById('alertSound');
        const enableSoundBtn = document.getElementById('enableSoundBtn');
        const soundStatus = document.getElementById('soundStatus');

        function setSoundUi(enabled, unlocked = false) {
            if (!soundStatus || !enableSoundBtn) {
                return;
            }

            if (enabled && unlocked) {
                soundStatus.classList.add('active');
                soundStatus.innerHTML = '<i class="ti ti-volume"></i><span>Sonido activo</span>';
                enableSoundBtn.innerHTML = '<i class="ti ti-volume-off"></i><span>Desactivar</span>';
                return;
            }

            if (enabled && !unlocked) {
                soundStatus.classList.remove('active');
                soundStatus.innerHTML = '<i class="ti ti-hand-click"></i><span>Toca para activar sonido</span>';
                enableSoundBtn.innerHTML = '<i class="ti ti-volume"></i><span>Activar</span>';
                return;
            }

            soundStatus.classList.remove('active');
            soundStatus.innerHTML = '<i class="ti ti-volume-off"></i><span>Sin sonido</span>';
            enableSoundBtn.innerHTML = '<i class="ti ti-volume"></i><span>Activar</span>';
        }

        async function unlockSound(persist = true) {
            if (!alertSound) {
                return false;
            }

            try {
                alertSound.volume = 0.01;
                await alertSound.play();
                alertSound.pause();
                alertSound.currentTime = 0;
                alertSound.volume = 0.85;

                soundEnabled = true;
                soundUnlocked = true;

                if (persist) {
                    localStorage.setItem(soundStorageKey, '1');
                }

                setSoundUi(true, true);

                return true;
            } catch (e) {
                soundEnabled = true;
                soundUnlocked = false;

                if (persist) {
                    localStorage.setItem(soundStorageKey, '1');
                }

                setSoundUi(true, false);
                console.warn('El navegador todavía no permite reproducir sonido.', e);

                return false;
            }
        }

        function disableSound() {
            soundEnabled = false;
            soundUnlocked = false;
            localStorage.removeItem(soundStorageKey);
            setSoundUi(false, false);
        }

        if (enableSoundBtn) {
            enableSoundBtn.addEventListener('click', async () => {
                if (soundEnabled && soundUnlocked) {
                    disableSound();
                    return;
                }

                await unlockSound(true);
            });
        }

        function initSoundPreference() {
            if (localStorage.getItem(soundStorageKey) === '1') {
                soundEnabled = true;
                soundUnlocked = false;
                setSoundUi(true, false);

                const unlockOnFirstInteraction = async () => {
                    if (soundEnabled && !soundUnlocked) {
                        await unlockSound(true);
                    }

                    window.removeEventListener('pointerdown', unlockOnFirstInteraction);
                    window.removeEventListener('keydown', unlockOnFirstInteraction);
                    window.removeEventListener('touchstart', unlockOnFirstInteraction);
                };

                window.addEventListener('pointerdown', unlockOnFirstInteraction, { once: true });
                window.addEventListener('keydown', unlockOnFirstInteraction, { once: true });
                window.addEventListener('touchstart', unlockOnFirstInteraction, { once: true });
            } else {
                setSoundUi(false, false);
            }
        }

        function escapeHtml(value) {
            return String(value ?? '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        }

        function iconForType(typeKey) {
            const icons = {
                towels: 'ti-bath',
                cleaning: 'ti-spray',
                maintenance: 'ti-tool',
                amenity: 'ti-droplet',
                luggage: 'ti-luggage',
                wakeup: 'ti-alarm',
                taxi: 'ti-car',
                suggestion: 'ti-message-chatbot',
                other: 'ti-message-dots',
            };

            return icons[typeKey] ?? 'ti-message-dots';
        }

        function statusBadge(status, label) {
            const config = {
                pending: {
                    cls: 'hd-status-pending',
                    icon: 'ti-bell-ringing',
                    text: label || 'Pendiente',
                },
                in_progress: {
                    cls: 'hd-status-progress',
                    icon: 'ti-progress-check',
                    text: label || 'En proceso',
                },
                completed: {
                    cls: 'hd-status-done',
                    icon: 'ti-circle-check',
                    text: label || 'Resuelta',
                },
                canceled: {
                    cls: 'hd-status-canceled',
                    icon: 'ti-circle-x',
                    text: label || 'Cancelada',
                },
            };

            const item = config[status] ?? {
                cls: 'hd-status-neutral',
                icon: 'ti-info-circle',
                text: label || status || 'Estado',
            };

            return `
                <span class="hd-status-pill ${item.cls}">
                    <i class="ti ${item.icon}" style="font-size: 14px; line-height: 1;"></i>
                    <span>${escapeHtml(item.text)}</span>
                </span>
            `;
        }

        function requestCardClass(status, isNew) {
            return [
                'card',
                'hd-request-card',
                `hd-request-${status || 'neutral'}`,
                isNew ? 'is-new' : '',
            ].filter(Boolean).join(' ');
        }

        function renderRequestCard(item, isNew) {
            const note = item.note
                ? `<div class="hd-soft-note text-muted small mt-2">${escapeHtml(item.note)}</div>`
                : `<div class="hd-soft-note text-muted small mt-2">Sin nota adicional.</div>`;

            const isOrbanaProcessing = orbanaProcessingIds.has(item.id);

            const takeButton = item.status === 'pending'
                ? `<button class="btn btn-outline-primary btn-sm" onclick="updateStatus(${item.id}, 'take')">
                        <i class="ti ti-hand-click me-1"></i>
                        Tomar
                   </button>`
                : '';

            const orbanaButton = item.type_key === 'taxi' && hotelServicePointUrl && item.status === 'pending'
                ? `<button
                        class="btn btn-primary btn-sm"
                        type="button"
                        id="orbanaBtn${item.id}"
                        onclick="sendToOrbana(${item.id})"
                        ${isOrbanaProcessing ? 'disabled' : ''}
                   >
                        <i class="ti ${isOrbanaProcessing ? 'ti-loader-2' : 'ti-taxi'} me-1"></i>
                        ${isOrbanaProcessing ? 'Enviando...' : 'Pedir por Orbana'}
                   </button>`
                : '';

            const orbanaProgressBadge = item.type_key === 'taxi' && item.status === 'in_progress'
                ? `<span class="hd-orbana-progress-pill">
                        <i class="ti ti-taxi"></i>
                        Taxi en proceso
                   </span>`
                : '';

            return `
                <article class="${requestCardClass(item.status, isNew)}">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between gap-3">
                            <div class="min-w-0">
                                <div class="d-flex align-items-center gap-2 fw-bold text-truncate">
                                    <span class="avatar avatar-sm hd-request-main-icon">
                                        <i class="ti ${iconForType(item.type_key)}"></i>
                                    </span>

                                    <span class="text-truncate">
                                        ${escapeHtml(item.point_label)} — ${escapeHtml(item.type_label)}
                                    </span>
                                </div>

                                <div class="text-muted small mt-1">
                                    <strong>${escapeHtml(item.created_human ?? '')}</strong>
                                    <span class="opacity-75"> · Pedido: ${escapeHtml(item.created_short ?? item.created_at ?? '')}</span>
                                </div>
                            </div>

                            <div class="flex-shrink-0">
                                ${statusBadge(item.status, item.status_label)}
                            </div>
                        </div>

                        ${note}

                        <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap mt-3">
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                ${orbanaProgressBadge}
                            </div>

                            <div class="d-flex align-items-center gap-2 flex-wrap justify-content-end">
                                ${orbanaButton}
                                ${takeButton}

                                <button class="btn hd-btn-finalize btn-sm" onclick="updateStatus(${item.id}, 'complete')">
                                    <i class="ti ti-circle-check me-1"></i>
                                    Finalizar
                                </button>

                                <button class="btn btn-outline-danger btn-sm" onclick="updateStatus(${item.id}, 'cancel')">
                                    <i class="ti ti-x me-1"></i>
                                    Cancelar
                                </button>
                            </div>
                        </div>
                    </div>
                </article>
            `;
        }

        async function playAlertIfAllowed() {
            if (!soundEnabled || !soundUnlocked || !alertSound) {
                return;
            }

            try {
                alertSound.currentTime = 0;
                alertSound.volume = 0.85;
                await alertSound.play();
            } catch (e) {
                soundUnlocked = false;
                setSoundUi(true, false);
                console.warn('No se pudo reproducir sonido.', e);
            }
        }

        async function loadFeed() {
            try {
                const response = await fetch(feedUrl, {
                    headers: {
                        'Accept': 'application/json',
                    }
                });

                if (!response.ok) {
                    throw new Error('Error al cargar solicitudes.');
                }

                const data = await response.json();

                requestsById = new Map();

                data.requests.forEach(item => {
                    requestsById.set(Number(item.id), item);

                    if (item.status !== 'pending') {
                        orbanaProcessingIds.delete(item.id);
                    }
                });

                document.getElementById('countPending').textContent = data.counts.pending;
                document.getElementById('countProgress').textContent = data.counts.in_progress;
                document.getElementById('countCompleted').textContent = data.counts.completed_today;
                document.getElementById('countCanceled').textContent = data.counts.canceled_today;

                const container = document.getElementById('requestsContainer');

                if (!data.requests.length) {
                    container.innerHTML = `
                        <div class="hd-empty">
                            <i class="ti ti-mood-smile" style="font-size: 34px;"></i>
                            <div style="margin-top: 8px;">No hay solicitudes activas.</div>
                        </div>
                    `;

                    knownIds = new Set();
                    firstLoad = false;
                    return;
                }

                let hasNew = false;
                const incomingIds = new Set();

                const html = data.requests.map(item => {
                    const isNew = !firstLoad && !knownIds.has(item.id);

                    if (isNew) {
                        hasNew = true;
                    }

                    incomingIds.add(item.id);
                    return renderRequestCard(item, isNew);
                }).join('');

                container.innerHTML = html;
                knownIds = incomingIds;

                if (hasNew) {
                    await playAlertIfAllowed();
                }

                firstLoad = false;
            } catch (e) {
                console.error(e);
            }
        }

        async function sendToOrbana(id) {
            if (!hotelServicePointUrl || orbanaProcessingIds.has(id)) {
                return;
            }

            orbanaProcessingIds.add(id);

            const btn = document.getElementById(`orbanaBtn${id}`);

            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="ti ti-loader-2 me-1"></i> Enviando...';
            }

            window.open(hotelServicePointUrl, '_blank', 'noopener');

            try {
                const response = await fetch(`${takeBaseUrl}/${id}/take`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({}),
                });

                const data = await response.json();

                if (!response.ok || !data.ok) {
                    orbanaProcessingIds.delete(id);

                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="ti ti-taxi me-1"></i> Pedir por Orbana';
                    }

                    alert(data.message || 'No se pudo poner la solicitud en proceso.');

                    return;
                }

                await loadFeed();
            } catch (e) {
                orbanaProcessingIds.delete(id);

                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="ti ti-taxi me-1"></i> Pedir por Orbana';
                }

                console.error(e);
                alert('Error de conexión al enviar la solicitud a proceso.');
            }
        }

        async function confirmCompleteRequest(id) {
            const item = requestsById.get(Number(id));

            const pointLabel = item?.point_label || 'esta ubicación';
            const typeLabel = item?.type_label || 'esta solicitud';

            if (!window.Swal) {
                return confirm(`¿Finalizar solicitud?\n\n${pointLabel} — ${typeLabel}`);
            }

            const result = await window.Swal.fire({
                title: '¿Finalizar solicitud?',
                html: `
                    <div class="text-start">
                        <p class="mb-2">
                            Esta solicitud se marcará como atendida y saldrá de la lista activa.
                        </p>

                        <div class="p-3 rounded-3" style="background:#f8fafc;border:1px solid #e5e7eb;">
                            <div class="small text-secondary mb-1">Ubicación</div>
                            <strong>${escapeHtml(pointLabel)}</strong>

                            <div class="small text-secondary mt-2 mb-1">Solicitud</div>
                            <strong>${escapeHtml(typeLabel)}</strong>
                        </div>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, finalizar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#16a34a',
                cancelButtonColor: '#6c757d',
                reverseButtons: true,
                focusCancel: true,
            });

            return result.isConfirmed;
        }

        async function updateStatus(id, action) {
            if (action === 'complete') {
                const confirmed = await confirmCompleteRequest(id);

                if (!confirmed) {
                    return;
                }
            }

            const url = `${takeBaseUrl}/${id}/${action}`;

            try {
                const response = await fetch(url, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({}),
                });

                const data = await response.json();

                if (!response.ok || !data.ok) {
                    alert(data.message || 'No se pudo actualizar la solicitud.');
                    return;
                }

                await loadFeed();
            } catch (e) {
                console.error(e);
                alert('Error de conexión.');
            }
        }

        initSoundPreference();
        loadFeed();
        setInterval(loadFeed, 8000);
    </script>
@endsection