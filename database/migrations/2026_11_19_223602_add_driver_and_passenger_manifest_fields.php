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
            if (!Schema::hasColumn('travel_trips', 'driver_1_name')) {
                $table->string('driver_1_name', 150)->nullable()->after('bus_number');
                $table->string('driver_1_nationality', 50)->nullable()->after('driver_1_name');
                $table->string('driver_1_phone', 50)->nullable()->after('driver_1_nationality');
                $table->string('driver_2_name', 150)->nullable()->after('driver_1_phone');
                $table->string('driver_2_nationality', 50)->nullable()->after('driver_2_name');
                $table->string('driver_2_phone', 50)->nullable()->after('driver_2_nationality');
            }
        });

        Schema::table('travel_bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('travel_bookings', 'passenger_nationality')) {
                $table->string('passenger_nationality', 50)->nullable()->after('passenger_gender');
            }
            if (!Schema::hasColumn('travel_bookings', 'pickup_location')) {
                $table->string('pickup_location', 150)->nullable()->after('passenger_nationality');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('travel_trips', function (Blueprint $table) {
            $table->dropColumn([
                'driver_1_name',
                'driver_1_nationality',
                'driver_1_phone',
                'driver_2_name',
                'driver_2_nationality',
                'driver_2_phone',
            ]);
        });

        Schema::table('travel_bookings', function (Blueprint $table) {
            $table->dropColumn([
                'passenger_nationality',
                'pickup_location',
            ]);
        });
    }
};
