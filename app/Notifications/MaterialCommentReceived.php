<?php

namespace App\Notifications;

use App\Models\MaterialComment;
use App\Models\NotificationPreference;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class MaterialCommentReceived extends Notification implements ShouldQueue
{
    use Queueable;

    protected string $notificationType = 'material_comment';

    /**
     * Create a new notification instance.
     */
    public function __construct(public MaterialComment $comment)
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
        $commenterName = $this->comment->user->name;
        $materialTitle = $this->comment->material->title;
        $prefix = $notifiable->getRolePrefix();

        try {
            $actionUrl = route("{$prefix}.materials.show", $this->comment->material_id);
        } catch (\Exception $e) {
            $actionUrl = url("/{$prefix}/materials/" . $this->comment->material_id);
        }

        return [
            'type' => 'material_comment',
            'comment_id' => $this->comment->id,
            'material_id' => $this->comment->material_id,
            'material_title' => $materialTitle,
            'commenter_name' => $commenterName,
            'content_preview' => Str::limit($this->comment->comment, 100),
            'action_url' => $actionUrl,
            'icon' => 'fas fa-comment',
            'color' => 'indigo',
            'message' => $commenterName . ' mengomentari: ' . $materialTitle,
        ];
    }

    /**
     * Get the push notification payload.
     *
     * @return array<string, string>
     */
    public function toPush(object $notifiable): array
    {
        $commenterName = $this->comment->user->name;
        $materialTitle = $this->comment->material->title;
        $prefix = $notifiable->getRolePrefix();

        try {
            $actionUrl = route("{$prefix}.materials.show", $this->comment->material_id);
        } catch (\Exception $e) {
            $actionUrl = url("/{$prefix}/materials/" . $this->comment->material_id);
        }

        return [
            'title' => 'Komentar Materi Baru',
            'body' => $commenterName . ' mengomentari: ' . $materialTitle,
            'icon' => 'fas fa-comment',
            'action_url' => $actionUrl,
        ];
    }
}
