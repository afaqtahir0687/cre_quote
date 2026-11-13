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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->nullable()->index();
            $table->string('category', 100)->index(); // 'Creativity', 'Marketing', 'Advertising'
            $table->string('name', 200);
            $table->string('slug', 220)->unique();
            $table->string('icon', 100)->nullable()->default('fas fa-concierge-bell');
            $table->text('description')->nullable();
            $table->json('features')->nullable();
            $table->decimal('starting_price', 12, 2)->nullable();
            $table->string('price_unit', 50)->nullable()->default('Project');
            $table->integer('sort_order')->default(0)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();

            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};

