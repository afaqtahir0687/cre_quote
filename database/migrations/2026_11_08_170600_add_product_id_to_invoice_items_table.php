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
        Schema::table('invoice_items', function (Blueprint $table) {
            if (!Schema::hasColumn('invoice_items', 'product_id')) {
                $table->foreignId('product_id')->nullable()->after('invoice_id')->constrained('products')->nullOnDelete();
            }
        });

        Schema::table('quote_items', function (Blueprint $table) {
            if (!Schema::hasColumn('quote_items', 'product_id')) {
                $table->foreignId('product_id')->nullable()->after('quote_id')->constrained('products')->nullOnDelete();
            }
        });

        Schema::table('job_order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('job_order_items', 'product_id')) {
                $table->foreignId('product_id')->nullable()->after('job_order_id')->constrained('products')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            if (Schema::hasColumn('invoice_items', 'product_id')) {
                $table->dropForeign(['product_id']);
                $table->dropColumn('product_id');
            }
        });

        Schema::table('quote_items', function (Blueprint $table) {
            if (Schema::hasColumn('quote_items', 'product_id')) {
                $table->dropForeign(['product_id']);
                $table->dropColumn('product_id');
            }
        });

        Schema::table('job_order_items', function (Blueprint $table) {
            if (Schema::hasColumn('job_order_items', 'product_id')) {
                $table->dropForeign(['product_id']);
                $table->dropColumn('product_id');
            }
        });
    }
};

