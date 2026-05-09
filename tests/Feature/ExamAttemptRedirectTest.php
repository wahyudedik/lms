<?php

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\User;

/**
 * Feature: dosen-mahasiswa-role
 *
 * Property 7: Exam attempt redirect follows role
 *
 * Untuk user dengan role `siswa` atau `mahasiswa`, setelah start exam redirect
 * harus ke route yang namanya diawali `{user->getRolePrefix()}.exams.take`.
 *
 * Validates: Requirements 6.1, 10.5
 */
it('redirects to the correct role-prefixed take route after starting an exam', function (string $role, string $expectedRoute) {
    // 1. Create a user with the given role
    $user = User::factory()->create(['role' => $role]);

    // 2. Create a course with an instructor
    $instructor = User::factory()->create(['role' => 'guru']);
    $course = Course::factory()->create([
        'instructor_id' => $instructor->id,
        'status' => 'published',
    ]);

    // 3. Create an active exam in that course (no time window, always active)
    $exam = Exam::factory()->create([
        'course_id' => $course->id,
        'created_by' => $instructor->id,
        'is_published' => true,
        'start_time' => null,
        'end_time' => null,
        'max_attempts' => 5,
    ]);

    // 4. Enroll the user in the course
    Enrollment::create([
        'user_id' => $user->id,
        'course_id' => $course->id,
        'status' => 'active',
    ]);

    // 5. Log in as the user via the login endpoint (session-based auth)
    $this->post('/login', ['email' => $user->email, 'password' => 'password']);
    $this->assertAuthenticatedAs($user);

    // 6. POST to the start exam route
    $response = $this->post(route("{$role}.exams.start", $exam));

    // 7. Fetch the created attempt from the database
    $attempt = ExamAttempt::where('exam_id', $exam->id)
        ->where('user_id', $user->id)
        ->latest()
        ->first();

    expect($attempt)->not->toBeNull();

    // 8. Assert the redirect goes to the expected role-prefixed take route
    $response->assertRedirect(route($expectedRoute, $attempt));
})->with([
    ['siswa', 'siswa.exams.take'],
    ['mahasiswa', 'mahasiswa.exams.take'],
]);
