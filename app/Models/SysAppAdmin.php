<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class SysAppAdmin extends Model
{
    protected $table = 'sysapp_admins';

    protected $fillable = [
        'name',
        'email',
        'password_hash',
        'role',
        'active',
        'failed_attempts',
        'locked_until',
        'last_login_at',
    ];

    protected $casts = [
        'active' => 'boolean',
        'locked_until' => 'datetime',
        'last_login_at' => 'datetime',
    ];

    public function isLocked(): bool
    {
        return $this->locked_until instanceof Carbon
            && $this->locked_until->isFuture();
    }

    public function canLogin(): bool
    {
        return $this->active && ! $this->isLocked();
    }

    public function resetFailedAttempts(): void
    {
        $this->forceFill([
            'failed_attempts' => 0,
            'locked_until' => null,
            'last_login_at' => now(),
        ])->save();
    }

    public function registerFailedAttempt(int $maxAttempts = 5, int $lockMinutes = 15): void
    {
        $failedAttempts = $this->failed_attempts + 1;

        $this->failed_attempts = $failedAttempts;

        if ($failedAttempts >= $maxAttempts) {
            $this->locked_until = now()->addMinutes($lockMinutes);
        }

        $this->save();
    }
}