<?php

namespace Tests\Feature\Authorization;

use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CoursePolicyTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function all_users_can_view_any_courses()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $guru = User::factory()->create(['role' => 'guru']);
        $siswa = User::factory()->create(['role' => 'siswa']);

        $this->assertTrue($admin->can('viewAny', Course::class));
        $this->assertTrue($guru->can('viewAny', Course::class));
        $this->assertTrue($siswa->can('viewAny', Course::class));
    }

    /** @test */
    public function admin_can_view_any_course()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $guru = User::factory()->create(['role' => 'guru']);
        $course = Course::factory()->create(['instructor_id' => $guru->id]);

        $this->assertTrue($admin->can('view', $course));
    }

    /** @test */
    public function guru_can_view_own_course()
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $course = Course::factory()->create(['instructor_id' => $guru->id]);

        $this->assertTrue($guru->can('view', $course));
    }

    /** @test */
    public function guru_cannot_view_other_guru_course()
    {
        $guru1 = User::factory()->create(['role' => 'guru']);
        $guru2 = User::factory()->create(['role' => 'guru']);
        $course = Course::factory()->create(['instructor_id' => $guru2->id]);

        $this->assertFalse($guru1->can('view', $course));
    }

    /** @test */
    public function siswa_can_view_enrolled_course()
    {
        $siswa = User::factory()->create(['role' => 'siswa']);
        $guru = User::factory()->create(['role' => 'guru']);
        $course = Course::factory()->create(['instructor_id' => $guru->id]);

        // Enroll siswa to course
        $course->enrollments()->create([
            'user_id' => $siswa->id,
            'status' => 'active',
        ]);

        $this->assertTrue($siswa->can('view', $course));
    }

    /** @test */
    public function siswa_cannot_view_non_enrolled_course()
    {
        $siswa = User::factory()->create(['role' => 'siswa']);
        $guru = User::factory()->create(['role' => 'guru']);
        $course = Course::factory()->create(['instructor_id' => $guru->id]);

        $this->assertFalse($siswa->can('view', $course));
    }

    /** @test */
    public function admin_can_create_courses()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->assertTrue($admin->can('create', Course::class));
    }

    /** @test */
    public function guru_can_create_courses()
    {
        $guru = User::factory()->create(['role' => 'guru']);

        $this->assertTrue($guru->can('create', Course::class));
    }

    /** @test */
    public function siswa_cannot_create_courses()
    {
        $siswa = User::factory()->create(['role' => 'siswa']);

        $this->assertFalse($siswa->can('create', Course::class));
    }

    /** @test */
    public function admin_can_update_any_course()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $guru = User::factory()->create(['role' => 'guru']);
        $course = Course::factory()->create(['instructor_id' => $guru->id]);

        $this->assertTrue($admin->can('update', $course));
    }

    /** @test */
    public function guru_can_update_own_course()
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $course = Course::factory()->create(['instructor_id' => $guru->id]);

        $this->assertTrue($guru->can('update', $course));
    }

    /** @test */
    public function guru_cannot_update_other_guru_course()
    {
        $guru1 = User::factory()->create(['role' => 'guru']);
        $guru2 = User::factory()->create(['role' => 'guru']);
        $course = Course::factory()->create(['instructor_id' => $guru2->id]);

        $this->assertFalse($guru1->can('update', $course));
    }
}

