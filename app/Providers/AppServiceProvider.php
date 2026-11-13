<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        // Share multi-shop context with all views
        view()->composer('*', function ($view) {
            $view->with('setting', current_setting());
            $view->with('currentShop', current_shop());
            if (auth()->check() && auth()->user()->isSuperAdmin()) {
                $view->with('allShops', \App\Models\Shop::orderBy('name')->get());
            }
        });

        // Share categorized services with website views for header dropdown
        view()->composer('website.*', function ($view) {
            try {
                if (\Illuminate\Support\Facades\Schema::hasTable('services')) {
                    $view->with('websiteServices', \App\Models\Service::active()->orderBy('category')->orderBy('sort_order')->get()->groupBy('category'));
                }
            } catch (\Throwable $e) {
                // Graceful fallback
            }
        });
    }
}
