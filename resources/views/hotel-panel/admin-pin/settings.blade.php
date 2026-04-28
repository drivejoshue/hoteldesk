@extends('layouts.hotel-panel')

@section('title', $hotel->name . ' · PIN admin')
@section('subtitle', 'Seguridad administrativa')

@section('topbar-actions')
    @include('hotel-panel.partials.topbar-actions')
@endsection

@section('content')
    <main class="container-tight py-4">
        <div class="card card-md shadow-sm">
            <div class="card-body">
                <div class="text-center mb-4">
                    <span class="avatar avatar-xl bg-primary-lt text-primary mb-3">
                        <i class="ti ti-shield-lock" style="font-size: 34px;"></i>
                    </span>

                    <h1 class="h2 mb-1">Cambiar PIN admin</h1>

                    <p class="text-secondary mb-0">
                        Este PIN protege reportes, códigos QR y opciones avanzadas del hotel.
                    </p>
                </div>

                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('hotel.admin-pin.settings.update', $hotel) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label" for="current_admin_pin">
                            PIN admin actual
                        </label>

                        <input
                            id="current_admin_pin"
                            name="current_admin_pin"
                            type="password"
                            inputmode="numeric"
                            maxlength="12"
                            class="form-control form-control-lg text-center @error('current_admin_pin') is-invalid @enderror"
                            autocomplete="off"
                            required
                        >

                        @error('current_admin_pin')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="admin_pin">
                            Nuevo PIN admin
                        </label>

                        <input
                            id="admin_pin"
                            name="admin_pin"
                            type="password"
                            inputmode="numeric"
                            minlength="4"
                            maxlength="12"
                            class="form-control form-control-lg text-center @error('admin_pin') is-invalid @enderror"
                            autocomplete="off"
                            required
                        >

                        @error('admin_pin')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label" for="admin_pin_confirmation">
                            Confirmar nuevo PIN admin
                        </label>

                        <input
                            id="admin_pin_confirmation"
                            name="admin_pin_confirmation"
                            type="password"
                            inputmode="numeric"
                            minlength="4"
                            maxlength="12"
                            class="form-control form-control-lg text-center"
                            autocomplete="off"
                            required
                        >
                    </div>

                    <button class="btn btn-primary btn-lg w-100" type="submit">
                        <i class="ti ti-device-floppy me-2"></i>
                        Guardar nuevo PIN admin
                    </button>
                </form>

                <div class="alert alert-info mt-4 mb-0">
                    <strong>Importante:</strong>
                    guarda este PIN con el responsable del hotel. Recepción no debe usarlo para operaciones diarias.
                </div>
            </div>
        </div>
    </main>
@endsection