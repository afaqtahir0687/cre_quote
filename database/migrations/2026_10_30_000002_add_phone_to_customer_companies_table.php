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
        if (Schema::hasTable('customer_companies') && !Schema::hasColumn('customer_companies', 'phone')) {
            Schema::table('customer_companies', function (Blueprint $table) {
                $table->string('phone')->nullable()->after('company_name');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('customer_companies') && Schema::hasColumn('customer_companies', 'phone')) {
            Schema::table('customer_companies', function (Blueprint $table) {
                $table->dropColumn('phone');
            });
        }
    }
};

