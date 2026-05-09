<?php

namespace App\Notifications;

use App\Models\Assignment;
use App\Models\NotificationPreference;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class AssignmentDeadlineReminder extends Notification implements ShouldQueue
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
        $pref = NotificationPreference::getForUser($notifiable->id, 'assignment_deadline_reminder');
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
        $remainingHours = $this->getRemainingHours();

        return [
            'title' => 'Pengingat Deadline Tugas',
            'body' => 'Tugas "' . $this->assignment->title . '" di kursus "' . $this->assignment->course->title . '" harus dikumpulkan dalam ' . $remainingHours . ' jam',
            'icon' => 'fas fa-clock',
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
        $remainingHours = $this->getRemainingHours();

        return [
            'type' => 'assignment_deadline_reminder',
            'assignment_id' => $this->assignment->id,
            'assignment_title' => $this->assignment->title,
            'course_id' => $this->assignment->course_id,
            'course_title' => $this->assignment->course->title,
            'deadline' => $this->assignment->deadline->toIso8601String(),
            'remaining_hours' => $remainingHours,
            'icon' => 'fas fa-clock',
            'color' => 'orange',
            'message' => 'Tugas "' . $this->assignment->title . '" di kursus "' . $this->assignment->course->title . '" harus dikumpulkan dalam ' . $remainingHours . ' jam',
            'action_url' => $notifiable->getNotificationUrl('assignment', $this->assignment->id),
            'action_text' => 'Lihat Tugas',
        ];
    }

    /**
     * Get the remaining hours until deadline.
     */
    private function getRemainingHours(): int
    {
        $remaining = now()->diffInHours($this->assignment->deadline, false);

        return max(0, (int) $remaining);
    }
}
