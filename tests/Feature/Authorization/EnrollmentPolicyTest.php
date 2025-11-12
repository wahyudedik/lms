<?php

namespace Tests\Feature\Authorization;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnrollmentPolicyTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function all_users_can_view_any_enrollments()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $guru = User::factory()->create(['role' => 'guru']);
        $siswa = User::factory()->create(['role' => 'siswa']);

        $this->assertTrue($admin->can('viewAny', Enrollment::class));
        $this->assertTrue($guru->can('viewAny', Enrollment::class));
        $this->assertTrue($siswa->can('viewAny', Enrollment::class));
    }

    /** @test */
    public function admin_can_view_any_enrollment()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $guru = User::factory()->create(['role' => 'guru']);
        $course = Course::factory()->create(['instructor_id' => $guru->id]);
        $siswa = User::factory()->create(['role' => 'siswa']);
        $enrollment = Enrollment::factory()->create([
            'user_id' => $siswa->id,
            'course_id' => $course->id,
        ]);

        $this->assertTrue($admin->can('view', $enrollment));
    }

    /** @test */
    public function guru_can_view_enrollment_from_own_course()
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $course = Course::factory()->create(['instructor_id' => $guru->id]);
        $siswa = User::factory()->create(['role' => 'siswa']);
        $enrollment = Enrollment::factory()->create([
            'user_id' => $siswa->id,
            'course_id' => $course->id,
        ]);

        $this->assertTrue($guru->can('view', $enrollment));
    }

    /** @test */
    public function guru_cannot_view_enrollment_from_other_guru_course()
    {
        $guru1 = User::factory()->create(['role' => 'guru']);
        $guru2 = User::factory()->create(['role' => 'guru']);
        $course = Course::factory()->create(['instructor_id' => $guru2->id]);
        $siswa = User::factory()->create(['role' => 'siswa']);
        $enrollment = Enrollment::factory()->create([
            'user_id' => $siswa->id,
            'course_id' => $course->id,
        ]);

        $this->assertFalse($guru1->can('view', $enrollment));
    }

    /** @test */
    public function siswa_can_view_own_enrollment()
    {
        $siswa = User::factory()->create(['role' => 'siswa']);
        $guru = User::factory()->create(['role' => 'guru']);
        $course = Course::factory()->create(['instructor_id' => $guru->id]);
        $enrollment = Enrollment::factory()->create([
            'user_id' => $siswa->id,
            'course_id' => $course->id,
        ]);

        $this->assertTrue($siswa->can('view', $enrollment));
    }

    /** @test */
    public function siswa_cannot_view_other_siswa_enrollment()
    {
        $siswa1 = User::factory()->create(['role' => 'siswa']);
        $siswa2 = User::factory()->create(['role' => 'siswa']);
        $guru = User::factory()->create(['role' => 'guru']);
        $course = Course::factory()->create(['instructor_id' => $guru->id]);
        $enrollment = Enrollment::factory()->create([
            'user_id' => $siswa2->id,
            'course_id' => $course->id,
        ]);

        $this->assertFalse($siswa1->can('view', $enrollment));
    }

    /** @test */
    public function admin_can_create_enrollments()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->assertTrue($admin->can('create', Enrollment::class));
    }

    /** @test */
    public function guru_can_create_enrollments()
    {
        $guru = User::factory()->create(['role' => 'guru']);

        $this->assertTrue($guru->can('create', Enrollment::class));
    }

    /** @test */
    public function siswa_can_create_enrollments()
    {
        $siswa = User::factory()->create(['role' => 'siswa']);

        $this->assertTrue($siswa->can('create', Enrollment::class));
    }

    /** @test */
    public function admin_can_update_any_enrollment()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $guru = User::factory()->create(['role' => 'guru']);
        $course = Course::factory()->create(['instructor_id' => $guru->id]);
        $siswa = User::factory()->create(['role' => 'siswa']);
        $enrollment = Enrollment::factory()->create([
            'user_id' => $siswa->id,
            'course_id' => $course->id,
        ]);

        $this->assertTrue($admin->can('update', $enrollment));
    }

    /** @test */
    public function guru_can_update_enrollment_from_own_course()
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $course = Course::factory()->create(['instructor_id' => $guru->id]);
        $siswa = User::factory()->create(['role' => 'siswa']);
        $enrollment = Enrollment::factory()->create([
            'user_id' => $siswa->id,
            'course_id' => $course->id,
        ]);

        $this->assertTrue($guru->can('update', $enrollment));
    }

    /** @test */
    public function siswa_cannot_update_enrollment()
    {
        $siswa = User::factory()->create(['role' => 'siswa']);
        $guru = User::factory()->create(['role' => 'guru']);
        $course = Course::factory()->create(['instructor_id' => $guru->id]);
        $enrollment = Enrollment::factory()->create([
            'user_id' => $siswa->id,
            'course_id' => $course->id,
        ]);

        $this->assertFalse($siswa->can('update', $enrollment));
    }
}

