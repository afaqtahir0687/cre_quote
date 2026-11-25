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
            if (!Schema::hasColumn('travel_bookings', 'hotel_city')) {
                $table->string('hotel_city', 100)->nullable()->after('hotel_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('travel_bookings', function (Blueprint $table) {
            if (Schema::hasColumn('travel_bookings', 'hotel_city')) {
                $table->dropColumn('hotel_city');
            }
        });
    }
};
