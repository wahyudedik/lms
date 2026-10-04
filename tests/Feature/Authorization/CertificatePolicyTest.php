<?php

namespace Tests\Feature\Authorization;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CertificatePolicyTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function all_users_can_view_any_certificates()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $guru = User::factory()->create(['role' => 'guru']);
        $siswa = User::factory()->create(['role' => 'siswa']);

        $this->assertTrue($admin->can('viewAny', Certificate::class));
        $this->assertTrue($guru->can('viewAny', Certificate::class));
        $this->assertTrue($siswa->can('viewAny', Certificate::class));
    }

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
    public function staff_roles_can_create_certificates()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $guru = User::factory()->create(['role' => 'guru']);
        $dosen = User::factory()->create(['role' => 'dosen']);

        $this->assertTrue($admin->can('create', Certificate::class));
        $this->assertTrue($guru->can('create', Certificate::class));
        $this->assertTrue($dosen->can('create', Certificate::class));
    }

    #[Test]
    public function students_cannot_create_certificates()
    {
        $siswa = User::factory()->create(['role' => 'siswa']);
        $mahasiswa = User::factory()->create(['role' => 'mahasiswa']);

        $this->assertFalse($siswa->can('create', Certificate::class));
        $this->assertFalse($mahasiswa->can('create', Certificate::class));
    }
}
