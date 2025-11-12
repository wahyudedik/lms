<?php

namespace Tests\Feature\Authorization;

use App\Models\Course;
use App\Models\Exam;
use App\Models\Question;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuestionPolicyTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_view_any_questions()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->assertTrue($admin->can('viewAny', Question::class));
    }

    /** @test */
    public function guru_can_view_any_questions()
    {
        $guru = User::factory()->create(['role' => 'guru']);

        $this->assertTrue($guru->can('viewAny', Question::class));
    }

    /** @test */
    public function siswa_cannot_view_any_questions()
    {
        $siswa = User::factory()->create(['role' => 'siswa']);

        $this->assertFalse($siswa->can('viewAny', Question::class));
    }

    /** @test */
    public function admin_can_view_any_question()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $guru = User::factory()->create(['role' => 'guru']);
        $course = Course::factory()->create(['instructor_id' => $guru->id]);
        $exam = Exam::factory()->create(['course_id' => $course->id]);
        $question = Question::factory()->create(['exam_id' => $exam->id]);

        $this->assertTrue($admin->can('view', $question));
    }

    /** @test */
    public function guru_can_view_own_question()
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $course = Course::factory()->create(['instructor_id' => $guru->id]);
        $exam = Exam::factory()->create(['course_id' => $course->id]);
        $question = Question::factory()->create(['exam_id' => $exam->id]);

        $this->assertTrue($guru->can('view', $question));
    }

    /** @test */
    public function guru_cannot_view_other_guru_question()
    {
        $guru1 = User::factory()->create(['role' => 'guru']);
        $guru2 = User::factory()->create(['role' => 'guru']);
        $course = Course::factory()->create(['instructor_id' => $guru2->id]);
        $exam = Exam::factory()->create(['course_id' => $course->id]);
        $question = Question::factory()->create(['exam_id' => $exam->id]);

        $this->assertFalse($guru1->can('view', $question));
    }

    /** @test */
    public function siswa_can_view_question_from_enrolled_course()
    {
        $siswa = User::factory()->create(['role' => 'siswa']);
        $guru = User::factory()->create(['role' => 'guru']);
        $course = Course::factory()->create(['instructor_id' => $guru->id]);
        $exam = Exam::factory()->create(['course_id' => $course->id]);
        $question = Question::factory()->create(['exam_id' => $exam->id]);

        // Enroll siswa to course
        $course->enrollments()->create([
            'user_id' => $siswa->id,
            'status' => 'active',
        ]);

        $this->assertTrue($siswa->can('view', $question));
    }

    /** @test */
    public function admin_can_create_questions()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->assertTrue($admin->can('create', Question::class));
    }

    /** @test */
    public function guru_can_create_questions()
    {
        $guru = User::factory()->create(['role' => 'guru']);

        $this->assertTrue($guru->can('create', Question::class));
    }

    /** @test */
    public function siswa_cannot_create_questions()
    {
        $siswa = User::factory()->create(['role' => 'siswa']);

        $this->assertFalse($siswa->can('create', Question::class));
    }

    /** @test */
    public function admin_can_update_any_question()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $guru = User::factory()->create(['role' => 'guru']);
        $course = Course::factory()->create(['instructor_id' => $guru->id]);
        $exam = Exam::factory()->create(['course_id' => $course->id]);
        $question = Question::factory()->create(['exam_id' => $exam->id]);

        $this->assertTrue($admin->can('update', $question));
    }

    /** @test */
    public function guru_can_update_own_question()
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $course = Course::factory()->create(['instructor_id' => $guru->id]);
        $exam = Exam::factory()->create(['course_id' => $course->id]);
        $question = Question::factory()->create(['exam_id' => $exam->id]);

        $this->assertTrue($guru->can('update', $question));
    }

    /** @test */
    public function guru_cannot_update_other_guru_question()
    {
        $guru1 = User::factory()->create(['role' => 'guru']);
        $guru2 = User::factory()->create(['role' => 'guru']);
        $course = Course::factory()->create(['instructor_id' => $guru2->id]);
        $exam = Exam::factory()->create(['course_id' => $course->id]);
        $question = Question::factory()->create(['exam_id' => $exam->id]);

        $this->assertFalse($guru1->can('update', $question));
    }
}

