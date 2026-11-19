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
        Schema::table('travel_trips', function (Blueprint $table) {
            if (!Schema::hasColumn('travel_trips', 'hotel_address')) {
                $table->string('hotel_address')->nullable()->after('hotel_name');
            }
        });

        Schema::table('travel_bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('travel_bookings', 'hotel_address')) {
                $table->string('hotel_address')->nullable()->after('hotel_name');
            }
            if (!Schema::hasColumn('travel_bookings', 'salesman_name')) {
                $table->string('salesman_name', 100)->nullable()->after('passenger_gender');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('travel_trips', function (Blueprint $table) {
            if (Schema::hasColumn('travel_trips', 'hotel_address')) {
                $table->dropColumn('hotel_address');
            }
        });

        Schema::table('travel_bookings', function (Blueprint $table) {
            if (Schema::hasColumn('travel_bookings', 'hotel_address')) {
                $table->dropColumn('hotel_address');
            }
            if (Schema::hasColumn('travel_bookings', 'salesman_name')) {
                $table->dropColumn('salesman_name');
            }
        });
    }
};
