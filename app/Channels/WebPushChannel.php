<?php

namespace App\Channels;

use App\Services\PushNotificationService;
use Illuminate\Notifications\Notification;

class WebPushChannel
{
    public function __construct(private PushNotificationService $pushService) {}

    public function send(mixed $notifiable, Notification $notification): void
    {
        if (!method_exists($notification, 'toPush')) {
            return;
        }

        $payload = $notification->toPush($notifiable);
        $this->pushService->sendToUser($notifiable, $payload);
    }
}
