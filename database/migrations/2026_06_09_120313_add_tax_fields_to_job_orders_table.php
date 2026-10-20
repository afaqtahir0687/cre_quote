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
        Schema::table('job_orders', function (Blueprint $table) {
            $table->decimal('subtotal', 10, 2)->default(0)->after('notes');
            $table->decimal('total_tax', 10, 2)->default(0)->after('subtotal');
            $table->decimal('total_discount', 10, 2)->default(0)->after('total_tax');
            $table->decimal('shipping', 10, 2)->default(0)->after('total_discount');
            $table->decimal('grand_total', 10, 2)->default(0)->after('shipping');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_orders', function (Blueprint $table) {
            $table->dropColumn(['subtotal', 'total_tax', 'total_discount', 'shipping', 'grand_total']);
        });
    }
};
