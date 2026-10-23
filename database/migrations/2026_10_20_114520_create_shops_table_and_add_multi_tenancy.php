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
        // 1. Create shops table
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('currency_symbol')->default('$');
            $table->string('invoice_prefix')->default('INV-');
            $table->string('quote_prefix')->default('QT-');
            $table->string('status')->default('active'); // active, inactive
            $table->string('logo')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Insert initial default shop
        $now = now();
        $shopId = DB::table('shops')->insertGetId([
            'name' => 'New Crelogics',
            'slug' => 'new-crelogics',
            'email' => 'newsignad@gmail.com',
            'phone' => '+966 50 000 0000',
            'currency_symbol' => '$',
            'invoice_prefix' => 'INV-',
            'quote_prefix' => 'QT-',
            'status' => 'active',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 3. Update users table role and add shop_id
        DB::statement("ALTER TABLE users MODIFY COLUMN role VARCHAR(50) DEFAULT 'employee'");

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('shop_id')->nullable()->after('id')->constrained('shops')->nullOnDelete();
        });

        // Make Afaq Tahir super_admin
        DB::table('users')
            ->where('username', 'AfaqTahir')
            ->orWhere('email', 'meemwork0687@gmail.com')
            ->orWhere('id', 2)
            ->update([
                'role' => 'super_admin',
                'shop_id' => $shopId,
            ]);

        // Ensure other users belong to the default shop
        DB::table('users')->whereNull('shop_id')->update([
            'shop_id' => $shopId,
        ]);

        // 4. Add shop_id to tenant models
        $tables = [
            'customers',
            'products',
            'quotes',
            'job_orders',
            'invoices',
            'delivery_notes',
            'departments',
            'employees',
            'settings',
            'system_logs',
        ];

        foreach ($tables as $tbl) {
            if (Schema::hasTable($tbl) && !Schema::hasColumn($tbl, 'shop_id')) {
                Schema::table($tbl, function (Blueprint $table) {
                    $table->foreignId('shop_id')->nullable()->after('id')->constrained('shops')->cascadeOnDelete();
                });
                DB::table($tbl)->whereNull('shop_id')->update(['shop_id' => $shopId]);
            }
        }

        // 5. Ensure a setting record exists for the default shop
        $existingSetting = DB::table('settings')->first();
        if (!$existingSetting) {
            DB::table('settings')->insert([
                'shop_id' => $shopId,
                'company_name' => 'New Crelogics',
                'company_email' => 'newsignad@gmail.com',
                'currency_symbol' => '$',
                'invoice_prefix' => 'INV-',
                'quote_prefix' => 'QT-',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        } else {
            DB::table('settings')->where('id', $existingSetting->id)->update(['shop_id' => $shopId]);
        }

        // 6. Update unique constraints to be scoped per shop
        try {
            Schema::table('quotes', function (Blueprint $table) {
                $table->dropUnique('quotes_quote_number_unique');
                $table->unique(['shop_id', 'quote_number']);
            });
        } catch (\Throwable $e) {}

        try {
            Schema::table('job_orders', function (Blueprint $table) {
                $table->dropUnique('job_orders_order_number_unique');
                $table->unique(['shop_id', 'order_number']);
            });
        } catch (\Throwable $e) {}

        try {
            Schema::table('invoices', function (Blueprint $table) {
                $table->dropUnique('invoices_invoice_number_unique');
                $table->unique(['shop_id', 'invoice_number']);
            });
        } catch (\Throwable $e) {}

        try {
            Schema::table('delivery_notes', function (Blueprint $table) {
                $table->dropUnique('delivery_notes_delivery_number_unique');
                $table->unique(['shop_id', 'delivery_number']);
            });
        } catch (\Throwable $e) {}
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'customers',
            'products',
            'quotes',
            'job_orders',
            'invoices',
            'delivery_notes',
            'departments',
            'employees',
            'settings',
            'system_logs',
        ];

        foreach ($tables as $tbl) {
            if (Schema::hasTable($tbl) && Schema::hasColumn($tbl, 'shop_id')) {
                Schema::table($tbl, function (Blueprint $table) use ($tbl) {
                    $table->dropForeign([$tbl . '_shop_id_foreign']);
                    $table->dropColumn('shop_id');
                });
            }
        }

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'shop_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropForeign(['users_shop_id_foreign']);
                $table->dropColumn('shop_id');
            });
        }

        Schema::dropIfExists('shops');
    }
};
