<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HotelPinResetRequest extends Model
{
    protected $table = 'hotel_pin_reset_requests';

    protected $fillable = [
        'hotel_id',
        'requester_name',
        'requester_phone',
        'note',
        'status',
        'reviewed_by',
        'reviewed_at',
        'reject_reason',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(SysAppAdmin::class, 'reviewed_by');
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'pending' => 'Pendiente',
            'completed' => 'Completada',
            'rejected' => 'Rechazada',
            'canceled' => 'Cancelada',
            default => $this->status,
        };
    }
}