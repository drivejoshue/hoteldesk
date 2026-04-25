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
    ];

    public function qrPoints(): HasMany
    {
        return $this->hasMany(HotelQrPoint::class);
    }

    public function requests(): HasMany
    {
        return $this->hasMany(HotelRequest::class);
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

    public function qrCreationRequests(): HasMany
{
    return $this->hasMany(HotelQrCreationRequest::class);
}

public function pinResetRequests(): HasMany
{
    return $this->hasMany(HotelPinResetRequest::class);
}
}