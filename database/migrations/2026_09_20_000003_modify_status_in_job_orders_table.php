<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE job_orders MODIFY COLUMN status VARCHAR(50) DEFAULT 'approved'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE job_orders MODIFY COLUMN status ENUM('draft', 'approved', 'in_progress', 'completed', 'cancelled') DEFAULT 'draft'");
    }
};
