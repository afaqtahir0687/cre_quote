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
            if (!Schema::hasColumn('settings', 'bank_stamp_logo')) {
                $table->string('bank_stamp_logo')->nullable()->after('company_logo');
            }
            if (!Schema::hasColumn('settings', 'bank_name')) {
                $table->string('bank_name')->nullable()->after('bank_stamp_logo');
            }
            if (!Schema::hasColumn('settings', 'bank_account_name')) {
                $table->string('bank_account_name')->nullable()->after('bank_name');
            }
            if (!Schema::hasColumn('settings', 'bank_account_no')) {
                $table->string('bank_account_no')->nullable()->after('bank_account_name');
            }
            if (!Schema::hasColumn('settings', 'bank_iban')) {
                $table->string('bank_iban')->nullable()->after('bank_account_no');
            }
            if (!Schema::hasColumn('settings', 'bank_swift')) {
                $table->string('bank_swift')->nullable()->after('bank_iban');
            }
        });

        Schema::table('shops', function (Blueprint $table) {
            if (!Schema::hasColumn('shops', 'bank_stamp_logo')) {
                $table->string('bank_stamp_logo')->nullable()->after('logo');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'bank_stamp_logo',
                'bank_name',
                'bank_account_name',
                'bank_account_no',
                'bank_iban',
                'bank_swift',
            ]);
        });

        Schema::table('shops', function (Blueprint $table) {
            $table->dropColumn('bank_stamp_logo');
        });
    }
};
