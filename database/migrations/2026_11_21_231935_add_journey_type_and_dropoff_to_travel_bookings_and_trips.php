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
            $table->string('journey_type', 30)->default('round_trip')->after('package_type'); // 'round_trip', 'one_way'
            $table->string('one_way_direction', 30)->nullable()->after('journey_type'); // 'outbound' (going), 'return' (coming back)
            $table->string('dropoff_location', 150)->nullable()->after('pickup_location'); // Intermediate stop or custom drop point e.g. Haram, Kudai, Taif, Hotel
        });

        Schema::table('travel_trips', function (Blueprint $table) {
            $table->decimal('one_way_price', 10, 2)->nullable()->after('price_without_hotel'); // Specific one-way fare if configured
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('travel_bookings', function (Blueprint $table) {
            $table->dropColumn(['journey_type', 'one_way_direction', 'dropoff_location']);
        });

        Schema::table('travel_trips', function (Blueprint $table) {
            $table->dropColumn(['one_way_price']);
        });
    }
};
