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
        Schema::table('cms_pages', function (Blueprint $table) {
            if (!Schema::hasColumn('cms_pages', 'title_ar')) {
                $table->string('title_ar')->nullable()->after('title');
            }
            if (!Schema::hasColumn('cms_pages', 'subtitle_ar')) {
                $table->string('subtitle_ar')->nullable()->after('subtitle');
            }
            if (!Schema::hasColumn('cms_pages', 'content_ar')) {
                $table->longText('content_ar')->nullable()->after('content');
            }
            if (!Schema::hasColumn('cms_pages', 'meta_title_ar')) {
                $table->string('meta_title_ar')->nullable()->after('meta_title');
            }
            if (!Schema::hasColumn('cms_pages', 'meta_description_ar')) {
                $table->text('meta_description_ar')->nullable()->after('meta_description');
            }
        });

        Schema::table('services', function (Blueprint $table) {
            if (!Schema::hasColumn('services', 'name_ar')) {
                $table->string('name_ar')->nullable()->after('name');
            }
            if (!Schema::hasColumn('services', 'sub_tag_ar')) {
                $table->string('sub_tag_ar', 150)->nullable()->after('sub_tag');
            }
            if (!Schema::hasColumn('services', 'headline_ar')) {
                $table->string('headline_ar')->nullable()->after('headline');
            }
            if (!Schema::hasColumn('services', 'badge_text_ar')) {
                $table->string('badge_text_ar', 100)->nullable()->after('badge_text');
            }
            if (!Schema::hasColumn('services', 'description_ar')) {
                $table->text('description_ar')->nullable()->after('description');
            }
            if (!Schema::hasColumn('services', 'content_ar')) {
                $table->longText('content_ar')->nullable()->after('content');
            }
            if (!Schema::hasColumn('services', 'features_ar')) {
                $table->json('features_ar')->nullable()->after('features');
            }
            if (!Schema::hasColumn('services', 'cta_title_ar')) {
                $table->string('cta_title_ar')->nullable()->after('cta_title');
            }
            if (!Schema::hasColumn('services', 'cta_subtitle_ar')) {
                $table->text('cta_subtitle_ar')->nullable()->after('cta_subtitle');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cms_pages', function (Blueprint $table) {
            $table->dropColumn(['title_ar', 'subtitle_ar', 'content_ar', 'meta_title_ar', 'meta_description_ar']);
        });

        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn([
                'name_ar', 'sub_tag_ar', 'headline_ar', 'badge_text_ar',
                'description_ar', 'content_ar', 'features_ar',
                'cta_title_ar', 'cta_subtitle_ar'
            ]);
        });
    }
};
