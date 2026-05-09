<?php

namespace App\Notifications;

use App\Models\ForumReply;
use App\Models\NotificationPreference;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class ForumReplyReceived extends Notification implements ShouldQueue
{
    use Queueable;

    protected string $notificationType = 'forum_reply';

    /**
     * Create a new notification instance.
     */
    public function __construct(public ForumReply $reply)
    {
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
        $replierName = $this->reply->user->name;
        $threadTitle = $this->reply->thread->title;

        try {
            $actionUrl = route('forum.thread', [
                $this->reply->thread->category->slug,
                $this->reply->thread->slug,
            ]);
        } catch (\Exception $e) {
            $actionUrl = url('/forum');
        }

        return [
            'type' => 'forum_reply',
            'reply_id' => $this->reply->id,
            'thread_id' => $this->reply->thread_id,
            'thread_title' => $threadTitle,
            'replier_name' => $replierName,
            'content_preview' => Str::limit($this->reply->content, 100),
            'action_url' => $actionUrl,
            'icon' => 'fas fa-comments',
            'color' => 'blue',
            'message' => $replierName . ' membalas thread: ' . $threadTitle,
        ];
    }

    /**
     * Get the push notification payload.
     *
     * @return array<string, string>
     */
    public function toPush(object $notifiable): array
    {
        $replierName = $this->reply->user->name;
        $threadTitle = $this->reply->thread->title;

        try {
            $actionUrl = route('forum.thread', [
                $this->reply->thread->category->slug,
                $this->reply->thread->slug,
            ]);
        } catch (\Exception $e) {
            $actionUrl = url('/forum');
        }

        return [
            'title' => 'Balasan Forum Baru',
            'body' => $replierName . ' membalas thread: ' . $threadTitle,
            'icon' => 'fas fa-comments',
            'action_url' => $actionUrl,
        ];
    }
}
