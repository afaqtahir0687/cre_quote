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
            if (!Schema::hasColumn('travel_trips', 'employee_id')) {
                $table->foreignId('employee_id')->nullable()->after('shop_id')->constrained('employees')->nullOnDelete();
            }
        });

        Schema::table('travel_bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('travel_bookings', 'employee_id')) {
                $table->foreignId('employee_id')->nullable()->after('shop_id')->constrained('employees')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('travel_trips', function (Blueprint $table) {
            if (Schema::hasColumn('travel_trips', 'employee_id')) {
                $table->dropForeign(['employee_id']);
                $table->dropColumn('employee_id');
            }
        });

        Schema::table('travel_bookings', function (Blueprint $table) {
            if (Schema::hasColumn('travel_bookings', 'employee_id')) {
                $table->dropForeign(['employee_id']);
                $table->dropColumn('employee_id');
            }
        });
    }
};
