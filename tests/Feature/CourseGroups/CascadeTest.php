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
| Property-Based Tests for Course Group Cascade Deletion
|--------------------------------------------------------------------------
|
| These tests validate Property 15 from the design document
| using randomized inputs with 100 iterations per property.
|
| Validates: Requirements 7.2
|
*/

it('Property 15: Course deletion cascades all group data', function () {
    /**
     * Validates: Requirements 7.2
     *
     * For any Course that is deleted, all associated CourseGroups, Group_Member records,
     * and Group_Assignment_Pivot records SHALL be cascade-deleted.
     */
    for ($i = 0; $i < 100; $i++) {
        // Setup: create a course with an instructor
        $instructor = User::factory()->create([
            'role' => fake()->randomElement(['guru', 'dosen']),
            'is_active' => true,
        ]);
        $course = Course::factory()->create(['instructor_id' => $instructor->id]);
        $courseId = $course->id;

        // Create random number of groups (1-4)
        $groupCount = fake()->numberBetween(1, 4);
        $groupIds = [];

        for ($g = 0; $g < $groupCount; $g++) {
            $group = CourseGroup::factory()->create(['course_id' => $course->id]);
            $groupIds[] = $group->id;

            // Add random members (1-3) to each group
            $memberCount = fake()->numberBetween(1, 3);
            for ($m = 0; $m < $memberCount; $m++) {
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

            // Add random content associations (0-2 materials, 0-2 assignments)
            $materialCount = fake()->numberBetween(0, 2);
            for ($mc = 0; $mc < $materialCount; $mc++) {
                $material = Material::factory()->create(['course_id' => $course->id]);
                DB::table('course_group_content')->insert([
                    'course_group_id' => $group->id,
                    'contentable_type' => Material::class,
                    'contentable_id' => $material->id,
                    'created_at' => now(),
                ]);
            }

            $assignmentCount = fake()->numberBetween(0, 2);
            for ($ac = 0; $ac < $assignmentCount; $ac++) {
                $assignment = Assignment::factory()->create(['course_id' => $course->id]);
                DB::table('course_group_content')->insert([
                    'course_group_id' => $group->id,
                    'contentable_type' => Assignment::class,
                    'contentable_id' => $assignment->id,
                    'created_at' => now(),
                ]);
            }
        }

        // Also create a DIFFERENT course with its own groups to verify isolation
        $otherInstructor = User::factory()->create([
            'role' => fake()->randomElement(['guru', 'dosen']),
            'is_active' => true,
        ]);
        $otherCourse = Course::factory()->create(['instructor_id' => $otherInstructor->id]);
        $otherGroup = CourseGroup::factory()->create(['course_id' => $otherCourse->id]);
        $otherStudent = User::factory()->create([
            'role' => fake()->randomElement(['siswa', 'mahasiswa']),
            'is_active' => true,
        ]);
        Enrollment::factory()->create([
            'user_id' => $otherStudent->id,
            'course_id' => $otherCourse->id,
            'status' => 'active',
        ]);
        $otherGroup->members()->attach($otherStudent->id);
        $otherMaterial = Material::factory()->create(['course_id' => $otherCourse->id]);
        DB::table('course_group_content')->insert([
            'course_group_id' => $otherGroup->id,
            'contentable_type' => Material::class,
            'contentable_id' => $otherMaterial->id,
            'created_at' => now(),
        ]);

        // Verify data exists before deletion
        expect(DB::table('course_groups')->where('course_id', $courseId)->count())
            ->toBe($groupCount, "Should have {$groupCount} groups before deletion (iteration {$i})");
        expect(DB::table('course_group_members')->whereIn('course_group_id', $groupIds)->count())
            ->toBeGreaterThan(0, "Should have members before deletion (iteration {$i})");

        // Action: delete the course (no SoftDeletes on Course model, so delete() triggers DB cascade)
        $course->delete();

        // Assert: zero CourseGroup records for that course
        expect(DB::table('course_groups')->where('course_id', $courseId)->count())
            ->toBe(0, "All course groups should be cascade-deleted (iteration {$i})");

        // Assert: zero course_group_members for those groups
        expect(DB::table('course_group_members')->whereIn('course_group_id', $groupIds)->count())
            ->toBe(0, "All group members should be cascade-deleted (iteration {$i})");

        // Assert: zero course_group_content for those groups
        expect(DB::table('course_group_content')->whereIn('course_group_id', $groupIds)->count())
            ->toBe(0, "All group content associations should be cascade-deleted (iteration {$i})");

        // Assert: other course's groups/members/content are NOT affected
        expect(DB::table('course_groups')->where('course_id', $otherCourse->id)->count())
            ->toBe(1, "Other course's groups should NOT be affected (iteration {$i})");
        expect(DB::table('course_group_members')->where('course_group_id', $otherGroup->id)->count())
            ->toBe(1, "Other course's group members should NOT be affected (iteration {$i})");
        expect(DB::table('course_group_content')->where('course_group_id', $otherGroup->id)->count())
            ->toBe(1, "Other course's group content should NOT be affected (iteration {$i})");
    }
})->group('property-tests', 'course-groups', 'cascade');
