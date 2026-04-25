@extends('layouts.hoteldesk-public')

@section('title', 'Solicitud enviada')

@section('content')
    <div class="text-center py-3">
        <span class="avatar avatar-xl bg-green-lt text-green mb-3">
            <i class="ti ti-check" style="font-size: 34px;"></i>
        </span>

        <h1 class="h2 mb-2">Solicitud enviada</h1>

        <p class="text-secondary mb-3">
            Recepción ya fue notificada.<br>
            Para hacer otra solicitud, vuelve a escanear el código QR.
        </p>

        <div class="badge bg-primary-lt text-primary mb-4">
            <i class="ti ti-ticket me-1"></i>
            Folio #{{ $hotelRequest->id }}
        </div>

        <button class="btn btn-primary btn-lg w-100" type="button" onclick="exitRequestPage()">
            <i class="ti ti-door-exit me-2"></i>
            Salir
        </button>

        <div class="alert alert-info mt-3 mb-0" id="closeHint" style="display: none;">
            Te estamos sacando de esta pantalla. Para enviar otra solicitud, escanea nuevamente el QR.
        </div>
    </div>

    <script>
        const exitUrl = 'https://www.google.com/';

        /*
         * Evita que el botón atrás regrese cómodamente al formulario.
         * El huésped debe volver a escanear el QR para crear otra solicitud.
         */
        try {
            window.history.replaceState({ sent: true }, '', window.location.href);
            window.history.pushState({ blocked: true }, '', window.location.href);

            window.addEventListener('popstate', () => {
                window.location.replace(exitUrl);
            });
        } catch (e) {
            console.warn('No se pudo controlar el historial del navegador.', e);
        }

        function exitRequestPage() {
            const closeHint = document.getElementById('closeHint');

            if (closeHint) {
                closeHint.style.display = 'block';
            }

            /*
             * Algunos navegadores cierran la pestaña si fue abierta desde otra ventana.
             * Si no se puede cerrar, redirige fuera de HotelDesk.
             */
            try {
                window.open('', '_self');
                window.close();
            } catch (e) {
                console.warn('El navegador no permitió cerrar la ventana.', e);
            }

            setTimeout(() => {
                window.location.replace(exitUrl);
            }, 350);
        }

        /*
         * Opcional: salida automática después de unos segundos.
         * Si no quieres auto-salida, borra este bloque.
         */
        setTimeout(() => {
            window.location.replace(exitUrl);
        }, 12000);
    </script>
@endsection