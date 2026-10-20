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
        Schema::table('settings', function (Blueprint $table) {
            $table->string('company_city')->nullable()->after('company_address');
            $table->string('company_country')->nullable()->after('company_city');
            $table->string('company_vat_no')->nullable()->after('company_tax_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['company_city', 'company_country', 'company_vat_no']);
        });
    }
};
