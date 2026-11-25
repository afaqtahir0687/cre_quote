<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('travel_bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('travel_bookings', 'makkah_nights')) {
                $table->unsignedSmallInteger('makkah_nights')->default(0)->nullable()->after('nights_count');
            }
            if (!Schema::hasColumn('travel_bookings', 'madinah_nights')) {
                $table->unsignedSmallInteger('madinah_nights')->default(0)->nullable()->after('makkah_nights');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('travel_bookings', function (Blueprint $table) {
            $colsToDrop = [];
            if (Schema::hasColumn('travel_bookings', 'makkah_nights')) {
                $colsToDrop[] = 'makkah_nights';
            }
            if (Schema::hasColumn('travel_bookings', 'madinah_nights')) {
                $colsToDrop[] = 'madinah_nights';
            }
            if (!empty($colsToDrop)) {
                $table->dropColumn($colsToDrop);
            }
        });
    }
};
