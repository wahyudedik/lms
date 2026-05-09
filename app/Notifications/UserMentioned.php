<?php

namespace App\Notifications;

use App\Models\NotificationPreference;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class UserMentioned extends Notification implements ShouldQueue
{
    use Queueable;

    protected string $notificationType = 'user_mentioned';

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public User $mentioner,
        public string $context,
        public string $sourceTitle,
        public string $contentPreview,
        public string $actionUrl,
    ) {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = [];
        $pref = NotificationPreference::getForUser($notifiable->id, $this->notificationType);

        if ($pref->via_database) {
            $channels[] = 'database';
        }

        if ($pref->via_push && Setting::get('push_notifications_enabled')) {
            $channels[] = 'push';
        }

        return $channels ?: ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $mentionerName = $this->mentioner->name;

        return [
            'type' => 'user_mentioned',
            'mentioner_name' => $mentionerName,
            'context' => $this->context,
            'source_title' => $this->sourceTitle,
            'content_preview' => $this->contentPreview,
            'action_url' => $this->actionUrl,
            'icon' => 'fas fa-at',
            'color' => 'purple',
            'message' => $mentionerName . ' menyebut Anda di: ' . $this->sourceTitle,
        ];
    }

    /**
     * Get the push notification payload.
     *
     * @return array<string, string>
     */
    public function toPush(object $notifiable): array
    {
        return [
            'title' => 'Anda Disebutkan',
            'body' => $this->mentioner->name . ' menyebut Anda di: ' . $this->sourceTitle,
            'icon' => 'fas fa-at',
            'action_url' => $this->actionUrl,
        ];
    }
}
