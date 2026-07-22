<?php

namespace App\Notifications;

use App\Models\CheatingIncident;
use App\Models\NotificationPreference;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CheatingIncidentDetected extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public CheatingIncident $incident)
    {
    }

    public function via(object $notifiable): array
    {
        $channels = ['mail'];
        $pref = NotificationPreference::getForUser($notifiable->id, 'cheating_incident_detected');
        if ($pref->via_database) {
            $channels[] = 'database';
        }
        if ($pref->via_push && Setting::get('push_notifications_enabled')) {
            $channels[] = 'push';
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $userName = $this->incident->user?->name ?? 'Guest';
        $userEmail = $this->incident->user?->email ?? '—';
        $examTitle = $this->incident->exam?->title ?? 'Exam';
        $courseTitle = $this->incident->exam?->course?->title ?? '—';
        $detectedAt = optional($this->incident->blocked_at ?? $this->incident->created_at)->format('d M Y, H:i');
        
        $details = $this->incident->details ?? [];
        $tabSwitches = $details['tab_switches'] ?? 0;
        $maxTabSwitches = $this->incident->exam?->max_tab_switches ?? 0;
        $windowBlurs = $details['window_blurs'] ?? 0;
        $fullscreenExits = $details['fullscreen_exits'] ?? 0;
        $multipleScreens = ($details['multiple_screens'] ?? 0) > 0 ? 'YA' : 'TIDAK';
        $inactivityTriggers = $details['inactivity_triggers'] ?? 0;
        $keyBlocks = $details['key_blocks'] ?? 0;

        $actionUrl = url("/admin/cheating-incidents/{$this->incident->id}");

        $status = $this->incident->status === 'blocked' ? 'BLOCKED (Akses login ditangguhkan)' : 'REVIEWING (Dalam peninjauan)';

        return (new MailMessage)
            ->subject('[PERINGATAN KECURANGAN] Insiden Baru Terdeteksi - ' . $userName)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line("Siswa/Mahasiswa: {$userName} ({$userEmail})")
            ->line("Ujian: {$examTitle} (Kursus: {$courseTitle})")
            ->line("Waktu: {$detectedAt}")
            ->line("Status Akun: {$status}")
            ->line("Alasan: " . ($this->incident->reason ?? '—'))
            ->line("Rincian Kecurangan:")
            ->line("- Pindah Tab: {$tabSwitches}x (Maksimal: {$maxTabSwitches})")
            ->line("- Keluar Aplikasi/Fokus: {$windowBlurs}x")
            ->line("- Keluar Layar Penuh: {$fullscreenExits}x")
            ->line("- Layar Ganda Terdeteksi: {$multipleScreens}")
            ->line("- Diam/Inaktif: {$inactivityTriggers}x")
            ->line("- Keyboard & Copy Block: {$keyBlocks}x")
            ->action('Review Incident', $actionUrl)
            ->line('Silakan tinjau insiden dan lakukan tindakan yang diperlukan.');
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
            'title' => 'Insiden Kecurangan Terdeteksi',
            'body' => ($this->incident->user?->name ?? 'Guest') . ' terdeteksi kecurangan pada ' . ($this->incident->exam?->title ?? 'Ujian'),
            'icon' => 'fas fa-shield-alt',
            'action_url' => $data['action_url'],
        ];
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
