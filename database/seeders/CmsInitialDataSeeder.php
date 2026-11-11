<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CmsSetting;
use App\Models\CmsSection;
use App\Models\CmsAppModule;
use App\Models\CmsFeature;
use App\Models\CmsShowcaseTab;
use App\Models\CmsSolution;
use App\Models\CmsStep;
use App\Models\CmsBenefit;
use App\Models\CmsTestimonial;
use App\Models\CmsFaq;
use App\Models\CmsNavItem;

class CmsInitialDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Settings
        $settings = [
            'site_name' => 'Crelogics ERP',
            'company_name' => 'Crelogics ERP',
            'tagline' => 'All-In-One Cloud Business ERP Solution',
            'meta_title' => 'Crelogics ERP - All-In-One Cloud ERP & Business Management Solution',
            'meta_description' => 'Manage job orders, quotations, invoices, customers, products and daily operations with Crelogics ERP. Built for modern enterprises and multi-shop businesses.',
            'contact_email' => 'support@crelogics.com',
            'contact_phone' => '+996 598 500 143',
            'whatsapp_number' => '996598500143',
            'company_address' => 'Business District, Modern Industrial Zone',
            'facebook_url' => 'https://facebook.com',
            'instagram_url' => 'https://instagram.com',
            'linkedin_url' => 'https://linkedin.com',
            'youtube_url' => 'https://youtube.com',
            'copyright_text' => 'Crelogics ERP. All rights reserved. Built with precision for modern business growth.',
            'primary_color' => '#4361EE',
            'secondary_color' => '#17243A',
            'accent_color' => '#00B894',
            'footer_description' => 'A complete, modern cloud ERP and business management system engineered to streamline job orders, quotations, invoices, multi-branch tracking, and inventory fulfillment.',
            'hero_badge' => 'Complete Cloud Business ERP Solution',
            'hero_headline' => 'Run Your Entire Business From',
            'hero_headline_highlight' => 'One Intelligent System',
            'hero_subheading' => 'Manage job orders, quotations, invoices, customers, products and daily operations with one connected, modern ERP designed for performance and scale.',
            'hero_primary_btn_text' => 'Start Free 7-Day Trial',
            'hero_primary_btn_url' => '/register',
            'hero_secondary_btn_text' => 'Explore Live Workflow',
            'hero_secondary_btn_url' => '#showcase',
            'social_proof_metric_mode' => 'dynamic', // 'dynamic' or 'manual'
            'social_proof_manual_count' => '5',
            'social_proof_handwritten_note' => 'happy',
            'social_proof_heading' => 'businesses',
            'social_proof_subheading' => 'who grow and manage their business with Crelogics ERP',
        ];

        foreach ($settings as $k => $v) {
            CmsSetting::updateOrCreate(['key' => $k], ['value' => $v, 'group' => 'general']);
        }

        // 2. Homepage Sections (Order & Visibility)
        $sections = [
            ['section_key' => 'hero', 'name' => 'Hero Banner', 'title' => 'Hero Banner', 'sort_order' => 1, 'is_active' => true],
            ['section_key' => 'trust', 'name' => 'Trust & Capabilities', 'title' => 'Everything Your Business Needs, Centralized in One Unified System', 'subtitle' => 'Built from the ground up to solve daily operational bottlenecks, reduce manual entry errors, and accelerate cash flow.', 'sort_order' => 2, 'is_active' => true],
            ['section_key' => 'modules', 'name' => 'Odoo-Style App Grid', 'title' => 'Everything Your Business Needs, All in One Place', 'subtitle' => 'Explore powerful business applications designed to simplify your everyday operations.', 'sort_order' => 3, 'is_active' => true],
            ['section_key' => 'social_proof', 'name' => 'Social Proof Mosaic', 'title' => 'Social Proof Mosaic', 'subtitle' => 'who grow and manage their business with Crelogics ERP', 'sort_order' => 4, 'is_active' => true],
            ['section_key' => 'showcase', 'name' => 'Product Workflow Showcase', 'title' => 'See Your Business in Action', 'subtitle' => 'Watch how fast your team can draft quotes, track jobs through production, and collect client payments with zero friction.', 'sort_order' => 5, 'is_active' => true],
            ['section_key' => 'features', 'name' => 'Features Grid', 'title' => 'Powerful Features to Simplify Your Business', 'subtitle' => 'Everything required to handle customer inquiries, manage production work, issue compliant tax invoices, and track outstanding revenue.', 'sort_order' => 6, 'is_active' => true],
            ['section_key' => 'how_it_works', 'name' => 'How It Works (Steps)', 'title' => 'Get Started in Three Simple Steps', 'subtitle' => 'No complex enterprise configurations or weeks of setup. Crelogics ERP gets your team up and operational on day one.', 'sort_order' => 7, 'is_active' => true],
            ['section_key' => 'solutions', 'name' => 'Industry Solutions', 'title' => 'Built for Businesses That Want Better Control', 'subtitle' => 'Engineered with practical flexibility to support specialized workflows, custom sizing calculations, and multi-location businesses.', 'sort_order' => 8, 'is_active' => true],
            ['section_key' => 'benefits', 'name' => 'Why Choose ERP (Comparison)', 'title' => 'Why Businesses Choose Crelogics ERP Over Generic Tools', 'subtitle' => 'Most small and mid-sized enterprises struggle with scattered spreadsheets, lost quotation drafts, and manual paperwork that slows down billing.', 'sort_order' => 9, 'is_active' => true],
            ['section_key' => 'pricing', 'name' => 'Plans & Pricing', 'title' => 'Transparent, Predictable Pricing', 'subtitle' => 'No hidden charges or surprise transaction fees. Select the plan that fits your current business scale and grow with confidence.', 'sort_order' => 10, 'is_active' => true],
            ['section_key' => 'testimonials', 'name' => 'Customer Testimonials', 'title' => 'Loved by Growing Enterprises', 'subtitle' => 'Real feedback from business owners and workshop managers using Crelogics ERP daily.', 'sort_order' => 11, 'is_active' => true],
            ['section_key' => 'faq', 'name' => 'Frequently Asked Questions', 'title' => 'Frequently Asked Questions', 'subtitle' => 'Everything you need to know about setting up Crelogics ERP, managing branches, and subscription options.', 'sort_order' => 12, 'is_active' => true],
            ['section_key' => 'cta', 'name' => 'Final Call To Action', 'title' => 'Ready to Take Total Control of Your Business Operations?', 'subtitle' => 'Eliminate scattered spreadsheets, accelerate customer quotation turnarounds, and keep your entire team synchronized from quote to final cash collection.', 'sort_order' => 13, 'is_active' => true],
            ['section_key' => 'contact', 'name' => 'Contact Us Form', 'title' => 'Get in Touch With Our Team', 'subtitle' => 'Have questions or need a custom demonstration? We are here to help.', 'sort_order' => 14, 'is_active' => true],
        ];

        foreach ($sections as $sec) {
            CmsSection::updateOrCreate(['section_key' => $sec['section_key']], $sec);
        }

        // 3. App Modules (18 Odoo-style launchers)
        $modules = [
            ['name' => 'Dashboard', 'slug' => 'dashboard', 'destination_url' => '#showcase', 'sort_order' => 1,
             'icon_svg' => '<svg viewBox="0 0 48 48" fill="none"><rect x="6" y="22" width="9" height="18" rx="3" fill="#4361EE"/><rect x="19.5" y="12" width="9" height="28" rx="3" fill="#00CEC9"/><rect x="33" y="18" width="9" height="22" rx="3" fill="#FF7675"/><circle cx="24" cy="8" r="3" fill="#FDCB6E"/></svg>'],
            ['name' => 'Job Orders', 'slug' => 'job-orders', 'destination_url' => '#showcase', 'sort_order' => 2,
             'icon_svg' => '<svg viewBox="0 0 48 48" fill="none"><rect x="8" y="6" width="32" height="36" rx="6" fill="#EEF2FF" stroke="#4361EE" stroke-width="2.5"/><path d="M16 4H32V8C32 9.1 31.1 10 30 10H18C16.9 10 16 9.1 16 8V4Z" fill="#4361EE"/><circle cx="16" cy="18" r="2.5" fill="#00B894"/><rect x="22" y="16.5" width="14" height="3" rx="1.5" fill="#17243A"/><circle cx="16" cy="26" r="2.5" fill="#00B894"/><rect x="22" y="24.5" width="14" height="3" rx="1.5" fill="#17243A"/><circle cx="16" cy="34" r="2.5" fill="#00B894"/><rect x="22" y="32.5" width="10" height="3" rx="1.5" fill="#17243A"/></svg>'],
            ['name' => 'Quotations', 'slug' => 'quotations', 'destination_url' => '#showcase', 'sort_order' => 3,
             'icon_svg' => '<svg viewBox="0 0 48 48" fill="none"><path d="M10 8C10 5.79 11.79 4 14 4H26L38 16V40C38 42.21 36.21 44 34 44H14C11.79 44 10 42.21 10 40V8Z" fill="#F3E8FF"/><path d="M26 4V16H38" fill="#D8B4FE"/><path d="M16 22H32M16 28H28M16 34H24" stroke="#6C5CE7" stroke-width="2.5" stroke-linecap="round"/><circle cx="34" cy="34" r="7" fill="#FD79A8"/><path d="M31.5 34H36.5M34 31.5V36.5" stroke="#FFFFFF" stroke-width="2" stroke-linecap="round"/></svg>'],
            ['name' => 'Invoices', 'slug' => 'invoices', 'destination_url' => '#showcase', 'sort_order' => 4,
             'icon_svg' => '<svg viewBox="0 0 48 48" fill="none"><rect x="8" y="7" width="32" height="34" rx="5" fill="#E6FBF5"/><path d="M8 35L14 38L20 35L26 38L32 35L40 39V7C40 5.34 38.66 4 37 4H11C9.34 4 8 5.34 8 7V35Z" fill="#00B894"/><circle cx="24" cy="18" r="7.5" fill="#FFFFFF"/><path d="M24 14V22M21.5 16.5C21.5 15.5 22.5 15 24 15C25.5 15 26.5 15.6 26.5 16.8C26.5 18.5 21.5 18 21.5 19.8C21.5 21 22.6 21.5 24 21.5C25.5 21.5 26.5 20.8 26.5 20" stroke="#00B894" stroke-width="1.8" stroke-linecap="round"/><rect x="14" y="28" width="20" height="2.5" rx="1.2" fill="#FFFFFF"/></svg>'],
            ['name' => 'Delivery Notes', 'slug' => 'delivery-notes', 'destination_url' => '#features', 'sort_order' => 5,
             'icon_svg' => '<svg viewBox="0 0 48 48" fill="none"><path d="M6 14C6 11.79 7.79 10 10 10H28V34H6V14Z" fill="#0984E3"/><path d="M28 16H36L42 24V34H28V16Z" fill="#00CEC9"/><circle cx="15" cy="35" r="4.5" fill="#17243A" stroke="#FFFFFF" stroke-width="2"/><circle cx="35" cy="35" r="4.5" fill="#17243A" stroke="#FFFFFF" stroke-width="2"/><rect x="12" y="16" width="10" height="7" rx="2" fill="#FDCB6E"/></svg>'],
            ['name' => 'Customers', 'slug' => 'customers', 'destination_url' => '#features', 'sort_order' => 6,
             'icon_svg' => '<svg viewBox="0 0 48 48" fill="none"><circle cx="20" cy="16" r="6" fill="#FF7675"/><path d="M9 36C9 30 14 26 20 26C26 26 31 30 31 36" fill="#E17055"/><circle cx="34" cy="18" r="4.5" fill="#00CEC9"/><path d="M28 36C28.5 32 32 29 36 29C39.5 29 42 32 42 36" fill="#0984E3"/></svg>'],
            ['name' => 'Products', 'slug' => 'products', 'destination_url' => '#features', 'sort_order' => 7,
             'icon_svg' => '<svg viewBox="0 0 48 48" fill="none"><path d="M24 5L40 14V34L24 43L8 34V14L24 5Z" fill="#FEF3C7"/><path d="M24 5L40 14L24 23L8 14L24 5Z" fill="#F59E0B"/><path d="M8 14L24 23V43L8 34V14Z" fill="#D97706"/><path d="M40 14L24 23V43L40 34V14Z" fill="#B45309"/><circle cx="24" cy="23" r="3.5" fill="#FFFFFF"/></svg>'],
            ['name' => 'Categories', 'slug' => 'categories', 'destination_url' => '#features', 'sort_order' => 8,
             'icon_svg' => '<svg viewBox="0 0 48 48" fill="none"><rect x="8" y="8" width="14" height="14" rx="4" fill="#8E44AD"/><rect x="26" y="8" width="14" height="14" rx="4" fill="#BE2EDD"/><rect x="8" y="26" width="14" height="14" rx="4" fill="#3498DB"/><rect x="26" y="26" width="14" height="14" rx="4" fill="#00B894"/></svg>'],
            ['name' => 'Units & Sizes', 'slug' => 'units', 'destination_url' => '#solutions', 'sort_order' => 9,
             'icon_svg' => '<svg viewBox="0 0 48 48" fill="none"><path d="M8 38L38 8L42 12L12 42L8 38Z" fill="#00CEC9"/><path d="M14 16L18 20M19 11L23 15M24 6L28 10M25 27L29 31M30 22L34 26M35 17L39 21" stroke="#17243A" stroke-width="2" stroke-linecap="round"/><circle cx="10" cy="40" r="3" fill="#FF7675"/><rect x="6" y="6" width="8" height="8" rx="2" fill="#FDCB6E"/></svg>'],
            ['name' => 'Employees', 'slug' => 'employees', 'destination_url' => '#features', 'sort_order' => 10,
             'icon_svg' => '<svg viewBox="0 0 48 48" fill="none"><rect x="10" y="6" width="28" height="36" rx="5" fill="#DBEAFE" stroke="#3B82F6" stroke-width="2"/><rect x="18" y="4" width="12" height="4" rx="2" fill="#3B82F6"/><circle cx="24" cy="18" r="5" fill="#3B82F6"/><path d="M16 32C16 28 19.5 26 24 26C28.5 26 32 28 32 32" fill="#1D4ED8"/><rect x="17" y="35" width="14" height="2" rx="1" fill="#93C5FD"/></svg>'],
            ['name' => 'Departments', 'slug' => 'departments', 'destination_url' => '#solutions', 'sort_order' => 11,
             'icon_svg' => '<svg viewBox="0 0 48 48" fill="none"><rect x="18" y="6" width="12" height="10" rx="3" fill="#E11D48"/><rect x="6" y="28" width="10" height="10" rx="3" fill="#4361EE"/><rect x="20" y="28" width="10" height="10" rx="3" fill="#00B894"/><rect x="34" y="28" width="10" height="10" rx="3" fill="#F59E0B"/><path d="M24 16V22M11 22H39M11 22V28M25 22V28M39 22V28" stroke="#64748B" stroke-width="2.5" stroke-linecap="round"/></svg>'],
            ['name' => 'Multi-Branch', 'slug' => 'multi-branch', 'destination_url' => '#showcase', 'sort_order' => 12,
             'icon_svg' => '<svg viewBox="0 0 48 48" fill="none"><path d="M6 18L24 6L42 18V40H6V18Z" fill="#1E293B"/><path d="M18 40V24H30V40H18Z" fill="#38BDF8"/><rect x="10" y="22" width="6" height="6" rx="1" fill="#F8FAFC"/><rect x="32" y="22" width="6" height="6" rx="1" fill="#F8FAFC"/><circle cx="24" cy="14" r="3" fill="#F59E0B"/></svg>'],
            ['name' => 'Accounting', 'slug' => 'accounting', 'destination_url' => '#showcase', 'sort_order' => 13,
             'icon_svg' => '<svg viewBox="0 0 48 48" fill="none"><rect x="6" y="12" width="30" height="20" rx="4" fill="#059669"/><circle cx="21" cy="22" r="5" fill="#34D399"/><circle cx="35" cy="30" r="9" fill="#EAB308" stroke="#FFFFFF" stroke-width="2"/><path d="M35 25V35M33 27H37" stroke="#FFFFFF" stroke-width="2" stroke-linecap="round"/></svg>'],
            ['name' => 'Reports & Ledger', 'slug' => 'reports', 'destination_url' => '#features', 'sort_order' => 14,
             'icon_svg' => '<svg viewBox="0 0 48 48" fill="none"><rect x="8" y="6" width="32" height="36" rx="5" fill="#EEF2FF"/><path d="M14 34L22 25L28 30L34 18" stroke="#4361EE" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/><circle cx="34" cy="18" r="3.5" fill="#FF7675"/><rect x="14" y="12" width="10" height="3" rx="1.5" fill="#94A3B8"/></svg>'],
            ['name' => 'Subscriptions', 'slug' => 'subscriptions', 'destination_url' => '#pricing', 'sort_order' => 15,
             'icon_svg' => '<svg viewBox="0 0 48 48" fill="none"><path d="M24 8C15.16 8 8 15.16 8 24C8 28.4 9.79 32.4 12.7 35.3L15.5 32.5C13.3 30.3 12 27.3 12 24C12 17.37 17.37 12 24 12V6L32 14L24 22V16C19.58 16 16 19.58 16 24C16 26.2 16.9 28.2 18.3 29.7L15.5 32.5" fill="#F97316"/><path d="M24 40C32.84 40 40 32.84 40 24C40 19.6 38.21 15.6 35.3 12.7L32.5 15.5C34.7 17.7 36 20.7 36 24C36 30.63 30.63 36 24 36V42L16 34L24 26V32C28.42 32 32 28.42 32 24C32 21.8 31.1 19.8 29.7 18.3L32.5 15.5" fill="#00CEC9"/></svg>'],
            ['name' => 'System Logs', 'slug' => 'system-logs', 'destination_url' => '#features', 'sort_order' => 16,
             'icon_svg' => '<svg viewBox="0 0 48 48" fill="none"><path d="M24 4L38 10V22C38 31.5 32 39 24 44C16 39 10 31.5 10 22V10L24 4Z" fill="#334155"/><path d="M24 16V25L30 28" stroke="#38BDF8" stroke-width="2.5" stroke-linecap="round"/><circle cx="24" cy="24" r="10" stroke="#38BDF8" stroke-width="2"/></svg>'],
            ['name' => 'Recycle Bin', 'slug' => 'recycle-bin', 'destination_url' => '#features', 'sort_order' => 17,
             'icon_svg' => '<svg viewBox="0 0 48 48" fill="none"><path d="M12 14H36M18 14V10C18 8.9 18.9 8 20 8H28C29.1 8 30 8.9 30 10V14M33 14V38C33 40.2 31.2 42 29 42H19C16.8 42 15 40.2 15 38V14" stroke="#DC2626" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M21 22V34M27 22V34" stroke="#F59E0B" stroke-width="2.5" stroke-linecap="round"/><circle cx="36" cy="34" r="6" fill="#16A34A"/><path d="M34 34L35.5 35.5L38.5 32.5" stroke="#FFFFFF" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>'],
            ['name' => 'ERP Settings', 'slug' => 'settings', 'destination_url' => '#features', 'sort_order' => 18,
             'icon_svg' => '<svg viewBox="0 0 48 48" fill="none"><circle cx="24" cy="24" r="16" fill="#4B5563"/><circle cx="24" cy="24" r="8" fill="#FFFFFF"/><circle cx="24" cy="24" r="5" fill="#4361EE"/><path d="M24 4V8M24 40V44M4 24H8M40 24H44M9.8 9.8L12.7 12.7M35.3 35.3L38.2 38.2M9.8 38.2L12.7 35.3M35.3 12.7L38.2 9.8" stroke="#4B5563" stroke-width="3.5" stroke-linecap="round"/></svg>'],
        ];

        foreach ($modules as $m) {
            CmsAppModule::updateOrCreate(['name' => $m['name']], $m);
        }

        // 4. Features
        $features = [
            [
                'title' => 'Job Order Management',
                'description' => 'Organize jobs with custom dimensions, technical requirements, and production milestones across departments.',
                'icon_class' => 'fas fa-clipboard-check',
                'icon_color_class' => 'icon-blue',
                'highlights' => ['Department routing', 'Production timeline', 'Statement printouts'],
                'sort_order' => 1,
            ],
            [
                'title' => 'Smart Quotations',
                'description' => 'Draft professional quotes with line items, tax percentage, line discounts, and instant 1-click customer PDF exports.',
                'icon_class' => 'fas fa-file-signature',
                'icon_color_class' => 'icon-indigo',
                'highlights' => ['Area/size calculations', 'Multi-currency & VAT', '1-click quote conversion'],
                'sort_order' => 2,
            ],
            [
                'title' => 'Invoicing & Payments',
                'description' => 'Generate tax invoices, record partial or full cash payments, calculate remaining balances, and send printable receipts.',
                'icon_class' => 'fas fa-file-invoice-dollar',
                'icon_color_class' => 'icon-green',
                'highlights' => ['Partial payment ledger', 'Print thermal or A4 PDF', 'Real-time status tags'],
                'sort_order' => 3,
            ],
            [
                'title' => 'Customer CRM & Ledgers',
                'description' => 'Maintain centralized client records, phone numbers, addresses, and comprehensive transaction statement reports.',
                'icon_class' => 'fas fa-users-cog',
                'icon_color_class' => 'icon-teal',
                'highlights' => ['Complete ledger history', 'Export statements to PDF', 'Quick phone search'],
                'sort_order' => 4,
            ],
            [
                'title' => 'Products & Service Catalog',
                'description' => 'Organize catalog items by categories and units (meter, square foot, piece, hour) with flexible default pricing.',
                'icon_class' => 'fas fa-cubes',
                'icon_color_class' => 'icon-amber',
                'highlights' => ['Categorized catalog', 'Flexible measurement units', 'Quick item autocompletion'],
                'sort_order' => 5,
            ],
            [
                'title' => 'Delivery Notes & Dispatch',
                'description' => 'Create proof-of-delivery documentation to accompany goods dispatch with driver details and receiver signature fields.',
                'icon_class' => 'fas fa-truck-loading',
                'icon_color_class' => 'icon-sky',
                'highlights' => ['Printable dispatch slips', 'Receiver sign area', 'Order link association'],
                'sort_order' => 6,
            ],
            [
                'title' => 'Multi-Shop & Branches',
                'description' => 'Manage multiple physical shops, showrooms, or workshop locations from one centralized Super Admin management portal.',
                'icon_class' => 'fas fa-sitemap',
                'icon_color_class' => 'icon-purple',
                'highlights' => ['Global overview metrics', 'Instant shop switching', 'Branch isolated records'],
                'sort_order' => 7,
            ],
            [
                'title' => 'Role-Based Security',
                'description' => 'Ensure staff members only access what they need. Protect sensitive pricing, profit margins, and owner configurations.',
                'icon_class' => 'fas fa-user-shield',
                'icon_color_class' => 'icon-red',
                'highlights' => ['Super Admin privileges', 'Shop manager controls', 'Employee worker access'],
                'sort_order' => 8,
            ],
        ];

        foreach ($features as $f) {
            CmsFeature::updateOrCreate(['title' => $f['title']], $f);
        }

        // 5. Showcase Tabs
        $tabs = [
            [
                'tab_key' => 'tab-dashboard',
                'tab_label' => '1. Executive Dashboard',
                'tab_icon' => 'fas fa-th-large',
                'badge_text' => 'Real-Time Visibility',
                'title' => 'Complete Branch & Revenue Control',
                'description' => 'The Super Admin Portal gives business owners an instant snapshot of multi-shop sales, pending payments, and active customer orders without asking staff for updates.',
                'highlights' => ['Switch between shops with one click', 'Track live unpaid client balances', 'Monitor quote conversion rates in real time'],
                'cta_text' => 'Try Dashboard Free',
                'cta_url' => '#pricing',
                'sort_order' => 1,
            ],
            [
                'tab_key' => 'tab-quotes',
                'tab_label' => '2. Instant Quotation Builder',
                'tab_icon' => 'fas fa-file-signature',
                'badge_text' => 'Fast Proposals',
                'title' => 'Smart Quotation Generator',
                'description' => 'Create accurate, branded quotations in seconds. Select customers with instant phone autocompletion, add line items with unit pricing, size, and auto-computed taxes.',
                'highlights' => ['Supports custom dimensions & meter quantities', 'Professional PDF download ready for WhatsApp', '1-Click convert to active Job Order or Invoice'],
                'cta_text' => 'Try Quotations Free',
                'cta_url' => '#pricing',
                'sort_order' => 2,
            ],
            [
                'tab_key' => 'tab-orders',
                'tab_label' => '3. Job Order Production',
                'tab_icon' => 'fas fa-tasks',
                'badge_text' => 'Production Tracking',
                'title' => 'End-to-End Job Order Management',
                'description' => 'Eliminate lost job instructions and workshop delays. Assign jobs to specific production departments, specify custom measurements, and keep track of actual progress.',
                'highlights' => ['Detailed specifications and material parameters', 'Multi-department workflow from design to fabrication', 'Status tags: Pending, In Progress, Completed'],
                'cta_text' => 'Track Orders Free',
                'cta_url' => '#pricing',
                'sort_order' => 3,
            ],
            [
                'tab_key' => 'tab-invoices',
                'tab_label' => '4. Tax Invoices & Ledger',
                'tab_icon' => 'fas fa-receipt',
                'badge_text' => 'Fast Accounting',
                'title' => 'Invoicing, Payments & Customer Ledgers',
                'description' => 'Record cash payments on the spot, maintain transparent ledger statements for every customer, and never lose track of who owes your business money.',
                'highlights' => ['Record partial cash-by-hand or bank payments', 'Auto-calculate remaining due balance', 'Comprehensive PDF statement export'],
                'cta_text' => 'Start Invoicing Free',
                'cta_url' => '#pricing',
                'sort_order' => 4,
            ],
        ];

        foreach ($tabs as $t) {
            CmsShowcaseTab::updateOrCreate(['tab_key' => $t['tab_key']], $t);
        }

        // 6. Solutions
        $solutions = [
            [
                'tag' => 'Signage & Advertising',
                'title' => 'Printing, Signboard & Advertising Agencies',
                'description' => 'Say goodbye to manual area calculations and pricing mistakes. Crelogics ERP is uniquely equipped to handle width x height dimensions, square feet, square meters, flex boards, acrylic lettering, and workshop departments.',
                'checklist' => ['Custom dimension and meter calculation inputs on items', 'Multi-department job progression (Design, Printing, Fabrication)', 'Fast customer PDF quotes with specifications clearly displayed'],
                'sort_order' => 1,
            ],
            [
                'tag' => 'Fabrication & Service',
                'title' => 'Service Contractors & Custom Fabrication',
                'description' => 'Manage custom client orders from request to final installation. Track job orders in real-time, record client down payments, and issue delivery receipts when work is fulfilled.',
                'checklist' => ['Step-by-step job order status updates from pending to done', 'Instant delivery note generation with receiver signature', 'Partial cash payment entries and clear outstanding receivables'],
                'sort_order' => 2,
            ],
            [
                'tag' => 'Distribution & Wholesale',
                'title' => 'Wholesale, Trading & Commercial Supply',
                'description' => 'Issue fast sales invoices, manage customer credit limits, generate printable statement PDFs for account reconciliation, and eliminate disputes over unpaid invoices.',
                'checklist' => ['Comprehensive customer transaction ledger with debit/credit balance', 'Print-ready customer statements with verified balance totals', 'Delivery documentation to accompany truck shipments'],
                'sort_order' => 3,
            ],
            [
                'tag' => 'Multi-Location',
                'title' => 'Multi-Branch Retailers & Workshop Chains',
                'description' => 'Run multiple shops or workshop locations under one master business account. The business owner gets a bird\'s-eye view of all branches while branch staff only see their local records.',
                'checklist' => ['Seamlessly switch between branch dashboards without re-logging in', 'Consolidated company revenue tracking alongside branch metrics', 'Branch-specific user roles, quotes, and invoice numbering'],
                'sort_order' => 4,
            ],
        ];

        foreach ($solutions as $s) {
            CmsSolution::updateOrCreate(['title' => $s['title']], $s);
        }

        // 7. Steps
        $steps = [
            ['step_number' => 1, 'title' => 'Set Up Your Business', 'description' => 'Register your account, upload your company logo, set your default currency and branch details, and invite staff members with secure role permissions.', 'badge_color' => '#4361EE', 'sort_order' => 1],
            ['step_number' => 2, 'title' => 'Organize Catalog & Clients', 'description' => 'Enter your standard products, materials, units of measurement, and client contacts. Everything is instantly available across quotes and invoices.', 'badge_color' => '#00B894', 'sort_order' => 2],
            ['step_number' => 3, 'title' => 'Manage, Issue & Grow', 'description' => 'Create instant quotes, dispatch job orders to your workshop, record payments, and track outstanding balances across all your business locations.', 'badge_color' => '#17243A', 'sort_order' => 3],
        ];

        foreach ($steps as $st) {
            CmsStep::updateOrCreate(['step_number' => $st['step_number']], $st);
        }

        // 8. Benefits
        $benefits = [
            ['title' => 'Faster Quotation-to-Cash Cycle', 'description' => 'Generate professional quotes in under 2 minutes and convert them to invoices with a single click once approved.', 'sort_order' => 1],
            ['title' => 'Zero Guesswork on Unpaid Balances', 'description' => 'Live customer ledgers display exactly who has paid, how much was received, and which accounts are overdue.', 'sort_order' => 2],
            ['title' => 'Clean Workshop Production Flow', 'description' => 'Job orders clearly communicate required dimensions, instructions, and department status without confusion.', 'sort_order' => 3],
            ['title' => 'Scale Across Branches Smoothly', 'description' => 'Add new branches or physical stores without purchasing separate software for each location.', 'sort_order' => 4],
        ];

        foreach ($benefits as $b) {
            CmsBenefit::updateOrCreate(['title' => $b['title']], $b);
        }

        // 9. Testimonials (Approved initial sample from real clients)
        $testimonials = [
            ['name' => 'Ali Imran', 'designation' => 'Managing Director', 'company' => 'Imran Traders', 'content' => 'Crelogics ERP eliminated our paper quotation chaos. We issue quotes on WhatsApp in two minutes and convert to invoices with one click.', 'rating' => 5, 'is_approved' => true, 'sort_order' => 1],
            ['name' => 'Nida Farooq', 'designation' => 'Operations Head', 'company' => 'Farooq Builders', 'content' => 'Tracking job orders through cutting, printing and welding departments has never been this smooth. Our team knows exactly what to do next.', 'rating' => 5, 'is_approved' => true, 'sort_order' => 2],
            ['name' => 'Salman Tariq', 'designation' => 'Founder', 'company' => 'Salman Agencies', 'content' => 'The multi-branch dashboard allows me to see both our retail showroom and factory revenue from anywhere on my phone.', 'rating' => 5, 'is_approved' => true, 'sort_order' => 3],
        ];

        foreach ($testimonials as $t) {
            CmsTestimonial::updateOrCreate(['name' => $t['name']], $t);
        }

        // 10. FAQs
        $faqs = [
            ['question' => 'What is included in the 7-day free trial?', 'answer' => 'The 7-day trial gives you full access to all standard ERP features: drafting quotes, generating invoices, routing job orders, tracking customers, and downloading PDF receipts. No credit card is required to begin.', 'sort_order' => 1],
            ['question' => 'Can I manage multiple shops or physical branches?', 'answer' => 'Yes! With the Business Multi-Shop package, you can create up to 5 branch locations. Business owners have a global overview and can switch between branches with one click, while staff members remain scoped to their assigned branch.', 'sort_order' => 2],
            ['question' => 'How does Crelogics ERP handle custom dimensions and sizing?', 'answer' => 'Crelogics ERP was specifically built with flexible measurement units (meters, square feet, linear dimensions, pieces, and custom units). You can enter width and height measurements directly into quotation and invoice line items.', 'sort_order' => 3],
            ['question' => 'Can I convert a quotation directly into an active Job Order or Invoice?', 'answer' => 'Yes. There is zero need to re-enter customer data or line items. Once a client accepts a quotation, you can convert it into a Job Order for the workshop or issue a Sales Invoice with a single click.', 'sort_order' => 4],
            ['question' => 'How do customer payments and ledger balances work?', 'answer' => 'When creating an invoice or recording a payment, you can enter the cash received. Crelogics ERP automatically calculates the remaining balance, logs the transaction in the client ledger, and displays the balance on printable statements.', 'sort_order' => 5],
            ['question' => 'Can I download and print PDF invoices, quotes, and delivery slips?', 'answer' => 'Yes. Every quotation, sales invoice, job order, and delivery note generates a clean, branded PDF featuring your business logo, branch details, tax calculation, and signature areas ready for printing or WhatsApp sharing.', 'sort_order' => 6],
            ['question' => 'How are subscriptions activated?', 'answer' => 'We support offline cash-by-hand activation as well as manual administrator approvals. You can select your package and connect directly with our support team on WhatsApp for instant onboarding.', 'sort_order' => 7],
        ];

        foreach ($faqs as $f) {
            CmsFaq::updateOrCreate(['question' => $f['question']], $f);
        }

        // 11. Nav Items
        $navs = [
            ['label' => 'Features', 'url' => '#features', 'location' => 'header', 'sort_order' => 1],
            ['label' => 'Modules', 'url' => '#modules', 'location' => 'header', 'sort_order' => 2],
            ['label' => 'Workflow', 'url' => '#showcase', 'location' => 'header', 'sort_order' => 3],
            ['label' => 'Solutions', 'url' => '#solutions', 'location' => 'header', 'sort_order' => 4],
            ['label' => 'Pricing', 'url' => '#pricing', 'location' => 'header', 'sort_order' => 5],
            ['label' => 'FAQ', 'url' => '#faq', 'location' => 'header', 'sort_order' => 6],
            ['label' => 'Contact', 'url' => '#contact', 'location' => 'header', 'sort_order' => 7],
        ];

        foreach ($navs as $n) {
            CmsNavItem::updateOrCreate(['label' => $n['label'], 'location' => $n['location']], $n);
        }
    }
}

