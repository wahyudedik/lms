<?php

use App\Models\Course;
use App\Models\Exam;
use App\Models\Material;
use App\Models\User;
use App\Policies\CoursePolicy;
use App\Policies\EnrollmentPolicy;
use App\Policies\ExamPolicy;
use App\Policies\MaterialPolicy;
use Tests\TestCase;

uses(TestCase::class);

/**
 * Feature: dosen-mahasiswa-role
 *
 * Property 4: Policy equivalence — dosen equals guru
 *
 * For any policy method and resource instance, the authorization result for a
 * dosen user should equal the authorization result for a guru user with
 * identical ownership relationships (same instructor_id).
 *
 * Validates: Requirements 3.1, 3.2, 3.3, 3.4
 */

/**
 * CoursePolicy::update() — dosen with same instructor_id as guru should get identical result.
 *
 * Validates: Requirements 3.1
 */
it('CoursePolicy::update() returns same result for dosen and guru with same instructor_id', function () {
    $instructorId = 1;

    $guru  = User::factory()->make(['role' => 'guru',  'id' => $instructorId]);
    $dosen = User::factory()->make(['role' => 'dosen', 'id' => $instructorId]);

    $ownCourse   = Course::factory()->make(['instructor_id' => $instructorId]);
    $otherCourse = Course::factory()->make(['instructor_id' => 999]);

    $policy = new CoursePolicy();

    // Owner scenario: both should be allowed
    expect($policy->update($dosen, $ownCourse))->toBe($policy->update($guru, $ownCourse));

    // Non-owner scenario: both should be denied
    expect($policy->update($dosen, $otherCourse))->toBe($policy->update($guru, $otherCourse));
});

/**
 * CoursePolicy::delete() — dosen with same instructor_id as guru should get identical result.
 *
 * Validates: Requirements 3.1
 */
it('CoursePolicy::delete() returns same result for dosen and guru with same instructor_id', function () {
    $instructorId = 1;

    $guru  = User::factory()->make(['role' => 'guru',  'id' => $instructorId]);
    $dosen = User::factory()->make(['role' => 'dosen', 'id' => $instructorId]);

    $ownCourse   = Course::factory()->make(['instructor_id' => $instructorId]);
    $otherCourse = Course::factory()->make(['instructor_id' => 999]);

    $policy = new CoursePolicy();

    // Owner scenario: both should be allowed
    expect($policy->delete($dosen, $ownCourse))->toBe($policy->delete($guru, $ownCourse));

    // Non-owner scenario: both should be denied
    expect($policy->delete($dosen, $otherCourse))->toBe($policy->delete($guru, $otherCourse));
});

/**
 * ExamPolicy::create() — dosen should get the same result as guru.
 *
 * ExamPolicy::create() only takes a User (no Exam instance).
 *
 * Validates: Requirements 3.2
 */
it('ExamPolicy::create() returns same result for dosen and guru', function () {
    $guru  = User::factory()->make(['role' => 'guru',  'id' => 1]);
    $dosen = User::factory()->make(['role' => 'dosen', 'id' => 1]);

    $policy = new ExamPolicy();

    expect($policy->create($dosen))->toBe($policy->create($guru));
});

/**
 * MaterialPolicy::create() — dosen should get the same result as guru.
 *
 * MaterialPolicy::create() only takes a User (no Material instance).
 *
 * Validates: Requirements 3.3
 */
it('MaterialPolicy::create() returns same result for dosen and guru', function () {
    $guru  = User::factory()->make(['role' => 'guru',  'id' => 1]);
    $dosen = User::factory()->make(['role' => 'dosen', 'id' => 1]);

    $policy = new MaterialPolicy();

    expect($policy->create($dosen))->toBe($policy->create($guru));
});

/**
 * Feature: dosen-mahasiswa-role
 *
 * Property 5: Policy equivalence — mahasiswa equals siswa
 *
 * For any policy method and resource instance, the authorization result for a
 * mahasiswa user should equal the authorization result for a siswa user with
 * identical enrollment relationships.
 *
 * The view() tests mock isEnrolledBy() on the Course model to avoid DB queries,
 * testing both the "not enrolled" (false) and "enrolled" (true) scenarios.
 *
 * Validates: Requirements 3.5, 3.6, 3.7, 3.8
 */

/**
 * CoursePolicy::view() — mahasiswa and siswa should get identical result regardless of
 * enrollment status (both enrolled → both true; both not enrolled → both false).
 *
 * isEnrolledBy() is mocked to avoid DB queries in this unit test.
 *
 * Validates: Requirements 3.5
 */
it('CoursePolicy::view() returns same result for mahasiswa and siswa when not enrolled', function () {
    $siswa     = User::factory()->make(['role' => 'siswa',     'id' => 1]);
    $mahasiswa = User::factory()->make(['role' => 'mahasiswa', 'id' => 2]);

    // Mock Course so isEnrolledBy() returns false (not enrolled scenario).
    $course = Mockery::mock(Course::class)->makePartial();
    $course->instructor_id = 999;
    $course->shouldReceive('isEnrolledBy')->andReturn(false);

    $policy = new CoursePolicy();

    expect($policy->view($mahasiswa, $course))->toBe($policy->view($siswa, $course));
});

/**
 * ExamPolicy::view() — mahasiswa and siswa should get identical result for an exam
 * whose course they are not enrolled in (both should be denied).
 *
 * The course relation is pre-loaded and isEnrolledBy() is mocked to avoid DB queries.
 *
 * Validates: Requirements 3.6
 */
it('ExamPolicy::view() returns same result for mahasiswa and siswa when not enrolled', function () {
    $siswa     = User::factory()->make(['role' => 'siswa',     'id' => 1]);
    $mahasiswa = User::factory()->make(['role' => 'mahasiswa', 'id' => 2]);

    // Mock Course so isEnrolledBy() returns false (not enrolled scenario).
    $course = Mockery::mock(Course::class)->makePartial();
    $course->instructor_id = 999;
    $course->shouldReceive('isEnrolledBy')->andReturn(false);

    // Build an Exam with explicit scalar values to avoid nested factory DB inserts,
    // then pre-load the mocked course relation.
    $exam = new Exam([
        'course_id'  => 999,
        'created_by' => 50,
        'title'      => 'Test Exam',
    ]);
    $exam->setRelation('course', $course);

    $policy = new ExamPolicy();

    expect($policy->view($mahasiswa, $exam))->toBe($policy->view($siswa, $exam));
});

/**
 * MaterialPolicy::view() — mahasiswa and siswa should get identical result for a material
 * whose course they are not enrolled in (both should be denied).
 *
 * The course relation is pre-loaded and isEnrolledBy() is mocked to avoid DB queries.
 *
 * Validates: Requirements 3.7
 */
it('MaterialPolicy::view() returns same result for mahasiswa and siswa when not enrolled', function () {
    $siswa     = User::factory()->make(['role' => 'siswa',     'id' => 1]);
    $mahasiswa = User::factory()->make(['role' => 'mahasiswa', 'id' => 2]);

    // Mock Course so isEnrolledBy() returns false (not enrolled scenario).
    $course = Mockery::mock(Course::class)->makePartial();
    $course->instructor_id = 999;
    $course->shouldReceive('isEnrolledBy')->andReturn(false);

    // Build a Material with explicit scalar values to avoid nested factory DB inserts,
    // then pre-load the mocked course relation.
    $material = new Material([
        'course_id'  => 999,
        'created_by' => 50,
        'title'      => 'Test Material',
        'type'       => 'file',
    ]);
    $material->setRelation('course', $course);

    $policy = new MaterialPolicy();

    expect($policy->view($mahasiswa, $material))->toBe($policy->view($siswa, $material));
});

/**
 * EnrollmentPolicy::create() — mahasiswa should get the same result as siswa.
 *
 * EnrollmentPolicy::create() only takes a User (no Enrollment instance).
 *
 * Validates: Requirements 3.8
 */
it('EnrollmentPolicy::create() returns same result for mahasiswa and siswa', function () {
    $siswa     = User::factory()->make(['role' => 'siswa',     'id' => 1]);
    $mahasiswa = User::factory()->make(['role' => 'mahasiswa', 'id' => 2]);

    $policy = new EnrollmentPolicy();

    expect($policy->create($mahasiswa))->toBe($policy->create($siswa));
});
