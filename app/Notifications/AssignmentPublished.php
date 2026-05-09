<?php

namespace App\Notifications;

use App\Models\Assignment;
use App\Models\NotificationPreference;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class AssignmentPublished extends Notification implements ShouldQueue
{
    use Queueable;

    public $assignment;

    /**
     * Create a new notification instance.
     */
    public function __construct(Assignment $assignment)
    {
        $this->assignment = $assignment;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = [];
        $pref = NotificationPreference::getForUser($notifiable->id, 'assignment_published');
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
            'title' => 'Tugas Baru',
            'body' => 'Tugas "' . $this->assignment->title . '" telah dipublikasikan di kursus "' . $this->assignment->course->title . '"',
            'icon' => 'fas fa-tasks',
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
            'type' => 'assignment_published',
            'assignment_id' => $this->assignment->id,
            'assignment_title' => $this->assignment->title,
            'course_id' => $this->assignment->course_id,
            'course_title' => $this->assignment->course->title,
            'icon' => 'fas fa-tasks',
            'color' => 'blue',
            'message' => 'Tugas baru "' . $this->assignment->title . '" telah dipublikasikan di kursus "' . $this->assignment->course->title . '"',
            'action_url' => $notifiable->getNotificationUrl('assignment', $this->assignment->id),
            'action_text' => 'Lihat Tugas',
        ];
    }
}
