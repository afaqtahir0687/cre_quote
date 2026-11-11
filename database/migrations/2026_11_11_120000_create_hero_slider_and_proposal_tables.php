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
        // 1. Homepage Hero Slides Table
        Schema::create('homepage_slides', function (Blueprint $table) {
            $table->id();
            $table->string('title')->comment('Internal admin label');
            $table->string('badge_text')->nullable();
            $table->string('heading');
            $table->string('heading_highlight')->nullable();
            $table->text('subheading')->nullable();
            $table->text('description')->nullable();
            $table->string('primary_btn_text')->nullable();
            $table->string('primary_btn_action')->default('url')->comment('url, open_proposal_modal, register');
            $table->string('primary_btn_url')->nullable();
            $table->string('secondary_btn_text')->nullable();
            $table->string('secondary_btn_url')->nullable();
            $table->string('bg_image')->nullable();
            $table->string('bg_image_mobile')->nullable();
            $table->string('fg_image')->nullable();
            $table->string('bg_color')->nullable();
            $table->string('text_align')->default('center')->comment('left, center, right');
            $table->string('content_position')->default('center')->comment('left, center, right');
            $table->string('slide_type')->default('marketing')->comment('existing_hero, marketing, custom');
            $table->string('animation_type')->default('fade')->comment('fade, slide, zoom');
            $table->integer('display_duration')->default(6000)->comment('milliseconds');
            $table->json('trust_bullets')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        // 2. Proposal Form Options Table (Services, Locations, Goals, Budgets, Timelines)
        Schema::create('proposal_options', function (Blueprint $table) {
            $table->id();
            $table->string('type')->index()->comment('service, location, goal, budget, timeline');
            $table->string('name');
            $table->string('subtitle')->nullable();
            $table->string('icon')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        // 3. Proposal Requests Submissions Table
        Schema::create('proposal_requests', function (Blueprint $table) {
            $table->id();
            $table->string('reference_no')->unique()->index();
            $table->string('service');
            $table->string('customer_location');
            $table->string('campaign_goal');
            $table->string('monthly_budget');
            $table->string('starting_timeline');
            $table->string('website_url')->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->index();
            $table->string('phone');
            $table->string('ip_address')->nullable();
            $table->string('status')->default('Pending')->index()->comment('Pending, Contacted, In Discussion, Proposal Sent, Converted, Rejected');
            $table->text('internal_notes')->nullable();
            $table->timestamp('notes_updated_at')->nullable();
            $table->foreignId('notes_updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 4. Seed Initial Slide 1 (Existing Homepage Hero) and Slide 2 (CreLogics Free Proposal)
        $now = now();
        DB::table('homepage_slides')->insert([
            [
                'title' => 'Crelogics ERP System Preview (Hero)',
                'badge_text' => 'Complete Business ERP Solution',
                'heading' => 'Run Your Entire Business From',
                'heading_highlight' => 'One Intelligent System',
                'subheading' => 'Cloud ERP & Operations Platform',
                'description' => 'Manage job orders, quotations, invoices, customers, products and daily operations with one connected, modern ERP designed for performance and scale.',
                'primary_btn_text' => 'Open ERP Portal',
                'primary_btn_action' => 'url',
                'primary_btn_url' => '/dashboard',
                'secondary_btn_text' => 'Explore Live Workflow',
                'secondary_btn_url' => '#modules',
                'bg_image' => null,
                'bg_image_mobile' => null,
                'fg_image' => null,
                'bg_color' => null,
                'text_align' => 'center',
                'content_position' => 'center',
                'slide_type' => 'existing_hero',
                'animation_type' => 'fade',
                'display_duration' => 7000,
                'trust_bullets' => json_encode([
                    'No credit card required',
                    '7-Day full access trial',
                    'Multi-shop & branch ready',
                    'Instant PDF quotes & invoices'
                ]),
                'sort_order' => 1,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title' => 'Smart Digital Solutions (Get Free Proposal)',
                'badge_text' => 'Smart Digital Marketing & Strategy',
                'heading' => 'Grow Your Business With',
                'heading_highlight' => 'Smart Digital Solutions',
                'subheading' => 'High-Performance ROI Marketing & Web Technologies',
                'description' => 'Transform your business with professional digital marketing, SEO, web design, and advertising solutions tailored to your goals.',
                'primary_btn_text' => 'GET YOUR FREE PROPOSAL',
                'primary_btn_action' => 'open_proposal_modal',
                'primary_btn_url' => '#proposalModal',
                'secondary_btn_text' => 'Explore Our Services',
                'secondary_btn_url' => '/services',
                'bg_image' => null,
                'bg_image_mobile' => null,
                'fg_image' => null,
                'bg_color' => null,
                'text_align' => 'center',
                'content_position' => 'center',
                'slide_type' => 'marketing',
                'animation_type' => 'fade',
                'display_duration' => 7000,
                'trust_bullets' => json_encode([
                    'Custom ROI Growth Strategy',
                    'Comprehensive 360° Audit',
                    'Dedicated Campaign Team',
                    'Guaranteed Fast Turnaround'
                ]),
                'sort_order' => 2,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        ]);

        // 5. Seed Initial Proposal Options
        $services = [
            ['name' => 'SEO (Search Engine Optimization)', 'subtitle' => 'Rank #1 on Google, drive organic buyers', 'icon' => 'fas fa-search', 'order' => 1],
            ['name' => 'Google Ads (Search & Display PPC)', 'subtitle' => 'Instant qualified high-intent leads', 'icon' => 'fab fa-google', 'order' => 2],
            ['name' => 'Social Media Marketing & Meta Ads', 'subtitle' => 'Facebook, Instagram & LinkedIn scaling', 'icon' => 'fas fa-share-alt', 'order' => 3],
            ['name' => 'Custom Web Design & Development', 'subtitle' => 'High-converting responsive web apps', 'icon' => 'fas fa-laptop-code', 'order' => 4],
            ['name' => 'Crelogics ERP & Cloud Software', 'subtitle' => 'Automate job orders, invoices & branches', 'icon' => 'fas fa-cubes', 'order' => 5],
            ['name' => 'Branding & Creative Identity', 'subtitle' => 'Logos, print collateral & corporate brand', 'icon' => 'fas fa-paint-brush', 'order' => 6],
        ];
        foreach ($services as $s) {
            DB::table('proposal_options')->insert([
                'type' => 'service',
                'name' => $s['name'],
                'subtitle' => $s['subtitle'],
                'icon' => $s['icon'],
                'sort_order' => $s['order'],
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $locations = [
            ['name' => 'Saudi Arabia (KSA)', 'icon' => 'fas fa-map-marker-alt', 'order' => 1],
            ['name' => 'United Arab Emirates (UAE)', 'icon' => 'fas fa-map-marker-alt', 'order' => 2],
            ['name' => 'Gulf / GCC Countries', 'icon' => 'fas fa-globe-asia', 'order' => 3],
            ['name' => 'United States & Canada', 'icon' => 'fas fa-globe-americas', 'order' => 4],
            ['name' => 'United Kingdom & Europe', 'icon' => 'fas fa-globe-europe', 'order' => 5],
            ['name' => 'Global / Worldwide', 'icon' => 'fas fa-globe', 'order' => 6],
        ];
        foreach ($locations as $l) {
            DB::table('proposal_options')->insert([
                'type' => 'location',
                'name' => $l['name'],
                'subtitle' => null,
                'icon' => $l['icon'],
                'sort_order' => $l['order'],
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $goals = [
            ['name' => 'Generate High-Quality Leads', 'subtitle' => 'More phone calls, inquiries and quotes', 'icon' => 'fas fa-funnel-dollar', 'order' => 1],
            ['name' => 'Boost E-Commerce Sales & Revenue', 'subtitle' => 'Maximize online purchases and ROAS', 'icon' => 'fas fa-shopping-cart', 'order' => 2],
            ['name' => 'Improve Search Engine Rankings (SEO)', 'subtitle' => 'Dominate competitor search keywords', 'icon' => 'fas fa-chart-line', 'order' => 3],
            ['name' => 'Build Strong Brand Awareness', 'subtitle' => 'Reach hundreds of thousands of prospects', 'icon' => 'fas fa-bullhorn', 'order' => 4],
            ['name' => 'Streamline Business with ERP Software', 'subtitle' => 'Automate inventory, orders and shops', 'icon' => 'fas fa-cogs', 'order' => 5],
            ['name' => 'Complete Website Redesign / Launch', 'subtitle' => 'Modern, fast and mobile-first experience', 'icon' => 'fas fa-desktop', 'order' => 6],
        ];
        foreach ($goals as $g) {
            DB::table('proposal_options')->insert([
                'type' => 'goal',
                'name' => $g['name'],
                'subtitle' => $g['subtitle'],
                'icon' => $g['icon'],
                'sort_order' => $g['order'],
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $budgets = [
            ['name' => 'Under $1,000 / month', 'icon' => 'fas fa-wallet', 'order' => 1],
            ['name' => '$1,000 – $2,500 / month', 'icon' => 'fas fa-wallet', 'order' => 2],
            ['name' => '$2,500 – $5,000 / month', 'icon' => 'fas fa-wallet', 'order' => 3],
            ['name' => '$5,000 – $10,000 / month', 'icon' => 'fas fa-coins', 'order' => 4],
            ['name' => '$10,000+ / Enterprise Scale', 'icon' => 'fas fa-gem', 'order' => 5],
        ];
        foreach ($budgets as $b) {
            DB::table('proposal_options')->insert([
                'type' => 'budget',
                'name' => $b['name'],
                'subtitle' => null,
                'icon' => $b['icon'],
                'sort_order' => $b['order'],
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $timelines = [
            ['name' => 'Immediately (Ready to launch)', 'icon' => 'fas fa-bolt', 'order' => 1],
            ['name' => 'Within 1 to 2 Weeks', 'icon' => 'fas fa-calendar-check', 'order' => 2],
            ['name' => 'Next Month', 'icon' => 'fas fa-calendar-alt', 'order' => 3],
            ['name' => 'Exploring Options & Researching', 'icon' => 'fas fa-compass', 'order' => 4],
        ];
        foreach ($timelines as $t) {
            DB::table('proposal_options')->insert([
                'type' => 'timeline',
                'name' => $t['name'],
                'subtitle' => null,
                'icon' => $t['icon'],
                'sort_order' => $t['order'],
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // 6. Seed Proposal Form CMS Settings
        $proposalSettings = [
            'proposal_form_enabled' => '1',
            'proposal_modal_title' => 'Get Your Free Custom Proposal',
            'proposal_modal_subtitle' => 'Answer 4 quick questions to receive a tailored digital roadmap, budget recommendations, and project timeline from CreLogics experts.',
            'proposal_notification_email' => 'creelogics@gmail.com',
            'proposal_submit_btn_text' => 'SEND PROPOSAL',
            'proposal_success_message' => 'Thank you! Your proposal request has been submitted successfully. Our team will contact you shortly.',
        ];
        foreach ($proposalSettings as $key => $val) {
            DB::table('cms_settings')->updateOrInsert(
                ['key' => $key],
                ['value' => $val, 'group' => 'proposal', 'created_at' => $now, 'updated_at' => $now]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proposal_requests');
        Schema::dropIfExists('proposal_options');
        Schema::dropIfExists('homepage_slides');
        DB::table('cms_settings')->where('group', 'proposal')->delete();
    }
};

