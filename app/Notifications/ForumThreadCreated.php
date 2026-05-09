<?php

namespace App\Notifications;

use App\Models\ForumThread;
use App\Models\NotificationPreference;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class ForumThreadCreated extends Notification implements ShouldQueue
{
    use Queueable;

    protected string $notificationType = 'forum_thread_created';

    /**
     * Create a new notification instance.
     */
    public function __construct(public ForumThread $thread)
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
        $creatorName = $this->thread->user->name;
        $threadTitle = $this->thread->title;

        try {
            $actionUrl = route('forum.thread', [
                $this->thread->category->slug,
                $this->thread->slug,
            ]);
        } catch (\Exception $e) {
            $actionUrl = url('/forum');
        }

        return [
            'type' => 'forum_thread_created',
            'thread_id' => $this->thread->id,
            'thread_title' => $threadTitle,
            'category_name' => $this->thread->category->name,
            'creator_name' => $creatorName,
            'content_preview' => Str::limit($this->thread->content, 100),
            'action_url' => $actionUrl,
            'icon' => 'fas fa-comments',
            'color' => 'teal',
            'message' => $creatorName . ' membuat thread: ' . $threadTitle,
        ];
    }

    /**
     * Get the push notification payload.
     *
     * @return array<string, string>
     */
    public function toPush(object $notifiable): array
    {
        $creatorName = $this->thread->user->name;
        $threadTitle = $this->thread->title;

        try {
            $actionUrl = route('forum.thread', [
                $this->thread->category->slug,
                $this->thread->slug,
            ]);
        } catch (\Exception $e) {
            $actionUrl = url('/forum');
        }

        return [
            'title' => 'Thread Forum Baru',
            'body' => $creatorName . ' membuat thread: ' . $threadTitle,
            'icon' => 'fas fa-comments',
            'action_url' => $actionUrl,
        ];
    }
}
