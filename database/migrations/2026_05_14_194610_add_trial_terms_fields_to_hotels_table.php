<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            $table->unsignedSmallInteger('trial_days')
                ->default(14)
                ->after('billing_cycle');

            $table->timestamp('trial_prepared_at')
                ->nullable()
                ->after('trial_days');

            $table->timestamp('terms_accepted_at')
                ->nullable()
                ->after('trial_prepared_at');

            $table->string('terms_accepted_by', 120)
                ->nullable()
                ->after('terms_accepted_at');

            $table->string('terms_accepted_ip', 45)
                ->nullable()
                ->after('terms_accepted_by');

            $table->string('terms_accepted_user_agent', 255)
                ->nullable()
                ->after('terms_accepted_ip');
        });
    }

    public function down(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            $table->dropColumn([
                'trial_days',
                'trial_prepared_at',
                'terms_accepted_at',
                'terms_accepted_by',
                'terms_accepted_ip',
                'terms_accepted_user_agent',
            ]);
        });
    }
};