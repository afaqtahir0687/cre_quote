<?php

use App\Models\Shop;
use App\Services\SubscriptionNotificationService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/**
 * Check for expired shop subscriptions and dispatch email notification.
 */
Artisan::command('subscriptions:check-expiry', function () {
    $this->info('Checking shop subscriptions for expiration...');

    $expiredShops = Shop::withoutGlobalScopes()
        ->where('subscription_status', 'active')
        ->whereNotNull('subscription_expires_at')
        ->where('subscription_expires_at', '<', now())
        ->whereNull('subscription_expiry_notified_at')
        ->get();

    $count = 0;
    foreach ($expiredShops as $shop) {
        $sent = SubscriptionNotificationService::sendPackageExpired($shop);
        if ($sent) {
            $this->info("Expiry email sent to shop: {$shop->name} (ID: {$shop->id})");
            $count++;
        }
    }

    $this->info("Completed. {$count} shop(s) notified.");
})->purpose('Check and notify shops whose subscriptions have expired');

Schedule::command('subscriptions:check-expiry')->hourly();

