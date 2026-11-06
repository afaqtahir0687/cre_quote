<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class LowStockAlertService
{
    /**
     * Send low stock notification email for a product.
     */
    public static function sendAlert(Product $product): bool
    {
        try {
            $product->loadMissing(['shop.owner', 'shop.admins', 'category', 'unit']);
            $shop = $product->shop;

            $recipients = collect();
            if ($shop) {
                if (!empty($shop->email)) {
                    $recipients->push($shop->email);
                }
                if ($shop->owner && !empty($shop->owner->email)) {
                    $recipients->push($shop->owner->email);
                }
                foreach ($shop->admins as $admin) {
                    if (!empty($admin->email)) {
                        $recipients->push($admin->email);
                    }
                }
            }

            // Fallback to setting email or config mail
            if ($recipients->isEmpty()) {
                $setting = current_setting();
                if ($setting && !empty($setting->company_email)) {
                    $recipients->push($setting->company_email);
                } else {
                    $fallback = config('mail.from.address');
                    if ($fallback) {
                        $recipients->push($fallback);
                    }
                }
            }

            $recipients = $recipients->unique()->filter();
            if ($recipients->isEmpty()) {
                Log::warning("Low stock alert for Product #{$product->id} skipped: No recipient email found.");
                return false;
            }

            $subject = "⚠️ Low Stock Alert (30% Remaining): " . $product->name . " [" . ($product->sku ?: 'No SKU') . "]";

            Mail::send(
                'emails.low_stock_alert',
                [
                    'product' => $product,
                    'shop' => $shop,
                    'subject' => $subject,
                ],
                function ($message) use ($recipients, $subject) {
                    $message->to($recipients->toArray())
                            ->subject($subject);
                }
            );

            // Mark notified
            $product->updateQuietly(['low_stock_notified_at' => now()]);

            Log::info("Low stock alert email sent for Product #{$product->id} to: " . implode(', ', $recipients->toArray()));
            return true;
        } catch (\Throwable $e) {
            Log::error("Failed to send low stock alert for Product #{$product->id}: " . $e->getMessage());
            return false;
        }
    }
}

