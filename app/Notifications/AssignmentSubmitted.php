<?php

namespace App\Notifications;

use App\Models\Assignment;
use App\Models\NotificationPreference;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class AssignmentSubmitted extends Notification implements ShouldQueue
{
    use Queueable;

    public $assignment;

    public $student;

    /**
     * Create a new notification instance.
     */
    public function __construct(Assignment $assignment, User $student)
    {
        $this->assignment = $assignment;
        $this->student = $student;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = [];
        $pref = NotificationPreference::getForUser($notifiable->id, 'assignment_submitted');
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
            'title' => 'Tugas Dikumpulkan',
            'body' => $this->student->name . ' mengumpulkan tugas "' . $this->assignment->title . '" di kursus "' . $this->assignment->course->title . '"',
            'icon' => 'fas fa-upload',
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
        $prefix = $notifiable->getRolePrefix();

        return [
            'type' => 'assignment_submitted',
            'assignment_id' => $this->assignment->id,
            'assignment_title' => $this->assignment->title,
            'course_id' => $this->assignment->course_id,
            'course_title' => $this->assignment->course->title,
            'student_id' => $this->student->id,
            'student_name' => $this->student->name,
            'icon' => 'fas fa-upload',
            'color' => 'green',
            'message' => $this->student->name . ' telah mengumpulkan tugas "' . $this->assignment->title . '" di kursus "' . $this->assignment->course->title . '"',
            'action_url' => route("{$prefix}.assignments.submissions.index", $this->assignment->id),
            'action_text' => 'Lihat Pengumpulan',
        ];
    }
}
