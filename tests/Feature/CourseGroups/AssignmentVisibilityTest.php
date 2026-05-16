<?php

use App\Models\Assignment;
use App\Models\Course;
use App\Models\CourseGroup;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| Property-Based Tests for Assignment Visibility
|--------------------------------------------------------------------------
|
| These tests validate Property 7 from the design document using randomized
| inputs with 100 iterations.
|
| Property 7: Assignment visibility respects group targeting
|
| For any Assignment in a course and any enrolled student: if the Assignment
| has no group associations, it SHALL be visible to the student; if the
| Assignment has one or more group associations, it SHALL be visible to the
| student if and only if the student is a member of at least one of those
| associated groups.
|
| Validates: Requirements 3.2, 3.3, 4.2
|
*/

it('Property 7: Assignment visibility respects group targeting', function () {
    /**
     * Validates: Requirements 3.2, 3.3, 4.2
     *
     * For any Assignment in a course and any enrolled student: if the Assignment has no
     * group associations, it SHALL be visible to the student; if the Assignment has one
     * or more group associations, it SHALL be visible to the student if and only if the
     * student is a member of at least one of those associated groups.
     */
    for ($i = 0; $i < 100; $i++) {
        // Setup: course with instructor
        $instructor = User::factory()->create([
            'role' => fake()->randomElement(['guru', 'dosen']),
            'is_active' => true,
        ]);
        $course = Course::factory()->create(['instructor_id' => $instructor->id]);

        // Create enrolled student
        $student = User::factory()->create([
            'role' => fake()->randomElement(['siswa', 'mahasiswa']),
            'is_active' => true,
        ]);
        Enrollment::factory()->create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'status' => 'active',
        ]);

        // Generate a random test scenario
        $scenario = fake()->randomElement([
            'ungrouped_visible_to_all',
            'targeted_visible_to_member',
            'targeted_invisible_to_non_member',
            'student_in_no_groups_sees_only_ungrouped',
            'student_in_multiple_groups_sees_all_their_assignments',
            'assignment_in_multiple_groups_visible_to_any_member',
        ]);

        switch ($scenario) {
            case 'ungrouped_visible_to_all':
                // An assignment with no group associations should be visible to ALL enrolled students
                $assignment = Assignment::factory()->create(['course_id' => $course->id]);

                $visibleAssignments = Assignment::query()
                    ->where('course_id', $course->id)
                    ->visibleToStudent($student)
                    ->get();

                expect($visibleAssignments->contains('id', $assignment->id))
                    ->toBeTrue("Ungrouped assignment should be visible to any enrolled student (iteration {$i})");
                break;

            case 'targeted_visible_to_member':
                // An assignment targeted to a group should be visible to a member of that group
                $group = CourseGroup::factory()->create(['course_id' => $course->id]);
                $group->members()->attach($student->id);

                $assignment = Assignment::factory()->create(['course_id' => $course->id]);
                DB::table('course_group_content')->insert([
                    'course_group_id' => $group->id,
                    'contentable_type' => 'App\\Models\\Assignment',
                    'contentable_id' => $assignment->id,
                    'created_at' => now(),
                ]);

                $visibleAssignments = Assignment::query()
                    ->where('course_id', $course->id)
                    ->visibleToStudent($student)
                    ->get();

                expect($visibleAssignments->contains('id', $assignment->id))
                    ->toBeTrue("Targeted assignment should be visible to group member (iteration {$i})");
                break;

            case 'targeted_invisible_to_non_member':
                // An assignment targeted to a group should NOT be visible to a non-member
                $group = CourseGroup::factory()->create(['course_id' => $course->id]);
                // Student is NOT added to the group

                $assignment = Assignment::factory()->create(['course_id' => $course->id]);
                DB::table('course_group_content')->insert([
                    'course_group_id' => $group->id,
                    'contentable_type' => 'App\\Models\\Assignment',
                    'contentable_id' => $assignment->id,
                    'created_at' => now(),
                ]);

                $visibleAssignments = Assignment::query()
                    ->where('course_id', $course->id)
                    ->visibleToStudent($student)
                    ->get();

                expect($visibleAssignments->contains('id', $assignment->id))
                    ->toBeFalse("Targeted assignment should NOT be visible to non-member (iteration {$i})");
                break;

            case 'student_in_no_groups_sees_only_ungrouped':
                // A student in no groups should see only ungrouped assignments
                $group = CourseGroup::factory()->create(['course_id' => $course->id]);

                $ungroupedAssignment = Assignment::factory()->create(['course_id' => $course->id]);
                $targetedAssignment = Assignment::factory()->create(['course_id' => $course->id]);

                DB::table('course_group_content')->insert([
                    'course_group_id' => $group->id,
                    'contentable_type' => 'App\\Models\\Assignment',
                    'contentable_id' => $targetedAssignment->id,
                    'created_at' => now(),
                ]);

                $visibleAssignments = Assignment::query()
                    ->where('course_id', $course->id)
                    ->visibleToStudent($student)
                    ->get();

                expect($visibleAssignments->contains('id', $ungroupedAssignment->id))
                    ->toBeTrue("Student in no groups should see ungrouped assignment (iteration {$i})");
                expect($visibleAssignments->contains('id', $targetedAssignment->id))
                    ->toBeFalse("Student in no groups should NOT see targeted assignment (iteration {$i})");
                break;

            case 'student_in_multiple_groups_sees_all_their_assignments':
                // A student in multiple groups should see assignments from ALL their groups
                $groupCount = fake()->numberBetween(2, 4);
                $groups = [];
                for ($g = 0; $g < $groupCount; $g++) {
                    $group = CourseGroup::factory()->create(['course_id' => $course->id]);
                    $group->members()->attach($student->id);
                    $groups[] = $group;
                }

                // Create one assignment per group
                $assignments = [];
                foreach ($groups as $group) {
                    $assignment = Assignment::factory()->create(['course_id' => $course->id]);
                    DB::table('course_group_content')->insert([
                        'course_group_id' => $group->id,
                        'contentable_type' => 'App\\Models\\Assignment',
                        'contentable_id' => $assignment->id,
                        'created_at' => now(),
                    ]);
                    $assignments[] = $assignment;
                }

                $visibleAssignments = Assignment::query()
                    ->where('course_id', $course->id)
                    ->visibleToStudent($student)
                    ->get();

                foreach ($assignments as $idx => $assignment) {
                    expect($visibleAssignments->contains('id', $assignment->id))
                        ->toBeTrue("Student in multiple groups should see assignment from group {$idx} (iteration {$i})");
                }
                break;

            case 'assignment_in_multiple_groups_visible_to_any_member':
                // An assignment associated with multiple groups should be visible to a member of ANY of those groups
                $groupCount = fake()->numberBetween(2, 4);
                $groups = [];
                for ($g = 0; $g < $groupCount; $g++) {
                    $groups[] = CourseGroup::factory()->create(['course_id' => $course->id]);
                }

                // Student is member of only ONE of the groups (randomly chosen)
                $memberGroupIndex = fake()->numberBetween(0, $groupCount - 1);
                $groups[$memberGroupIndex]->members()->attach($student->id);

                // Assignment is associated with ALL groups
                $assignment = Assignment::factory()->create(['course_id' => $course->id]);
                foreach ($groups as $group) {
                    DB::table('course_group_content')->insert([
                        'course_group_id' => $group->id,
                        'contentable_type' => 'App\\Models\\Assignment',
                        'contentable_id' => $assignment->id,
                        'created_at' => now(),
                    ]);
                }

                $visibleAssignments = Assignment::query()
                    ->where('course_id', $course->id)
                    ->visibleToStudent($student)
                    ->get();

                expect($visibleAssignments->contains('id', $assignment->id))
                    ->toBeTrue("Assignment in multiple groups should be visible to member of any one group (iteration {$i})");
                break;
        }
    }
})->group('property-tests', 'course-groups', 'assignment-visibility');

/*
|--------------------------------------------------------------------------
| Property 10: Changing Assignment group associations preserves existing submissions
|--------------------------------------------------------------------------
|
| For any Assignment with existing submissions, modifying or removing group
| associations SHALL not delete, alter, or affect any existing
| AssignmentSubmission records.
|
| Validates: Requirements 3.5, 3.6
|
*/

it('Property 10: Changing Assignment group associations preserves existing submissions', function () {
    /**
     * Validates: Requirements 3.5, 3.6
     *
     * For any Assignment with existing submissions, modifying or removing group
     * associations SHALL not delete, alter, or affect any existing AssignmentSubmission records.
     */
    for ($i = 0; $i < 100; $i++) {
        // Setup: course with instructor
        $instructor = User::factory()->create([
            'role' => fake()->randomElement(['guru', 'dosen']),
            'is_active' => true,
        ]);
        $course = Course::factory()->create(['instructor_id' => $instructor->id]);

        // Create groups for the course
        $groupCount = fake()->numberBetween(2, 5);
        $groups = [];
        for ($g = 0; $g < $groupCount; $g++) {
            $groups[] = CourseGroup::factory()->create(['course_id' => $course->id]);
        }

        // Create assignment and associate with some initial groups
        $assignment = Assignment::factory()->create(['course_id' => $course->id]);
        $initialGroupIds = fake()->randomElements(
            array_map(fn ($g) => $g->id, $groups),
            fake()->numberBetween(1, min(3, $groupCount))
        );
        $assignment->courseGroups()->sync($initialGroupIds);

        // Create enrolled students and submissions
        $submissionCount = fake()->numberBetween(1, 5);
        $submissions = [];
        for ($s = 0; $s < $submissionCount; $s++) {
            $student = User::factory()->create([
                'role' => fake()->randomElement(['siswa', 'mahasiswa']),
                'is_active' => true,
            ]);
            Enrollment::factory()->create([
                'user_id' => $student->id,
                'course_id' => $course->id,
                'status' => 'active',
            ]);

            // Add student to one of the initial groups
            $memberGroup = $groups[array_rand($initialGroupIds)];
            if (!$memberGroup->members()->where('user_id', $student->id)->exists()) {
                $memberGroup->members()->attach($student->id);
            }

            // Create submission manually (no factory available)
            $submission = \App\Models\AssignmentSubmission::create([
                'assignment_id' => $assignment->id,
                'user_id' => $student->id,
                'file_path' => 'submissions/' . fake()->uuid() . '.pdf',
                'file_name' => fake()->word() . '.pdf',
                'file_size' => fake()->numberBetween(1024, 10485760),
                'status' => fake()->randomElement(['submitted', 'late', 'graded']),
                'score' => fake()->optional(0.5)->numberBetween(0, 100),
                'final_score' => fake()->optional(0.5)->randomFloat(2, 0, 100),
                'feedback' => fake()->optional(0.3)->sentence(),
                'penalty_applied' => fake()->optional(0.2)->numberBetween(0, 50),
                'revision_count' => fake()->numberBetween(0, 3),
                'submitted_at' => now()->subDays(fake()->numberBetween(1, 7)),
                'graded_at' => fake()->optional(0.4)->dateTimeBetween('-3 days', 'now'),
            ]);
            $submissions[] = $submission;
        }

        // Snapshot submissions before group change
        $submissionsBefore = \App\Models\AssignmentSubmission::where('assignment_id', $assignment->id)
            ->orderBy('id')
            ->get()
            ->toArray();
        $countBefore = count($submissionsBefore);

        // Choose a random group change scenario
        $scenario = fake()->randomElement([
            'add_new_groups',
            'remove_some_groups',
            'remove_all_groups',
            'change_to_different_groups',
        ]);

        switch ($scenario) {
            case 'add_new_groups':
                // Add additional groups to the assignment
                $allGroupIds = array_map(fn ($g) => $g->id, $groups);
                $newGroupIds = array_unique(array_merge($initialGroupIds, $allGroupIds));
                $assignment->courseGroups()->sync($newGroupIds);
                break;

            case 'remove_some_groups':
                // Remove some groups (keep at least one)
                if (count($initialGroupIds) > 1) {
                    $keepCount = fake()->numberBetween(1, count($initialGroupIds) - 1);
                    $keptGroupIds = array_slice($initialGroupIds, 0, $keepCount);
                    $assignment->courseGroups()->sync($keptGroupIds);
                } else {
                    // Only one group, just re-sync with same
                    $assignment->courseGroups()->sync($initialGroupIds);
                }
                break;

            case 'remove_all_groups':
                // Remove all group associations (revert to ungrouped)
                $assignment->courseGroups()->sync([]);
                break;

            case 'change_to_different_groups':
                // Change to a completely different set of groups
                $availableGroupIds = array_map(fn ($g) => $g->id, $groups);
                $differentGroupIds = array_diff($availableGroupIds, $initialGroupIds);
                if (!empty($differentGroupIds)) {
                    $newGroupIds = fake()->randomElements(
                        array_values($differentGroupIds),
                        fake()->numberBetween(1, min(3, count($differentGroupIds)))
                    );
                    $assignment->courseGroups()->sync($newGroupIds);
                } else {
                    // All groups were already assigned, just sync with a subset
                    $assignment->courseGroups()->sync([array_values($availableGroupIds)[0]]);
                }
                break;
        }

        // Assert: all existing submissions remain intact
        $submissionsAfter = \App\Models\AssignmentSubmission::where('assignment_id', $assignment->id)
            ->orderBy('id')
            ->get()
            ->toArray();
        $countAfter = count($submissionsAfter);

        // Count must be unchanged
        expect($countAfter)->toBe(
            $countBefore,
            "Submission count changed after '{$scenario}' group change (iteration {$i}): was {$countBefore}, now {$countAfter}"
        );

        // Each submission's data must be unchanged
        foreach ($submissionsBefore as $idx => $before) {
            $after = $submissionsAfter[$idx];

            expect($after['id'])->toBe($before['id'],
                "Submission ID mismatch after '{$scenario}' (iteration {$i})");
            expect($after['assignment_id'])->toBe($before['assignment_id'],
                "Submission assignment_id changed after '{$scenario}' (iteration {$i})");
            expect($after['user_id'])->toBe($before['user_id'],
                "Submission user_id changed after '{$scenario}' (iteration {$i})");
            expect($after['file_path'])->toBe($before['file_path'],
                "Submission file_path changed after '{$scenario}' (iteration {$i})");
            expect($after['file_name'])->toBe($before['file_name'],
                "Submission file_name changed after '{$scenario}' (iteration {$i})");
            expect($after['file_size'])->toBe($before['file_size'],
                "Submission file_size changed after '{$scenario}' (iteration {$i})");
            expect($after['status'])->toBe($before['status'],
                "Submission status changed after '{$scenario}' (iteration {$i})");
            expect($after['score'])->toBe($before['score'],
                "Submission score changed after '{$scenario}' (iteration {$i})");
            expect($after['feedback'])->toBe($before['feedback'],
                "Submission feedback changed after '{$scenario}' (iteration {$i})");
            expect($after['penalty_applied'])->toBe($before['penalty_applied'],
                "Submission penalty_applied changed after '{$scenario}' (iteration {$i})");
            expect($after['revision_count'])->toBe($before['revision_count'],
                "Submission revision_count changed after '{$scenario}' (iteration {$i})");
        }
    }
})->group('property-tests', 'course-groups', 'assignment-visibility', 'assignment-submissions');
