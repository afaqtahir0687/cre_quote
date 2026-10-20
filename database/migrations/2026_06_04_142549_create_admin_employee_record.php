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
        // Create employee record for admin user
        $adminUser = \App\Models\User::where('email', 'admin@invoice.com')->first();
        if ($adminUser && !\App\Models\Employee::where('user_id', $adminUser->id)->exists()) {
            \App\Models\Employee::create([
                'user_id' => $adminUser->id,
                'name' => $adminUser->name,
                'designation' => 'Administrator',
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Delete employee record for admin user
        $adminUser = \App\Models\User::where('email', 'admin@invoice.com')->first();
        if ($adminUser) {
            \App\Models\Employee::where('user_id', $adminUser->id)->delete();
        }
    }
};
