<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hotel_qr_points', function (Blueprint $table) {
            if (! Schema::hasColumn('hotel_qr_points', 'previous_public_code')) {
                $table->string('previous_public_code', 80)->nullable()->after('public_code');
            }

            if (! Schema::hasColumn('hotel_qr_points', 'regenerated_at')) {
                $table->timestamp('regenerated_at')->nullable()->after('previous_public_code');
            }

            if (! Schema::hasColumn('hotel_qr_points', 'invalidated_at')) {
                $table->timestamp('invalidated_at')->nullable()->after('active');
            }

            if (! Schema::hasColumn('hotel_qr_points', 'invalidated_reason')) {
                $table->string('invalidated_reason', 255)->nullable()->after('invalidated_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('hotel_qr_points', function (Blueprint $table) {
            if (Schema::hasColumn('hotel_qr_points', 'invalidated_reason')) {
                $table->dropColumn('invalidated_reason');
            }

            if (Schema::hasColumn('hotel_qr_points', 'invalidated_at')) {
                $table->dropColumn('invalidated_at');
            }

            if (Schema::hasColumn('hotel_qr_points', 'regenerated_at')) {
                $table->dropColumn('regenerated_at');
            }

            if (Schema::hasColumn('hotel_qr_points', 'previous_public_code')) {
                $table->dropColumn('previous_public_code');
            }
        });
    }
};