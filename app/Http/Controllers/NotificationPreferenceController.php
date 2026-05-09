<?php

namespace App\Http\Controllers;

use App\Models\NotificationPreference;
use Illuminate\Http\Request;

class NotificationPreferenceController extends Controller
{
    public function index()
    {
        $preferences = NotificationPreference::where('user_id', auth()->id())->get()
            ->keyBy('notification_type');

        $types = [
            'exam_graded' => 'Nilai Ujian',
            'exam_scheduled' => 'Ujian Dijadwalkan',
            'material_published' => 'Materi Dipublikasikan',
            'forum_reply' => 'Balasan Forum',
            'material_comment' => 'Komentar Materi',
            'user_mentioned' => 'Mention/Tag',
            'enrollment_created' => 'Pendaftaran Baru',
            'certificate_available' => 'Sertifikat Tersedia',
            'cheating_incident_detected' => 'Insiden Kecurangan',
            'forum_thread_created' => 'Thread Forum Baru',
        ];

        return view('notifications.preferences', compact('preferences', 'types'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'preferences' => 'required|array',
            'preferences.*.via_database' => 'boolean',
            'preferences.*.via_push' => 'boolean',
        ]);

        foreach ($validated['preferences'] as $type => $prefs) {
            NotificationPreference::updateOrCreate(
                ['user_id' => auth()->id(), 'notification_type' => $type],
                [
                    'via_database' => $prefs['via_database'] ?? true,
                    'via_push' => $prefs['via_push'] ?? true,
                ]
            );
        }

        return redirect()->back()->with('success', 'Preferensi notifikasi berhasil disimpan!');
    }
}
