<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Hotel extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'pin_hash',
        'admin_pin_hash',
        'admin_pin_changed_at',
        'admin_failed_attempts',
        'admin_locked_until',
        'logo_path',
        'phone',
        'email',
        'address',

        // Estado general
        'status',
        'public_requests_enabled',
        'panel_enabled',
        'taxi_enabled',

        // Licencia / plan
        'plan_code',
        'license_status',
        'billing_cycle',
        'trial_days',
        'trial_prepared_at',
        'trial_starts_at',
        'trial_ends_at',
        'license_starts_at',
        'license_ends_at',
        'monthly_price_cents',
        'annual_price_cents',
        'max_qr_points',
        'max_rooms',
        'license_notes',

        // Términos del trial
        'terms_accepted_at',
        'terms_accepted_by',
        'terms_accepted_ip',
        'terms_accepted_user_agent',

        // Personalización / integración
        'primary_color',
        'settings',
        'service_point_url',
    ];

    protected $casts = [
        'public_requests_enabled' => 'boolean',
        'panel_enabled' => 'boolean',
        'taxi_enabled' => 'boolean',
        'settings' => 'array',

        'admin_pin_changed_at' => 'datetime',
        'admin_locked_until' => 'datetime',
        'admin_failed_attempts' => 'integer',

        'trial_days' => 'integer',
        'trial_prepared_at' => 'datetime',
        'trial_starts_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'license_starts_at' => 'datetime',
        'license_ends_at' => 'datetime',

        'terms_accepted_at' => 'datetime',

        'monthly_price_cents' => 'integer',
        'annual_price_cents' => 'integer',
        'max_qr_points' => 'integer',
        'max_rooms' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    public function qrPoints(): HasMany
    {
        return $this->hasMany(HotelQrPoint::class);
    }

    public function requests(): HasMany
    {
        return $this->hasMany(HotelRequest::class);
    }

    public function qrCreationRequests(): HasMany
    {
        return $this->hasMany(HotelQrCreationRequest::class);
    }

    public function pinResetRequests(): HasMany
    {
        return $this->hasMany(HotelPinResetRequest::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Licencia / trial
    |--------------------------------------------------------------------------
    */

    public function isLicenseActive(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        return match ($this->license_status) {
            'trial' => $this->trial_ends_at instanceof Carbon
                && now()->lessThanOrEqualTo($this->trial_ends_at),

            'active' => $this->license_ends_at === null
                || now()->lessThanOrEqualTo($this->license_ends_at),

            default => false,
        };
    }

    public function licenseExpired(): bool
    {
        return ! $this->isLicenseActive();
    }

    public function isTrial(): bool
    {
        return $this->license_status === 'trial';
    }

    public function isTrialPending(): bool
    {
        return $this->license_status === 'pending_trial';
    }

    public function trialTermsAccepted(): bool
    {
        return $this->terms_accepted_at !== null;
    }

    public function needsTrialTermsAcceptance(): bool
    {
        return $this->isTrialPending()
            && ! $this->trialTermsAccepted();
    }

    public function licenseEndsAt(): ?Carbon
    {
        return match ($this->license_status) {
            'trial' => $this->trial_ends_at,
            'active' => $this->license_ends_at,
            default => null,
        };
    }

    public function trialDaysRemaining(): ?int
    {
        if (! $this->isTrial() || ! $this->trial_ends_at) {
            return null;
        }

        return (int) max(
            0,
            now()->startOfDay()->diffInDays(
                $this->trial_ends_at->copy()->startOfDay(),
                false
            )
        );
    }

    public function licenseDaysRemaining(): ?int
    {
        $endsAt = $this->licenseEndsAt();

        if (! $endsAt) {
            return null;
        }

        return (int) max(
            0,
            now()->startOfDay()->diffInDays(
                $endsAt->copy()->startOfDay(),
                false
            )
        );
    }

    public function licenseLabel(): string
    {
        return match ($this->license_status) {
            'pending_trial' => 'Prueba pendiente',
            'trial' => 'Prueba',
            'active' => 'Activa',
            'expired' => 'Vencida',
            'suspended' => 'Suspendida',
            'canceled' => 'Cancelada',
            default => 'Sin licencia',
        };
    }

    public function licenseStatusClass(): string
    {
        return match ($this->license_status) {
            'pending_trial' => 'bg-yellow-lt text-yellow',
            'trial' => 'bg-blue-lt text-blue',
            'active' => 'bg-green-lt text-green',
            'expired' => 'bg-orange-lt text-orange',
            'suspended', 'canceled' => 'bg-red-lt text-red',
            default => 'bg-secondary-lt text-secondary',
        };
    }

    public function planLabel(): string
    {
        return match ($this->plan_code) {
            'lite' => 'Lite',
            'standard' => 'Estándar',
            'pro' => 'Pro',
            'custom' => 'Personalizado',
            default => ucfirst((string) $this->plan_code),
        };
    }

    public function billingCycleLabel(): string
    {
        return match ($this->billing_cycle) {
            'trial' => 'Prueba',
            'monthly' => 'Mensual',
            'annual' => 'Anual',
            'manual' => 'Manual',
            default => 'Sin ciclo',
        };
    }

    public function formattedMonthlyPrice(): string
    {
        $amount = ((int) ($this->monthly_price_cents ?? 0)) / 100;

        return '$' . number_format($amount, 2) . ' MXN';
    }

    public function formattedAnnualPrice(): string
    {
        $amount = ((int) ($this->annual_price_cents ?? 0)) / 100;

        return '$' . number_format($amount, 2) . ' MXN';
    }

    /*
    |--------------------------------------------------------------------------
    | Disponibilidad operativa
    |--------------------------------------------------------------------------
    */

public function isPublicAvailable(): bool
{
    return $this->status === 'active'
        && $this->public_requests_enabled
        && $this->isLicenseActive();
}

public function isPanelAvailable(): bool
{
    if ($this->status !== 'active') {
        return false;
    }

    if (! $this->panel_enabled) {
        return false;
    }

    /*
    |--------------------------------------------------------------------------
    | Importante:
    |--------------------------------------------------------------------------
    | El panel debe permitir entrar cuando la prueba está pendiente.
    | Así el hotel puede aceptar términos y activar el trial.
    | La operación real del dashboard queda protegida por hotel.license.
    |
    */

    return $this->isLicenseActive()
        || $this->isTrialPending();
}

    /*
    |--------------------------------------------------------------------------
    | Sesiones del hotel
    |--------------------------------------------------------------------------
    */

    public function sessionKey(): string
    {
        return 'hoteldesk.hotel.' . $this->id . '.authenticated';
    }

    public function adminSessionKey(): string
    {
        return 'hoteldesk.hotel.' . $this->id . '.admin_authenticated';
    }

    public function adminVerifiedAtSessionKey(): string
    {
        return 'hoteldesk.hotel.' . $this->id . '.admin_verified_at';
    }

    /*
    |--------------------------------------------------------------------------
    | Seguridad PIN admin
    |--------------------------------------------------------------------------
    */

    public function isAdminPinLocked(): bool
    {
        return $this->admin_locked_until !== null
            && $this->admin_locked_until->isFuture();
    }

    public function registerAdminPinFailedAttempt(): void
    {
        $attempts = (int) $this->admin_failed_attempts + 1;

        $this->forceFill([
            'admin_failed_attempts' => $attempts,
            'admin_locked_until' => $attempts >= 5 ? now()->addMinutes(10) : null,
        ])->save();
    }

    public function resetAdminPinAttempts(): void
    {
        $this->forceFill([
            'admin_failed_attempts' => 0,
            'admin_locked_until' => null,
        ])->save();
    }
}