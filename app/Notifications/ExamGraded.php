<?php

namespace App\Notifications;

use App\Models\ExamAttempt;
use App\Models\NotificationPreference;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ExamGraded extends Notification implements ShouldQueue
{
    use Queueable;

    public $attempt;

    /**
     * Create a new notification instance.
     */
    public function __construct(ExamAttempt $attempt)
    {
        $this->attempt = $attempt;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = [];
        $pref = NotificationPreference::getForUser($notifiable->id, 'exam_graded');
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
        $passed = $this->attempt->passed;
        $data = $this->toArray($notifiable);

        return [
            'title' => 'Nilai Ujian Keluar',
            'body' => 'Nilai ujian ' . $this->attempt->exam->title . ': ' . number_format($this->attempt->score, 2) . '% - ' . ($passed ? 'LULUS' : 'TIDAK LULUS'),
            'icon' => 'fas fa-check-circle',
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
        $passed = $this->attempt->passed;

        return [
            'type' => 'exam_graded',
            'attempt_id' => $this->attempt->id,
            'exam_id' => $this->attempt->exam_id,
            'exam_title' => $this->attempt->exam->title,
            'course_id' => $this->attempt->exam->course_id,
            'course_title' => $this->attempt->exam->course->title,
            'score' => number_format($this->attempt->score, 2),
            'passed' => $passed,
            'icon' => $passed ? 'fas fa-check-circle' : 'fas fa-times-circle',
            'color' => $passed ? 'green' : 'red',
            'message' => 'Nilai ujian "' . $this->attempt->exam->title . '" sudah keluar: ' . number_format($this->attempt->score, 2) . '% - ' . ($passed ? 'LULUS' : 'TIDAK LULUS'),
            'action_url' => $notifiable->getNotificationUrl('exam', $this->attempt->exam_id),
            'action_text' => 'Lihat Hasil',
        ];
    }
}
