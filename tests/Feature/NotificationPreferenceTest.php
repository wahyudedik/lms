<?php

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\NotificationPreference;
use App\Models\Setting;
use App\Models\User;
use App\Notifications\ExamGraded;
use App\Services\PushNotificationService;
use Illuminate\Support\Facades\Notification;

/**
 * Validates: CP-5 (Preference Respect)
 */

it('via_database=false means notification is NOT stored in notifications table', function () {
    $student = User::factory()->create(['role' => 'siswa']);

    // Enable push globally so the push channel is available as alternative
    Setting::set('push_notifications_enabled', '1', 'boolean', 'notification');

    // Set preference to disable database channel but enable push
    NotificationPreference::create([
        'user_id' => $student->id,
        'notification_type' => 'exam_graded',
        'via_database' => false,
        'via_push' => true,
    ]);

    $guru = User::factory()->create(['role' => 'guru']);
    $course = Course::factory()->create(['instructor_id' => $guru->id]);
    $exam = Exam::factory()->create(['course_id' => $course->id, 'created_by' => $guru->id]);

    $attempt = ExamAttempt::create([
        'exam_id' => $exam->id,
        'user_id' => $student->id,
        'status' => 'graded',
        'score' => 85,
        'passed' => true,
        'is_guest' => false,
        'started_at' => now()->subHour(),
        'submitted_at' => now(),
    ]);

    // Send notification directly (sync queue in testing)
    $student->notify(new ExamGraded($attempt));

    // Verify no notification stored in database (push channel was used instead)
    expect($student->notifications()->count())->toBe(0);
});

it('via_push=false means push is not sent', function () {
    Notification::fake();

    $student = User::factory()->create(['role' => 'siswa']);

    // Enable push globally
    Setting::set('push_notifications_enabled', '1', 'boolean', 'notification');

    // Set preference to disable push channel
    NotificationPreference::create([
        'user_id' => $student->id,
        'notification_type' => 'exam_graded',
        'via_database' => true,
        'via_push' => false,
    ]);

    $guru = User::factory()->create(['role' => 'guru']);
    $course = Course::factory()->create(['instructor_id' => $guru->id]);
    $exam = Exam::factory()->create(['course_id' => $course->id, 'created_by' => $guru->id]);

    $attempt = ExamAttempt::create([
        'exam_id' => $exam->id,
        'user_id' => $student->id,
        'status' => 'graded',
        'score' => 85,
        'passed' => true,
        'is_guest' => false,
        'started_at' => now()->subHour(),
        'submitted_at' => now(),
    ]);

    $student->notify(new ExamGraded($attempt));

    // Verify notification was sent via database channel but not push
    Notification::assertSentTo($student, ExamGraded::class, function ($notification, $channels) {
        return in_array('database', $channels) && !in_array('push', $channels);
    });
});

it('default preference means both via_database=true and via_push=true when no record exists', function () {
    $student = User::factory()->create(['role' => 'siswa']);

    // No preference record exists — should use defaults
    $pref = NotificationPreference::getForUser($student->id, 'exam_graded');

    expect($pref->via_database)->toBeTrue();
    expect($pref->via_push)->toBeTrue();
});
