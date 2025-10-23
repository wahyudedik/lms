<?php

namespace App\Notifications;

use App\Models\Exam;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ExamScheduled extends Notification
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
        return ['database'];
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
            'action_url' => route('siswa.exams.show', $this->exam),
            'action_text' => 'Lihat Ujian',
        ];
    }
}
