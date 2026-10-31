<?php

namespace App\Services;

use App\Models\Package;
use App\Models\Shop;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SubscriptionNotificationService
{
    /**
     * Gather valid recipient email addresses for a shop (shop email + admin users).
     */
    public static function getShopRecipients(Shop $shop): array
    {
        $recipients = collect();

        if (!empty($shop->email) && filter_var($shop->email, FILTER_VALIDATE_EMAIL)) {
            $recipients->push(trim($shop->email));
        }

        // Shop admins
        $shop->loadMissing('admins');
        foreach ($shop->admins as $admin) {
            if (!empty($admin->email) && filter_var($admin->email, FILTER_VALIDATE_EMAIL)) {
                $recipients->push(trim($admin->email));
            }
        }

        return $recipients->unique()->values()->toArray();
    }

    /**
     * Send Package Activated email to shop admin.
     */
    public static function sendPackageActivated(Shop $shop, Package $package, $amountPaid = null): bool
    {
        $recipients = self::getShopRecipients($shop);

        if (empty($recipients)) {
            Log::info("No recipient email found for shop '{$shop->name}' to send activation email.");
            return false;
        }

        $subject = "Subscription Activated: {$package->name} - {$shop->name}";
        $recipientName = $shop->admins->first()?->name ?? $shop->name;

        try {
            Mail::send(
                'emails.subscription_notification',
                [
                    'shop' => $shop,
                    'package' => $package,
                    'type' => 'activated',
                    'amountPaid' => $amountPaid,
                    'recipientName' => $recipientName,
                    'emailSubject' => $subject,
                ],
                function ($message) use ($recipients, $subject) {
                    $message->to($recipients)
                            ->subject($subject);
                }
            );

            // Clear expiry notification flag so when it expires in future, new email can be sent
            $shop->updateQuietly(['subscription_expiry_notified_at' => null]);

            Log::info("Subscription activated email sent to: " . implode(', ', $recipients));
            return true;
        } catch (\Throwable $e) {
            Log::error("Failed to send subscription activation email to '{$shop->name}': " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send Package Expired email to shop admin.
     */
    public static function sendPackageExpired(Shop $shop): bool
    {
        $recipients = self::getShopRecipients($shop);

        if (empty($recipients)) {
            Log::info("No recipient email found for shop '{$shop->name}' to send expiry email.");
            return false;
        }

        $package = $shop->package ?? new Package(['name' => 'Subscription Plan']);
        $subject = "Action Required: Subscription Expired - {$shop->name}";
        $recipientName = $shop->admins->first()?->name ?? $shop->name;

        try {
            Mail::send(
                'emails.subscription_notification',
                [
                    'shop' => $shop,
                    'package' => $package,
                    'type' => 'expired',
                    'recipientName' => $recipientName,
                    'emailSubject' => $subject,
                ],
                function ($message) use ($recipients, $subject) {
                    $message->to($recipients)
                            ->subject($subject);
                }
            );

            // Mark shop as notified so we don't spam on every request
            $shop->updateQuietly([
                'subscription_expiry_notified_at' => now(),
                'subscription_status' => 'expired',
            ]);

            Log::info("Subscription expired email sent to: " . implode(', ', $recipients));
            return true;
        } catch (\Throwable $e) {
            Log::error("Failed to send subscription expired email to '{$shop->name}': " . $e->getMessage());
            return false;
        }
    }
}

