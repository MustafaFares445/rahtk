<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\FcmToken;
use App\Models\FcmRecipient;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NewProductNotification;

class ProductObserver
{
    /**
     * Handle the Product "created" event.
     */
    public function created(Product $product): void
    {
        $this->sendNewProductNotification($product);
    }

    /**
     * Send notification to all FCM tokens
     */
    private function sendNewProductNotification(Product $product): void
    {
        try {
            $tokens = FcmToken::query()->pluck('token')->toArray();

            if (empty($tokens)) {
                return;
            }

            // Create notification recipients for each token
            $recipients = collect($tokens)->map(function ($token) {
                return new FcmRecipient($token);
            });

            // Send notification to all recipients
            Notification::send($recipients, new NewProductNotification($product));

        } catch (\Exception $e) {
            Log::error("Failed to send new product notification: " . $e->getMessage());
        }
    }
}
