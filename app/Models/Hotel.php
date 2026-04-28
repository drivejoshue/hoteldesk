<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'status',
        'public_requests_enabled',
        'panel_enabled',
        'taxi_enabled',
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
    ];

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

    public function isPublicAvailable(): bool
    {
        return $this->status === 'active' && $this->public_requests_enabled;
    }

    public function isPanelAvailable(): bool
    {
        return in_array($this->status, ['draft', 'active', 'paused'], true)
            && $this->panel_enabled;
    }

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