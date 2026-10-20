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
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Seed default departments/machines
        $defaults = [
            ['name' => 'Workshop', 'description' => 'General fabrication and assembly workshop'],
            ['name' => 'Banner Machine', 'description' => 'Flex and banner printing machinery'],
            ['name' => 'Banner / Flex', 'description' => 'Flex printing & banner processing unit'],
            ['name' => 'Sticker Print', 'description' => 'Sticker printing and vinyl cutting department'],
            ['name' => 'Sign Board', 'description' => 'Sign board design, frame, and mounting team'],
        ];

        foreach ($defaults as $dept) {
            DB::table('departments')->insert([
                'name' => $dept['name'],
                'description' => $dept['description'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
