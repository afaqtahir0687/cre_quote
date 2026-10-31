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
            if (!Schema::hasColumn('settings', 'bank_name_ar')) {
                $table->string('bank_name_ar')->nullable()->after('bank_name');
            }
            if (!Schema::hasColumn('settings', 'company_stamp')) {
                $table->string('company_stamp')->nullable()->after('bank_stamp_logo');
            }
            if (!Schema::hasColumn('settings', 'use_custom_bank_image')) {
                $table->boolean('use_custom_bank_image')->default(false)->after('company_stamp');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['bank_name_ar', 'company_stamp', 'use_custom_bank_image']);
        });
    }
};
