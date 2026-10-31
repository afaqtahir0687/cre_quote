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
        if (Schema::hasTable('quotes') && !Schema::hasColumn('quotes', 'show_customer_name')) {
            Schema::table('quotes', function (Blueprint $table) {
                $table->boolean('show_customer_name')->default(true)->after('customer_company_id');
            });
        }

        if (Schema::hasTable('job_orders') && !Schema::hasColumn('job_orders', 'show_customer_name')) {
            Schema::table('job_orders', function (Blueprint $table) {
                $table->boolean('show_customer_name')->default(true)->after('customer_company_id');
            });
        }

        if (Schema::hasTable('invoices') && !Schema::hasColumn('invoices', 'show_customer_name')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->boolean('show_customer_name')->default(true)->after('customer_company_id');
            });
        }

        if (Schema::hasTable('delivery_notes') && !Schema::hasColumn('delivery_notes', 'show_customer_name')) {
            Schema::table('delivery_notes', function (Blueprint $table) {
                $table->boolean('show_customer_name')->default(true)->after('customer_company_id');
            });
        }

        if (Schema::hasTable('settings') && !Schema::hasColumn('settings', 'show_customer_name_on_pdf')) {
            Schema::table('settings', function (Blueprint $table) {
                $table->boolean('show_customer_name_on_pdf')->default(true)->after('quote_prefix');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('quotes') && Schema::hasColumn('quotes', 'show_customer_name')) {
            Schema::table('quotes', function (Blueprint $table) {
                $table->dropColumn('show_customer_name');
            });
        }

        if (Schema::hasTable('job_orders') && Schema::hasColumn('job_orders', 'show_customer_name')) {
            Schema::table('job_orders', function (Blueprint $table) {
                $table->dropColumn('show_customer_name');
            });
        }

        if (Schema::hasTable('invoices') && Schema::hasColumn('invoices', 'show_customer_name')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->dropColumn('show_customer_name');
            });
        }

        if (Schema::hasTable('delivery_notes') && Schema::hasColumn('delivery_notes', 'show_customer_name')) {
            Schema::table('delivery_notes', function (Blueprint $table) {
                $table->dropColumn('show_customer_name');
            });
        }

        if (Schema::hasTable('settings') && Schema::hasColumn('settings', 'show_customer_name_on_pdf')) {
            Schema::table('settings', function (Blueprint $table) {
                $table->dropColumn('show_customer_name_on_pdf');
            });
        }
    }
};

