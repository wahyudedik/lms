<?php

use App\Models\Course;
use App\Models\CourseGroup;
use App\Models\Enrollment;
use App\Models\Material;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| Property-Based Tests for Material Visibility
|--------------------------------------------------------------------------
|
| These tests validate Property 6: Material visibility respects group targeting
| using randomized inputs with 100 iterations per property.
|
| Validates: Requirements 2.2, 2.3, 4.1, 4.7
|
*/

it('Property 6: Ungrouped material is visible to ALL enrolled students', function () {
    /**
     * Validates: Requirements 2.2, 4.1
     *
     * For any Material in a course that has no group associations,
     * it SHALL be visible to ALL enrolled students via the visibleToStudent scope.
     */
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

        // Create a random enrolled student
        $student = User::factory()->create([
            'role' => fake()->randomElement(['siswa', 'mahasiswa']),
            'is_active' => true,
        ]);
        Enrollment::factory()->create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'status' => 'active',
        ]);

        // Optionally create some groups in the course (but NOT associated with this material)
        $groupCount = fake()->numberBetween(0, 3);
        for ($j = 0; $j < $groupCount; $j++) {
            $group = CourseGroup::factory()->create(['course_id' => $course->id]);
            // Optionally add student to some groups
            if (fake()->boolean()) {
                $group->members()->attach($student->id);
            }
        }

        // Query: material should be visible to the student
        $visibleMaterials = Material::query()
            ->where('course_id', $course->id)
            ->visibleToStudent($student)
            ->pluck('id');

        expect($visibleMaterials->contains($material->id))->toBeTrue(
            "Ungrouped material should be visible to any enrolled student (iteration {$i})"
        );
    }
})->group('property-tests', 'course-groups', 'material-visibility');

it('Property 6: Targeted material is visible ONLY to group members', function () {
    /**
     * Validates: Requirements 2.3, 4.1
     *
     * For any Material that has one or more group associations,
     * it SHALL be visible to the student if and only if the student is a member
     * of at least one of those associated groups.
     */
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

        // Associate material with all groups
        foreach ($groups as $group) {
            DB::table('course_group_content')->insert([
                'course_group_id' => $group->id,
                'contentable_type' => Material::class,
                'contentable_id' => $material->id,
                'created_at' => now(),
            ]);
        }

        // Create a student who IS a member of at least one group
        $memberStudent = User::factory()->create([
            'role' => fake()->randomElement(['siswa', 'mahasiswa']),
            'is_active' => true,
        ]);
        Enrollment::factory()->create([
            'user_id' => $memberStudent->id,
            'course_id' => $course->id,
            'status' => 'active',
        ]);
        // Add to a random subset of the groups (at least one)
        $memberGroups = $groups->random(fake()->numberBetween(1, $groupCount));
        foreach ($memberGroups as $group) {
            $group->members()->attach($memberStudent->id);
        }

        // Create a student who is NOT a member of any associated group
        $nonMemberStudent = User::factory()->create([
            'role' => fake()->randomElement(['siswa', 'mahasiswa']),
            'is_active' => true,
        ]);
        Enrollment::factory()->create([
            'user_id' => $nonMemberStudent->id,
            'course_id' => $course->id,
            'status' => 'active',
        ]);

        // Member should see the material
        $visibleToMember = Material::query()
            ->where('course_id', $course->id)
            ->visibleToStudent($memberStudent)
            ->pluck('id');

        expect($visibleToMember->contains($material->id))->toBeTrue(
            "Targeted material should be visible to group member (iteration {$i})"
        );

        // Non-member should NOT see the material
        $visibleToNonMember = Material::query()
            ->where('course_id', $course->id)
            ->visibleToStudent($nonMemberStudent)
            ->pluck('id');

        expect($visibleToNonMember->contains($material->id))->toBeFalse(
            "Targeted material should NOT be visible to non-group-member (iteration {$i})"
        );
    }
})->group('property-tests', 'course-groups', 'material-visibility');

it('Property 6: Student in no groups sees only ungrouped materials', function () {
    /**
     * Validates: Requirements 4.7
     *
     * For any enrolled student who belongs to no Course_Groups in a course,
     * the student SHALL see only ungrouped materials (materials with no group associations).
     */
    for ($i = 0; $i < 100; $i++) {
        // Setup: course with instructor
        $instructor = User::factory()->create([
            'role' => fake()->randomElement(['guru', 'dosen']),
            'is_active' => true,
        ]);
        $course = Course::factory()->create(['instructor_id' => $instructor->id]);

        // Create groups
        $groupCount = fake()->numberBetween(1, 3);
        $groups = collect();
        for ($j = 0; $j < $groupCount; $j++) {
            $groups->push(CourseGroup::factory()->create(['course_id' => $course->id]));
        }

        // Create ungrouped materials (1-3)
        $ungroupedCount = fake()->numberBetween(1, 3);
        $ungroupedMaterials = collect();
        for ($j = 0; $j < $ungroupedCount; $j++) {
            $ungroupedMaterials->push(Material::factory()->create([
                'course_id' => $course->id,
                'is_published' => true,
            ]));
        }

        // Create targeted materials (1-3)
        $targetedCount = fake()->numberBetween(1, 3);
        $targetedMaterials = collect();
        for ($j = 0; $j < $targetedCount; $j++) {
            $material = Material::factory()->create([
                'course_id' => $course->id,
                'is_published' => true,
            ]);
            $targetedMaterials->push($material);

            // Associate with a random group
            $targetGroup = $groups->random();
            DB::table('course_group_content')->insert([
                'course_group_id' => $targetGroup->id,
                'contentable_type' => Material::class,
                'contentable_id' => $material->id,
                'created_at' => now(),
            ]);
        }

        // Create a student who belongs to NO groups
        $student = User::factory()->create([
            'role' => fake()->randomElement(['siswa', 'mahasiswa']),
            'is_active' => true,
        ]);
        Enrollment::factory()->create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'status' => 'active',
        ]);

        // Query visible materials
        $visibleIds = Material::query()
            ->where('course_id', $course->id)
            ->visibleToStudent($student)
            ->pluck('id');

        // Should see all ungrouped materials
        foreach ($ungroupedMaterials as $material) {
            expect($visibleIds->contains($material->id))->toBeTrue(
                "Student in no groups should see ungrouped material (iteration {$i})"
            );
        }

        // Should NOT see any targeted materials
        foreach ($targetedMaterials as $material) {
            expect($visibleIds->contains($material->id))->toBeFalse(
                "Student in no groups should NOT see targeted material (iteration {$i})"
            );
        }
    }
})->group('property-tests', 'course-groups', 'material-visibility');

it('Property 6: Student in multiple groups sees materials from all their groups', function () {
    /**
     * Validates: Requirements 2.3, 4.1
     *
     * For any enrolled student who belongs to multiple Course_Groups,
     * the student SHALL see materials targeted to ANY of their groups.
     */
    for ($i = 0; $i < 100; $i++) {
        // Setup: course with instructor
        $instructor = User::factory()->create([
            'role' => fake()->randomElement(['guru', 'dosen']),
            'is_active' => true,
        ]);
        $course = Course::factory()->create(['instructor_id' => $instructor->id]);

        // Create 3 groups
        $groupA = CourseGroup::factory()->create(['course_id' => $course->id]);
        $groupB = CourseGroup::factory()->create(['course_id' => $course->id]);
        $groupC = CourseGroup::factory()->create(['course_id' => $course->id]);

        // Create materials targeted to different groups
        $materialA = Material::factory()->create([
            'course_id' => $course->id,
            'is_published' => true,
        ]);
        DB::table('course_group_content')->insert([
            'course_group_id' => $groupA->id,
            'contentable_type' => Material::class,
            'contentable_id' => $materialA->id,
            'created_at' => now(),
        ]);

        $materialB = Material::factory()->create([
            'course_id' => $course->id,
            'is_published' => true,
        ]);
        DB::table('course_group_content')->insert([
            'course_group_id' => $groupB->id,
            'contentable_type' => Material::class,
            'contentable_id' => $materialB->id,
            'created_at' => now(),
        ]);

        $materialC = Material::factory()->create([
            'course_id' => $course->id,
            'is_published' => true,
        ]);
        DB::table('course_group_content')->insert([
            'course_group_id' => $groupC->id,
            'contentable_type' => Material::class,
            'contentable_id' => $materialC->id,
            'created_at' => now(),
        ]);

        // Create a student who belongs to groups A and B (but NOT C)
        $student = User::factory()->create([
            'role' => fake()->randomElement(['siswa', 'mahasiswa']),
            'is_active' => true,
        ]);
        Enrollment::factory()->create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'status' => 'active',
        ]);
        $groupA->members()->attach($student->id);
        $groupB->members()->attach($student->id);

        // Query visible materials
        $visibleIds = Material::query()
            ->where('course_id', $course->id)
            ->visibleToStudent($student)
            ->pluck('id');

        // Should see materials from group A and B
        expect($visibleIds->contains($materialA->id))->toBeTrue(
            "Student in group A should see material targeted to group A (iteration {$i})"
        );
        expect($visibleIds->contains($materialB->id))->toBeTrue(
            "Student in group B should see material targeted to group B (iteration {$i})"
        );

        // Should NOT see material from group C
        expect($visibleIds->contains($materialC->id))->toBeFalse(
            "Student NOT in group C should NOT see material targeted to group C (iteration {$i})"
        );
    }
})->group('property-tests', 'course-groups', 'material-visibility');

it('Property 6: Material in multiple groups is visible to members of ANY of those groups', function () {
    /**
     * Validates: Requirements 2.3, 4.1
     *
     * For any Material associated with multiple groups,
     * it SHALL be visible to a student who is a member of ANY one of those groups.
     */
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

        // Create a material associated with ALL groups
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

        // Create a student who is a member of only ONE of the groups (randomly chosen)
        $student = User::factory()->create([
            'role' => fake()->randomElement(['siswa', 'mahasiswa']),
            'is_active' => true,
        ]);
        Enrollment::factory()->create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'status' => 'active',
        ]);
        $singleGroup = $groups->random();
        $singleGroup->members()->attach($student->id);

        // The material should be visible because student is in at least one associated group
        $visibleIds = Material::query()
            ->where('course_id', $course->id)
            ->visibleToStudent($student)
            ->pluck('id');

        expect($visibleIds->contains($material->id))->toBeTrue(
            "Material in multiple groups should be visible to member of ANY associated group (iteration {$i})"
        );
    }
})->group('property-tests', 'course-groups', 'material-visibility');

it('Property 9: Removing all group associations reverts Material to ungrouped', function () {
    /**
     * Validates: Requirements 2.4
     *
     * For any Material that has group associations, removing all associations
     * SHALL make the Material visible to all enrolled students in the course
     * (equivalent to ungrouped content).
     */
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

        // Associate material with all groups (making it targeted)
        $material->courseGroups()->sync($groups->pluck('id')->toArray());

        // Create enrolled students: some are group members, some are not
        $memberStudent = User::factory()->create([
            'role' => fake()->randomElement(['siswa', 'mahasiswa']),
            'is_active' => true,
        ]);
        Enrollment::factory()->create([
            'user_id' => $memberStudent->id,
            'course_id' => $course->id,
            'status' => 'active',
        ]);
        $groups->first()->members()->attach($memberStudent->id);

        $nonMemberStudent = User::factory()->create([
            'role' => fake()->randomElement(['siswa', 'mahasiswa']),
            'is_active' => true,
        ]);
        Enrollment::factory()->create([
            'user_id' => $nonMemberStudent->id,
            'course_id' => $course->id,
            'status' => 'active',
        ]);

        // Verify: before removal, non-member should NOT see the material
        $visibleBeforeRemoval = Material::query()
            ->where('course_id', $course->id)
            ->visibleToStudent($nonMemberStudent)
            ->pluck('id');

        expect($visibleBeforeRemoval->contains($material->id))->toBeFalse(
            "Targeted material should NOT be visible to non-member before removal (iteration {$i})"
        );

        // Action: remove all group associations (sync with empty array)
        $material->courseGroups()->sync([]);

        // Assert: material is now visible to ALL enrolled students (including former non-members)
        $visibleToMember = Material::query()
            ->where('course_id', $course->id)
            ->visibleToStudent($memberStudent)
            ->pluck('id');

        expect($visibleToMember->contains($material->id))->toBeTrue(
            "Material should be visible to former group member after removing all associations (iteration {$i})"
        );

        $visibleToNonMember = Material::query()
            ->where('course_id', $course->id)
            ->visibleToStudent($nonMemberStudent)
            ->pluck('id');

        expect($visibleToNonMember->contains($material->id))->toBeTrue(
            "Material should be visible to non-member after removing all associations (iteration {$i})"
        );
    }
})->group('property-tests', 'course-groups', 'material-visibility');

/*
|--------------------------------------------------------------------------
| Property 11: Membership removal immediately revokes targeted access
|--------------------------------------------------------------------------
|
| For any student removed from a CourseGroup, the student SHALL lose visibility
| of Targeted_Content associated exclusively with that group, but SHALL retain
| visibility of content associated with other groups the student still belongs to.
|
| Validates: Requirements 2.7
|
*/

it('Property 11: Student removed from group loses access to exclusively targeted content', function () {
    /**
     * Validates: Requirements 2.7
     *
     * Scenario 1: Student in one group, removed → loses access to that group's targeted content.
     */
    for ($i = 0; $i < 100; $i++) {
        // Setup: course with instructor
        $instructor = User::factory()->create([
            'role' => fake()->randomElement(['guru', 'dosen']),
            'is_active' => true,
        ]);
        $course = Course::factory()->create(['instructor_id' => $instructor->id]);

        // Create a group
        $group = CourseGroup::factory()->create(['course_id' => $course->id]);

        // Create a material targeted exclusively to this group
        $material = Material::factory()->create([
            'course_id' => $course->id,
            'is_published' => true,
        ]);
        DB::table('course_group_content')->insert([
            'course_group_id' => $group->id,
            'contentable_type' => Material::class,
            'contentable_id' => $material->id,
            'created_at' => now(),
        ]);

        // Create a student enrolled and added to the group
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

        // Verify: student CAN see the material before removal
        $visibleBefore = Material::query()
            ->where('course_id', $course->id)
            ->visibleToStudent($student)
            ->pluck('id');

        expect($visibleBefore->contains($material->id))->toBeTrue(
            "Student should see targeted material while in group (iteration {$i})"
        );

        // Remove membership
        $group->members()->detach($student->id);

        // Verify: student can NO LONGER see the material after removal
        $visibleAfter = Material::query()
            ->where('course_id', $course->id)
            ->visibleToStudent($student)
            ->pluck('id');

        expect($visibleAfter->contains($material->id))->toBeFalse(
            "Student should lose access to targeted material after removal from group (iteration {$i})"
        );
    }
})->group('property-tests', 'course-groups', 'material-visibility', 'membership-removal');

it('Property 11: Student removed from one group retains access to content shared with another group', function () {
    /**
     * Validates: Requirements 2.7
     *
     * Scenario 2: Student in two groups, removed from one → retains access to content
     * shared with the other group.
     */
    for ($i = 0; $i < 100; $i++) {
        // Setup: course with instructor
        $instructor = User::factory()->create([
            'role' => fake()->randomElement(['guru', 'dosen']),
            'is_active' => true,
        ]);
        $course = Course::factory()->create(['instructor_id' => $instructor->id]);

        // Create two groups
        $groupA = CourseGroup::factory()->create(['course_id' => $course->id]);
        $groupB = CourseGroup::factory()->create(['course_id' => $course->id]);

        // Create a material targeted to BOTH groups
        $sharedMaterial = Material::factory()->create([
            'course_id' => $course->id,
            'is_published' => true,
        ]);
        DB::table('course_group_content')->insert([
            ['course_group_id' => $groupA->id, 'contentable_type' => Material::class, 'contentable_id' => $sharedMaterial->id, 'created_at' => now()],
            ['course_group_id' => $groupB->id, 'contentable_type' => Material::class, 'contentable_id' => $sharedMaterial->id, 'created_at' => now()],
        ]);

        // Create a material targeted ONLY to group A
        $exclusiveMaterial = Material::factory()->create([
            'course_id' => $course->id,
            'is_published' => true,
        ]);
        DB::table('course_group_content')->insert([
            'course_group_id' => $groupA->id,
            'contentable_type' => Material::class,
            'contentable_id' => $exclusiveMaterial->id,
            'created_at' => now(),
        ]);

        // Create a student enrolled and added to BOTH groups
        $student = User::factory()->create([
            'role' => fake()->randomElement(['siswa', 'mahasiswa']),
            'is_active' => true,
        ]);
        Enrollment::factory()->create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'status' => 'active',
        ]);
        $groupA->members()->attach($student->id);
        $groupB->members()->attach($student->id);

        // Verify: student can see both materials before removal
        $visibleBefore = Material::query()
            ->where('course_id', $course->id)
            ->visibleToStudent($student)
            ->pluck('id');

        expect($visibleBefore->contains($sharedMaterial->id))->toBeTrue(
            "Student should see shared material while in both groups (iteration {$i})"
        );
        expect($visibleBefore->contains($exclusiveMaterial->id))->toBeTrue(
            "Student should see exclusive material while in group A (iteration {$i})"
        );

        // Remove student from group A only
        $groupA->members()->detach($student->id);

        // Verify: student retains access to shared material (still in group B)
        $visibleAfter = Material::query()
            ->where('course_id', $course->id)
            ->visibleToStudent($student)
            ->pluck('id');

        expect($visibleAfter->contains($sharedMaterial->id))->toBeTrue(
            "Student should retain access to shared material via group B (iteration {$i})"
        );

        // Verify: student loses access to material exclusive to group A
        expect($visibleAfter->contains($exclusiveMaterial->id))->toBeFalse(
            "Student should lose access to material exclusive to group A after removal (iteration {$i})"
        );
    }
})->group('property-tests', 'course-groups', 'material-visibility', 'membership-removal');

it('Property 11: Material in multiple groups remains visible when student removed from one but still in another', function () {
    /**
     * Validates: Requirements 2.7
     *
     * Scenario 3: Material associated with groups A and B, student removed from A
     * but still in B → retains access to the material.
     */
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

        // Create a material associated with ALL groups
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

        // Create a student enrolled and added to at least 2 groups
        $student = User::factory()->create([
            'role' => fake()->randomElement(['siswa', 'mahasiswa']),
            'is_active' => true,
        ]);
        Enrollment::factory()->create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'status' => 'active',
        ]);

        // Add student to all groups
        foreach ($groups as $group) {
            $group->members()->attach($student->id);
        }

        // Pick a random group to remove the student from
        $removedGroup = $groups->random();

        // Verify: student can see the material before removal
        $visibleBefore = Material::query()
            ->where('course_id', $course->id)
            ->visibleToStudent($student)
            ->pluck('id');

        expect($visibleBefore->contains($material->id))->toBeTrue(
            "Student should see material while in all groups (iteration {$i})"
        );

        // Remove student from one group
        $removedGroup->members()->detach($student->id);

        // Verify: student STILL sees the material (still in other groups)
        $visibleAfter = Material::query()
            ->where('course_id', $course->id)
            ->visibleToStudent($student)
            ->pluck('id');

        expect($visibleAfter->contains($material->id))->toBeTrue(
            "Student should retain access to material when still in other associated groups (iteration {$i})"
        );
    }
})->group('property-tests', 'course-groups', 'material-visibility', 'membership-removal');
