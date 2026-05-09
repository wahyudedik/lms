<?php

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Course;
use App\Models\CourseGradeWeight;
use App\Models\Enrollment;
use App\Models\User;
use App\Notifications\AssignmentDeadlineReminder;
use App\Notifications\AssignmentPublished;
use App\Notifications\AssignmentSubmitted;
use App\Notifications\SubmissionGraded;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| Feature Tests for Assignment Submission System
|--------------------------------------------------------------------------
*/

// ============================================================
// SECTION 20.1: Feature tests for assignment CRUD
// Requirements: 1.1, 1.3, 2.1, 2.2, 2.6
// ============================================================

it('creates an assignment via guru controller', function () {
    Notification::fake();
    $guru = User::factory()->create(['role' => 'guru', 'is_active' => true]);
    $course = Course::factory()->create(['instructor_id' => $guru->id]);

    $response = $this->actingAs($guru)->post(
        route('guru.courses.assignments.store', $course),
        [
            'title' => 'Tugas Pertama',
            'description' => 'Deskripsi tugas',
            'deadline' => now()->addDays(7)->format('Y-m-d H:i:s'),
            'max_score' => 100,
            'late_policy' => 'reject',
            'allowed_file_types' => ['pdf', 'doc'],
            'is_published' => true,
        ]
    );

    $response->assertRedirect(route('guru.courses.assignments.index', $course));
    $this->assertDatabaseHas('assignments', [
        'course_id' => $course->id,
        'title' => 'Tugas Pertama',
        'created_by' => $guru->id,
        'max_score' => 100,
        'late_policy' => 'reject',
        'is_published' => true,
    ]);
});

it('reads an assignment via guru show route', function () {
    $guru = User::factory()->create(['role' => 'guru', 'is_active' => true]);
    $course = Course::factory()->create(['instructor_id' => $guru->id]);
    $assignment = Assignment::create([
        'course_id' => $course->id,
        'created_by' => $guru->id,
        'title' => 'Tugas Show Test',
        'deadline' => now()->addDays(7),
        'max_score' => 100,
        'late_policy' => 'reject',
        'is_published' => true,
        'published_at' => now(),
    ]);

    $response = $this->actingAs($guru)->get(
        route('guru.courses.assignments.show', [$course, $assignment])
    );

    $response->assertStatus(200);
    $response->assertSee('Tugas Show Test');
});


it('updates an assignment via guru controller', function () {
    $guru = User::factory()->create(['role' => 'guru', 'is_active' => true]);
    $course = Course::factory()->create(['instructor_id' => $guru->id]);
    $assignment = Assignment::create([
        'course_id' => $course->id,
        'created_by' => $guru->id,
        'title' => 'Original Title',
        'deadline' => now()->addDays(7),
        'max_score' => 100,
        'late_policy' => 'reject',
        'is_published' => false,
    ]);

    $response = $this->actingAs($guru)->put(
        route('guru.courses.assignments.update', [$course, $assignment]),
        [
            'title' => 'Updated Title',
            'description' => 'Updated description',
            'deadline' => now()->addDays(14)->format('Y-m-d H:i:s'),
            'max_score' => 80,
            'late_policy' => 'allow',
            'is_published' => false,
        ]
    );

    $response->assertRedirect(route('guru.courses.assignments.index', $course));
    $this->assertDatabaseHas('assignments', [
        'id' => $assignment->id,
        'title' => 'Updated Title',
        'max_score' => 80,
        'late_policy' => 'allow',
    ]);
});

it('soft-deletes an assignment via guru controller', function () {
    $guru = User::factory()->create(['role' => 'guru', 'is_active' => true]);
    $course = Course::factory()->create(['instructor_id' => $guru->id]);
    $assignment = Assignment::create([
        'course_id' => $course->id,
        'created_by' => $guru->id,
        'title' => 'To Be Deleted',
        'deadline' => now()->addDays(7),
        'max_score' => 100,
        'late_policy' => 'reject',
        'is_published' => false,
    ]);

    $response = $this->actingAs($guru)->delete(
        route('guru.courses.assignments.destroy', [$course, $assignment])
    );

    $response->assertRedirect(route('guru.courses.assignments.index', $course));
    $this->assertSoftDeleted('assignments', ['id' => $assignment->id]);
});

it('returns validation errors for invalid assignment inputs', function () {
    $guru = User::factory()->create(['role' => 'guru', 'is_active' => true]);
    $course = Course::factory()->create(['instructor_id' => $guru->id]);

    $response = $this->actingAs($guru)->post(
        route('guru.courses.assignments.store', $course),
        [
            'title' => '',
            'deadline' => now()->subDay()->format('Y-m-d H:i:s'),
            'max_score' => 0,
            'late_policy' => 'invalid',
        ]
    );

    $response->assertSessionHasErrors(['title', 'deadline', 'max_score', 'late_policy']);
});

it('prevents non-creator from editing an assignment', function () {
    $guru = User::factory()->create(['role' => 'guru', 'is_active' => true]);
    $otherGuru = User::factory()->create(['role' => 'guru', 'is_active' => true]);
    $course = Course::factory()->create(['instructor_id' => $guru->id]);
    $assignment = Assignment::create([
        'course_id' => $course->id,
        'created_by' => $guru->id,
        'title' => 'Protected Assignment',
        'deadline' => now()->addDays(7),
        'max_score' => 100,
        'late_policy' => 'reject',
        'is_published' => false,
    ]);

    $response = $this->actingAs($otherGuru)->put(
        route('guru.courses.assignments.update', [$course, $assignment]),
        [
            'title' => 'Hacked Title',
            'deadline' => now()->addDays(14)->format('Y-m-d H:i:s'),
            'max_score' => 100,
            'late_policy' => 'reject',
            'is_published' => false,
        ]
    );

    $response->assertForbidden();
    $this->assertDatabaseHas('assignments', [
        'id' => $assignment->id,
        'title' => 'Protected Assignment',
    ]);
});

it('prevents non-creator from deleting an assignment', function () {
    $guru = User::factory()->create(['role' => 'guru', 'is_active' => true]);
    $otherGuru = User::factory()->create(['role' => 'guru', 'is_active' => true]);
    $course = Course::factory()->create(['instructor_id' => $guru->id]);
    $assignment = Assignment::create([
        'course_id' => $course->id,
        'created_by' => $guru->id,
        'title' => 'Cannot Delete',
        'deadline' => now()->addDays(7),
        'max_score' => 100,
        'late_policy' => 'reject',
        'is_published' => false,
    ]);

    $response = $this->actingAs($otherGuru)->delete(
        route('guru.courses.assignments.destroy', [$course, $assignment])
    );

    $response->assertForbidden();
    $this->assertDatabaseHas('assignments', ['id' => $assignment->id, 'deleted_at' => null]);
});


// ============================================================
// SECTION 20.2: Feature tests for submission lifecycle
// Requirements: 3.1, 3.2, 4.1, 4.3, 4.4, 4.5, 5.1, 5.2, 5.3, 6.1, 6.3
// ============================================================

it('completes full submission lifecycle: submit, revise, grade', function () {
    Notification::fake();
    Storage::fake('public');

    $guru = User::factory()->create(['role' => 'guru', 'is_active' => true]);
    $siswa = User::factory()->create(['role' => 'siswa', 'is_active' => true]);
    $course = Course::factory()->create(['instructor_id' => $guru->id]);
    Enrollment::factory()->create([
        'user_id' => $siswa->id,
        'course_id' => $course->id,
        'status' => 'active',
    ]);
    $assignment = Assignment::create([
        'course_id' => $course->id,
        'created_by' => $guru->id,
        'title' => 'Lifecycle Test',
        'deadline' => now()->addDays(7),
        'max_score' => 100,
        'late_policy' => 'reject',
        'is_published' => true,
        'published_at' => now(),
        'allowed_file_types' => ['pdf'],
    ]);

    // Step 1: Submit
    $file = UploadedFile::fake()->create('tugas.pdf', 500, 'application/pdf');
    $response = $this->actingAs($siswa)->post(
        route('siswa.assignments.submit', $assignment),
        ['file' => $file]
    );

    $response->assertRedirect(route('siswa.assignments.show', $assignment));
    $this->assertDatabaseHas('assignment_submissions', [
        'assignment_id' => $assignment->id,
        'user_id' => $siswa->id,
        'status' => 'submitted',
        'revision_count' => 0,
    ]);

    // Step 2: Revise
    $revisedFile = UploadedFile::fake()->create('tugas_v2.pdf', 600, 'application/pdf');
    $response = $this->actingAs($siswa)->post(
        route('siswa.assignments.submit', $assignment),
        ['file' => $revisedFile]
    );

    $response->assertRedirect(route('siswa.assignments.show', $assignment));
    $submission = AssignmentSubmission::where('assignment_id', $assignment->id)
        ->where('user_id', $siswa->id)
        ->first();
    expect($submission->revision_count)->toBe(1);
    expect($submission->file_name)->toBe('tugas_v2.pdf');

    // Step 3: Grade
    $response = $this->actingAs($guru)->post(
        route('guru.assignments.submissions.grade', [$assignment, $submission]),
        ['score' => 85, 'feedback' => 'Bagus!']
    );

    $response->assertRedirect(route('guru.assignments.submissions.show', [$assignment, $submission]));
    $submission->refresh();
    expect($submission->status)->toBe('graded');
    expect($submission->score)->toBe(85);
    expect((float) $submission->final_score)->toBe(85.0);
});

it('rejects file upload with wrong type', function () {
    Storage::fake('public');

    $guru = User::factory()->create(['role' => 'guru', 'is_active' => true]);
    $siswa = User::factory()->create(['role' => 'siswa', 'is_active' => true]);
    $course = Course::factory()->create(['instructor_id' => $guru->id]);
    Enrollment::factory()->create([
        'user_id' => $siswa->id,
        'course_id' => $course->id,
        'status' => 'active',
    ]);
    $assignment = Assignment::create([
        'course_id' => $course->id,
        'created_by' => $guru->id,
        'title' => 'PDF Only Assignment',
        'deadline' => now()->addDays(7),
        'max_score' => 100,
        'late_policy' => 'reject',
        'is_published' => true,
        'published_at' => now(),
        'allowed_file_types' => ['pdf'],
    ]);

    $file = UploadedFile::fake()->create('tugas.exe', 500, 'application/x-msdownload');
    $response = $this->actingAs($siswa)->post(
        route('siswa.assignments.submit', $assignment),
        ['file' => $file]
    );

    $response->assertSessionHasErrors('file');
    $this->assertDatabaseMissing('assignment_submissions', [
        'assignment_id' => $assignment->id,
        'user_id' => $siswa->id,
    ]);
});

it('rejects oversized file upload', function () {
    Storage::fake('public');

    $guru = User::factory()->create(['role' => 'guru', 'is_active' => true]);
    $siswa = User::factory()->create(['role' => 'siswa', 'is_active' => true]);
    $course = Course::factory()->create(['instructor_id' => $guru->id]);
    Enrollment::factory()->create([
        'user_id' => $siswa->id,
        'course_id' => $course->id,
        'status' => 'active',
    ]);
    $assignment = Assignment::create([
        'course_id' => $course->id,
        'created_by' => $guru->id,
        'title' => 'Size Limit Test',
        'deadline' => now()->addDays(7),
        'max_score' => 100,
        'late_policy' => 'reject',
        'is_published' => true,
        'published_at' => now(),
        'allowed_file_types' => ['pdf'],
    ]);

    // 11 MB file (exceeds 10 MB limit)
    $file = UploadedFile::fake()->create('large.pdf', 11264, 'application/pdf');
    $response = $this->actingAs($siswa)->post(
        route('siswa.assignments.submit', $assignment),
        ['file' => $file]
    );

    $response->assertSessionHasErrors('file');
    $this->assertDatabaseMissing('assignment_submissions', [
        'assignment_id' => $assignment->id,
        'user_id' => $siswa->id,
    ]);
});

it('rejects submission after deadline with reject policy', function () {
    Storage::fake('public');

    $guru = User::factory()->create(['role' => 'guru', 'is_active' => true]);
    $siswa = User::factory()->create(['role' => 'siswa', 'is_active' => true]);
    $course = Course::factory()->create(['instructor_id' => $guru->id]);
    Enrollment::factory()->create([
        'user_id' => $siswa->id,
        'course_id' => $course->id,
        'status' => 'active',
    ]);
    $assignment = Assignment::create([
        'course_id' => $course->id,
        'created_by' => $guru->id,
        'title' => 'Expired Assignment',
        'deadline' => now()->subHour(),
        'max_score' => 100,
        'late_policy' => 'reject',
        'is_published' => true,
        'published_at' => now()->subDays(7),
    ]);

    $file = UploadedFile::fake()->create('tugas.pdf', 500, 'application/pdf');
    $response = $this->actingAs($siswa)->post(
        route('siswa.assignments.submit', $assignment),
        ['file' => $file]
    );

    // Policy submit check fails: canAcceptSubmission() returns false
    $response->assertForbidden();
});

it('accepts late submission with allow policy and marks as late', function () {
    Notification::fake();
    Storage::fake('public');

    $guru = User::factory()->create(['role' => 'guru', 'is_active' => true]);
    $siswa = User::factory()->create(['role' => 'siswa', 'is_active' => true]);
    $course = Course::factory()->create(['instructor_id' => $guru->id]);
    Enrollment::factory()->create([
        'user_id' => $siswa->id,
        'course_id' => $course->id,
        'status' => 'active',
    ]);
    $assignment = Assignment::create([
        'course_id' => $course->id,
        'created_by' => $guru->id,
        'title' => 'Late Allow Assignment',
        'deadline' => now()->subHour(),
        'max_score' => 100,
        'late_policy' => 'allow',
        'is_published' => true,
        'published_at' => now()->subDays(7),
    ]);

    $file = UploadedFile::fake()->create('tugas.pdf', 500, 'application/pdf');
    $response = $this->actingAs($siswa)->post(
        route('siswa.assignments.submit', $assignment),
        ['file' => $file]
    );

    $response->assertRedirect(route('siswa.assignments.show', $assignment));
    $this->assertDatabaseHas('assignment_submissions', [
        'assignment_id' => $assignment->id,
        'user_id' => $siswa->id,
        'status' => 'late',
    ]);
});

it('accepts late submission with penalty policy and records penalty', function () {
    Notification::fake();
    Storage::fake('public');

    $guru = User::factory()->create(['role' => 'guru', 'is_active' => true]);
    $siswa = User::factory()->create(['role' => 'siswa', 'is_active' => true]);
    $course = Course::factory()->create(['instructor_id' => $guru->id]);
    Enrollment::factory()->create([
        'user_id' => $siswa->id,
        'course_id' => $course->id,
        'status' => 'active',
    ]);
    $assignment = Assignment::create([
        'course_id' => $course->id,
        'created_by' => $guru->id,
        'title' => 'Penalty Assignment',
        'deadline' => now()->subHour(),
        'max_score' => 100,
        'late_policy' => 'penalty',
        'penalty_percentage' => 20,
        'is_published' => true,
        'published_at' => now()->subDays(7),
    ]);

    $file = UploadedFile::fake()->create('tugas.pdf', 500, 'application/pdf');
    $response = $this->actingAs($siswa)->post(
        route('siswa.assignments.submit', $assignment),
        ['file' => $file]
    );

    $response->assertRedirect(route('siswa.assignments.show', $assignment));
    $this->assertDatabaseHas('assignment_submissions', [
        'assignment_id' => $assignment->id,
        'user_id' => $siswa->id,
        'status' => 'late',
        'penalty_applied' => 20,
    ]);
});

it('revision replaces old file and increments revision_count', function () {
    Notification::fake();
    Storage::fake('public');

    $guru = User::factory()->create(['role' => 'guru', 'is_active' => true]);
    $siswa = User::factory()->create(['role' => 'siswa', 'is_active' => true]);
    $course = Course::factory()->create(['instructor_id' => $guru->id]);
    Enrollment::factory()->create([
        'user_id' => $siswa->id,
        'course_id' => $course->id,
        'status' => 'active',
    ]);
    $assignment = Assignment::create([
        'course_id' => $course->id,
        'created_by' => $guru->id,
        'title' => 'Revision Test',
        'deadline' => now()->addDays(7),
        'max_score' => 100,
        'late_policy' => 'reject',
        'is_published' => true,
        'published_at' => now(),
        'allowed_file_types' => ['pdf'],
    ]);

    // First submission
    $file1 = UploadedFile::fake()->create('v1.pdf', 500, 'application/pdf');
    $this->actingAs($siswa)->post(
        route('siswa.assignments.submit', $assignment),
        ['file' => $file1]
    );

    $submission = AssignmentSubmission::where('assignment_id', $assignment->id)
        ->where('user_id', $siswa->id)->first();
    $oldFilePath = $submission->file_path;
    expect($submission->revision_count)->toBe(0);

    // Second submission (revision)
    $file2 = UploadedFile::fake()->create('v2.pdf', 600, 'application/pdf');
    $this->actingAs($siswa)->post(
        route('siswa.assignments.submit', $assignment),
        ['file' => $file2]
    );

    $submission->refresh();
    expect($submission->revision_count)->toBe(1);
    expect($submission->file_name)->toBe('v2.pdf');
    Storage::disk('public')->assertMissing($oldFilePath);
});

it('prevents revision of graded submission', function () {
    Notification::fake();
    Storage::fake('public');

    $guru = User::factory()->create(['role' => 'guru', 'is_active' => true]);
    $siswa = User::factory()->create(['role' => 'siswa', 'is_active' => true]);
    $course = Course::factory()->create(['instructor_id' => $guru->id]);
    Enrollment::factory()->create([
        'user_id' => $siswa->id,
        'course_id' => $course->id,
        'status' => 'active',
    ]);
    $assignment = Assignment::create([
        'course_id' => $course->id,
        'created_by' => $guru->id,
        'title' => 'Graded Assignment',
        'deadline' => now()->addDays(7),
        'max_score' => 100,
        'late_policy' => 'reject',
        'is_published' => true,
        'published_at' => now(),
        'allowed_file_types' => ['pdf'],
    ]);

    AssignmentSubmission::create([
        'assignment_id' => $assignment->id,
        'user_id' => $siswa->id,
        'file_path' => 'assignments/1/1/tugas.pdf',
        'file_name' => 'tugas.pdf',
        'file_size' => 500000,
        'status' => 'graded',
        'score' => 80,
        'final_score' => 80.00,
        'revision_count' => 0,
        'submitted_at' => now()->subDay(),
        'graded_at' => now(),
        'graded_by' => $guru->id,
    ]);

    $file = UploadedFile::fake()->create('revision.pdf', 500, 'application/pdf');
    $response = $this->actingAs($siswa)->post(
        route('siswa.assignments.submit', $assignment),
        ['file' => $file]
    );

    $response->assertSessionHasErrors('file');
});

it('grades a late submission with penalty and calculates final_score correctly', function () {
    Notification::fake();

    $guru = User::factory()->create(['role' => 'guru', 'is_active' => true]);
    $siswa = User::factory()->create(['role' => 'siswa', 'is_active' => true]);
    $course = Course::factory()->create(['instructor_id' => $guru->id]);
    $assignment = Assignment::create([
        'course_id' => $course->id,
        'created_by' => $guru->id,
        'title' => 'Penalty Grade Test',
        'deadline' => now()->subDay(),
        'max_score' => 100,
        'late_policy' => 'penalty',
        'penalty_percentage' => 25,
        'is_published' => true,
        'published_at' => now()->subDays(7),
    ]);

    $submission = AssignmentSubmission::create([
        'assignment_id' => $assignment->id,
        'user_id' => $siswa->id,
        'file_path' => 'assignments/test/file.pdf',
        'file_name' => 'file.pdf',
        'file_size' => 500000,
        'status' => 'late',
        'penalty_applied' => 25,
        'revision_count' => 0,
        'submitted_at' => now(),
    ]);

    $response = $this->actingAs($guru)->post(
        route('guru.assignments.submissions.grade', [$assignment, $submission]),
        ['score' => 80, 'feedback' => 'Late but good']
    );

    $response->assertRedirect();
    $submission->refresh();
    expect($submission->status)->toBe('graded');
    expect($submission->score)->toBe(80);
    // final_score = 80 - (80 * 25 / 100) = 80 - 20 = 60
    expect((float) $submission->final_score)->toBe(60.0);
});


// ============================================================
// SECTION 20.3: Feature tests for notifications
// Requirements: 7.1, 7.2, 7.3, 7.4
// ============================================================

it('dispatches AssignmentPublished notification on publish via toggle', function () {
    Notification::fake();

    $guru = User::factory()->create(['role' => 'guru', 'is_active' => true]);
    $siswa = User::factory()->create(['role' => 'siswa', 'is_active' => true]);
    $course = Course::factory()->create(['instructor_id' => $guru->id]);
    Enrollment::factory()->create([
        'user_id' => $siswa->id,
        'course_id' => $course->id,
        'status' => 'active',
    ]);
    $assignment = Assignment::create([
        'course_id' => $course->id,
        'created_by' => $guru->id,
        'title' => 'Notification Test',
        'deadline' => now()->addDays(7),
        'max_score' => 100,
        'late_policy' => 'reject',
        'is_published' => false,
    ]);

    $response = $this->actingAs($guru)->post(
        route('guru.courses.assignments.toggle-status', [$course, $assignment])
    );

    $response->assertRedirect();
    $assignment->refresh();
    expect($assignment->is_published)->toBeTrue();

    Notification::assertSentTo($siswa, AssignmentPublished::class);
});

it('dispatches AssignmentSubmitted notification on submit', function () {
    Notification::fake();
    Storage::fake('public');

    $guru = User::factory()->create(['role' => 'guru', 'is_active' => true]);
    $siswa = User::factory()->create(['role' => 'siswa', 'is_active' => true]);
    $course = Course::factory()->create(['instructor_id' => $guru->id]);
    Enrollment::factory()->create([
        'user_id' => $siswa->id,
        'course_id' => $course->id,
        'status' => 'active',
    ]);
    $assignment = Assignment::create([
        'course_id' => $course->id,
        'created_by' => $guru->id,
        'title' => 'Submit Notification Test',
        'deadline' => now()->addDays(7),
        'max_score' => 100,
        'late_policy' => 'reject',
        'is_published' => true,
        'published_at' => now(),
        'allowed_file_types' => ['pdf'],
    ]);

    $file = UploadedFile::fake()->create('tugas.pdf', 500, 'application/pdf');
    $this->actingAs($siswa)->post(
        route('siswa.assignments.submit', $assignment),
        ['file' => $file]
    );

    Notification::assertSentTo($guru, AssignmentSubmitted::class);
});

it('dispatches SubmissionGraded notification on grade', function () {
    Notification::fake();

    $guru = User::factory()->create(['role' => 'guru', 'is_active' => true]);
    $siswa = User::factory()->create(['role' => 'siswa', 'is_active' => true]);
    $course = Course::factory()->create(['instructor_id' => $guru->id]);
    $assignment = Assignment::create([
        'course_id' => $course->id,
        'created_by' => $guru->id,
        'title' => 'Grade Notification Test',
        'deadline' => now()->addDays(7),
        'max_score' => 100,
        'late_policy' => 'reject',
        'is_published' => true,
        'published_at' => now(),
    ]);

    $submission = AssignmentSubmission::create([
        'assignment_id' => $assignment->id,
        'user_id' => $siswa->id,
        'file_path' => 'assignments/test/file.pdf',
        'file_name' => 'file.pdf',
        'file_size' => 500000,
        'status' => 'submitted',
        'revision_count' => 0,
        'submitted_at' => now(),
    ]);

    $this->actingAs($guru)->post(
        route('guru.assignments.submissions.grade', [$assignment, $submission]),
        ['score' => 90, 'feedback' => 'Excellent']
    );

    Notification::assertSentTo($siswa, SubmissionGraded::class);
});

it('deadline reminder command sends to students who have not submitted', function () {
    Notification::fake();

    $guru = User::factory()->create(['role' => 'guru', 'is_active' => true]);
    $siswaWithSubmission = User::factory()->create(['role' => 'siswa', 'is_active' => true]);
    $siswaWithoutSubmission = User::factory()->create(['role' => 'siswa', 'is_active' => true]);
    $course = Course::factory()->create(['instructor_id' => $guru->id]);

    Enrollment::factory()->create([
        'user_id' => $siswaWithSubmission->id,
        'course_id' => $course->id,
        'status' => 'active',
    ]);
    Enrollment::factory()->create([
        'user_id' => $siswaWithoutSubmission->id,
        'course_id' => $course->id,
        'status' => 'active',
    ]);

    $assignment = Assignment::create([
        'course_id' => $course->id,
        'created_by' => $guru->id,
        'title' => 'Deadline Soon',
        'deadline' => now()->addHours(12),
        'max_score' => 100,
        'late_policy' => 'reject',
        'is_published' => true,
        'published_at' => now()->subDays(5),
    ]);

    AssignmentSubmission::create([
        'assignment_id' => $assignment->id,
        'user_id' => $siswaWithSubmission->id,
        'file_path' => 'assignments/test/file.pdf',
        'file_name' => 'file.pdf',
        'file_size' => 500000,
        'status' => 'submitted',
        'revision_count' => 0,
        'submitted_at' => now()->subDay(),
    ]);

    $this->artisan('assignments:send-deadline-reminders')
        ->assertExitCode(0);

    Notification::assertSentTo($siswaWithoutSubmission, AssignmentDeadlineReminder::class);
    Notification::assertNotSentTo($siswaWithSubmission, AssignmentDeadlineReminder::class);
});

// ============================================================
// SECTION 20.4: Feature tests for grade weights and analytics
// Requirements: 10.1, 10.2, 10.3, 10.4, 10.6
// ============================================================

it('creates grade weights with valid sum of 100', function () {
    $guru = User::factory()->create(['role' => 'guru', 'is_active' => true]);
    $course = Course::factory()->create(['instructor_id' => $guru->id]);

    $response = $this->actingAs($guru)->post(
        route('guru.courses.grade-weights.update', $course),
        [
            'assignment_weight' => 40,
            'exam_weight' => 60,
        ]
    );

    $response->assertRedirect();
    $response->assertSessionHas('success');
    $this->assertDatabaseHas('course_grade_weights', [
        'course_id' => $course->id,
        'assignment_weight' => 40,
        'exam_weight' => 60,
    ]);
});

it('rejects grade weights that do not sum to 100', function () {
    $guru = User::factory()->create(['role' => 'guru', 'is_active' => true]);
    $course = Course::factory()->create(['instructor_id' => $guru->id]);

    $response = $this->actingAs($guru)->post(
        route('guru.courses.grade-weights.update', $course),
        [
            'assignment_weight' => 40,
            'exam_weight' => 40,
        ]
    );

    $response->assertSessionHasErrors('assignment_weight');
    $this->assertDatabaseMissing('course_grade_weights', [
        'course_id' => $course->id,
    ]);
});

it('rejects grade weights outside 0-100 range', function () {
    $guru = User::factory()->create(['role' => 'guru', 'is_active' => true]);
    $course = Course::factory()->create(['instructor_id' => $guru->id]);

    $response = $this->actingAs($guru)->post(
        route('guru.courses.grade-weights.update', $course),
        [
            'assignment_weight' => 110,
            'exam_weight' => -10,
        ]
    );

    $response->assertSessionHasErrors(['assignment_weight', 'exam_weight']);
});

it('uses default weights 30/70 when none configured', function () {
    $course = Course::factory()->create();

    $gradeWeight = CourseGradeWeight::getForCourse($course->id);

    expect($gradeWeight->assignment_weight)->toBe(30);
    expect($gradeWeight->exam_weight)->toBe(70);
    expect($gradeWeight->exists)->toBeFalse();
});

it('calculates weighted grade correctly', function () {
    $guru = User::factory()->create(['role' => 'guru', 'is_active' => true]);
    $siswa = User::factory()->create(['role' => 'siswa', 'is_active' => true]);
    $course = Course::factory()->create(['instructor_id' => $guru->id]);
    Enrollment::factory()->create([
        'user_id' => $siswa->id,
        'course_id' => $course->id,
        'status' => 'active',
    ]);

    CourseGradeWeight::create([
        'course_id' => $course->id,
        'assignment_weight' => 40,
        'exam_weight' => 60,
    ]);

    $assignment = Assignment::create([
        'course_id' => $course->id,
        'created_by' => $guru->id,
        'title' => 'Weighted Grade Test',
        'deadline' => now()->addDays(7),
        'max_score' => 100,
        'late_policy' => 'reject',
        'is_published' => true,
        'published_at' => now(),
    ]);

    AssignmentSubmission::create([
        'assignment_id' => $assignment->id,
        'user_id' => $siswa->id,
        'file_path' => 'assignments/test/file.pdf',
        'file_name' => 'file.pdf',
        'file_size' => 500000,
        'status' => 'graded',
        'score' => 80,
        'final_score' => 80.00,
        'revision_count' => 0,
        'submitted_at' => now()->subDay(),
        'graded_at' => now(),
        'graded_by' => $guru->id,
    ]);

    $gradingService = app(\App\Services\AssignmentGradingService::class);
    $courseGrade = $gradingService->calculateCourseGrade($siswa, $course);

    // With no exam scores: (80 * 40/100) + (0 * 60/100) = 32
    expect($courseGrade)->toBe(32.0);
});

it('recalculates grades when weights are updated', function () {
    $guru = User::factory()->create(['role' => 'guru', 'is_active' => true]);
    $siswa = User::factory()->create(['role' => 'siswa', 'is_active' => true]);
    $course = Course::factory()->create(['instructor_id' => $guru->id]);
    Enrollment::factory()->create([
        'user_id' => $siswa->id,
        'course_id' => $course->id,
        'status' => 'active',
    ]);

    CourseGradeWeight::create([
        'course_id' => $course->id,
        'assignment_weight' => 30,
        'exam_weight' => 70,
    ]);

    $assignment = Assignment::create([
        'course_id' => $course->id,
        'created_by' => $guru->id,
        'title' => 'Recalc Test',
        'deadline' => now()->addDays(7),
        'max_score' => 100,
        'late_policy' => 'reject',
        'is_published' => true,
        'published_at' => now(),
    ]);

    AssignmentSubmission::create([
        'assignment_id' => $assignment->id,
        'user_id' => $siswa->id,
        'file_path' => 'assignments/test/file.pdf',
        'file_name' => 'file.pdf',
        'file_size' => 500000,
        'status' => 'graded',
        'score' => 90,
        'final_score' => 90.00,
        'revision_count' => 0,
        'submitted_at' => now()->subDay(),
        'graded_at' => now(),
        'graded_by' => $guru->id,
    ]);

    $response = $this->actingAs($guru)->post(
        route('guru.courses.grade-weights.update', $course),
        [
            'assignment_weight' => 50,
            'exam_weight' => 50,
        ]
    );

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('course_grade_weights', [
        'course_id' => $course->id,
        'assignment_weight' => 50,
        'exam_weight' => 50,
    ]);

    $gradingService = app(\App\Services\AssignmentGradingService::class);
    $courseGrade = $gradingService->calculateCourseGrade($siswa, $course);

    // With updated weights: (90 * 50/100) + (0 * 50/100) = 45
    expect($courseGrade)->toBe(45.0);
});
