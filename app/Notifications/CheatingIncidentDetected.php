<?php

namespace App\Notifications;

use App\Models\CheatingIncident;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class CheatingIncidentDetected extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public CheatingIncident $incident)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $userName = $this->incident->user?->name ?? 'Guest';
        $examTitle = $this->incident->exam?->title ?? 'Exam';

        $actionUrl = url("/admin/cheating-incidents/{$this->incident->id}");

        return (new MailMessage)
            ->subject('Cheating Incident Detected')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line("A cheating incident was detected for {$userName} on exam {$examTitle}.")
            ->line('Reason: ' . ($this->incident->reason ?? 'No details provided.'))
            ->action('Review Incident', $actionUrl)
            ->line('Please review and take appropriate action.');
    }

    public function toArray(object $notifiable): array
    {
        $userName = $this->incident->user?->name ?? 'Guest';
        $examTitle = $this->incident->exam?->title ?? 'Ujian';

        $actionUrl = url("/admin/cheating-incidents/{$this->incident->id}");

        return [
            'message' => "{$userName} terdeteksi melakukan kecurangan pada {$examTitle}.",
            'details' => $this->incident->reason
                ? "Alasan: {$this->incident->reason}"
                : null,
            'action_text' => 'Tinjau insiden',
            'action_url' => $actionUrl,
            'icon' => 'fas fa-shield-alt',
            'color' => 'red',
            'incident_id' => $this->incident->id,
            'user_id' => $this->incident->user_id,
            'user_name' => $this->incident->user?->name,
            'exam_id' => $this->incident->exam_id,
            'exam_title' => $this->incident->exam?->title,
            'reason' => $this->incident->reason,
            'status' => $this->incident->status,
            'blocked_at' => optional($this->incident->blocked_at)->toIso8601String(),
        ];
    }
}

