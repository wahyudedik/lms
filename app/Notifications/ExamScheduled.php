<?php

namespace App\Notifications;

use App\Models\Exam;
use App\Models\NotificationPreference;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ExamScheduled extends Notification implements ShouldQueue
{
    use Queueable;

    public $exam;

    /**
     * Create a new notification instance.
     */
    public function __construct(Exam $exam)
    {
        $this->exam = $exam;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = [];
        $pref = NotificationPreference::getForUser($notifiable->id, 'exam_scheduled');
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
            'title' => 'Ujian Baru Tersedia',
            'body' => 'Ujian ' . $this->exam->title . ' tersedia di kursus ' . $this->exam->course->title,
            'icon' => 'fas fa-clipboard-list',
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
            'type' => 'exam_scheduled',
            'exam_id' => $this->exam->id,
            'exam_title' => $this->exam->title,
            'course_id' => $this->exam->course_id,
            'course_title' => $this->exam->course->title,
            'start_time' => $this->exam->start_time?->format('d M Y, H:i'),
            'end_time' => $this->exam->end_time?->format('d M Y, H:i'),
            'icon' => 'fas fa-clipboard-list',
            'color' => 'green',
            'message' => 'Ujian "' . $this->exam->title . '" tersedia di kursus "' . $this->exam->course->title . '"',
            'action_url' => $notifiable->getNotificationUrl('exam', $this->exam->id),
            'action_text' => 'Lihat Ujian',
        ];
    }
}
