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
        if (!Schema::hasTable('dynamic_translations')) {
            Schema::create('dynamic_translations', function (Blueprint $table) {
                $table->id();
                $table->string('hash', 64)->index();
                $table->text('source_text');
                $table->text('translated_text');
                $table->string('source_lang', 10);
                $table->string('target_lang', 10);
                $table->timestamps();

                $table->unique(['hash', 'target_lang']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dynamic_translations');
    }
};

