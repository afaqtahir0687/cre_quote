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
        // 1. Create customer_companies table
        if (!Schema::hasTable('customer_companies')) {
            Schema::create('customer_companies', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('customer_id');
                $table->string('company_name');
                $table->string('vat_no')->nullable();
                $table->string('cr_no')->nullable();
                $table->text('address')->nullable();
                $table->string('city')->nullable();
                $table->string('zip')->nullable();
                $table->string('country')->nullable()->default('Saudi Arabia');
                $table->boolean('is_default')->default(false);
                $table->timestamps();

                $table->foreign('customer_id')
                    ->references('id')
                    ->on('customers')
                    ->onDelete('cascade');
            });
        }

        // 2. Add customer_company_id to quotes, job_orders, invoices, delivery_notes
        $tables = ['quotes', 'job_orders', 'invoices', 'delivery_notes'];
        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName) && !Schema::hasColumn($tableName, 'customer_company_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->unsignedBigInteger('customer_company_id')->nullable()->after('customer_id');
                    $table->foreign('customer_company_id')
                        ->references('id')
                        ->on('customer_companies')
                        ->onDelete('set null');
                });
            }
        }

        // 3. Migrate existing customer legacy data into customer_companies
        $existingCustomers = DB::table('customers')->get();
        foreach ($existingCustomers as $customer) {
            // Check if this customer already has a company record
            $existingCompany = DB::table('customer_companies')->where('customer_id', $customer->id)->first();
            if (!$existingCompany) {
                $companyName = !empty($customer->company_name) ? $customer->company_name : ($customer->name . ' Branch');
                $companyId = DB::table('customer_companies')->insertGetId([
                    'customer_id'   => $customer->id,
                    'company_name'  => $companyName,
                    'vat_no'        => $customer->vat_no ?? null,
                    'cr_no'         => $customer->cr_no ?? null,
                    'address'       => $customer->address ?? null,
                    'city'          => $customer->city ?? null,
                    'zip'           => $customer->zip ?? null,
                    'country'       => $customer->country ?? 'Saudi Arabia',
                    'is_default'    => 1,
                    'created_at'    => $customer->created_at ?? now(),
                    'updated_at'    => $customer->updated_at ?? now(),
                ]);

                // Link existing quotes, job orders, invoices, delivery notes
                foreach ($tables as $tName) {
                    if (Schema::hasTable($tName) && Schema::hasColumn($tName, 'customer_company_id')) {
                        DB::table($tName)
                            ->where('customer_id', $customer->id)
                            ->whereNull('customer_company_id')
                            ->update(['customer_company_id' => $companyId]);
                    }
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = ['quotes', 'job_orders', 'invoices', 'delivery_notes'];
        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'customer_company_id')) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    $table->dropForeign([$tableName . '_customer_company_id_foreign']);
                    $table->dropColumn('customer_company_id');
                });
            }
        }

        Schema::dropIfExists('customer_companies');
    }
};
