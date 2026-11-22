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
            $table->unsignedInteger('vip_seats_count')->default(10)->after('total_seats');
            $table->unsignedInteger('business_seats_count')->default(15)->after('vip_seats_count');
            $table->unsignedInteger('economy_seats_count')->default(24)->after('business_seats_count');
            
            $table->json('vip_seat_numbers')->nullable()->after('economy_seats_count');
            $table->json('business_seat_numbers')->nullable()->after('vip_seat_numbers');
            $table->json('economy_seat_numbers')->nullable()->after('business_seat_numbers');

            $table->decimal('vip_price', 10, 2)->nullable()->after('price_with_hotel');
            $table->decimal('business_price', 10, 2)->nullable()->after('vip_price');
            $table->decimal('economy_price', 10, 2)->nullable()->after('business_price');
        });

        Schema::table('travel_bookings', function (Blueprint $table) {
            $table->string('seat_class', 30)->default('economy')->after('seat_number'); // vip, business, economy
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('travel_bookings', function (Blueprint $table) {
            $table->dropColumn('seat_class');
        });

        Schema::table('travel_trips', function (Blueprint $table) {
            $table->dropColumn([
                'vip_seats_count',
                'business_seats_count',
                'economy_seats_count',
                'vip_seat_numbers',
                'business_seat_numbers',
                'economy_seat_numbers',
                'vip_price',
                'business_price',
                'economy_price',
            ]);
        });
    }
};
