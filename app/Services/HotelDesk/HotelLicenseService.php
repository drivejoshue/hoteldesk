<?php

namespace App\Services\HotelDesk;

use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HotelLicenseService
{
    public function prepareTrial(Hotel $hotel, int $days = 14): Hotel
    {
        return DB::transaction(function () use ($hotel, $days) {
            $hotel->forceFill([
                'status' => 'active',
                'panel_enabled' => true,
                'public_requests_enabled' => true,

                'plan_code' => 'lite',
                'license_status' => 'pending_trial',
                'billing_cycle' => 'trial',

                'trial_days' => $days,
                'trial_prepared_at' => now(),
                'trial_starts_at' => null,
                'trial_ends_at' => null,

                'license_starts_at' => null,
                'license_ends_at' => null,

                'terms_accepted_at' => null,
                'terms_accepted_by' => null,
                'terms_accepted_ip' => null,
                'terms_accepted_user_agent' => null,

                'monthly_price_cents' => 24900,
                'annual_price_cents' => 249900,
                'max_qr_points' => 10,
                'max_rooms' => 10,
            ])->save();

            return $hotel->fresh();
        });
    }

    public function activateTrial(Hotel $hotel, int $days = 14): Hotel
    {
        return $this->prepareTrial($hotel, $days);
    }

    public function acceptTermsAndStartTrial(
        Hotel $hotel,
        Request $request,
        ?string $acceptedBy = null
    ): Hotel {
        return DB::transaction(function () use ($hotel, $request, $acceptedBy) {
            $days = max(1, (int) ($hotel->trial_days ?: 14));

            $hotel->forceFill([
                'status' => 'active',
                'panel_enabled' => true,
                'public_requests_enabled' => true,

                'license_status' => 'trial',
                'billing_cycle' => 'trial',

                'trial_starts_at' => now(),
                'trial_ends_at' => now()->addDays($days)->endOfDay(),

                'terms_accepted_at' => now(),
                'terms_accepted_by' => $acceptedBy,
                'terms_accepted_ip' => $request->ip(),
                'terms_accepted_user_agent' => substr((string) $request->userAgent(), 0, 255),
            ])->save();

            return $hotel->fresh();
        });
    }

    public function activateAnnualLite(Hotel $hotel): Hotel
    {
        return DB::transaction(function () use ($hotel) {
            $hotel->forceFill([
                'status' => 'active',
                'panel_enabled' => true,
                'public_requests_enabled' => true,

                'plan_code' => 'lite',
                'license_status' => 'active',
                'billing_cycle' => 'annual',

                'license_starts_at' => now(),
                'license_ends_at' => now()->addYear()->endOfDay(),

                'monthly_price_cents' => 24900,
                'annual_price_cents' => 249900,
            ])->save();

            return $hotel->fresh();
        });
    }

    public function activateMonthlyLite(Hotel $hotel): Hotel
    {
        return DB::transaction(function () use ($hotel) {
            $hotel->forceFill([
                'status' => 'active',
                'panel_enabled' => true,
                'public_requests_enabled' => true,

                'plan_code' => 'lite',
                'license_status' => 'active',
                'billing_cycle' => 'monthly',

                'license_starts_at' => now(),
                'license_ends_at' => now()->addMonth()->endOfDay(),

                'monthly_price_cents' => 24900,
                'annual_price_cents' => 249900,
            ])->save();

            return $hotel->fresh();
        });
    }

    public function suspend(Hotel $hotel, ?string $reason = null): Hotel
    {
        $hotel->forceFill([
            'license_status' => 'suspended',
            'panel_enabled' => false,
            'public_requests_enabled' => false,
            'license_notes' => $reason,
        ])->save();

        return $hotel->fresh();
    }

    public function markExpired(Hotel $hotel): Hotel
    {
        $hotel->forceFill([
            'license_status' => 'expired',
            'panel_enabled' => false,
            'public_requests_enabled' => false,
        ])->save();

        return $hotel->fresh();
    }
}