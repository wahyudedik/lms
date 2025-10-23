<?php

namespace App\Notifications;

use App\Models\ExamAttempt;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ExamGraded extends Notification
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
        return ['database'];
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
            'action_url' => route('siswa.exams.review-attempt', $this->attempt),
            'action_text' => 'Lihat Hasil',
        ];
    }
}
