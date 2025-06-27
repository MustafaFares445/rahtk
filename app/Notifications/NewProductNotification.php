<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class NewProductNotification extends Notification
{
    use Queueable;

    protected $product;

    public function __construct($product)
    {
        $this->product = $product;
        $this->onQueue('notifications'); // Use a dedicated queue
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return [FcmChannel::class];
    }

    /**
     * Get the FCM representation of the notification.
     */
    public function toFcm($notifiable): FcmMessage
    {
        return (new FcmMessage(notification: new FcmNotification(
                title: 'New Product Available!',
                body: "Check out our new product: {$this->product->name}",
                image: $this->product->getFirstMediaUrl('images') ?? "", // optional product image
            )))
            ->data([
                'productId' => (string) $this->product->id,
                'productTitle' => $this->product->title,
                'action' => 'new_product',
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK', // for Flutter apps
            ])
            ->custom([
                'android' => [
                    'notification' => [
                        'color' => '#FF6B35',
                        'sound' => 'default',
                        'channel_id' => 'new_products', // make sure this channel exists in your app
                    ],
                    'fcm_options' => [
                        'analytics_label' => 'new_product',
                    ],
                ],
                'apns' => [
                    'payload' => [
                        'aps' => [
                            'sound' => 'default'
                        ],
                    ],
                    'fcm_options' => [
                        'analytics_label' => 'new_product',
                    ],
                ],
            ]);
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'productId' => (string) $this->product->id,
            'productTitle' => (string) $this->product->title,
            'message' => "New product available: {$this->product->name}",
        ];
    }
}