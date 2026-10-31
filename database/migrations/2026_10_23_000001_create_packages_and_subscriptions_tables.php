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
        // 1. Packages table
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->string('billing_cycle')->default('yearly'); // weekly, yearly
            $table->integer('duration_days')->default(365);
            $table->integer('max_shops')->default(1);
            $table->integer('max_employees')->nullable(); // null = unlimited
            $table->json('features')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // 2. Add package and subscription columns to shops table
        Schema::table('shops', function (Blueprint $table) {
            $table->foreignId('package_id')->nullable()->after('status')->constrained('packages')->nullOnDelete();
            $table->timestamp('subscription_starts_at')->nullable()->after('package_id');
            $table->timestamp('subscription_expires_at')->nullable()->after('subscription_starts_at');
            $table->string('subscription_status')->default('active')->after('subscription_expires_at');
            $table->foreignId('parent_shop_id')->nullable()->after('subscription_status')->constrained('shops')->nullOnDelete();
            $table->foreignId('owner_id')->nullable()->after('parent_shop_id')->constrained('users')->nullOnDelete();
        });

        // 3. Subscriptions history / cash payment logs table
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained('shops')->cascadeOnDelete();
            $table->foreignId('package_id')->constrained('packages')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('payment_method')->default('cash_by_hand');
            $table->string('payment_status')->default('paid');
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->string('status')->default('active');
            $table->text('payment_notes')->nullable();
            $table->foreignId('recorded_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 4. Seed initial packages
        $now = now();
        $packages = [
            [
                'name' => 'Test Account (1 Week Trial)',
                'slug' => 'trial',
                'description' => '7-day test account for system trial & evaluation. Automatically expires after 1 week.',
                'price' => 0.00,
                'billing_cycle' => 'weekly',
                'duration_days' => 7,
                'max_shops' => 1,
                'max_employees' => 3,
                'features' => json_encode([
                    '7 Days Full Test Period',
                    'Single Shop Location (1 Shop)',
                    'Up to 3 Staff Members',
                    'Quotes & Invoices Management',
                    'Job Orders & Delivery Notes',
                    'Offline Cash By Hand Activation'
                ]),
                'is_active' => true,
                'sort_order' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Yearly Standard Package',
                'slug' => 'yearly',
                'description' => 'Full business and production system for single shop for 1 complete year.',
                'price' => 500.00,
                'billing_cycle' => 'yearly',
                'duration_days' => 365,
                'max_shops' => 1,
                'max_employees' => 15,
                'features' => json_encode([
                    '1 Full Year Access (365 Days)',
                    'Single Shop Location (1 Shop)',
                    'Up to 15 Staff Members',
                    'Unlimited Quotes, Invoices & PDFs',
                    'Department Production Tracking',
                    'Customer Ledger & Statements',
                    'Payment Mode: Cash By Hand'
                ]),
                'is_active' => true,
                'sort_order' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Business Multi-Shop Package',
                'slug' => 'business',
                'description' => 'Enterprise multi-branch solution to manage multiple shops under one business.',
                'price' => 1500.00,
                'billing_cycle' => 'yearly',
                'duration_days' => 365,
                'max_shops' => 5,
                'max_employees' => null,
                'features' => json_encode([
                    '1 Full Year Access (365 Days)',
                    'Multi-Shop Support (Up to 5 Branches)',
                    'Unlimited Staff & Employees',
                    'Central Business Owner Switcher',
                    'All Departments & Production Units',
                    'Branch-wise Financial Tracking',
                    'Priority Cash By Hand Support'
                ]),
                'is_active' => true,
                'sort_order' => 3,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('packages')->insert($packages);

        // Assign Yearly Standard package to existing shops as default so their data remains active
        $yearlyPkg = DB::table('packages')->where('slug', 'yearly')->first();
        if ($yearlyPkg) {
            DB::table('shops')->update([
                'package_id' => $yearlyPkg->id,
                'subscription_starts_at' => $now,
                'subscription_expires_at' => $now->copy()->addDays(365),
                'subscription_status' => 'active',
            ]);

            // Also create an initial subscription record for existing shops
            $existingShops = DB::table('shops')->get();
            foreach ($existingShops as $shop) {
                DB::table('subscriptions')->insert([
                    'shop_id' => $shop->id,
                    'package_id' => $yearlyPkg->id,
                    'payment_method' => 'cash_by_hand',
                    'payment_status' => 'paid',
                    'amount_paid' => $yearlyPkg->price,
                    'starts_at' => $now,
                    'expires_at' => $now->copy()->addDays(365),
                    'status' => 'active',
                    'payment_notes' => 'Initial migration setup (Cash by Hand recorded)',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->dropForeign(['package_id']);
            $table->dropForeign(['parent_shop_id']);
            $table->dropForeign(['owner_id']);
            $table->dropColumn([
                'package_id',
                'subscription_starts_at',
                'subscription_expires_at',
                'subscription_status',
                'parent_shop_id',
                'owner_id',
            ]);
        });

        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('packages');
    }
};
