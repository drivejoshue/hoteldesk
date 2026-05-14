<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            $table->string('plan_code', 30)->default('lite')->after('status');
            $table->string('license_status', 30)->default('trial')->after('plan_code');
            $table->string('billing_cycle', 30)->default('trial')->after('license_status');

            $table->timestamp('trial_starts_at')->nullable()->after('billing_cycle');
            $table->timestamp('trial_ends_at')->nullable()->after('trial_starts_at');

            $table->timestamp('license_starts_at')->nullable()->after('trial_ends_at');
            $table->timestamp('license_ends_at')->nullable()->after('license_starts_at');

            $table->unsignedInteger('monthly_price_cents')->nullable()->after('license_ends_at');
            $table->unsignedInteger('annual_price_cents')->nullable()->after('monthly_price_cents');

            $table->unsignedInteger('max_qr_points')->nullable()->after('annual_price_cents');
            $table->unsignedInteger('max_rooms')->nullable()->after('max_qr_points');

            $table->text('license_notes')->nullable()->after('max_rooms');

            $table->index(['license_status', 'trial_ends_at']);
            $table->index(['license_status', 'license_ends_at']);
            $table->index(['plan_code', 'billing_cycle']);
        });
    }

    public function down(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            $table->dropIndex(['license_status', 'trial_ends_at']);
            $table->dropIndex(['license_status', 'license_ends_at']);
            $table->dropIndex(['plan_code', 'billing_cycle']);

            $table->dropColumn([
                'plan_code',
                'license_status',
                'billing_cycle',
                'trial_starts_at',
                'trial_ends_at',
                'license_starts_at',
                'license_ends_at',
                'monthly_price_cents',
                'annual_price_cents',
                'max_qr_points',
                'max_rooms',
                'license_notes',
            ]);
        });
    }
};