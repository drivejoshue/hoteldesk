<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HotelRequest extends Model
{
    protected $fillable = [
        'hotel_id',
        'hotel_qr_point_id',
        'point_label',
        'type_key',
        'title',
        'note',
        'status',
        'source',
        'guest_name',
        'ip_address',
        'user_agent',
        'taken_at',
        'completed_at',
        'canceled_at',
    ];

    protected $casts = [
        'taken_at' => 'datetime',
        'completed_at' => 'datetime',
        'canceled_at' => 'datetime',
    ];

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    public function qrPoint(): BelongsTo
    {
        return $this->belongsTo(HotelQrPoint::class, 'hotel_qr_point_id');
    }

    public function statusLabel(): string
    {
        return config('hoteldesk.statuses.' . $this->status, $this->status);
    }

    public function typeLabel(): string
    {
        return config('hoteldesk.request_types.' . $this->type_key . '.label', $this->title);
    }

    public function typeIcon(): string
    {
        return config('hoteldesk.request_types.' . $this->type_key . '.icon', '•');
    }
}