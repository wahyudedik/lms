<?php

use App\Models\Assignment;
use App\Models\Course;
use App\Models\CourseGroup;
use App\Models\Enrollment;
use App\Models\Material;
use App\Models\NotificationPreference;
use App\Models\User;
use App\Services\GroupTargetedNotificationService;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| Unit Tests for Direct URL Access Denial and Edge Cases
|--------------------------------------------------------------------------
|
| These tests validate specific examples and edge cases for:
| - 403 response when non-member accesses targeted Material via URL (Requirement 4.3)
| - 403 response when non-member accesses targeted Assignment via URL (Requirement 4.4)
| - 403 response when non-member submits to targeted Assignment (Requirement 4.6)
| - Multi-group membership allowed (Requirement 1.8)
| - Unauthenticated redirect to login (Requirement 5.5)
| - Notification preference channel filtering (Requirements 6.5, 6.6)
|
*/

/*
|--------------------------------------------------------------------------
| Requirement 4.3: Non-member gets 403 when accessing targeted Material via URL
|--------------------------------------------------------------------------
*/

it('returns 403 when non-member student accesses targeted material via direct URL', function () {
    // Setup: instructor and course
    $instructor = User::factory()->create(['role' => 'guru', 'is_active' => true]);
    $course = Course::factory()->create(['instructor_id' => $instructor->id]);

    // Create a group and a targeted material
    $group = CourseGroup::factory()->create(['course_id' => $course->id]);
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

    // Create a non-member student (enrolled but NOT in the group)
    $nonMember = User::factory()->create(['role' => 'siswa', 'is_active' => true]);
    Enrollment::factory()->create([
        'user_id' => $nonMember->id,
        'course_id' => $course->id,
        'status' => 'active',
    ]);

    // Act: access material show page directly
    $response = $this->actingAs($nonMember)->get("/siswa/materials/{$material->id}");

    // Assert: 403 Forbidden
    $response->assertStatus(403);
})->group('unit-tests', 'course-groups', 'access-denial');

it('returns 403 when mahasiswa non-member accesses targeted material via direct URL', function () {
    // Setup: instructor and course
    $instructor = User::factory()->create(['role' => 'dosen', 'is_active' => true]);
    $course = Course::factory()->create(['instructor_id' => $instructor->id]);

    // Create a group and a targeted material
    $group = CourseGroup::factory()->create(['course_id' => $course->id]);
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

    // Create a non-member mahasiswa (enrolled but NOT in the group)
    $nonMember = User::factory()->create(['role' => 'mahasiswa', 'is_active' => true]);
    Enrollment::factory()->create([
        'user_id' => $nonMember->id,
        'course_id' => $course->id,
        'status' => 'active',
    ]);

    // Act: access material show page directly via mahasiswa prefix
    $response = $this->actingAs($nonMember)->get("/mahasiswa/materials/{$material->id}");

    // Assert: 403 Forbidden
    $response->assertStatus(403);
})->group('unit-tests', 'course-groups', 'access-denial');

/*
|--------------------------------------------------------------------------
| Requirement 4.4: Non-member gets 403 when accessing targeted Assignment via URL
|--------------------------------------------------------------------------
*/

it('returns 403 when non-member student accesses targeted assignment via direct URL', function () {
    // Setup: instructor and course
    $instructor = User::factory()->create(['role' => 'guru', 'is_active' => true]);
    $course = Course::factory()->create(['instructor_id' => $instructor->id]);

    // Create a group and a targeted assignment
    $group = CourseGroup::factory()->create(['course_id' => $course->id]);
    $assignment = Assignment::factory()->create([
        'course_id' => $course->id,
        'is_published' => true,
        'created_by' => $instructor->id,
    ]);
    DB::table('course_group_content')->insert([
        'course_group_id' => $group->id,
        'contentable_type' => Assignment::class,
        'contentable_id' => $assignment->id,
        'created_at' => now(),
    ]);

    // Create a non-member student (enrolled but NOT in the group)
    $nonMember = User::factory()->create(['role' => 'siswa', 'is_active' => true]);
    Enrollment::factory()->create([
        'user_id' => $nonMember->id,
        'course_id' => $course->id,
        'status' => 'active',
    ]);

    // Act: access assignment show page directly
    $response = $this->actingAs($nonMember)->get("/siswa/assignments/{$assignment->id}");

    // Assert: 403 Forbidden
    $response->assertStatus(403);
})->group('unit-tests', 'course-groups', 'access-denial');

it('returns 403 when mahasiswa non-member accesses targeted assignment via direct URL', function () {
    // Setup: instructor and course
    $instructor = User::factory()->create(['role' => 'dosen', 'is_active' => true]);
    $course = Course::factory()->create(['instructor_id' => $instructor->id]);

    // Create a group and a targeted assignment
    $group = CourseGroup::factory()->create(['course_id' => $course->id]);
    $assignment = Assignment::factory()->create([
        'course_id' => $course->id,
        'is_published' => true,
        'created_by' => $instructor->id,
    ]);
    DB::table('course_group_content')->insert([
        'course_group_id' => $group->id,
        'contentable_type' => Assignment::class,
        'contentable_id' => $assignment->id,
        'created_at' => now(),
    ]);

    // Create a non-member mahasiswa (enrolled but NOT in the group)
    $nonMember = User::factory()->create(['role' => 'mahasiswa', 'is_active' => true]);
    Enrollment::factory()->create([
        'user_id' => $nonMember->id,
        'course_id' => $course->id,
        'status' => 'active',
    ]);

    // Act: access assignment show page directly via mahasiswa prefix
    $response = $this->actingAs($nonMember)->get("/mahasiswa/assignments/{$assignment->id}");

    // Assert: 403 Forbidden
    $response->assertStatus(403);
})->group('unit-tests', 'course-groups', 'access-denial');

/*
|--------------------------------------------------------------------------
| Requirement 4.6: Non-member gets 403 when submitting to targeted Assignment
|--------------------------------------------------------------------------
*/

it('returns 403 when non-member student submits to targeted assignment', function () {
    // Setup: instructor and course
    $instructor = User::factory()->create(['role' => 'guru', 'is_active' => true]);
    $course = Course::factory()->create(['instructor_id' => $instructor->id]);

    // Create a group and a targeted assignment with future deadline
    $group = CourseGroup::factory()->create(['course_id' => $course->id]);
    $assignment = Assignment::factory()->create([
        'course_id' => $course->id,
        'is_published' => true,
        'created_by' => $instructor->id,
        'deadline' => now()->addDays(7),
        'late_policy' => 'allow',
    ]);
    DB::table('course_group_content')->insert([
        'course_group_id' => $group->id,
        'contentable_type' => Assignment::class,
        'contentable_id' => $assignment->id,
        'created_at' => now(),
    ]);

    // Create a non-member student (enrolled but NOT in the group)
    $nonMember = User::factory()->create(['role' => 'siswa', 'is_active' => true]);
    Enrollment::factory()->create([
        'user_id' => $nonMember->id,
        'course_id' => $course->id,
        'status' => 'active',
    ]);

    // Act: attempt to submit to the assignment
    $response = $this->actingAs($nonMember)->post("/siswa/assignments/{$assignment->id}/submit", [
        'file' => \Illuminate\Http\UploadedFile::fake()->create('homework.pdf', 100, 'application/pdf'),
    ]);

    // Assert: 403 Forbidden
    $response->assertStatus(403);
})->group('unit-tests', 'course-groups', 'access-denial');

it('returns 403 when mahasiswa non-member submits to targeted assignment', function () {
    // Setup: instructor and course
    $instructor = User::factory()->create(['role' => 'dosen', 'is_active' => true]);
    $course = Course::factory()->create(['instructor_id' => $instructor->id]);

    // Create a group and a targeted assignment with future deadline
    $group = CourseGroup::factory()->create(['course_id' => $course->id]);
    $assignment = Assignment::factory()->create([
        'course_id' => $course->id,
        'is_published' => true,
        'created_by' => $instructor->id,
        'deadline' => now()->addDays(7),
        'late_policy' => 'allow',
    ]);
    DB::table('course_group_content')->insert([
        'course_group_id' => $group->id,
        'contentable_type' => Assignment::class,
        'contentable_id' => $assignment->id,
        'created_at' => now(),
    ]);

    // Create a non-member mahasiswa (enrolled but NOT in the group)
    $nonMember = User::factory()->create(['role' => 'mahasiswa', 'is_active' => true]);
    Enrollment::factory()->create([
        'user_id' => $nonMember->id,
        'course_id' => $course->id,
        'status' => 'active',
    ]);

    // Act: attempt to submit to the assignment via mahasiswa prefix
    $response = $this->actingAs($nonMember)->post("/mahasiswa/assignments/{$assignment->id}/submit", [
        'file' => \Illuminate\Http\UploadedFile::fake()->create('homework.pdf', 100, 'application/pdf'),
    ]);

    // Assert: 403 Forbidden
    $response->assertStatus(403);
})->group('unit-tests', 'course-groups', 'access-denial');

/*
|--------------------------------------------------------------------------
| Requirement 1.8 (updated): Exclusive group membership per course
|--------------------------------------------------------------------------
*/

it('rejects adding a student to a second group within the same course', function () {
    // Setup: instructor and course
    $instructor = User::factory()->create(['role' => 'guru', 'is_active' => true]);
    $course = Course::factory()->create(['instructor_id' => $instructor->id]);

    // Create multiple groups
    $groupA = CourseGroup::factory()->create(['course_id' => $course->id]);
    $groupB = CourseGroup::factory()->create(['course_id' => $course->id]);

    // Create an enrolled student
    $student = User::factory()->create(['role' => 'siswa', 'is_active' => true]);
    Enrollment::factory()->create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'status' => 'active',
    ]);

    // Add student to group A via HTTP (should succeed)
    $response = $this->actingAs($instructor)->post(
        route('guru.courses.groups.members.store', [$course, $groupA]),
        ['user_id' => $student->id]
    );
    $response->assertSessionHasNoErrors();

    // Try to add same student to group B via HTTP (should be rejected)
    $response = $this->actingAs($instructor)->post(
        route('guru.courses.groups.members.store', [$course, $groupB]),
        ['user_id' => $student->id]
    );
    $response->assertSessionHasErrors('user_id');

    // Assert: student is only in group A
    expect($groupA->members()->where('user_id', $student->id)->exists())->toBeTrue();
    expect($groupB->members()->where('user_id', $student->id)->exists())->toBeFalse();

    // Assert: membership count is 1
    $membershipCount = DB::table('course_group_members')
        ->where('user_id', $student->id)
        ->whereIn('course_group_id', [$groupA->id, $groupB->id])
        ->count();

    expect($membershipCount)->toBe(1);
})->group('unit-tests', 'course-groups', 'exclusive-group');

it('allows a student to belong to groups in different courses simultaneously', function () {
    // Setup: two courses with different instructors
    $instructor1 = User::factory()->create(['role' => 'guru', 'is_active' => true]);
    $instructor2 = User::factory()->create(['role' => 'dosen', 'is_active' => true]);
    $course1 = Course::factory()->create(['instructor_id' => $instructor1->id]);
    $course2 = Course::factory()->create(['instructor_id' => $instructor2->id]);

    // Create groups in each course
    $group1 = CourseGroup::factory()->create(['course_id' => $course1->id]);
    $group2 = CourseGroup::factory()->create(['course_id' => $course2->id]);

    // Create a student enrolled in both courses
    $student = User::factory()->create(['role' => 'siswa', 'is_active' => true]);
    Enrollment::factory()->create([
        'user_id' => $student->id,
        'course_id' => $course1->id,
        'status' => 'active',
    ]);
    Enrollment::factory()->create([
        'user_id' => $student->id,
        'course_id' => $course2->id,
        'status' => 'active',
    ]);

    // Add student to groups in both courses
    $group1->members()->attach($student->id);
    $group2->members()->attach($student->id);

    // Assert: student is a member of both groups
    expect($group1->members()->where('user_id', $student->id)->exists())->toBeTrue();
    expect($group2->members()->where('user_id', $student->id)->exists())->toBeTrue();
})->group('unit-tests', 'course-groups', 'multi-group');

/*
|--------------------------------------------------------------------------
| Requirement 5.5: Unauthenticated redirect to login
|--------------------------------------------------------------------------
*/

it('redirects unauthenticated user to login when accessing group management endpoint', function () {
    // Setup: a course
    $instructor = User::factory()->create(['role' => 'guru', 'is_active' => true]);
    $course = Course::factory()->create(['instructor_id' => $instructor->id]);

    // Act: access group management index without authentication
    $response = $this->get("/guru/courses/{$course->id}/groups");

    // Assert: redirect to login
    $response->assertRedirect(route('login'));
})->group('unit-tests', 'course-groups', 'unauthenticated');

it('redirects unauthenticated user to login when accessing group store endpoint', function () {
    // Setup: a course
    $instructor = User::factory()->create(['role' => 'guru', 'is_active' => true]);
    $course = Course::factory()->create(['instructor_id' => $instructor->id]);

    // Act: attempt to create a group without authentication
    $response = $this->post("/guru/courses/{$course->id}/groups", [
        'name' => 'Test Group',
    ]);

    // Assert: redirect to login
    $response->assertRedirect(route('login'));
})->group('unit-tests', 'course-groups', 'unauthenticated');

it('redirects unauthenticated user to login when accessing material show endpoint', function () {
    // Setup: a material
    $instructor = User::factory()->create(['role' => 'guru', 'is_active' => true]);
    $course = Course::factory()->create(['instructor_id' => $instructor->id]);
    $material = Material::factory()->create([
        'course_id' => $course->id,
        'is_published' => true,
    ]);

    // Act: access material show without authentication
    $response = $this->get("/siswa/materials/{$material->id}");

    // Assert: redirect to login
    $response->assertRedirect(route('login'));
})->group('unit-tests', 'course-groups', 'unauthenticated');

it('redirects unauthenticated user to login when accessing assignment show endpoint', function () {
    // Setup: an assignment
    $instructor = User::factory()->create(['role' => 'guru', 'is_active' => true]);
    $course = Course::factory()->create(['instructor_id' => $instructor->id]);
    $assignment = Assignment::factory()->create([
        'course_id' => $course->id,
        'is_published' => true,
        'created_by' => $instructor->id,
    ]);

    // Act: access assignment show without authentication
    $response = $this->get("/siswa/assignments/{$assignment->id}");

    // Assert: redirect to login
    $response->assertRedirect(route('login'));
})->group('unit-tests', 'course-groups', 'unauthenticated');

/*
|--------------------------------------------------------------------------
| Requirements 6.5, 6.6: Notification preference channel filtering
|--------------------------------------------------------------------------
*/

it('excludes user from recipients when both via_database and via_push are disabled', function () {
    // Setup: instructor and course
    $instructor = User::factory()->create(['role' => 'guru', 'is_active' => true]);
    $course = Course::factory()->create(['instructor_id' => $instructor->id]);

    // Create an ungrouped material
    $material = Material::factory()->create([
        'course_id' => $course->id,
        'is_published' => true,
    ]);

    // Create two enrolled students
    $studentWithPrefs = User::factory()->create(['role' => 'siswa', 'is_active' => true]);
    Enrollment::factory()->create([
        'user_id' => $studentWithPrefs->id,
        'course_id' => $course->id,
        'status' => 'active',
    ]);

    $studentWithoutPrefs = User::factory()->create(['role' => 'siswa', 'is_active' => true]);
    Enrollment::factory()->create([
        'user_id' => $studentWithoutPrefs->id,
        'course_id' => $course->id,
        'status' => 'active',
    ]);

    // Disable both channels for one student
    NotificationPreference::create([
        'user_id' => $studentWithPrefs->id,
        'notification_type' => 'material_published',
        'via_database' => false,
        'via_push' => false,
    ]);

    // Get recipients
    $service = new GroupTargetedNotificationService();
    $recipients = $service->getRecipientsForMaterial($material);
    $recipientIds = $recipients->pluck('id');

    // Assert: student with disabled preferences is excluded
    expect($recipientIds->contains($studentWithPrefs->id))->toBeFalse();
    // Assert: student without explicit preferences (defaults to enabled) is included
    expect($recipientIds->contains($studentWithoutPrefs->id))->toBeTrue();
})->group('unit-tests', 'course-groups', 'notification-preferences');

it('includes user in recipients when via_database is true but via_push is false', function () {
    // Setup: instructor and course
    $instructor = User::factory()->create(['role' => 'guru', 'is_active' => true]);
    $course = Course::factory()->create(['instructor_id' => $instructor->id]);

    // Create an ungrouped assignment
    $assignment = Assignment::factory()->create([
        'course_id' => $course->id,
        'is_published' => true,
        'created_by' => $instructor->id,
    ]);

    // Create an enrolled student with via_database=true, via_push=false
    $student = User::factory()->create(['role' => 'siswa', 'is_active' => true]);
    Enrollment::factory()->create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'status' => 'active',
    ]);

    NotificationPreference::create([
        'user_id' => $student->id,
        'notification_type' => 'assignment_published',
        'via_database' => true,
        'via_push' => false,
    ]);

    // Get recipients
    $service = new GroupTargetedNotificationService();
    $recipients = $service->getRecipientsForAssignment($assignment);
    $recipientIds = $recipients->pluck('id');

    // Assert: student is still included (at least one channel is enabled)
    expect($recipientIds->contains($student->id))->toBeTrue();
})->group('unit-tests', 'course-groups', 'notification-preferences');

it('includes user in recipients when via_push is true but via_database is false', function () {
    // Setup: instructor and course
    $instructor = User::factory()->create(['role' => 'guru', 'is_active' => true]);
    $course = Course::factory()->create(['instructor_id' => $instructor->id]);

    // Create an ungrouped material
    $material = Material::factory()->create([
        'course_id' => $course->id,
        'is_published' => true,
    ]);

    // Create an enrolled student with via_database=false, via_push=true
    $student = User::factory()->create(['role' => 'siswa', 'is_active' => true]);
    Enrollment::factory()->create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'status' => 'active',
    ]);

    NotificationPreference::create([
        'user_id' => $student->id,
        'notification_type' => 'material_published',
        'via_database' => false,
        'via_push' => true,
    ]);

    // Get recipients
    $service = new GroupTargetedNotificationService();
    $recipients = $service->getRecipientsForMaterial($material);
    $recipientIds = $recipients->pluck('id');

    // Assert: student is still included (at least one channel is enabled)
    expect($recipientIds->contains($student->id))->toBeTrue();
})->group('unit-tests', 'course-groups', 'notification-preferences');

it('excludes user from group-targeted notification when both channels disabled', function () {
    // Setup: instructor and course
    $instructor = User::factory()->create(['role' => 'guru', 'is_active' => true]);
    $course = Course::factory()->create(['instructor_id' => $instructor->id]);

    // Create a group and targeted assignment
    $group = CourseGroup::factory()->create(['course_id' => $course->id]);
    $assignment = Assignment::factory()->create([
        'course_id' => $course->id,
        'is_published' => true,
        'created_by' => $instructor->id,
    ]);
    DB::table('course_group_content')->insert([
        'course_group_id' => $group->id,
        'contentable_type' => Assignment::class,
        'contentable_id' => $assignment->id,
        'created_at' => now(),
    ]);

    // Create two group members
    $memberWithDisabledPrefs = User::factory()->create(['role' => 'siswa', 'is_active' => true]);
    Enrollment::factory()->create([
        'user_id' => $memberWithDisabledPrefs->id,
        'course_id' => $course->id,
        'status' => 'active',
    ]);
    $group->members()->attach($memberWithDisabledPrefs->id);

    $memberWithEnabledPrefs = User::factory()->create(['role' => 'siswa', 'is_active' => true]);
    Enrollment::factory()->create([
        'user_id' => $memberWithEnabledPrefs->id,
        'course_id' => $course->id,
        'status' => 'active',
    ]);
    $group->members()->attach($memberWithEnabledPrefs->id);

    // Disable both channels for one member
    NotificationPreference::create([
        'user_id' => $memberWithDisabledPrefs->id,
        'notification_type' => 'assignment_published',
        'via_database' => false,
        'via_push' => false,
    ]);

    // Get recipients
    $service = new GroupTargetedNotificationService();
    $recipients = $service->getRecipientsForAssignment($assignment);
    $recipientIds = $recipients->pluck('id');

    // Assert: member with disabled preferences is excluded
    expect($recipientIds->contains($memberWithDisabledPrefs->id))->toBeFalse();
    // Assert: member with default preferences (enabled) is included
    expect($recipientIds->contains($memberWithEnabledPrefs->id))->toBeTrue();
})->group('unit-tests', 'course-groups', 'notification-preferences');
