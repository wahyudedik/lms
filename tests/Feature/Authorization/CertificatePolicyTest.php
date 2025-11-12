<?php

namespace Tests\Feature\Authorization;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CertificatePolicyTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function all_users_can_view_any_certificates()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $guru = User::factory()->create(['role' => 'guru']);
        $siswa = User::factory()->create(['role' => 'siswa']);

        $this->assertTrue($admin->can('viewAny', Certificate::class));
        $this->assertTrue($guru->can('viewAny', Certificate::class));
        $this->assertTrue($siswa->can('viewAny', Certificate::class));
    }

    /** @test */
    public function owner_can_view_certificate()
    {
        $siswa = User::factory()->create(['role' => 'siswa']);
        $guru = User::factory()->create(['role' => 'guru']);
        $course = Course::factory()->create(['instructor_id' => $guru->id]);
        $enrollment = Enrollment::factory()->create([
            'user_id' => $siswa->id,
            'course_id' => $course->id,
        ]);
        $certificate = Certificate::factory()->create([
            'user_id' => $siswa->id,
            'course_id' => $course->id,
            'enrollment_id' => $enrollment->id,
        ]);

        $this->assertTrue($siswa->can('view', $certificate));
    }

    /** @test */
    public function admin_can_view_any_certificate()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $siswa = User::factory()->create(['role' => 'siswa']);
        $guru = User::factory()->create(['role' => 'guru']);
        $course = Course::factory()->create(['instructor_id' => $guru->id]);
        $certificate = Certificate::factory()->create([
            'user_id' => $siswa->id,
            'course_id' => $course->id,
        ]);

        $this->assertTrue($admin->can('view', $certificate));
    }

    /** @test */
    public function guru_can_view_certificate_from_own_course()
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $course = Course::factory()->create(['instructor_id' => $guru->id]);
        $siswa = User::factory()->create(['role' => 'siswa']);
        $certificate = Certificate::factory()->create([
            'user_id' => $siswa->id,
            'course_id' => $course->id,
        ]);

        $this->assertTrue($guru->can('view', $certificate));
    }

    /** @test */
    public function guru_cannot_view_certificate_from_other_guru_course()
    {
        $guru1 = User::factory()->create(['role' => 'guru']);
        $guru2 = User::factory()->create(['role' => 'guru']);
        $course = Course::factory()->create(['instructor_id' => $guru2->id]);
        $siswa = User::factory()->create(['role' => 'siswa']);
        $certificate = Certificate::factory()->create([
            'user_id' => $siswa->id,
            'course_id' => $course->id,
        ]);

        $this->assertFalse($guru1->can('view', $certificate));
    }

    /** @test */
    public function siswa_cannot_view_other_siswa_certificate()
    {
        $siswa1 = User::factory()->create(['role' => 'siswa']);
        $siswa2 = User::factory()->create(['role' => 'siswa']);
        $guru = User::factory()->create(['role' => 'guru']);
        $course = Course::factory()->create(['instructor_id' => $guru->id]);
        $certificate = Certificate::factory()->create([
            'user_id' => $siswa2->id,
            'course_id' => $course->id,
        ]);

        $this->assertFalse($siswa1->can('view', $certificate));
    }

    /** @test */
    public function admin_can_download_any_certificate()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $siswa = User::factory()->create(['role' => 'siswa']);
        $guru = User::factory()->create(['role' => 'guru']);
        $course = Course::factory()->create(['instructor_id' => $guru->id]);
        $certificate = Certificate::factory()->create([
            'user_id' => $siswa->id,
            'course_id' => $course->id,
        ]);

        $this->assertTrue($admin->can('download', $certificate));
    }

    /** @test */
    public function owner_can_download_certificate()
    {
        $siswa = User::factory()->create(['role' => 'siswa']);
        $guru = User::factory()->create(['role' => 'guru']);
        $course = Course::factory()->create(['instructor_id' => $guru->id]);
        $certificate = Certificate::factory()->create([
            'user_id' => $siswa->id,
            'course_id' => $course->id,
        ]);

        $this->assertTrue($siswa->can('download', $certificate));
    }

    /** @test */
    public function admin_can_delete_any_certificate()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $siswa = User::factory()->create(['role' => 'siswa']);
        $guru = User::factory()->create(['role' => 'guru']);
        $course = Course::factory()->create(['instructor_id' => $guru->id]);
        $certificate = Certificate::factory()->create([
            'user_id' => $siswa->id,
            'course_id' => $course->id,
        ]);

        $this->assertTrue($admin->can('delete', $certificate));
    }

    /** @test */
    public function guru_cannot_delete_certificate()
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $course = Course::factory()->create(['instructor_id' => $guru->id]);
        $siswa = User::factory()->create(['role' => 'siswa']);
        $certificate = Certificate::factory()->create([
            'user_id' => $siswa->id,
            'course_id' => $course->id,
        ]);

        $this->assertFalse($guru->can('delete', $certificate));
    }

    /** @test */
    public function siswa_cannot_delete_certificate()
    {
        $siswa = User::factory()->create(['role' => 'siswa']);
        $guru = User::factory()->create(['role' => 'guru']);
        $course = Course::factory()->create(['instructor_id' => $guru->id]);
        $certificate = Certificate::factory()->create([
            'user_id' => $siswa->id,
            'course_id' => $course->id,
        ]);

        $this->assertFalse($siswa->can('delete', $certificate));
    }
}

