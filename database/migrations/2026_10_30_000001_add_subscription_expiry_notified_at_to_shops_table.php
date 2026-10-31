<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('shops', 'subscription_expiry_notified_at')) {
            Schema::table('shops', function (Blueprint $table) {
                $table->timestamp('subscription_expiry_notified_at')->nullable()->after('subscription_expires_at');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('shops', 'subscription_expiry_notified_at')) {
            Schema::table('shops', function (Blueprint $table) {
                $table->dropColumn('subscription_expiry_notified_at');
            });
        }
    }
};

