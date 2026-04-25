<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HotelQrPoint extends Model
{
    protected $table = 'hotel_qr_points';

    protected $fillable = [
        'hotel_id',
        'label',
        'type',
        'floor',
        'public_code',
        'mode',
        'fixed_request_type',
        'allowed_request_types',
        'active',
    ];

    protected $casts = [
        'allowed_request_types' => 'array',
        'active' => 'boolean',
    ];

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    public function requests(): HasMany
    {
        return $this->hasMany(HotelRequest::class, 'hotel_qr_point_id');
    }

    public function availableRequestTypes(): array
    {
        $all = config('hoteldesk.request_types', []);

        if ($this->mode === 'direct' && $this->fixed_request_type) {
            return array_intersect_key($all, [$this->fixed_request_type => true]);
        }

        if ($this->mode === 'limited' && is_array($this->allowed_request_types)) {
            return array_intersect_key($all, array_flip($this->allowed_request_types));
        }

        return $all;
    }
}