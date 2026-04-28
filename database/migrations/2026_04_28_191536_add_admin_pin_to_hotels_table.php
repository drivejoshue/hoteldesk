<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            if (! Schema::hasColumn('hotels', 'admin_pin_hash')) {
                $table->string('admin_pin_hash')->nullable()->after('pin_hash');
            }

            if (! Schema::hasColumn('hotels', 'admin_pin_changed_at')) {
                $table->timestamp('admin_pin_changed_at')->nullable()->after('admin_pin_hash');
            }

            if (! Schema::hasColumn('hotels', 'admin_failed_attempts')) {
                $table->unsignedTinyInteger('admin_failed_attempts')->default(0)->after('admin_pin_changed_at');
            }

            if (! Schema::hasColumn('hotels', 'admin_locked_until')) {
                $table->timestamp('admin_locked_until')->nullable()->after('admin_failed_attempts');
            }
        });

        /*
         * Inicialmente dejamos el PIN admin igual al PIN normal si no existe.
         * Así no te quedas bloqueado. Luego el jefe del hotel lo cambia.
         */
        DB::table('hotels')
            ->whereNull('admin_pin_hash')
            ->orderBy('id')
            ->get()
            ->each(function ($hotel) {
                DB::table('hotels')
                    ->where('id', $hotel->id)
                    ->update([
                        'admin_pin_hash' => $hotel->pin_hash,
                        'admin_pin_changed_at' => now(),
                    ]);
            });
    }

    public function down(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            if (Schema::hasColumn('hotels', 'admin_locked_until')) {
                $table->dropColumn('admin_locked_until');
            }

            if (Schema::hasColumn('hotels', 'admin_failed_attempts')) {
                $table->dropColumn('admin_failed_attempts');
            }

            if (Schema::hasColumn('hotels', 'admin_pin_changed_at')) {
                $table->dropColumn('admin_pin_changed_at');
            }

            if (Schema::hasColumn('hotels', 'admin_pin_hash')) {
                $table->dropColumn('admin_pin_hash');
            }
        });
    }
};