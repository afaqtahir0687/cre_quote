<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE job_orders MODIFY COLUMN payment_status VARCHAR(50) DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE job_orders MODIFY COLUMN payment_status ENUM('pending', 'partial', 'paid') DEFAULT 'pending'");
    }
};
