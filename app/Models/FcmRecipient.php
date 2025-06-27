<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;

class FcmRecipient
{
    use Notifiable;

    protected $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Route notifications for the FCM channel.
     */
    public function routeNotificationForFcm()
    {
        return $this->token;
    }

    /**
     * Get the notification routing information for the given driver.
     */
    public function getKey()
    {
        return $this->token;
    }
}