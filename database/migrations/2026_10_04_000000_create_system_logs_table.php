<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('system_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action'); // e.g., 'Deleted'
            $table->string('model_type'); // e.g., 'Quote', 'Invoice'
            $table->text('description')->nullable(); // e.g., 'Deleted Quote #123'
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('system_logs');
    }
};
