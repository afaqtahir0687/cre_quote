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
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'category_id')) {
                $table->unsignedBigInteger('category_id')->nullable()->after('shop_id');
                $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
            }

            if (!Schema::hasColumn('products', 'unit_id')) {
                $table->unsignedBigInteger('unit_id')->nullable()->after('category_id');
                $table->foreign('unit_id')->references('id')->on('units')->onDelete('set null');
            }

            if (!Schema::hasColumn('products', 'purchase_price')) {
                $table->decimal('purchase_price', 15, 2)->default(0)->after('price');
            }

            if (!Schema::hasColumn('products', 'sale_price')) {
                $table->decimal('sale_price', 15, 2)->default(0)->after('purchase_price');
            }

            if (!Schema::hasColumn('products', 'initial_stock')) {
                $table->integer('initial_stock')->default(0)->after('stock');
            }

            if (!Schema::hasColumn('products', 'image')) {
                $table->string('image')->nullable()->after('initial_stock');
            }

            if (!Schema::hasColumn('products', 'images')) {
                $table->json('images')->nullable()->after('image');
            }

            if (!Schema::hasColumn('products', 'low_stock_notified_at')) {
                $table->timestamp('low_stock_notified_at')->nullable()->after('images');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'category_id')) {
                $table->dropForeign(['category_id']);
                $table->dropColumn('category_id');
            }
            if (Schema::hasColumn('products', 'unit_id')) {
                $table->dropForeign(['unit_id']);
                $table->dropColumn('unit_id');
            }
            $table->dropColumn([
                'purchase_price',
                'sale_price',
                'initial_stock',
                'image',
                'images',
                'low_stock_notified_at',
            ]);
        });
    }
};

