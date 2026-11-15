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
        if (!Schema::hasTable('business_types')) {
            Schema::create('business_types', function (Blueprint $table) {
                $table->id();
                $table->string('slug')->unique();
                $table->string('name');
                $table->string('name_ar')->nullable();
                $table->string('icon')->default('fas fa-briefcase');
                $table->text('description')->nullable();
                $table->text('description_ar')->nullable();
                $table->json('default_modules')->nullable();
                $table->json('default_departments')->nullable();
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->boolean('is_system')->default(false);
                $table->timestamps();
            });

            // Initial Seed with default industry categories
            $initialData = [
                [
                    'slug' => 'printing_advertising',
                    'name' => 'Printing, Signage & Advertising',
                    'name_ar' => 'الطباعة واللوحات والدعاية والإعلان',
                    'icon' => 'fas fa-print',
                    'description' => 'Flex printing, 3D channel signage, vehicle graphics, job order production.',
                    'description_ar' => 'طباعة الفليكس والبنر، لوحات الحروف البارزة ثلاثية الأبعاد، وأوامر العمل والإنتاج.',
                    'default_modules' => json_encode([
                        'job_orders', 'quotations', 'invoices', 'delivery_notes', 'customers',
                        'products', 'services', 'categories', 'units', 'employees', 'departments'
                    ]),
                    'default_departments' => json_encode([
                        'Printing & Production', 'Graphic Design & Creative', 'Installation & Fabrication', 'Sales & Marketing'
                    ]),
                    'sort_order' => 1,
                    'is_active' => true,
                    'is_system' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'slug' => 'travel_umrah',
                    'name' => 'Umrah, Travel & Tourism Agency',
                    'name_ar' => 'وكالة العمرة والسياحة والأسفار',
                    'icon' => 'fas fa-kaaba',
                    'description' => 'Bus seat booking, Umrah trips, passenger boarding passes, hotel & travel expense packages.',
                    'description_ar' => 'حجز مقاعد الحافلات، رحلات العمرة، بطاقات صعود الركاب، وباقات الفنادق ومصاريف السفر.',
                    'default_modules' => json_encode([
                        'travel_tourism', 'customers', 'invoices', 'employees', 'departments'
                    ]),
                    'default_departments' => json_encode([
                        'Transport & Fleet Operations', 'Ticketing & Visa Operations', 'Hotels & Accommodations', 'Customer Relations'
                    ]),
                    'sort_order' => 2,
                    'is_active' => true,
                    'is_system' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'slug' => 'general_trading',
                    'name' => 'General Trading & Retail Store',
                    'name_ar' => 'التجارة العامة والمؤسسات التجارية',
                    'icon' => 'fas fa-shopping-cart',
                    'description' => 'Inventory, products, sales invoices, customer ledger balances.',
                    'description_ar' => 'إدارة المخزون، كتالوج المنتجات، فواتير المبيعات، وأرصدة حسابات العملاء.',
                    'default_modules' => json_encode([
                        'invoices', 'customers', 'products', 'categories', 'units', 'employees', 'departments'
                    ]),
                    'default_departments' => json_encode([
                        'Sales & Showroom', 'Warehouse & Inventory', 'Purchasing & Procurement', 'Accounts & Finance'
                    ]),
                    'sort_order' => 3,
                    'is_active' => true,
                    'is_system' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'slug' => 'services_agency',
                    'name' => 'Digital & Professional Services',
                    'name_ar' => 'الخدمات الرقمية والاستشارية والتقنية',
                    'icon' => 'fas fa-laptop-code',
                    'description' => 'Client proposals, project milestones, hourly/fixed services billing.',
                    'description_ar' => 'عروض العملاء، مراحل المشاريع، وفواتير الخدمات بالساعة أو المبلغ المقطوع.',
                    'default_modules' => json_encode([
                        'quotations', 'invoices', 'customers', 'services', 'employees', 'departments'
                    ]),
                    'default_departments' => json_encode([
                        'Consulting & Strategy', 'Development & Tech Services', 'Client Success & Support', 'Finance & Administration'
                    ]),
                    'sort_order' => 4,
                    'is_active' => true,
                    'is_system' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'slug' => 'custom',
                    'name' => 'Custom / Full Enterprise Suite',
                    'name_ar' => 'أعمال مخصصة شاملة لكافة الوحدات',
                    'icon' => 'fas fa-cubes',
                    'description' => 'Tailor-fit permissions with all ERP modules selectable.',
                    'description_ar' => 'صلاحيات مخصصة بالكامل مع إمكانية تفعيل كافة موديولات النظام.',
                    'default_modules' => json_encode([
                        'job_orders', 'quotations', 'invoices', 'delivery_notes', 'customers',
                        'products', 'services', 'categories', 'units', 'employees', 'departments', 'travel_tourism'
                    ]),
                    'default_departments' => json_encode([
                        'Executive Management', 'Operations & Fulfillment', 'Accounts & Finance'
                    ]),
                    'sort_order' => 5,
                    'is_active' => true,
                    'is_system' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ];

            DB::table('business_types')->insert($initialData);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_types');
    }
};
