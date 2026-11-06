<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\QuoteItem;
use App\Models\InvoiceItem;
use App\Models\JobOrderItem;
use App\Models\Department;
use App\Services\TranslationService;

class WarmDynamicTranslations extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'translations:warm';

    /**
     * The console command description.
     */
    protected $description = 'Pre-warm dynamic translations for all existing products, items, and departments';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting translation pre-warming for existing database records...');

        // 1. Departments
        $depts = Department::all();
        foreach ($depts as $dept) {
            TranslationService::warmCache($dept->name);
        }
        $this->info("Warmed {$depts->count()} departments.");

        // 2. Products
        $products = Product::all();
        foreach ($products as $prod) {
            TranslationService::warmCache($prod->name);
            if (!empty($prod->description)) {
                TranslationService::warmCache($prod->description);
            }
        }
        $this->info("Warmed {$products->count()} products.");

        // 3. Quote Items
        $quoteItems = QuoteItem::all();
        foreach ($quoteItems as $qi) {
            TranslationService::warmCache($qi->description);
        }
        $this->info("Warmed {$quoteItems->count()} quote items.");

        // 4. Invoice Items
        $invoiceItems = InvoiceItem::all();
        foreach ($invoiceItems as $ii) {
            TranslationService::warmCache($ii->description);
        }
        $this->info("Warmed {$invoiceItems->count()} invoice items.");

        // 5. Job Order Items
        $jobItems = JobOrderItem::all();
        foreach ($jobItems as $ji) {
            TranslationService::warmCache($ji->description);
        }
        $this->info("Warmed {$jobItems->count()} job order items.");

        // 6. Employees
        $employees = \App\Models\Employee::all();
        foreach ($employees as $emp) {
            TranslationService::warmCache($emp->name);
            TranslationService::warmCache($emp->designation);
        }
        $this->info("Warmed {$employees->count()} employees.");

        // 7. Customers & Companies
        $customers = \App\Models\Customer::all();
        foreach ($customers as $c) {
            TranslationService::warmCache($c->name);
            if (!empty($c->company_name)) TranslationService::warmCache($c->company_name);
            if (!empty($c->city)) TranslationService::warmCache($c->city);
        }
        $companies = \App\Models\CustomerCompany::all();
        foreach ($companies as $comp) {
            if (!empty($comp->company_name)) TranslationService::warmCache($comp->company_name);
            if (!empty($comp->city)) TranslationService::warmCache($comp->city);
        }
        $this->info("Warmed {$customers->count()} customers and {$companies->count()} customer companies.");

        // 8. Shops & Branches
        $shops = \App\Models\Shop::all();
        foreach ($shops as $s) {
            TranslationService::warmCache($s->name);
            if (!empty($s->city)) TranslationService::warmCache($s->city);
        }
        $this->info("Warmed {$shops->count()} shops.");

        // 9. Packages
        $packages = \App\Models\Package::all();
        foreach ($packages as $pkg) {
            TranslationService::warmCache($pkg->name);
            if (!empty($pkg->description)) TranslationService::warmCache($pkg->description);
        }
        $this->info("Warmed {$packages->count()} packages.");

        $this->info('Dynamic translations successfully pre-warmed!');
    }
}

