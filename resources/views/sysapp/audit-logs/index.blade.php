@extends('sysapp.layout')

@section('title', 'Logs · HotelDesk Lite')

@section('content')
    <div class="page-head">
        <div>
            <h1>Logs de auditoría</h1>
            <div class="muted">Registro de acciones sensibles dentro de HotelDesk Lite.</div>
        </div>
    </div>

    <form class="card" method="GET" action="{{ route('sysapp.audit-logs.index') }}" style="margin-bottom: 18px;">
        <div class="grid grid-3">
            <div class="field">
                <label>Acción</label>
                <select name="action">
                    <option value="">Todas</option>
                    @foreach($actions as $action)
                        <option value="{{ $action }}" @selected(request('action') === $action)>
                            {{ $action }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="field">
                <label>Hotel</label>
                <select name="hotel_id">
                    <option value="">Todos</option>
                    @foreach($hotels as $hotel)
                        <option value="{{ $hotel->id }}" @selected((string) request('hotel_id') === (string) $hotel->id)>
                            {{ $hotel->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="field">
                <label>Buscar</label>
                <input name="q" value="{{ request('q') }}" placeholder="IP, acción o descripción">
            </div>
        </div>

        <div class="actions">
            <button class="btn btn-primary" type="submit">Filtrar</button>
            <a class="btn btn-soft" href="{{ route('sysapp.audit-logs.index') }}">Limpiar</a>
        </div>
    </form>

    <div class="card">
        <table class="table">
            <thead>
            <tr>
                <th>Fecha</th>
                <th>Admin</th>
                <th>Hotel</th>
                <th>Acción</th>
                <th>Descripción</th>
                <th>IP</th>
                <th>Meta</th>
            </tr>
            </thead>

            <tbody>
            @forelse($logs as $log)
                <tr>
                    <td>
                        {{ $log->created_at?->format('d/m/Y H:i:s') }}
                    </td>

                    <td>
                        @if($log->admin)
                            <strong>{{ $log->admin->name }}</strong><br>
                            <span class="muted">{{ $log->admin->email }}</span>
                        @else
                            <span class="muted">Sistema / no autenticado</span>
                        @endif
                    </td>

                    <td>
                        {{ $log->hotel?->name ?? '—' }}
                    </td>

                    <td>
                        <span class="badge badge-draft">
                            {{ $log->action }}
                        </span>
                    </td>

                    <td>
                        {{ $log->description ?: '—' }}
                    </td>

                    <td>
                        {{ $log->ip_address ?: '—' }}
                    </td>

                    <td>
                        @if($log->meta)
                            <details>
                                <summary>Ver</summary>
                                <pre style="max-width: 320px; overflow:auto; background:#f8fafc; padding:10px; border-radius:10px;">{{ json_encode($log->meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                            </details>
                        @else
                            —
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">No hay logs registrados.</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        <div style="margin-top: 14px;">
            {{ $logs->links() }}
        </div>
    </div>
@endsection