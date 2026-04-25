@extends('layouts.hotel-panel')

@section('title', $hotel->name . ' · Recepción')
@section('subtitle', 'Panel de solicitudes')

@section('topbar-actions')
    @include('hotel-panel.partials.topbar-actions', ['showSound' => true])
@endsection

@section('content')
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
        taxi: 'ti-taxi',
        suggestion: 'ti-message-chatbot',
        other: 'ti-message-dots',
    };

    return icons[typeKey] ?? 'ti-message-dots';
}

        function statusBadge(status, label) {
            const cls = status === 'pending' ? 'hd-status-pending' : 'hd-status-progress';
            return `<span class="hd-status-pill ${cls}">${escapeHtml(label)}</span>`;
        }

        function renderRequestCard(item, isNew) {
            const note = item.note
                ? `<div class="hd-note">${escapeHtml(item.note)}</div>`
                : `<div class="hd-note">Sin nota adicional.</div>`;

            const takeButton = item.status === 'pending'
                ? `<button class="hd-btn hd-btn-primary" onclick="updateStatus(${item.id}, 'take')">
                        <i class="ti ti-hand-click"></i> Tomar
                   </button>`
                : '';

            const orbanaButton = item.type_key === 'taxi' && hotelServicePointUrl
                ? `<a class="hd-btn hd-btn-primary" href="${escapeHtml(hotelServicePointUrl)}" target="_blank" rel="noopener">
                        <i class="ti ti-taxi"></i> Pedir por Orbana
                   </a>`
                : '';

            return `
                <article class="hd-request-card ${isNew ? 'is-new' : ''}">
                    <div class="hd-request-head">
                        <div>
                            <div class="hd-request-title">
                                <i class="ti ${iconForType(item.type_key)}"></i>
                                ${escapeHtml(item.point_label)} — ${escapeHtml(item.type_label)}
                            </div>
                           <div class="hd-request-meta">
    <strong>${escapeHtml(item.created_human ?? '')}</strong>
    <span style="opacity:.75;"> · Pedido: ${escapeHtml(item.created_short ?? item.created_at ?? '')}</span>
</div>
                        </div>

                        ${statusBadge(item.status, item.status_label)}
                    </div>

                    ${note}

                    <div class="hd-card-actions">
                        ${orbanaButton}
                        ${takeButton}

                        <button class="hd-btn hd-btn-success" onclick="updateStatus(${item.id}, 'complete')">
                            <i class="ti ti-check"></i> Resolver
                        </button>

                        <button class="hd-btn hd-btn-danger" onclick="updateStatus(${item.id}, 'cancel')">
                            <i class="ti ti-x"></i> Cancelar
                        </button>
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

        async function updateStatus(id, action) {
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