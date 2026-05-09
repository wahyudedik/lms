<?php

namespace App\Notifications;

use App\Models\Enrollment;
use App\Models\NotificationPreference;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class EnrollmentCreated extends Notification implements ShouldQueue
{
    use Queueable;

    protected string $notificationType = 'enrollment_created';

    /**
     * Create a new notification instance.
     */
    public function __construct(public Enrollment $enrollment)
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
        $studentName = $this->enrollment->user->name;
        $courseTitle = $this->enrollment->course->title;
        $prefix = $notifiable->getRolePrefix();

        try {
            $actionUrl = route("{$prefix}.courses.show", $this->enrollment->course_id);
        } catch (\Exception $e) {
            $actionUrl = url("/{$prefix}/courses/" . $this->enrollment->course_id);
        }

        return [
            'type' => 'enrollment_created',
            'student_name' => $studentName,
            'course_title' => $courseTitle,
            'enrolled_at' => $this->enrollment->enrolled_at->format('d M Y'),
            'action_url' => $actionUrl,
            'icon' => 'fas fa-user-plus',
            'color' => 'green',
            'message' => $studentName . ' mendaftar ke kursus: ' . $courseTitle,
        ];
    }

    /**
     * Get the push notification payload.
     *
     * @return array<string, string>
     */
    public function toPush(object $notifiable): array
    {
        $studentName = $this->enrollment->user->name;
        $courseTitle = $this->enrollment->course->title;
        $prefix = $notifiable->getRolePrefix();

        try {
            $actionUrl = route("{$prefix}.courses.show", $this->enrollment->course_id);
        } catch (\Exception $e) {
            $actionUrl = url("/{$prefix}/courses/" . $this->enrollment->course_id);
        }

        return [
            'title' => 'Siswa Baru Mendaftar',
            'body' => $studentName . ' mendaftar ke kursus: ' . $courseTitle,
            'icon' => 'fas fa-user-plus',
            'action_url' => $actionUrl,
        ];
    }
}
