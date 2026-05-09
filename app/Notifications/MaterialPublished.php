<?php

namespace App\Notifications;

use App\Models\Material;
use App\Models\NotificationPreference;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class MaterialPublished extends Notification implements ShouldQueue
{
    use Queueable;

    public $material;

    /**
     * Create a new notification instance.
     */
    public function __construct(Material $material)
    {
        $this->material = $material;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = [];
        $pref = NotificationPreference::getForUser($notifiable->id, 'material_published');
        if ($pref->via_database) {
            $channels[] = 'database';
        }
        if ($pref->via_push && Setting::get('push_notifications_enabled')) {
            $channels[] = 'push';
        }

        return $channels ?: ['database'];
    }

    /**
     * Get the push notification representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toPush(object $notifiable): array
    {
        $data = $this->toArray($notifiable);

        return [
            'title' => 'Materi Baru',
            'body' => 'Materi ' . $this->material->title . ' tersedia di kursus ' . $this->material->course->title,
            'icon' => 'fas fa-book',
            'action_url' => $data['action_url'],
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'material_published',
            'material_id' => $this->material->id,
            'material_title' => $this->material->title,
            'course_id' => $this->material->course_id,
            'course_title' => $this->material->course->title,
            'icon' => 'fas fa-book',
            'color' => 'blue',
            'message' => 'Materi baru "' . $this->material->title . '" telah ditambahkan di kursus "' . $this->material->course->title . '"',
            'action_url' => $notifiable->getNotificationUrl('course', $this->material->course_id),
            'action_text' => 'Lihat Materi',
        ];
    }
}
