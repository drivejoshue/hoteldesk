<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HotelQrCreationRequest extends Model
{
    protected $table = 'hotel_qr_creation_requests';

    protected $fillable = [
        'hotel_id',
        'label',
        'type',
        'floor',
        'mode',
        'fixed_request_type',
        'allowed_request_types',
        'note',
        'status',
        'reviewed_by',
        'reviewed_at',
        'reject_reason',
        'created_qr_point_id',
    ];

    protected $casts = [
        'allowed_request_types' => 'array',
        'reviewed_at' => 'datetime',
    ];

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    public function createdQrPoint(): BelongsTo
    {
        return $this->belongsTo(HotelQrPoint::class, 'created_qr_point_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(SysAppAdmin::class, 'reviewed_by');
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'pending' => 'Pendiente',
            'approved' => 'Aprobada',
            'rejected' => 'Rechazada',
            'canceled' => 'Cancelada',
            default => $this->status,
        };
    }
}