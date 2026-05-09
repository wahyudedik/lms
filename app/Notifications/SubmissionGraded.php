<?php

namespace App\Notifications;

use App\Models\AssignmentSubmission;
use App\Models\NotificationPreference;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class SubmissionGraded extends Notification implements ShouldQueue
{
    use Queueable;

    public $submission;

    /**
     * Create a new notification instance.
     */
    public function __construct(AssignmentSubmission $submission)
    {
        $this->submission = $submission;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = [];
        $pref = NotificationPreference::getForUser($notifiable->id, 'submission_graded');
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
            'title' => 'Tugas Dinilai',
            'body' => 'Tugas "' . $this->submission->assignment->title . '" telah dinilai. Nilai: ' . $this->submission->score . '/' . $this->submission->assignment->max_score,
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
        $feedbackPreview = $this->submission->feedback
            ? Str::limit($this->submission->feedback, 100)
            : null;

        $prefix = $notifiable->getRolePrefix();

        return [
            'type' => 'submission_graded',
            'assignment_id' => $this->submission->assignment_id,
            'assignment_title' => $this->submission->assignment->title,
            'submission_id' => $this->submission->id,
            'score' => $this->submission->score,
            'max_score' => $this->submission->assignment->max_score,
            'final_score' => $this->submission->final_score,
            'feedback_preview' => $feedbackPreview,
            'icon' => 'fas fa-check-circle',
            'color' => 'green',
            'message' => 'Tugas "' . $this->submission->assignment->title . '" telah dinilai. Nilai: ' . $this->submission->score . '/' . $this->submission->assignment->max_score,
            'action_url' => route("{$prefix}.assignments.show", $this->submission->assignment_id),
            'action_text' => 'Lihat Nilai',
        ];
    }
}
