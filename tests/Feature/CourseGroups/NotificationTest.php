<?php

use App\Models\Assignment;
use App\Models\Course;
use App\Models\CourseGroup;
use App\Models\Enrollment;
use App\Models\Material;
use App\Models\User;
use App\Services\GroupTargetedNotificationService;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| Property-Based Tests for Notification Recipients
|--------------------------------------------------------------------------
|
| These tests validate Property 13: Notification recipients match group targeting
| using randomized inputs with 100 iterations per property.
|
| Validates: Requirements 6.1, 6.2, 6.3, 6.4
|
*/

it('Property 13: Ungrouped material → recipients = all actively enrolled students', function () {
    /**
     * Validates: Requirements 6.3
     *
     * For any Material that has no group associations, notifications SHALL be sent
     * to all users with active enrollment in the course.
     */
    $service = new GroupTargetedNotificationService();

    for ($i = 0; $i < 100; $i++) {
        // Setup: course with instructor
        $instructor = User::factory()->create([
            'role' => fake()->randomElement(['guru', 'dosen']),
            'is_active' => true,
        ]);
        $course = Course::factory()->create(['instructor_id' => $instructor->id]);

        // Create an ungrouped material (no group associations)
        $material = Material::factory()->create([
            'course_id' => $course->id,
            'is_published' => true,
        ]);

        // Create actively enrolled students (1-5)
        $activeStudentCount = fake()->numberBetween(1, 5);
        $activeStudents = collect();
        for ($j = 0; $j < $activeStudentCount; $j++) {
            $student = User::factory()->create([
                'role' => fake()->randomElement(['siswa', 'mahasiswa']),
                'is_active' => true,
            ]);
            Enrollment::factory()->create([
                'user_id' => $student->id,
                'course_id' => $course->id,
                'status' => 'active',
            ]);
            $activeStudents->push($student);
        }

        // Optionally create some groups (but NOT associated with this material)
        $groupCount = fake()->numberBetween(0, 2);
        for ($j = 0; $j < $groupCount; $j++) {
            $group = CourseGroup::factory()->create(['course_id' => $course->id]);
            // Optionally add some students to groups
            if (fake()->boolean() && $activeStudents->isNotEmpty()) {
                $group->members()->attach($activeStudents->random()->id);
            }
        }

        // Get recipients from service
        $recipients = $service->getRecipientsForMaterial($material);
        $recipientIds = $recipients->pluck('id')->sort()->values();
        $expectedIds = $activeStudents->pluck('id')->sort()->values();

        expect($recipientIds->toArray())->toBe($expectedIds->toArray(),
            "Ungrouped material should notify ALL actively enrolled students (iteration {$i})"
        );
    }
})->group('property-tests', 'course-groups', 'notifications');

it('Property 13: Grouped material → recipients = only group members with active enrollment', function () {
    /**
     * Validates: Requirements 6.1
     *
     * For any Material that has group associations, notifications SHALL be sent
     * to exactly the set of users who have active enrollment AND are members of
     * at least one associated group.
     */
    $service = new GroupTargetedNotificationService();

    for ($i = 0; $i < 100; $i++) {
        // Setup: course with instructor
        $instructor = User::factory()->create([
            'role' => fake()->randomElement(['guru', 'dosen']),
            'is_active' => true,
        ]);
        $course = Course::factory()->create(['instructor_id' => $instructor->id]);

        // Create groups (1-3)
        $groupCount = fake()->numberBetween(1, 3);
        $groups = collect();
        for ($j = 0; $j < $groupCount; $j++) {
            $groups->push(CourseGroup::factory()->create(['course_id' => $course->id]));
        }

        // Create a material targeted to these groups
        $material = Material::factory()->create([
            'course_id' => $course->id,
            'is_published' => true,
        ]);
        foreach ($groups as $group) {
            DB::table('course_group_content')->insert([
                'course_group_id' => $group->id,
                'contentable_type' => Material::class,
                'contentable_id' => $material->id,
                'created_at' => now(),
            ]);
        }

        // Create students who ARE members of at least one associated group
        $memberCount = fake()->numberBetween(1, 3);
        $memberStudents = collect();
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
            // Add to a random associated group
            $groups->random()->members()->attach($student->id);
            $memberStudents->push($student);
        }

        // Create students who are NOT members of any associated group
        $nonMemberCount = fake()->numberBetween(1, 3);
        for ($j = 0; $j < $nonMemberCount; $j++) {
            $student = User::factory()->create([
                'role' => fake()->randomElement(['siswa', 'mahasiswa']),
                'is_active' => true,
            ]);
            Enrollment::factory()->create([
                'user_id' => $student->id,
                'course_id' => $course->id,
                'status' => 'active',
            ]);
        }

        // Get recipients from service
        $recipients = $service->getRecipientsForMaterial($material);
        $recipientIds = $recipients->pluck('id')->sort()->values();
        $expectedIds = $memberStudents->pluck('id')->sort()->values();

        expect($recipientIds->toArray())->toBe($expectedIds->toArray(),
            "Grouped material should notify ONLY group members with active enrollment (iteration {$i})"
        );
    }
})->group('property-tests', 'course-groups', 'notifications');

it('Property 13: Ungrouped assignment → recipients = all actively enrolled students', function () {
    /**
     * Validates: Requirements 6.4
     *
     * For any Assignment that has no group associations, notifications SHALL be sent
     * to all users with active enrollment in the course.
     */
    $service = new GroupTargetedNotificationService();

    for ($i = 0; $i < 100; $i++) {
        // Setup: course with instructor
        $instructor = User::factory()->create([
            'role' => fake()->randomElement(['guru', 'dosen']),
            'is_active' => true,
        ]);
        $course = Course::factory()->create(['instructor_id' => $instructor->id]);

        // Create an ungrouped assignment (no group associations)
        $assignment = Assignment::factory()->create([
            'course_id' => $course->id,
            'is_published' => true,
        ]);

        // Create actively enrolled students (1-5)
        $activeStudentCount = fake()->numberBetween(1, 5);
        $activeStudents = collect();
        for ($j = 0; $j < $activeStudentCount; $j++) {
            $student = User::factory()->create([
                'role' => fake()->randomElement(['siswa', 'mahasiswa']),
                'is_active' => true,
            ]);
            Enrollment::factory()->create([
                'user_id' => $student->id,
                'course_id' => $course->id,
                'status' => 'active',
            ]);
            $activeStudents->push($student);
        }

        // Optionally create some groups (but NOT associated with this assignment)
        $groupCount = fake()->numberBetween(0, 2);
        for ($j = 0; $j < $groupCount; $j++) {
            $group = CourseGroup::factory()->create(['course_id' => $course->id]);
            if (fake()->boolean() && $activeStudents->isNotEmpty()) {
                $group->members()->attach($activeStudents->random()->id);
            }
        }

        // Get recipients from service
        $recipients = $service->getRecipientsForAssignment($assignment);
        $recipientIds = $recipients->pluck('id')->sort()->values();
        $expectedIds = $activeStudents->pluck('id')->sort()->values();

        expect($recipientIds->toArray())->toBe($expectedIds->toArray(),
            "Ungrouped assignment should notify ALL actively enrolled students (iteration {$i})"
        );
    }
})->group('property-tests', 'course-groups', 'notifications');

it('Property 13: Grouped assignment → recipients = only group members with active enrollment', function () {
    /**
     * Validates: Requirements 6.2
     *
     * For any Assignment that has group associations, notifications SHALL be sent
     * to exactly the set of users who have active enrollment AND are members of
     * at least one associated group.
     */
    $service = new GroupTargetedNotificationService();

    for ($i = 0; $i < 100; $i++) {
        // Setup: course with instructor
        $instructor = User::factory()->create([
            'role' => fake()->randomElement(['guru', 'dosen']),
            'is_active' => true,
        ]);
        $course = Course::factory()->create(['instructor_id' => $instructor->id]);

        // Create groups (1-3)
        $groupCount = fake()->numberBetween(1, 3);
        $groups = collect();
        for ($j = 0; $j < $groupCount; $j++) {
            $groups->push(CourseGroup::factory()->create(['course_id' => $course->id]));
        }

        // Create an assignment targeted to these groups
        $assignment = Assignment::factory()->create([
            'course_id' => $course->id,
            'is_published' => true,
        ]);
        foreach ($groups as $group) {
            DB::table('course_group_content')->insert([
                'course_group_id' => $group->id,
                'contentable_type' => Assignment::class,
                'contentable_id' => $assignment->id,
                'created_at' => now(),
            ]);
        }

        // Create students who ARE members of at least one associated group
        $memberCount = fake()->numberBetween(1, 3);
        $memberStudents = collect();
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
            // Add to a random associated group
            $groups->random()->members()->attach($student->id);
            $memberStudents->push($student);
        }

        // Create students who are NOT members of any associated group
        $nonMemberCount = fake()->numberBetween(1, 3);
        for ($j = 0; $j < $nonMemberCount; $j++) {
            $student = User::factory()->create([
                'role' => fake()->randomElement(['siswa', 'mahasiswa']),
                'is_active' => true,
            ]);
            Enrollment::factory()->create([
                'user_id' => $student->id,
                'course_id' => $course->id,
                'status' => 'active',
            ]);
        }

        // Get recipients from service
        $recipients = $service->getRecipientsForAssignment($assignment);
        $recipientIds = $recipients->pluck('id')->sort()->values();
        $expectedIds = $memberStudents->pluck('id')->sort()->values();

        expect($recipientIds->toArray())->toBe($expectedIds->toArray(),
            "Grouped assignment should notify ONLY group members with active enrollment (iteration {$i})"
        );
    }
})->group('property-tests', 'course-groups', 'notifications');

it('Property 13: Student with dropped enrollment is NOT included even if group member', function () {
    /**
     * Validates: Requirements 6.1, 6.2
     *
     * For any content (Material or Assignment) with group associations,
     * a student who is a group member but has a dropped enrollment
     * SHALL NOT receive notifications.
     */
    $service = new GroupTargetedNotificationService();

    for ($i = 0; $i < 100; $i++) {
        // Setup: course with instructor
        $instructor = User::factory()->create([
            'role' => fake()->randomElement(['guru', 'dosen']),
            'is_active' => true,
        ]);
        $course = Course::factory()->create(['instructor_id' => $instructor->id]);

        // Create a group
        $group = CourseGroup::factory()->create(['course_id' => $course->id]);

        // Randomly test with Material or Assignment
        $useMaterial = fake()->boolean();

        if ($useMaterial) {
            $content = Material::factory()->create([
                'course_id' => $course->id,
                'is_published' => true,
            ]);
            DB::table('course_group_content')->insert([
                'course_group_id' => $group->id,
                'contentable_type' => Material::class,
                'contentable_id' => $content->id,
                'created_at' => now(),
            ]);
        } else {
            $content = Assignment::factory()->create([
                'course_id' => $course->id,
                'is_published' => true,
            ]);
            DB::table('course_group_content')->insert([
                'course_group_id' => $group->id,
                'contentable_type' => Assignment::class,
                'contentable_id' => $content->id,
                'created_at' => now(),
            ]);
        }

        // Create a student with active enrollment and group membership
        $activeStudent = User::factory()->create([
            'role' => fake()->randomElement(['siswa', 'mahasiswa']),
            'is_active' => true,
        ]);
        Enrollment::factory()->create([
            'user_id' => $activeStudent->id,
            'course_id' => $course->id,
            'status' => 'active',
        ]);
        $group->members()->attach($activeStudent->id);

        // Create a student with DROPPED enrollment but still a group member
        $droppedStudent = User::factory()->create([
            'role' => fake()->randomElement(['siswa', 'mahasiswa']),
            'is_active' => true,
        ]);
        Enrollment::factory()->create([
            'user_id' => $droppedStudent->id,
            'course_id' => $course->id,
            'status' => 'dropped',
        ]);
        $group->members()->attach($droppedStudent->id);

        // Get recipients from service
        $recipients = $useMaterial
            ? $service->getRecipientsForMaterial($content)
            : $service->getRecipientsForAssignment($content);

        $recipientIds = $recipients->pluck('id');

        // Active student should be included
        expect($recipientIds->contains($activeStudent->id))->toBeTrue(
            "Active enrolled group member should receive notification (iteration {$i})"
        );

        // Dropped student should NOT be included
        expect($recipientIds->contains($droppedStudent->id))->toBeFalse(
            "Dropped enrollment student should NOT receive notification even if group member (iteration {$i})"
        );
    }
})->group('property-tests', 'course-groups', 'notifications');

it('Property 13: Student in multiple associated groups is included only once (no duplicates)', function () {
    /**
     * Validates: Requirements 6.1, 6.2
     *
     * For any content associated with multiple groups, a student who is a member
     * of more than one associated group SHALL appear only once in the recipients list.
     */
    $service = new GroupTargetedNotificationService();

    for ($i = 0; $i < 100; $i++) {
        // Setup: course with instructor
        $instructor = User::factory()->create([
            'role' => fake()->randomElement(['guru', 'dosen']),
            'is_active' => true,
        ]);
        $course = Course::factory()->create(['instructor_id' => $instructor->id]);

        // Create multiple groups (2-4)
        $groupCount = fake()->numberBetween(2, 4);
        $groups = collect();
        for ($j = 0; $j < $groupCount; $j++) {
            $groups->push(CourseGroup::factory()->create(['course_id' => $course->id]));
        }

        // Randomly test with Material or Assignment
        $useMaterial = fake()->boolean();

        if ($useMaterial) {
            $content = Material::factory()->create([
                'course_id' => $course->id,
                'is_published' => true,
            ]);
            foreach ($groups as $group) {
                DB::table('course_group_content')->insert([
                    'course_group_id' => $group->id,
                    'contentable_type' => Material::class,
                    'contentable_id' => $content->id,
                    'created_at' => now(),
                ]);
            }
        } else {
            $content = Assignment::factory()->create([
                'course_id' => $course->id,
                'is_published' => true,
            ]);
            foreach ($groups as $group) {
                DB::table('course_group_content')->insert([
                    'course_group_id' => $group->id,
                    'contentable_type' => Assignment::class,
                    'contentable_id' => $content->id,
                    'created_at' => now(),
                ]);
            }
        }

        // Create a student who is a member of ALL associated groups
        $student = User::factory()->create([
            'role' => fake()->randomElement(['siswa', 'mahasiswa']),
            'is_active' => true,
        ]);
        Enrollment::factory()->create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'status' => 'active',
        ]);
        foreach ($groups as $group) {
            $group->members()->attach($student->id);
        }

        // Get recipients from service
        $recipients = $useMaterial
            ? $service->getRecipientsForMaterial($content)
            : $service->getRecipientsForAssignment($content);

        $recipientIds = $recipients->pluck('id');

        // Student should appear exactly once (no duplicates)
        $occurrences = $recipientIds->filter(fn ($id) => $id === $student->id)->count();

        expect($occurrences)->toBe(1,
            "Student in multiple associated groups should appear exactly once in recipients (iteration {$i})"
        );
    }
})->group('property-tests', 'course-groups', 'notifications');
