<?php

use App\Models\Course;
use App\Models\CourseGroup;
use App\Models\Enrollment;
use App\Models\Exam;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Exam Visibility Verification Test
|--------------------------------------------------------------------------
|
| This test verifies Property 12: Exams are always visible regardless of
| group membership. The Exam model does NOT have a visibleToStudent scope
| or courseGroups relationship, and the Siswa\ExamController does NOT apply
| any group filtering.
|
| Validates: Requirements 4.5
|
*/

it('Property 12: Exams are visible to all enrolled students regardless of group membership', function () {
    /**
     * Validates: Requirements 4.5
     *
     * For any Exam in a course and any enrolled student, the Exam SHALL be
     * visible regardless of whether the student belongs to any CourseGroup or not.
     *
     * This test creates a course with groups, enrolls students with varying
     * group memberships (including none), and verifies ALL students can query
     * exams using the same logic as the controller (no group filtering applied).
     */
    $instructor = User::factory()->create([
        'role' => 'guru',
        'is_active' => true,
    ]);
    $course = Course::factory()->create(['instructor_id' => $instructor->id]);

    // Create groups in the course
    $groupA = CourseGroup::factory()->create(['course_id' => $course->id]);
    $groupB = CourseGroup::factory()->create(['course_id' => $course->id]);

    // Create published exams in the course (no time constraints for simplicity)
    $exam1 = Exam::factory()->create([
        'course_id' => $course->id,
        'created_by' => $instructor->id,
        'is_published' => true,
        'start_time' => null,
        'end_time' => null,
    ]);
    $exam2 = Exam::factory()->create([
        'course_id' => $course->id,
        'created_by' => $instructor->id,
        'is_published' => true,
        'start_time' => null,
        'end_time' => null,
    ]);

    // Student NOT in any group
    $studentNoGroup = User::factory()->create([
        'role' => 'siswa',
        'is_active' => true,
    ]);
    Enrollment::factory()->create([
        'user_id' => $studentNoGroup->id,
        'course_id' => $course->id,
        'status' => 'active',
    ]);

    // Student in group A only
    $studentGroupA = User::factory()->create([
        'role' => 'siswa',
        'is_active' => true,
    ]);
    Enrollment::factory()->create([
        'user_id' => $studentGroupA->id,
        'course_id' => $course->id,
        'status' => 'active',
    ]);
    $groupA->members()->attach($studentGroupA->id);

    // Student in group B only
    $studentGroupB = User::factory()->create([
        'role' => 'mahasiswa',
        'is_active' => true,
    ]);
    Enrollment::factory()->create([
        'user_id' => $studentGroupB->id,
        'course_id' => $course->id,
        'status' => 'active',
    ]);
    $groupB->members()->attach($studentGroupB->id);

    // Student in both groups
    $studentBothGroups = User::factory()->create([
        'role' => 'siswa',
        'is_active' => true,
    ]);
    Enrollment::factory()->create([
        'user_id' => $studentBothGroups->id,
        'course_id' => $course->id,
        'status' => 'active',
    ]);
    $groupA->members()->attach($studentBothGroups->id);
    $groupB->members()->attach($studentBothGroups->id);

    // Verify: ALL students can query exams using the same logic as the controller
    // (enrolled course IDs + published + active scopes — NO group filtering)
    $students = [$studentNoGroup, $studentGroupA, $studentGroupB, $studentBothGroups];

    foreach ($students as $student) {
        $enrolledCourseIds = $student->enrollments()
            ->where('status', 'active')
            ->pluck('course_id');

        $visibleExams = Exam::whereIn('course_id', $enrolledCourseIds)
            ->published()
            ->active()
            ->pluck('id');

        expect($visibleExams->contains($exam1->id))->toBeTrue(
            "Exam 1 should be visible to student regardless of group membership"
        );
        expect($visibleExams->contains($exam2->id))->toBeTrue(
            "Exam 2 should be visible to student regardless of group membership"
        );
    }
})->group('course-groups', 'exam-visibility');

it('Property 12: Student not in any group can access exam detail page', function () {
    /**
     * Validates: Requirements 4.5
     *
     * A student who is NOT a member of any CourseGroup should still be able
     * to access the exam show page without any 403 error.
     */
    $instructor = User::factory()->create([
        'role' => 'guru',
        'is_active' => true,
    ]);
    $course = Course::factory()->create(['instructor_id' => $instructor->id]);

    // Create groups (student will NOT be in any)
    CourseGroup::factory()->create(['course_id' => $course->id]);
    CourseGroup::factory()->create(['course_id' => $course->id]);

    // Create a published, active exam (null time = always active)
    $exam = Exam::factory()->create([
        'course_id' => $course->id,
        'created_by' => $instructor->id,
        'is_published' => true,
        'start_time' => null,
        'end_time' => null,
    ]);

    // Create an enrolled student NOT in any group
    $student = User::factory()->create([
        'role' => 'siswa',
        'is_active' => true,
    ]);
    Enrollment::factory()->create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'status' => 'active',
    ]);

    // Student should be able to access the exam detail page
    $response = $this->actingAs($student)->get(
        route('siswa.exams.show', $exam)
    );

    $response->assertStatus(200);
})->group('course-groups', 'exam-visibility');

it('Property 12: Exam model does not have visibleToStudent scope or courseGroups relationship', function () {
    /**
     * Validates: Requirements 4.5
     *
     * Structural verification: The Exam model should NOT have a visibleToStudent
     * scope or courseGroups relationship, ensuring the group system cannot
     * accidentally filter exams.
     */
    $exam = new Exam();

    // Verify no visibleToStudent scope exists
    expect(method_exists($exam, 'scopeVisibleToStudent'))->toBeFalse(
        'Exam model should NOT have a visibleToStudent scope'
    );

    // Verify no courseGroups relationship exists
    expect(method_exists($exam, 'courseGroups'))->toBeFalse(
        'Exam model should NOT have a courseGroups relationship'
    );
})->group('course-groups', 'exam-visibility');

it('Property 12: Exams are always visible regardless of group membership [PBT 100 iterations]', function () {
    /**
     * Validates: Requirements 4.5
     *
     * Property-based test with 100 iterations.
     * For any Exam in a course and any enrolled student, the Exam SHALL be
     * visible regardless of whether the student belongs to any CourseGroup or not.
     *
     * Randomizes: number of groups (0-5), number of exams (1-3),
     * student group membership (none, some, all).
     */
    for ($i = 0; $i < 100; $i++) {
        // Setup: course with instructor
        $instructor = User::factory()->create([
            'role' => fake()->randomElement(['guru', 'dosen']),
            'is_active' => true,
        ]);
        $course = Course::factory()->create(['instructor_id' => $instructor->id]);

        // Create a random number of groups (0-5) in the course
        $groupCount = fake()->numberBetween(0, 5);
        $groups = collect();
        for ($j = 0; $j < $groupCount; $j++) {
            $groups->push(CourseGroup::factory()->create(['course_id' => $course->id]));
        }

        // Create random number of published exams (1-3) with null time constraints
        $examCount = fake()->numberBetween(1, 3);
        $exams = collect();
        for ($j = 0; $j < $examCount; $j++) {
            $exams->push(Exam::factory()->create([
                'course_id' => $course->id,
                'created_by' => $instructor->id,
                'is_published' => true,
                'start_time' => null,
                'end_time' => null,
            ]));
        }

        // Create an enrolled student with randomized group membership
        $student = User::factory()->create([
            'role' => fake()->randomElement(['siswa', 'mahasiswa']),
            'is_active' => true,
        ]);
        Enrollment::factory()->create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'status' => 'active',
        ]);

        // Randomize group membership: none, some, or all
        if ($groupCount > 0) {
            $membershipStrategy = fake()->randomElement(['none', 'some', 'all']);
            if ($membershipStrategy === 'some') {
                $memberCount = fake()->numberBetween(1, $groupCount);
                $memberGroups = $groups->random($memberCount);
                foreach ($memberGroups as $group) {
                    $group->members()->attach($student->id);
                }
            } elseif ($membershipStrategy === 'all') {
                foreach ($groups as $group) {
                    $group->members()->attach($student->id);
                }
            }
            // 'none' — student is not added to any group
        }

        // Query exams using the same logic as the Siswa\ExamController
        $enrolledCourseIds = $student->enrollments()
            ->where('status', 'active')
            ->pluck('course_id');

        $visibleExams = Exam::whereIn('course_id', $enrolledCourseIds)
            ->published()
            ->active()
            ->pluck('id');

        // ALL exams should be visible regardless of group membership
        foreach ($exams as $exam) {
            expect($visibleExams->contains($exam->id))->toBeTrue(
                "Exam '{$exam->title}' should be visible to student regardless of group membership (iteration {$i})"
            );
        }
    }
})->group('property-tests', 'course-groups', 'exam-visibility');

it('Property 12: Exam query in controller does not reference course_group tables', function () {
    /**
     * Validates: Requirements 4.5
     *
     * Verify that the exam listing query for students does not join or
     * subquery against any course_group tables.
     */
    $instructor = User::factory()->create([
        'role' => 'guru',
        'is_active' => true,
    ]);
    $course = Course::factory()->create(['instructor_id' => $instructor->id]);

    $student = User::factory()->create([
        'role' => 'siswa',
        'is_active' => true,
    ]);
    Enrollment::factory()->create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'status' => 'active',
    ]);

    // Build the same query the controller uses
    $enrolledCourseIds = $student->enrollments()
        ->where('status', 'active')
        ->pluck('course_id');

    $query = Exam::with(['course', 'creator'])
        ->whereIn('course_id', $enrolledCourseIds)
        ->published()
        ->active();

    $sql = $query->toSql();

    // The SQL should NOT reference any group tables
    expect($sql)->not->toContain('course_group');
    expect($sql)->not->toContain('course_group_members');
    expect($sql)->not->toContain('course_group_content');
})->group('course-groups', 'exam-visibility');
