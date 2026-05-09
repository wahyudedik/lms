<?php

namespace App\Notifications;

use App\Models\Certificate;
use App\Models\NotificationPreference;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class CertificateAvailable extends Notification implements ShouldQueue
{
    use Queueable;

    protected string $notificationType = 'certificate_available';

    /**
     * Create a new notification instance.
     */
    public function __construct(public Certificate $certificate)
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
        $courseTitle = $this->certificate->course_title;

        return [
            'type' => 'certificate_available',
            'course_title' => $courseTitle,
            'issue_date' => $this->certificate->issue_date->format('d M Y'),
            'certificate_number' => $this->certificate->certificate_number,
            'action_url' => $notifiable->getNotificationUrl('certificate', $this->certificate->id),
            'icon' => 'fas fa-certificate',
            'color' => 'yellow',
            'message' => 'Sertifikat kursus ' . $courseTitle . ' sudah tersedia',
        ];
    }

    /**
     * Get the push notification payload.
     *
     * @return array<string, string>
     */
    public function toPush(object $notifiable): array
    {
        $courseTitle = $this->certificate->course_title;

        return [
            'title' => 'Sertifikat Tersedia',
            'body' => 'Sertifikat kursus ' . $courseTitle . ' sudah tersedia',
            'icon' => 'fas fa-certificate',
            'action_url' => $notifiable->getNotificationUrl('certificate', $this->certificate->id),
        ];
    }
}
