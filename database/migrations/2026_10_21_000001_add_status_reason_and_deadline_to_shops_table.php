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
            $table->string('status_reason')->nullable()->after('status');
            $table->text('status_message')->nullable()->after('status_reason');
            $table->integer('grace_period_days')->nullable()->after('status_message');
            $table->date('action_deadline')->nullable()->after('grace_period_days');
            $table->timestamp('status_updated_at')->nullable()->after('action_deadline');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->dropColumn([
                'status_reason',
                'status_message',
                'grace_period_days',
                'action_deadline',
                'status_updated_at',
            ]);
        });
    }
};
