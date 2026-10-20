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
        Schema::table('delivery_notes', function (Blueprint $table) {
            // Drop foreign key for quote_id
            $table->dropForeign(['quote_id']);
            // Drop quote_id column
            $table->dropColumn('quote_id');
            
            // Add invoice_id column
            $table->foreignId('invoice_id')->nullable()->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery_notes', function (Blueprint $table) {
            // Drop foreign key for invoice_id
            $table->dropForeign(['invoice_id']);
            // Drop invoice_id column
            $table->dropColumn('invoice_id');
            
            // Add quote_id column back
            $table->foreignId('quote_id')->constrained()->onDelete('cascade');
        });
    }
};
