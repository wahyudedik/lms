<?php

use App\Models\Certificate;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\User;
use App\Notifications\CertificateAvailable;
use App\Notifications\EnrollmentCreated;
use App\Notifications\ExamGraded;
use App\Notifications\ExamScheduled;
use App\Notifications\MaterialPublished;
use Illuminate\Support\Facades\Notification;

/**
 * Validates: CP-1 (Notification Isolation) & CP-2 (Self-Notification Prevention)
 */

it('ExamGraded is only sent to the attempt owner', function () {
    Notification::fake();

    $guru = User::factory()->create(['role' => 'guru']);
    $admin = User::factory()->create(['role' => 'admin']);
    $student = User::factory()->create(['role' => 'siswa']);
    $otherStudent = User::factory()->create(['role' => 'siswa']);

    $course = Course::factory()->create(['instructor_id' => $guru->id]);
    $exam = Exam::factory()->create(['course_id' => $course->id, 'created_by' => $guru->id]);

    // Enroll both students
    Enrollment::factory()->create(['user_id' => $student->id, 'course_id' => $course->id, 'status' => 'active']);
    Enrollment::factory()->create(['user_id' => $otherStudent->id, 'course_id' => $course->id, 'status' => 'active']);

    // Send notification only to the attempt owner
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

    Notification::assertSentTo($student, ExamGraded::class);
    Notification::assertNotSentTo($otherStudent, ExamGraded::class);
    Notification::assertNotSentTo($guru, ExamGraded::class);
    Notification::assertNotSentTo($admin, ExamGraded::class);
});

it('ExamScheduled is only sent to enrolled active students', function () {
    Notification::fake();

    $guru = User::factory()->create(['role' => 'guru']);
    $admin = User::factory()->create(['role' => 'admin']);
    $enrolledStudent = User::factory()->create(['role' => 'siswa']);
    $notEnrolledStudent = User::factory()->create(['role' => 'siswa']);

    $course = Course::factory()->create(['instructor_id' => $guru->id]);
    $exam = Exam::factory()->create(['course_id' => $course->id, 'created_by' => $guru->id, 'is_published' => true]);

    Enrollment::factory()->create(['user_id' => $enrolledStudent->id, 'course_id' => $course->id, 'status' => 'active']);

    // Send to enrolled students only
    $enrolledStudent->notify(new ExamScheduled($exam));

    Notification::assertSentTo($enrolledStudent, ExamScheduled::class);
    Notification::assertNotSentTo($notEnrolledStudent, ExamScheduled::class);
    Notification::assertNotSentTo($guru, ExamScheduled::class);
    Notification::assertNotSentTo($admin, ExamScheduled::class);
});

it('MaterialPublished is only sent to enrolled active students', function () {
    Notification::fake();

    $guru = User::factory()->create(['role' => 'guru']);
    $admin = User::factory()->create(['role' => 'admin']);
    $enrolledStudent = User::factory()->create(['role' => 'siswa']);
    $notEnrolledStudent = User::factory()->create(['role' => 'siswa']);

    $course = Course::factory()->create(['instructor_id' => $guru->id]);

    Enrollment::factory()->create(['user_id' => $enrolledStudent->id, 'course_id' => $course->id, 'status' => 'active']);

    $material = \App\Models\Material::create([
        'course_id' => $course->id,
        'title' => 'Test Material',
        'type' => 'file',
        'is_published' => true,
        'created_by' => $guru->id,
        'order' => 1,
    ]);

    // Send to enrolled students only
    $enrolledStudent->notify(new MaterialPublished($material));

    Notification::assertSentTo($enrolledStudent, MaterialPublished::class);
    Notification::assertNotSentTo($notEnrolledStudent, MaterialPublished::class);
    Notification::assertNotSentTo($guru, MaterialPublished::class);
    Notification::assertNotSentTo($admin, MaterialPublished::class);
});

it('EnrollmentCreated is only sent to the course instructor', function () {
    Notification::fake();

    $guru = User::factory()->create(['role' => 'guru']);
    $admin = User::factory()->create(['role' => 'admin']);
    $student = User::factory()->create(['role' => 'siswa']);
    $otherGuru = User::factory()->create(['role' => 'guru']);

    $course = Course::factory()->create(['instructor_id' => $guru->id]);

    $enrollment = Enrollment::factory()->create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'status' => 'active',
    ]);

    // Send only to the instructor
    $guru->notify(new EnrollmentCreated($enrollment));

    Notification::assertSentTo($guru, EnrollmentCreated::class);
    Notification::assertNotSentTo($admin, EnrollmentCreated::class);
    Notification::assertNotSentTo($student, EnrollmentCreated::class);
    Notification::assertNotSentTo($otherGuru, EnrollmentCreated::class);
});

it('CertificateAvailable is only sent to the certificate owner', function () {
    Notification::fake();

    $guru = User::factory()->create(['role' => 'guru']);
    $admin = User::factory()->create(['role' => 'admin']);
    $student = User::factory()->create(['role' => 'siswa']);
    $otherStudent = User::factory()->create(['role' => 'siswa']);

    $course = Course::factory()->create(['instructor_id' => $guru->id]);

    $enrollment = Enrollment::factory()->create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'status' => 'completed',
        'completed_at' => now(),
    ]);

    $certificate = Certificate::create([
        'enrollment_id' => $enrollment->id,
        'user_id' => $student->id,
        'course_id' => $course->id,
        'student_name' => $student->name,
        'course_title' => $course->title,
        'issue_date' => now(),
        'completion_date' => now(),
        'is_valid' => true,
    ]);

    // Send only to the certificate owner
    $student->notify(new CertificateAvailable($certificate));

    Notification::assertSentTo($student, CertificateAvailable::class);
    Notification::assertNotSentTo($otherStudent, CertificateAvailable::class);
    Notification::assertNotSentTo($guru, CertificateAvailable::class);
    Notification::assertNotSentTo($admin, CertificateAvailable::class);
});

it('does not send self-notification when user triggers the event', function () {
    Notification::fake();

    $guru = User::factory()->create(['role' => 'guru']);
    $course = Course::factory()->create(['instructor_id' => $guru->id]);
    $exam = Exam::factory()->create(['course_id' => $course->id, 'created_by' => $guru->id, 'is_published' => true]);

    // Guru should NOT receive ExamScheduled for their own exam
    Notification::assertNotSentTo($guru, ExamScheduled::class);

    // Guru should NOT receive MaterialPublished for their own material
    Notification::assertNotSentTo($guru, MaterialPublished::class);
});
