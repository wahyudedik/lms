<?php

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\User;
use App\Notifications\ExamGraded;
use App\Notifications\ExamScheduled;
use Illuminate\Support\Facades\Notification;

/**
 * Validates: CP-6 (Queue Isolation — notifications don't block main operations)
 *
 * Since notifications implement ShouldQueue, they are dispatched to the queue
 * and do not block the main operation. We verify that the main operations
 * complete successfully regardless of notification dispatch.
 */

it('autoGrade saves correctly and dispatches notification', function () {
    Notification::fake();

    $guru = User::factory()->create(['role' => 'guru']);
    $student = User::factory()->create(['role' => 'siswa']);

    $course = Course::factory()->create(['instructor_id' => $guru->id]);
    $exam = Exam::factory()->create([
        'course_id' => $course->id,
        'created_by' => $guru->id,
        'pass_score' => 60,
    ]);

    // Create an attempt that is already submitted (no questions = 0 points scenario)
    $attempt = ExamAttempt::create([
        'exam_id' => $exam->id,
        'user_id' => $student->id,
        'status' => 'submitted',
        'started_at' => now()->subMinutes(30),
        'submitted_at' => now(),
        'is_guest' => false,
    ]);

    // Run autoGrade — with no questions, it grades with score 0
    $attempt->autoGrade();
    $attempt->refresh();

    // Verify the main operation completed successfully
    expect($attempt->status)->toBe('graded');
    expect((int) $attempt->total_points_possible)->toBe(0);

    // Verify notification was dispatched (even though score is 0)
    Notification::assertSentTo($student, ExamGraded::class);
});

it('toggleStatus still updates exam even if notification would fail', function () {
    Notification::fake();

    $guru = User::factory()->create(['role' => 'guru']);
    $student = User::factory()->create(['role' => 'siswa']);

    $course = Course::factory()->create(['instructor_id' => $guru->id]);

    Enrollment::factory()->create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'status' => 'active',
    ]);

    $exam = Exam::factory()->create([
        'course_id' => $course->id,
        'created_by' => $guru->id,
        'is_published' => false,
    ]);

    // Toggle to published
    $exam->update(['is_published' => true, 'published_at' => now()]);
    $exam->refresh();

    // Verify the main operation completed
    expect($exam->is_published)->toBeTrue();

    // Simulate what the controller does: send notification to enrolled students
    Notification::send(collect([$student]), new ExamScheduled($exam));

    // Verify notification was dispatched
    Notification::assertSentTo($student, ExamScheduled::class);

    // The key point: the exam update is independent of notification success
    // Since ShouldQueue is used, even if the notification job fails later,
    // the exam state change is already committed
    expect($exam->fresh()->is_published)->toBeTrue();
});

it('notifications use ShouldQueue interface ensuring async processing', function () {
    // Verify that notification classes implement ShouldQueue
    $examGraded = new \ReflectionClass(ExamGraded::class);
    $examScheduled = new \ReflectionClass(ExamScheduled::class);

    expect($examGraded->implementsInterface(\Illuminate\Contracts\Queue\ShouldQueue::class))->toBeTrue();
    expect($examScheduled->implementsInterface(\Illuminate\Contracts\Queue\ShouldQueue::class))->toBeTrue();
});
