<?php

namespace Tests\Feature\Authorization;

use App\Models\Course;
use App\Models\Exam;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExamPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function admin_can_view_any_exams()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->assertTrue($admin->can('viewAny', Exam::class));
    }

    /** @test */
    public function guru_can_view_any_exams()
    {
        $guru = User::factory()->create(['role' => 'guru']);

        $this->assertTrue($guru->can('viewAny', Exam::class));
    }

    /** @test */
    public function siswa_cannot_view_any_exams()
    {
        $siswa = User::factory()->create(['role' => 'siswa']);

        $this->assertFalse($siswa->can('viewAny', Exam::class));
    }

    /** @test */
    public function admin_can_view_any_exam()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $guru = User::factory()->create(['role' => 'guru']);
        $course = Course::factory()->create(['instructor_id' => $guru->id]);
        $exam = Exam::factory()->create(['course_id' => $course->id]);

        $this->assertTrue($admin->can('view', $exam));
    }

    /** @test */
    public function guru_can_view_own_exam()
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $course = Course::factory()->create(['instructor_id' => $guru->id]);
        $exam = Exam::factory()->create(['course_id' => $course->id]);

        $this->assertTrue($guru->can('view', $exam));
    }

    /** @test */
    public function guru_cannot_view_other_guru_exam()
    {
        $guru1 = User::factory()->create(['role' => 'guru']);
        $guru2 = User::factory()->create(['role' => 'guru']);
        $course = Course::factory()->create(['instructor_id' => $guru2->id]);
        $exam = Exam::factory()->create(['course_id' => $course->id]);

        $this->assertFalse($guru1->can('view', $exam));
    }

    /** @test */
    public function siswa_can_view_exam_from_enrolled_course()
    {
        $siswa = User::factory()->create(['role' => 'siswa']);
        $guru = User::factory()->create(['role' => 'guru']);
        $course = Course::factory()->create(['instructor_id' => $guru->id]);
        $exam = Exam::factory()->create(['course_id' => $course->id]);

        // Enroll siswa to course
        $course->enrollments()->create([
            'user_id' => $siswa->id,
            'status' => 'active',
        ]);

        $this->assertTrue($siswa->can('view', $exam));
    }

    /** @test */
    public function siswa_cannot_view_exam_from_non_enrolled_course()
    {
        $siswa = User::factory()->create(['role' => 'siswa']);
        $guru = User::factory()->create(['role' => 'guru']);
        $course = Course::factory()->create(['instructor_id' => $guru->id]);
        $exam = Exam::factory()->create(['course_id' => $course->id]);

        $this->assertFalse($siswa->can('view', $exam));
    }

    /** @test */
    public function admin_can_create_exams()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->assertTrue($admin->can('create', Exam::class));
    }

    /** @test */
    public function guru_can_create_exams()
    {
        $guru = User::factory()->create(['role' => 'guru']);

        $this->assertTrue($guru->can('create', Exam::class));
    }

    /** @test */
    public function siswa_cannot_create_exams()
    {
        $siswa = User::factory()->create(['role' => 'siswa']);

        $this->assertFalse($siswa->can('create', Exam::class));
    }

    /** @test */
    public function admin_can_update_any_exam()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $guru = User::factory()->create(['role' => 'guru']);
        $course = Course::factory()->create(['instructor_id' => $guru->id]);
        $exam = Exam::factory()->create(['course_id' => $course->id]);

        $this->assertTrue($admin->can('update', $exam));
    }

    /** @test */
    public function guru_can_update_own_exam()
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $course = Course::factory()->create(['instructor_id' => $guru->id]);
        $exam = Exam::factory()->create(['course_id' => $course->id]);

        $this->assertTrue($guru->can('update', $exam));
    }

    /** @test */
    public function guru_cannot_update_other_guru_exam()
    {
        $guru1 = User::factory()->create(['role' => 'guru']);
        $guru2 = User::factory()->create(['role' => 'guru']);
        $course = Course::factory()->create(['instructor_id' => $guru2->id]);
        $exam = Exam::factory()->create(['course_id' => $course->id]);

        $this->assertFalse($guru1->can('update', $exam));
    }

    /** @test */
    public function admin_can_delete_any_exam()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $guru = User::factory()->create(['role' => 'guru']);
        $course = Course::factory()->create(['instructor_id' => $guru->id]);
        $exam = Exam::factory()->create(['course_id' => $course->id]);

        $this->assertTrue($admin->can('delete', $exam));
    }

    /** @test */
    public function guru_can_delete_own_exam()
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $course = Course::factory()->create(['instructor_id' => $guru->id]);
        $exam = Exam::factory()->create(['course_id' => $course->id]);

        $this->assertTrue($guru->can('delete', $exam));
    }

    /** @test */
    public function guru_cannot_delete_other_guru_exam()
    {
        $guru1 = User::factory()->create(['role' => 'guru']);
        $guru2 = User::factory()->create(['role' => 'guru']);
        $course = Course::factory()->create(['instructor_id' => $guru2->id]);
        $exam = Exam::factory()->create(['course_id' => $course->id]);

        $this->assertFalse($guru1->can('delete', $exam));
    }
}

