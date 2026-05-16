<?php

use App\Models\Course;
use App\Models\CourseGroup;
use App\Models\User;
use App\Policies\CourseGroupPolicy;

/*
|--------------------------------------------------------------------------
| Property-Based Tests for Course Group Authorization
|--------------------------------------------------------------------------
|
| These tests validate Property 3: Authorization policy correctness
| using randomized inputs with 100 iterations per property.
|
| Validates: Requirements 1.9, 5.1, 5.2, 5.3, 5.4, 5.6
|
*/

it('Property 3: Authorization policy correctness - admin always allowed', function () {
    /**
     * Validates: Requirements 1.9, 5.1
     *
     * For any admin user and any course, all group management operations SHALL be allowed
     * regardless of course ownership.
     */
    $policy = new CourseGroupPolicy();

    for ($i = 0; $i < 100; $i++) {
        $admin = User::factory()->create(['role' => 'admin']);

        // Create a course owned by a random instructor (not the admin)
        $instructor = User::factory()->create(['role' => fake()->randomElement(['guru', 'dosen'])]);
        $course = Course::factory()->create(['instructor_id' => $instructor->id]);
        $group = CourseGroup::factory()->create(['course_id' => $course->id]);

        // All policy methods should return true for admin
        expect($policy->viewAny($admin, $course))->toBeTrue(
            "Admin should always be allowed to viewAny (iteration {$i})"
        );
        expect($policy->create($admin, $course))->toBeTrue(
            "Admin should always be allowed to create (iteration {$i})"
        );
        expect($policy->update($admin, $group))->toBeTrue(
            "Admin should always be allowed to update (iteration {$i})"
        );
        expect($policy->delete($admin, $group))->toBeTrue(
            "Admin should always be allowed to delete (iteration {$i})"
        );
        expect($policy->manageMember($admin, $group))->toBeTrue(
            "Admin should always be allowed to manageMember (iteration {$i})"
        );
    }
})->group('property-tests', 'course-groups', 'authorization');

it('Property 3: Authorization policy correctness - guru/dosen owner allowed', function () {
    /**
     * Validates: Requirements 1.9, 5.2
     *
     * For any guru or dosen user who owns the course (course.instructor_id === user.id),
     * all group management operations SHALL be allowed.
     */
    $policy = new CourseGroupPolicy();

    for ($i = 0; $i < 100; $i++) {
        $role = fake()->randomElement(['guru', 'dosen']);
        $owner = User::factory()->create(['role' => $role]);
        $course = Course::factory()->create(['instructor_id' => $owner->id]);
        $group = CourseGroup::factory()->create(['course_id' => $course->id]);

        // All policy methods should return true for course owner
        expect($policy->viewAny($owner, $course))->toBeTrue(
            "{$role} owner should be allowed to viewAny (iteration {$i})"
        );
        expect($policy->create($owner, $course))->toBeTrue(
            "{$role} owner should be allowed to create (iteration {$i})"
        );
        expect($policy->update($owner, $group))->toBeTrue(
            "{$role} owner should be allowed to update (iteration {$i})"
        );
        expect($policy->delete($owner, $group))->toBeTrue(
            "{$role} owner should be allowed to delete (iteration {$i})"
        );
        expect($policy->manageMember($owner, $group))->toBeTrue(
            "{$role} owner should be allowed to manageMember (iteration {$i})"
        );
    }
})->group('property-tests', 'course-groups', 'authorization');

it('Property 3: Authorization policy correctness - guru/dosen non-owner denied', function () {
    /**
     * Validates: Requirements 1.9, 5.3
     *
     * For any guru or dosen user who does NOT own the course (course.instructor_id !== user.id),
     * all group management operations SHALL be denied.
     */
    $policy = new CourseGroupPolicy();

    for ($i = 0; $i < 100; $i++) {
        $role = fake()->randomElement(['guru', 'dosen']);
        $nonOwner = User::factory()->create(['role' => $role]);

        // Create a course owned by a DIFFERENT instructor
        $actualOwner = User::factory()->create(['role' => fake()->randomElement(['guru', 'dosen'])]);
        $course = Course::factory()->create(['instructor_id' => $actualOwner->id]);
        $group = CourseGroup::factory()->create(['course_id' => $course->id]);

        // All policy methods should return false for non-owner
        expect($policy->viewAny($nonOwner, $course))->toBeFalse(
            "{$role} non-owner should be denied viewAny (iteration {$i})"
        );
        expect($policy->create($nonOwner, $course))->toBeFalse(
            "{$role} non-owner should be denied create (iteration {$i})"
        );
        expect($policy->update($nonOwner, $group))->toBeFalse(
            "{$role} non-owner should be denied update (iteration {$i})"
        );
        expect($policy->delete($nonOwner, $group))->toBeFalse(
            "{$role} non-owner should be denied delete (iteration {$i})"
        );
        expect($policy->manageMember($nonOwner, $group))->toBeFalse(
            "{$role} non-owner should be denied manageMember (iteration {$i})"
        );
    }
})->group('property-tests', 'course-groups', 'authorization');

it('Property 3: Authorization policy correctness - siswa/mahasiswa always denied', function () {
    /**
     * Validates: Requirements 5.4
     *
     * For any siswa or mahasiswa user, all group management operations SHALL be denied
     * regardless of any other conditions.
     */
    $policy = new CourseGroupPolicy();

    for ($i = 0; $i < 100; $i++) {
        $role = fake()->randomElement(['siswa', 'mahasiswa']);
        $student = User::factory()->create(['role' => $role]);

        // Create a course (student has no ownership concept)
        $instructor = User::factory()->create(['role' => fake()->randomElement(['guru', 'dosen'])]);
        $course = Course::factory()->create(['instructor_id' => $instructor->id]);
        $group = CourseGroup::factory()->create(['course_id' => $course->id]);

        // All policy methods should return false for students
        expect($policy->viewAny($student, $course))->toBeFalse(
            "{$role} should be denied viewAny (iteration {$i})"
        );
        expect($policy->create($student, $course))->toBeFalse(
            "{$role} should be denied create (iteration {$i})"
        );
        expect($policy->update($student, $group))->toBeFalse(
            "{$role} should be denied update (iteration {$i})"
        );
        expect($policy->delete($student, $group))->toBeFalse(
            "{$role} should be denied delete (iteration {$i})"
        );
        expect($policy->manageMember($student, $group))->toBeFalse(
            "{$role} should be denied manageMember (iteration {$i})"
        );
    }
})->group('property-tests', 'course-groups', 'authorization');

it('Property 3: Authorization policy correctness - role equivalence', function () {
    /**
     * Validates: Requirements 5.6
     *
     * The policy SHALL produce identical results for guru/dosen (role equivalence)
     * and identical denial for siswa/mahasiswa (role equivalence).
     */
    $policy = new CourseGroupPolicy();

    for ($i = 0; $i < 100; $i++) {
        // Test guru/dosen equivalence
        $guru = User::factory()->create(['role' => 'guru']);
        $dosen = User::factory()->create(['role' => 'dosen']);

        $instructor = User::factory()->create(['role' => 'guru']);
        $course = Course::factory()->create(['instructor_id' => $instructor->id]);
        $group = CourseGroup::factory()->create(['course_id' => $course->id]);

        // Both guru and dosen (non-owners) should get identical results
        expect($policy->viewAny($guru, $course))->toBe(
            $policy->viewAny($dosen, $course),
            "guru and dosen should have identical viewAny result (iteration {$i})"
        );
        expect($policy->create($guru, $course))->toBe(
            $policy->create($dosen, $course),
            "guru and dosen should have identical create result (iteration {$i})"
        );
        expect($policy->update($guru, $group))->toBe(
            $policy->update($dosen, $group),
            "guru and dosen should have identical update result (iteration {$i})"
        );
        expect($policy->delete($guru, $group))->toBe(
            $policy->delete($dosen, $group),
            "guru and dosen should have identical delete result (iteration {$i})"
        );
        expect($policy->manageMember($guru, $group))->toBe(
            $policy->manageMember($dosen, $group),
            "guru and dosen should have identical manageMember result (iteration {$i})"
        );

        // Test siswa/mahasiswa equivalence
        $siswa = User::factory()->create(['role' => 'siswa']);
        $mahasiswa = User::factory()->create(['role' => 'mahasiswa']);

        expect($policy->viewAny($siswa, $course))->toBe(
            $policy->viewAny($mahasiswa, $course),
            "siswa and mahasiswa should have identical viewAny result (iteration {$i})"
        );
        expect($policy->create($siswa, $course))->toBe(
            $policy->create($mahasiswa, $course),
            "siswa and mahasiswa should have identical create result (iteration {$i})"
        );
        expect($policy->update($siswa, $group))->toBe(
            $policy->update($mahasiswa, $group),
            "siswa and mahasiswa should have identical update result (iteration {$i})"
        );
        expect($policy->delete($siswa, $group))->toBe(
            $policy->delete($mahasiswa, $group),
            "siswa and mahasiswa should have identical delete result (iteration {$i})"
        );
        expect($policy->manageMember($siswa, $group))->toBe(
            $policy->manageMember($mahasiswa, $group),
            "siswa and mahasiswa should have identical manageMember result (iteration {$i})"
        );
    }
})->group('property-tests', 'course-groups', 'authorization');
