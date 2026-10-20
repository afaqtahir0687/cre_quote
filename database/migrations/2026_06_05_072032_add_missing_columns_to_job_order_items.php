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
        Schema::table('job_order_items', function (Blueprint $table) {
            $table->string('item_text')->nullable()->after('description');
            $table->decimal('tax_percentage', 10, 2)->default(0)->after('total');
            $table->decimal('discount', 10, 2)->default(0)->after('tax_percentage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_order_items', function (Blueprint $table) {
            $table->dropColumn(['item_text', 'tax_percentage', 'discount']);
        });
    }
};
