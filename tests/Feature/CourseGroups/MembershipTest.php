<?php

use App\Models\Course;
use App\Models\CourseGroup;
use App\Models\Enrollment;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Property-Based Tests for Course Group Membership
|--------------------------------------------------------------------------
|
| These tests validate Property 4: Membership requires active enrollment
| using randomized inputs with 100 iterations per property.
|
| Validates: Requirements 1.5, 1.7, 7.4
|
*/

it('Property 4: Membership requires active enrollment - user with active enrollment succeeds', function () {
    /**
     * Validates: Requirements 1.5, 1.7, 7.4
     *
     * For any user with an active enrollment in the same course as the group,
     * adding the user as a Group_Member SHALL succeed.
     */
    for ($i = 0; $i < 100; $i++) {
        $instructor = User::factory()->create([
            'role' => fake()->randomElement(['guru', 'dosen']),
            'is_active' => true,
        ]);
        $course = Course::factory()->create(['instructor_id' => $instructor->id]);
        $group = CourseGroup::factory()->create(['course_id' => $course->id]);

        $student = User::factory()->create([
            'role' => fake()->randomElement(['siswa', 'mahasiswa']),
            'is_active' => true,
        ]);
        Enrollment::factory()->create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'status' => 'active',
        ]);

        $response = $this->actingAs($instructor)->post(
            route('guru.courses.groups.members.store', [$course, $group]),
            ['user_id' => $student->id]
        );

        // Should succeed - redirect with no errors
        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        // Verify membership was created
        expect($group->members()->where('user_id', $student->id)->exists())->toBeTrue(
            "Student with active enrollment should be added to group (iteration {$i})"
        );
    }
})->group('property-tests', 'course-groups', 'membership');

it('Property 4: Membership requires active enrollment - user with non-active enrollment rejected', function () {
    /**
     * Validates: Requirements 1.5, 1.7, 7.4
     *
     * For any user with a non-active enrollment (dropped, completed) in the same course,
     * adding the user as a Group_Member SHALL be rejected with the error
     * "Siswa tidak terdaftar aktif di kursus ini."
     */
    $nonActiveStatuses = ['dropped', 'completed'];

    for ($i = 0; $i < 100; $i++) {
        $instructor = User::factory()->create([
            'role' => fake()->randomElement(['guru', 'dosen']),
            'is_active' => true,
        ]);
        $course = Course::factory()->create(['instructor_id' => $instructor->id]);
        $group = CourseGroup::factory()->create(['course_id' => $course->id]);

        $student = User::factory()->create([
            'role' => fake()->randomElement(['siswa', 'mahasiswa']),
            'is_active' => true,
        ]);
        $status = fake()->randomElement($nonActiveStatuses);
        Enrollment::factory()->create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'status' => $status,
        ]);

        $response = $this->actingAs($instructor)->post(
            route('guru.courses.groups.members.store', [$course, $group]),
            ['user_id' => $student->id]
        );

        // Should be rejected with validation error
        $response->assertSessionHasErrors('user_id');

        // Verify membership was NOT created
        expect($group->members()->where('user_id', $student->id)->exists())->toBeFalse(
            "Student with '{$status}' enrollment should NOT be added to group (iteration {$i})"
        );
    }
})->group('property-tests', 'course-groups', 'membership');

it('Property 4: Membership requires active enrollment - user with no enrollment rejected', function () {
    /**
     * Validates: Requirements 1.5, 1.7, 7.4
     *
     * For any user with no enrollment at all in the course,
     * adding the user as a Group_Member SHALL be rejected with the error
     * "Siswa tidak terdaftar aktif di kursus ini."
     */
    for ($i = 0; $i < 100; $i++) {
        $instructor = User::factory()->create([
            'role' => fake()->randomElement(['guru', 'dosen']),
            'is_active' => true,
        ]);
        $course = Course::factory()->create(['instructor_id' => $instructor->id]);
        $group = CourseGroup::factory()->create(['course_id' => $course->id]);

        $student = User::factory()->create([
            'role' => fake()->randomElement(['siswa', 'mahasiswa']),
            'is_active' => true,
        ]);
        // No enrollment created for this student in this course

        $response = $this->actingAs($instructor)->post(
            route('guru.courses.groups.members.store', [$course, $group]),
            ['user_id' => $student->id]
        );

        // Should be rejected with validation error
        $response->assertSessionHasErrors('user_id');

        // Verify membership was NOT created
        expect($group->members()->where('user_id', $student->id)->exists())->toBeFalse(
            "Student with no enrollment should NOT be added to group (iteration {$i})"
        );
    }
})->group('property-tests', 'course-groups', 'membership');
