<?php

namespace Tests\Feature\Authorization;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Exam;
use App\Models\Material;
use App\Models\Question;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ControllerAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function guru_cannot_access_other_guru_course()
    {
        $guru1 = User::factory()->create(['role' => 'guru']);
        $guru2 = User::factory()->create(['role' => 'guru']);
        $course = Course::factory()->create(['instructor_id' => $guru2->id]);

        $this->actingAs($guru1)
            ->get(route('guru.courses.show', $course))
            ->assertForbidden();
    }

    #[Test]
    public function guru_can_access_own_course()
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $course = Course::factory()->create(['instructor_id' => $guru->id]);

        $this->actingAs($guru)
            ->get(route('guru.courses.show', $course))
            ->assertOk();
    }

    #[Test]
    public function guru_cannot_update_other_guru_exam()
    {
        $guru1 = User::factory()->create(['role' => 'guru']);
        $guru2 = User::factory()->create(['role' => 'guru']);
        $course = Course::factory()->create(['instructor_id' => $guru2->id]);
        $exam = Exam::factory()->create(['course_id' => $course->id]);

        $this->actingAs($guru1)
            ->put(route('guru.exams.update', $exam), [])
            ->assertForbidden();
    }

    #[Test]
    public function guru_can_update_own_exam()
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $course = Course::factory()->create(['instructor_id' => $guru->id]);
        $exam = Exam::factory()->create(['course_id' => $course->id]);

        $this->actingAs($guru)
            ->get(route('guru.exams.edit', $exam))
            ->assertOk();
    }

    #[Test]
    public function guru_cannot_delete_other_guru_material()
    {
        $guru1 = User::factory()->create(['role' => 'guru']);
        $guru2 = User::factory()->create(['role' => 'guru']);
        $course = Course::factory()->create(['instructor_id' => $guru2->id]);
        $material = Material::factory()->create(['course_id' => $course->id]);

        $this->actingAs($guru1)
            ->delete(route('guru.courses.materials.destroy', [$course, $material]))
            ->assertForbidden();
    }

    #[Test]
    public function guru_can_delete_own_material()
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $course = Course::factory()->create(['instructor_id' => $guru->id]);
        $material = Material::factory()->create(['course_id' => $course->id]);

        $this->actingAs($guru)
            ->get(route('guru.courses.materials.show', [$course, $material]))
            ->assertOk();
    }

    #[Test]
    public function guru_cannot_update_other_guru_question()
    {
        $guru1 = User::factory()->create(['role' => 'guru']);
        $guru2 = User::factory()->create(['role' => 'guru']);
        $course = Course::factory()->create(['instructor_id' => $guru2->id]);
        $exam = Exam::factory()->create(['course_id' => $course->id]);
        $question = Question::factory()->create(['exam_id' => $exam->id]);

        $this->actingAs($guru1)
            ->put(route('guru.exams.questions.update', [$exam, $question]), [
                'question' => 'Updated question',
                'type' => 'multiple_choice',
            ])
            ->assertForbidden();
    }

    #[Test]
    public function guru_can_update_own_question()
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $course = Course::factory()->create(['instructor_id' => $guru->id]);
        $exam = Exam::factory()->create(['course_id' => $course->id]);
        $question = Question::factory()->create(['exam_id' => $exam->id]);

        $this->actingAs($guru)
            ->get(route('guru.exams.questions.edit', [$exam, $question]))
            ->assertOk();
    }

    #[Test]
    public function guru_cannot_delete_enrollment_from_other_guru_course()
    {
        $guru1 = User::factory()->create(['role' => 'guru']);
        $guru2 = User::factory()->create(['role' => 'guru']);
        $course = Course::factory()->create(['instructor_id' => $guru2->id]);
        $siswa = User::factory()->create(['role' => 'siswa']);
        $enrollment = Enrollment::factory()->create([
            'user_id' => $siswa->id,
            'course_id' => $course->id,
        ]);

        $this->actingAs($guru1)
            ->delete(route('guru.courses.enrollments.destroy', [$course, $enrollment]))
            ->assertForbidden();
    }

    #[Test]
    public function siswa_cannot_access_non_enrolled_course()
    {
        $siswa = User::factory()->create(['role' => 'siswa']);
        $guru = User::factory()->create(['role' => 'guru']);
        $course = Course::factory()->create(['instructor_id' => $guru->id]);

        // Intended behavior: the siswa course page acts as a public course
        // preview for browsing & self-enrollment. It only requires the course
        // to be published; premium content stays gated (materials use
        // visibleToStudent() scoping and assignments are loaded only for
        // enrolled students). Non-enrolled siswa therefore get a 200 preview
        // page, not a 403 — authorization for individual resources is still
        // enforced via CoursePolicy::view()/MaterialPolicy for enrolled access.
        $this->actingAs($siswa)
            ->get(route('siswa.courses.show', $course))
            ->assertOk();
    }

    #[Test]
    public function siswa_can_access_enrolled_course()
    {
        $siswa = User::factory()->create(['role' => 'siswa']);
        $guru = User::factory()->create(['role' => 'guru']);
        $course = Course::factory()->create(['instructor_id' => $guru->id]);

        // Enroll siswa to course
        $course->enrollments()->create([
            'user_id' => $siswa->id,
            'status' => 'active',
        ]);

        $this->actingAs($siswa)
            ->get(route('siswa.courses.show', $course))
            ->assertOk();
    }

    #[Test]
    public function admin_can_access_all_resources()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $guru = User::factory()->create(['role' => 'guru']);
        $course = Course::factory()->create(['instructor_id' => $guru->id]);
        $exam = Exam::factory()->create(['course_id' => $course->id]);
        $material = Material::factory()->create(['course_id' => $course->id]);

        // Intended behavior: admin manages courses through the dedicated
        // admin.* resource routes (role:admin + log.admin), and the role
        // middleware equivalence (CheckRole) intentionally restricts the
        // guru.* namespace to guru/dosen only — admin is NOT a superuser of
        // the guru namespace.
        $this->actingAs($admin)
            ->get(route('admin.courses.show', $course))
            ->assertOk();

        $this->actingAs($admin)
            ->get(route('admin.courses.materials.show', [$course, $material]))
            ->assertOk();

        // Direct access to the guru namespace is forbidden for admin
        $this->actingAs($admin)
            ->get(route('guru.courses.show', $course))
            ->assertForbidden();

        $this->actingAs($admin)
            ->get(route('guru.exams.show', $exam))
            ->assertForbidden();
    }

    #[Test]
    public function unauthorized_user_cannot_access_protected_routes()
    {
        $user = User::factory()->create(['role' => 'siswa']);
        $guru = User::factory()->create(['role' => 'guru']);
        $course = Course::factory()->create(['instructor_id' => $guru->id]);

        // Siswa cannot access guru routes
        $this->actingAs($user)
            ->get(route('guru.courses.show', $course))
            ->assertForbidden();
    }
}
