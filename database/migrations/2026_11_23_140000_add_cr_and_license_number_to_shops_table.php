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
        Schema::table('shops', function (Blueprint $table) {
            if (!Schema::hasColumn('shops', 'cr_number')) {
                $table->string('cr_number', 100)->nullable()->after('phone');
            }
            if (!Schema::hasColumn('shops', 'license_number')) {
                $table->string('license_number', 100)->nullable()->after('cr_number');
            }
        });

        Schema::table('settings', function (Blueprint $table) {
            if (!Schema::hasColumn('settings', 'company_license_no')) {
                $table->string('company_license_no', 100)->nullable()->after('company_cr_no');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shops', function (Blueprint $table) {
            if (Schema::hasColumn('shops', 'license_number')) {
                $table->dropColumn('license_number');
            }
            if (Schema::hasColumn('shops', 'cr_number')) {
                $table->dropColumn('cr_number');
            }
        });

        Schema::table('settings', function (Blueprint $table) {
            if (Schema::hasColumn('settings', 'company_license_no')) {
                $table->dropColumn('company_license_no');
            }
        });
    }
};
