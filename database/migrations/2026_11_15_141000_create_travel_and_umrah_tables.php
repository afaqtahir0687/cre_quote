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
        // 1. Travel Trips Table (الرحلات البرية والعمرة)
        Schema::create('travel_trips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained('shops')->onDelete('cascade');
            $table->string('trip_code', 50);
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('bus_name')->default('Luxury Coach Bus');
            $table->string('bus_number', 50)->nullable();
            $table->unsignedInteger('total_seats')->default(49);
            $table->string('departure_city');
            $table->string('destination_city');
            $table->dateTime('departure_date');
            $table->dateTime('return_date')->nullable();
            $table->unsignedInteger('duration_days')->default(10);
            $table->decimal('price_without_hotel', 10, 2)->default(0.00); // Seat & travel fare only
            $table->decimal('price_with_hotel', 10, 2)->default(0.00);    // Complete package with hotel
            $table->string('hotel_name')->nullable();
            $table->string('hotel_rating', 20)->nullable(); // e.g. 3-Star, 4-Star, 5-Star
            $table->string('status', 30)->default('upcoming'); // upcoming, ongoing, completed, cancelled
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['shop_id', 'status']);
            $table->index(['shop_id', 'trip_code']);
        });

        // 2. Travel Bookings Table (حجوزات المقاعد والمسافرين)
        Schema::create('travel_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained('shops')->onDelete('cascade');
            $table->foreignId('travel_trip_id')->constrained('travel_trips')->onDelete('cascade');
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->string('booking_reference', 50);
            $table->string('passenger_name');
            $table->string('passenger_phone', 50);
            $table->string('passenger_id_number', 50)->nullable(); // Passport, Iqama or National ID
            $table->string('passenger_gender', 15)->default('male'); // male, female
            $table->unsignedInteger('seat_number'); // 1 to 49
            $table->string('package_type', 30)->default('with_hotel'); // with_hotel, without_hotel
            $table->string('hotel_name')->nullable();
            $table->string('room_type', 50)->nullable(); // quad_sharing, triple_sharing, double_sharing, private_room
            $table->unsignedInteger('nights_count')->nullable();
            $table->decimal('travel_fare', 10, 2)->default(0.00);
            $table->decimal('hotel_expense', 10, 2)->default(0.00);
            $table->decimal('extra_charges', 10, 2)->default(0.00);
            $table->decimal('discount', 10, 2)->default(0.00);
            $table->decimal('tax_amount', 10, 2)->default(0.00);
            $table->decimal('total_amount', 10, 2)->default(0.00);
            $table->decimal('paid_amount', 10, 2)->default(0.00);
            $table->decimal('balance_due', 10, 2)->default(0.00);
            $table->string('payment_status', 30)->default('unpaid'); // unpaid, partial, paid
            $table->string('payment_method', 50)->default('cash'); // cash, bank_transfer, card, online
            $table->string('booking_status', 30)->default('confirmed'); // confirmed, cancelled
            $table->foreignId('invoice_id')->nullable()->constrained('invoices')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            // Strict seat uniqueness per trip (when confirmed)
            $table->index(['shop_id', 'travel_trip_id']);
            $table->index(['travel_trip_id', 'seat_number']);
            $table->index(['shop_id', 'booking_reference']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('travel_bookings');
        Schema::dropIfExists('travel_trips');
    }
};
