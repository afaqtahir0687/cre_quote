<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations for Crelogics ERP Public CMS.
     */
    public function up(): void
    {
        // 1. CMS Global Settings (Key-Value)
        Schema::create('cms_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique()->index();
            $table->longText('value')->nullable();
            $table->string('group')->default('general')->index();
            $table->timestamps();
        });

        // 2. CMS Homepage Sections Manager (ordering, enable/disable, title, subtitle)
        Schema::create('cms_sections', function (Blueprint $table) {
            $table->id();
            $table->string('section_key')->unique()->index();
            $table->string('name');
            $table->string('title')->nullable();
            $table->text('subtitle')->nullable();
            $table->string('badge_text')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->integer('sort_order')->default(0);
            $table->json('custom_data')->nullable();
            $table->timestamps();
        });

        // 3. CMS Application / Modules Grid (Odoo-style apps)
        Schema::create('cms_app_modules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->text('description')->nullable();
            $table->longText('icon_svg')->nullable();
            $table->string('icon_bg')->nullable();
            $table->string('icon_color')->nullable();
            $table->string('destination_url')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        // 4. CMS Features Grid
        Schema::create('cms_features', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('icon_class')->default('fas fa-check');
            $table->string('icon_color_class')->default('icon-blue');
            $table->json('highlights')->nullable();
            $table->string('link_url')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        // 5. CMS Product Showcase / Workflow Tabs
        Schema::create('cms_showcase_tabs', function (Blueprint $table) {
            $table->id();
            $table->string('tab_key')->index();
            $table->string('tab_label');
            $table->string('tab_icon')->default('fas fa-desktop');
            $table->string('badge_text')->nullable();
            $table->string('title');
            $table->text('description');
            $table->json('highlights')->nullable();
            $table->string('screenshot_path')->nullable();
            $table->string('cta_text')->nullable();
            $table->string('cta_url')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        // 6. CMS Business Solutions
        Schema::create('cms_solutions', function (Blueprint $table) {
            $table->id();
            $table->string('tag');
            $table->string('title');
            $table->text('description');
            $table->json('checklist')->nullable();
            $table->string('icon_class')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        // 7. CMS How It Works (Steps)
        Schema::create('cms_steps', function (Blueprint $table) {
            $table->id();
            $table->integer('step_number')->default(1);
            $table->string('title');
            $table->text('description');
            $table->string('badge_color')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        // 8. CMS Benefits
        Schema::create('cms_benefits', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('icon_class')->default('fas fa-check');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        // 9. CMS Testimonials (with approval workflow)
        Schema::create('cms_testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('designation')->nullable();
            $table->string('company')->nullable();
            $table->string('avatar')->nullable();
            $table->text('content');
            $table->tinyInteger('rating')->default(5);
            $table->boolean('is_approved')->default(true)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // 10. CMS FAQs
        Schema::create('cms_faqs', function (Blueprint $table) {
            $table->id();
            $table->string('question');
            $table->text('answer');
            $table->string('category')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        // 11. CMS Navigation Menu & Footer Links
        Schema::create('cms_nav_items', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->string('url');
            $table->string('location')->default('header')->index(); // 'header', 'footer_nav', 'footer_modules', 'footer_legal'
            $table->string('target')->default('_self');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        // 12. Contact Messages
        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('subject')->nullable();
            $table->text('message');
            $table->boolean('is_read')->default(false)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_messages');
        Schema::dropIfExists('cms_nav_items');
        Schema::dropIfExists('cms_faqs');
        Schema::dropIfExists('cms_testimonials');
        Schema::dropIfExists('cms_benefits');
        Schema::dropIfExists('cms_steps');
        Schema::dropIfExists('cms_solutions');
        Schema::dropIfExists('cms_showcase_tabs');
        Schema::dropIfExists('cms_features');
        Schema::dropIfExists('cms_app_modules');
        Schema::dropIfExists('cms_sections');
        Schema::dropIfExists('cms_settings');
    }
};

