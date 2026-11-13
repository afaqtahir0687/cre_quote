<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations to make services 100% dynamic.
     */
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->string('sub_tag', 150)->nullable()->default('BEST FOR YOU')->after('name');
            $table->string('headline', 255)->nullable()->after('sub_tag');
            $table->string('badge_text', 100)->nullable()->default('Verified Quality')->after('headline');
            $table->string('image', 255)->nullable()->after('icon');
            
            // Feature highlight box 1
            $table->string('feature_1_title', 150)->nullable()->default('Actionable Audit')->after('features');
            $table->string('feature_1_text', 255)->nullable()->default('Detailed scoping & technical roadmap')->after('feature_1_title');
            $table->string('feature_1_icon', 100)->nullable()->default('fas fa-bullhorn')->after('feature_1_text');

            // Feature highlight box 2
            $table->string('feature_2_title', 150)->nullable()->default('What We Charge')->after('feature_1_icon');
            $table->string('feature_2_text', 255)->nullable()->after('feature_2_title');
            $table->string('feature_2_icon', 100)->nullable()->default('fas fa-chart-pie')->after('feature_2_text');

            // Process & Benefits
            $table->json('process_steps')->nullable()->after('feature_2_icon');
            $table->json('why_choose_us')->nullable()->after('process_steps');

            // Bottom CTA
            $table->string('cta_title', 255)->nullable()->after('why_choose_us');
            $table->text('cta_subtitle')->nullable()->after('cta_title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn([
                'sub_tag',
                'headline',
                'badge_text',
                'image',
                'feature_1_title',
                'feature_1_text',
                'feature_1_icon',
                'feature_2_title',
                'feature_2_text',
                'feature_2_icon',
                'process_steps',
                'why_choose_us',
                'cta_title',
                'cta_subtitle',
            ]);
        });
    }
};

