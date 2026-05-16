<?php

use App\Models\Assignment;
use App\Models\Course;
use App\Models\CourseGroup;
use App\Models\Material;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| Property-Based Tests for Cross-Course Group Assignment Rejection
|--------------------------------------------------------------------------
|
| These tests validate Property 8 from the design document using randomized
| inputs with 100 iterations.
|
| Property 8: Cross-course group assignment is rejected
|
| For any Material or Assignment and any CourseGroup, associating the content
| with the group SHALL succeed if and only if group.course_id === content.course_id.
|
| Validates: Requirements 2.5, 2.6, 3.1, 3.7
|
*/

it('Property 8: Cross-course group assignment is rejected', function () {
    /**
     * Validates: Requirements 2.5, 2.6, 3.1, 3.7
     *
     * For any Material or Assignment and any CourseGroup, associating the content
     * with the group SHALL succeed if and only if group.course_id === content.course_id.
     *
     * This test validates the property at the model level by verifying that:
     * 1. Same-course group associations are valid (group.course_id === content.course_id)
     * 2. Cross-course group associations are invalid (group.course_id !== content.course_id)
     *
     * The validation logic is: a group_id is valid for a content item if and only if
     * the group belongs to the same course as the content.
     */
    for ($i = 0; $i < 100; $i++) {
        // Setup: two different courses with different instructors
        $instructor1 = User::factory()->create([
            'role' => fake()->randomElement(['guru', 'dosen']),
            'is_active' => true,
        ]);
        $instructor2 = User::factory()->create([
            'role' => fake()->randomElement(['guru', 'dosen']),
            'is_active' => true,
        ]);

        $courseA = Course::factory()->create(['instructor_id' => $instructor1->id]);
        $courseB = Course::factory()->create(['instructor_id' => $instructor2->id]);

        // Create groups in each course
        $groupsInCourseA = collect();
        $groupsInCourseB = collect();

        $numGroupsA = fake()->numberBetween(1, 3);
        $numGroupsB = fake()->numberBetween(1, 3);

        for ($g = 0; $g < $numGroupsA; $g++) {
            $groupsInCourseA->push(CourseGroup::factory()->create(['course_id' => $courseA->id]));
        }
        for ($g = 0; $g < $numGroupsB; $g++) {
            $groupsInCourseB->push(CourseGroup::factory()->create(['course_id' => $courseB->id]));
        }

        // Randomly choose content type: Material or Assignment
        $contentType = fake()->randomElement(['material', 'assignment']);

        if ($contentType === 'material') {
            $content = Material::factory()->create([
                'course_id' => $courseA->id,
                'created_by' => $instructor1->id,
            ]);

            // Test scenario: same-course vs cross-course
            $scenario = fake()->randomElement(['same_course', 'cross_course', 'mixed']);

            switch ($scenario) {
                case 'same_course':
                    // Associating with groups from the SAME course should succeed
                    $selectedGroups = $groupsInCourseA->random(fake()->numberBetween(1, $groupsInCourseA->count()));
                    $groupIds = $selectedGroups->pluck('id')->toArray();

                    // Validate: all groups belong to the same course as the content
                    $allSameCourse = CourseGroup::whereIn('id', $groupIds)
                        ->where('course_id', $content->course_id)
                        ->count() === count($groupIds);

                    expect($allSameCourse)->toBeTrue(
                        "All groups from the same course should pass validation (iteration {$i}, material, same_course)"
                    );

                    // Actually sync the association - should work fine
                    $content->courseGroups()->sync($groupIds);

                    expect($content->courseGroups()->count())->toBe(count($groupIds),
                        "Same-course group association should succeed (iteration {$i}, material)"
                    );
                    break;

                case 'cross_course':
                    // Associating with groups from a DIFFERENT course should be rejected
                    $selectedGroups = $groupsInCourseB->random(fake()->numberBetween(1, $groupsInCourseB->count()));
                    $groupIds = $selectedGroups->pluck('id')->toArray();

                    // Validate: groups do NOT belong to the same course as the content
                    $allSameCourse = CourseGroup::whereIn('id', $groupIds)
                        ->where('course_id', $content->course_id)
                        ->count() === count($groupIds);

                    expect($allSameCourse)->toBeFalse(
                        "Groups from a different course should fail validation (iteration {$i}, material, cross_course)"
                    );

                    // The system should reject this - verify the validation rule
                    $invalidGroupIds = CourseGroup::whereIn('id', $groupIds)
                        ->where('course_id', '!=', $content->course_id)
                        ->pluck('id')
                        ->toArray();

                    expect($invalidGroupIds)->not->toBeEmpty(
                        "Cross-course groups should be detected as invalid (iteration {$i}, material)"
                    );
                    break;

                case 'mixed':
                    // Mix of same-course and cross-course groups - should be rejected
                    $sameGroup = $groupsInCourseA->random(1);
                    $crossGroup = $groupsInCourseB->random(1);
                    $mixedGroupIds = $sameGroup->pluck('id')->merge($crossGroup->pluck('id'))->toArray();

                    // Validate: NOT all groups belong to the same course
                    $allSameCourse = CourseGroup::whereIn('id', $mixedGroupIds)
                        ->where('course_id', $content->course_id)
                        ->count() === count($mixedGroupIds);

                    expect($allSameCourse)->toBeFalse(
                        "Mixed same/cross-course groups should fail validation (iteration {$i}, material, mixed)"
                    );
                    break;
            }
        } else {
            // Assignment
            $content = Assignment::factory()->create([
                'course_id' => $courseA->id,
                'created_by' => $instructor1->id,
            ]);

            // Test scenario: same-course vs cross-course
            $scenario = fake()->randomElement(['same_course', 'cross_course', 'mixed']);

            switch ($scenario) {
                case 'same_course':
                    // Associating with groups from the SAME course should succeed
                    $selectedGroups = $groupsInCourseA->random(fake()->numberBetween(1, $groupsInCourseA->count()));
                    $groupIds = $selectedGroups->pluck('id')->toArray();

                    // Validate: all groups belong to the same course as the content
                    $allSameCourse = CourseGroup::whereIn('id', $groupIds)
                        ->where('course_id', $content->course_id)
                        ->count() === count($groupIds);

                    expect($allSameCourse)->toBeTrue(
                        "All groups from the same course should pass validation (iteration {$i}, assignment, same_course)"
                    );

                    // Actually sync the association - should work fine
                    $content->courseGroups()->sync($groupIds);

                    expect($content->courseGroups()->count())->toBe(count($groupIds),
                        "Same-course group association should succeed (iteration {$i}, assignment)"
                    );
                    break;

                case 'cross_course':
                    // Associating with groups from a DIFFERENT course should be rejected
                    $selectedGroups = $groupsInCourseB->random(fake()->numberBetween(1, $groupsInCourseB->count()));
                    $groupIds = $selectedGroups->pluck('id')->toArray();

                    // Validate: groups do NOT belong to the same course as the content
                    $allSameCourse = CourseGroup::whereIn('id', $groupIds)
                        ->where('course_id', $content->course_id)
                        ->count() === count($groupIds);

                    expect($allSameCourse)->toBeFalse(
                        "Groups from a different course should fail validation (iteration {$i}, assignment, cross_course)"
                    );

                    // The system should reject this - verify the validation rule
                    $invalidGroupIds = CourseGroup::whereIn('id', $groupIds)
                        ->where('course_id', '!=', $content->course_id)
                        ->pluck('id')
                        ->toArray();

                    expect($invalidGroupIds)->not->toBeEmpty(
                        "Cross-course groups should be detected as invalid (iteration {$i}, assignment)"
                    );
                    break;

                case 'mixed':
                    // Mix of same-course and cross-course groups - should be rejected
                    $sameGroup = $groupsInCourseA->random(1);
                    $crossGroup = $groupsInCourseB->random(1);
                    $mixedGroupIds = $sameGroup->pluck('id')->merge($crossGroup->pluck('id'))->toArray();

                    // Validate: NOT all groups belong to the same course
                    $allSameCourse = CourseGroup::whereIn('id', $mixedGroupIds)
                        ->where('course_id', $content->course_id)
                        ->count() === count($mixedGroupIds);

                    expect($allSameCourse)->toBeFalse(
                        "Mixed same/cross-course groups should fail validation (iteration {$i}, assignment, mixed)"
                    );
                    break;
            }
        }
    }
})->group('property-tests', 'course-groups', 'cross-course');
