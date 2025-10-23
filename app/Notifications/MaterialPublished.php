<?php

namespace App\Notifications;

use App\Models\Material;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MaterialPublished extends Notification
{
    use Queueable;

    public $material;

    /**
     * Create a new notification instance.
     */
    public function __construct(Material $material)
    {
        $this->material = $material;
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
            'type' => 'material_published',
            'material_id' => $this->material->id,
            'material_title' => $this->material->title,
            'course_id' => $this->material->course_id,
            'course_title' => $this->material->course->title,
            'icon' => 'fas fa-book',
            'color' => 'blue',
            'message' => 'Materi baru "' . $this->material->title . '" telah ditambahkan di kursus "' . $this->material->course->title . '"',
            'action_url' => route('siswa.courses.show', $this->material->course_id),
            'action_text' => 'Lihat Materi',
        ];
    }
}
