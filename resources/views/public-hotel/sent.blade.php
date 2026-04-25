@extends('layouts.hoteldesk-public')

@section('title', 'Solicitud enviada')

@section('content')
    <section class="hd-card hd-success-screen">
        <div class="hd-success-icon">
            <i class="ti ti-check"></i>
        </div>

        <h1 class="hd-title">Solicitud enviada</h1>

        <p class="hd-text-muted" style="line-height: 1.5;">
            Recepción ya fue notificada.<br>
            Puedes cerrar esta pantalla.
        </p>

        <div class="hd-folio">
            <i class="ti ti-ticket"></i>
            Folio #{{ $hotelRequest->id }}
        </div>

        <div style="display: grid; gap: 10px; margin-top: 22px;">
            <button class="hd-btn hd-btn-primary hd-btn-full" type="button" onclick="tryCloseWindow()">
                <i class="ti ti-x"></i>
                Cerrar pantalla
            </button>

           
        </div>

        <div class="hd-bottom-hint" id="closeHint" style="display: none;">
            Si el navegador no cierra esta pantalla automáticamente, puedes cerrarla manualmente.
        </div>
    </section>

    <script>
        function tryCloseWindow() {
            window.close();

            setTimeout(() => {
                const closeHint = document.getElementById('closeHint');

                if (closeHint) {
                    closeHint.style.display = 'block';
                }
            }, 300);
        }
    </script>
@endsection