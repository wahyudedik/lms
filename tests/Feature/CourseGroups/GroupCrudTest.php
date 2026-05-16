<?php

use App\Models\Assignment;
use App\Models\Course;
use App\Models\CourseGroup;
use App\Models\Enrollment;
use App\Models\Material;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| Property-Based Tests for Course Group CRUD
|--------------------------------------------------------------------------
|
| These tests validate Properties 1, 2, and 5 from the design document
| using randomized inputs with 100 iterations per property.
|
| Validates: Requirements 1.3, 1.4, 1.10, 7.3, 7.5
|
*/

it('Property 1: Group name validation enforces trimmed, non-empty, max-255, case-insensitive uniqueness per course', function () {
    /**
     * Validates: Requirements 1.4, 1.10, 7.3
     *
     * For any string input as a group name within a course, the system SHALL accept the name
     * if and only if trim(name) has length between 1 and 255 characters AND no other group
     * in the same course has a name where LOWER(TRIM(existing_name)) === LOWER(TRIM(new_name)).
     */
    for ($i = 0; $i < 100; $i++) {
        // Setup: instructor with a course
        $instructor = User::factory()->create([
            'role' => fake()->randomElement(['guru', 'dosen']),
            'is_active' => true,
        ]);
        $course = Course::factory()->create(['instructor_id' => $instructor->id]);

        // Generate a random test scenario
        $scenario = fake()->randomElement([
            'valid_name',
            'empty_name',
            'whitespace_only',
            'too_long',
            'case_insensitive_duplicate',
            'trimmed_duplicate',
            'valid_unique',
            'max_length_valid',
        ]);

        switch ($scenario) {
            case 'valid_name':
                // A valid name should be accepted
                $name = fake()->words(fake()->numberBetween(1, 3), true);
                $response = $this->actingAs($instructor)->post(
                    route('guru.courses.groups.store', $course),
                    ['name' => $name]
                );
                $response->assertSessionHasNoErrors();
                expect($course->courseGroups()->whereRaw('LOWER(TRIM(name)) = ?', [strtolower(trim($name))])->exists())
                    ->toBeTrue("Valid name '{$name}' should be accepted (iteration {$i})");
                break;

            case 'empty_name':
                // Empty string should be rejected
                $response = $this->actingAs($instructor)->post(
                    route('guru.courses.groups.store', $course),
                    ['name' => '']
                );
                $response->assertSessionHasErrors('name');
                break;

            case 'whitespace_only':
                // Whitespace-only should be rejected (trimmed becomes empty)
                $whitespace = str_repeat(' ', fake()->numberBetween(1, 10));
                $response = $this->actingAs($instructor)->post(
                    route('guru.courses.groups.store', $course),
                    ['name' => $whitespace]
                );
                // After trimming, the name becomes empty - should be rejected
                $response->assertSessionHasErrors('name');
                break;

            case 'too_long':
                // Name exceeding 255 chars should be rejected
                $longName = str_repeat('a', 256);
                $response = $this->actingAs($instructor)->post(
                    route('guru.courses.groups.store', $course),
                    ['name' => $longName]
                );
                $response->assertSessionHasErrors('name');
                break;

            case 'case_insensitive_duplicate':
                // Creating a group with same name in different case should be rejected
                $originalName = 'Group-' . fake()->unique()->uuid();
                $course->courseGroups()->create(['name' => $originalName]);

                // Try with different case
                $variations = [
                    strtoupper($originalName),
                    strtolower($originalName),
                    ucwords(strtolower($originalName)),
                ];
                $duplicateName = fake()->randomElement($variations);

                $response = $this->actingAs($instructor)->post(
                    route('guru.courses.groups.store', $course),
                    ['name' => $duplicateName]
                );
                $response->assertSessionHasErrors('name');
                // Count should remain 1
                expect($course->courseGroups()->count())->toBe(1,
                    "Case-insensitive duplicate '{$duplicateName}' of '{$originalName}' should be rejected (iteration {$i})"
                );
                break;

            case 'trimmed_duplicate':
                // Creating a group with same name but extra whitespace should be rejected
                $originalName = 'Group-' . fake()->unique()->uuid();
                $course->courseGroups()->create(['name' => $originalName]);

                // Try with leading/trailing whitespace
                $paddedName = '  ' . $originalName . '  ';
                $response = $this->actingAs($instructor)->post(
                    route('guru.courses.groups.store', $course),
                    ['name' => $paddedName]
                );
                $response->assertSessionHasErrors('name');
                expect($course->courseGroups()->count())->toBe(1,
                    "Trimmed duplicate '{$paddedName}' of '{$originalName}' should be rejected (iteration {$i})"
                );
                break;

            case 'valid_unique':
                // Two different names in the same course should both be accepted
                $name1 = 'Group-' . fake()->unique()->uuid();
                $name2 = 'Group-' . fake()->unique()->uuid();

                $this->actingAs($instructor)->post(
                    route('guru.courses.groups.store', $course),
                    ['name' => $name1]
                );
                $response = $this->actingAs($instructor)->post(
                    route('guru.courses.groups.store', $course),
                    ['name' => $name2]
                );
                $response->assertSessionHasNoErrors();
                expect($course->courseGroups()->count())->toBe(2,
                    "Two unique names should both be accepted (iteration {$i})"
                );
                break;

            case 'max_length_valid':
                // Name with exactly 255 chars should be accepted
                $maxName = str_repeat('x', 255);
                $response = $this->actingAs($instructor)->post(
                    route('guru.courses.groups.store', $course),
                    ['name' => $maxName]
                );
                $response->assertSessionHasNoErrors();
                expect($course->courseGroups()->count())->toBe(1,
                    "Name with exactly 255 chars should be accepted (iteration {$i})"
                );
                break;
        }
    }
})->group('property-tests', 'course-groups', 'crud');

it('Property 2: Group deletion cascades all related records', function () {
    /**
     * Validates: Requirements 1.3
     *
     * For any CourseGroup with associated Group_Member records and Group_Assignment_Pivot records,
     * deleting the group SHALL result in zero Group_Member records and zero Group_Assignment_Pivot
     * records referencing that group's ID.
     */
    for ($i = 0; $i < 100; $i++) {
        // Setup: instructor with a course and a group
        $instructor = User::factory()->create([
            'role' => fake()->randomElement(['guru', 'dosen']),
            'is_active' => true,
        ]);
        $course = Course::factory()->create(['instructor_id' => $instructor->id]);
        $group = CourseGroup::factory()->create(['course_id' => $course->id]);

        // Add random number of members (1-5)
        $memberCount = fake()->numberBetween(1, 5);
        for ($j = 0; $j < $memberCount; $j++) {
            $student = User::factory()->create([
                'role' => fake()->randomElement(['siswa', 'mahasiswa']),
                'is_active' => true,
            ]);
            Enrollment::factory()->create([
                'user_id' => $student->id,
                'course_id' => $course->id,
                'status' => 'active',
            ]);
            $group->members()->attach($student->id);
        }

        // Add random number of content associations (0-3 materials, 0-3 assignments)
        $materialCount = fake()->numberBetween(0, 3);
        for ($j = 0; $j < $materialCount; $j++) {
            $material = Material::factory()->create(['course_id' => $course->id]);
            DB::table('course_group_content')->insert([
                'course_group_id' => $group->id,
                'contentable_type' => Material::class,
                'contentable_id' => $material->id,
                'created_at' => now(),
            ]);
        }

        $assignmentCount = fake()->numberBetween(0, 3);
        for ($j = 0; $j < $assignmentCount; $j++) {
            $assignment = Assignment::factory()->create(['course_id' => $course->id]);
            DB::table('course_group_content')->insert([
                'course_group_id' => $group->id,
                'contentable_type' => Assignment::class,
                'contentable_id' => $assignment->id,
                'created_at' => now(),
            ]);
        }

        // Verify records exist before deletion
        $groupId = $group->id;
        expect(DB::table('course_group_members')->where('course_group_id', $groupId)->count())
            ->toBe($memberCount, "Should have {$memberCount} members before deletion (iteration {$i})");
        expect(DB::table('course_group_content')->where('course_group_id', $groupId)->count())
            ->toBe($materialCount + $assignmentCount, "Should have content records before deletion (iteration {$i})");

        // Delete the group via HTTP request
        $response = $this->actingAs($instructor)->delete(
            route('guru.courses.groups.destroy', [$course, $group])
        );
        $response->assertRedirect();

        // Verify cascade: no member records referencing this group
        expect(DB::table('course_group_members')->where('course_group_id', $groupId)->count())
            ->toBe(0, "All member records should be deleted after group deletion (iteration {$i})");

        // Verify cascade: no content records referencing this group
        expect(DB::table('course_group_content')->where('course_group_id', $groupId)->count())
            ->toBe(0, "All content records should be deleted after group deletion (iteration {$i})");

        // Verify the group itself is deleted
        expect(CourseGroup::find($groupId))->toBeNull(
            "Group should be deleted (iteration {$i})"
        );
    }
})->group('property-tests', 'course-groups', 'crud');

it('Property 5: Duplicate membership is rejected', function () {
    /**
     * Validates: Requirements 7.5
     *
     * For any user who is already a Group_Member of a CourseGroup, attempting to add them
     * again to the same group SHALL be rejected with a validation error, and the membership
     * count SHALL remain unchanged.
     */
    for ($i = 0; $i < 100; $i++) {
        // Setup: instructor with a course, group, and enrolled student
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

        // First add: should succeed
        $response = $this->actingAs($instructor)->post(
            route('guru.courses.groups.members.store', [$course, $group]),
            ['user_id' => $student->id]
        );
        $response->assertSessionHasNoErrors();

        $memberCountAfterFirst = $group->members()->count();
        expect($memberCountAfterFirst)->toBe(1,
            "First membership add should succeed (iteration {$i})"
        );

        // Second add (duplicate): should be rejected
        $response = $this->actingAs($instructor)->post(
            route('guru.courses.groups.members.store', [$course, $group]),
            ['user_id' => $student->id]
        );
        $response->assertSessionHasErrors('user_id');

        // Membership count should remain unchanged
        $memberCountAfterDuplicate = $group->members()->count();
        expect($memberCountAfterDuplicate)->toBe($memberCountAfterFirst,
            "Duplicate membership should be rejected and count unchanged (iteration {$i})"
        );
    }
})->group('property-tests', 'course-groups', 'crud');
