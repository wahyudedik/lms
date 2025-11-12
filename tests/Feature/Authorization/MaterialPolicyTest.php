<?php

namespace Tests\Feature\Authorization;

use App\Models\Course;
use App\Models\Material;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MaterialPolicyTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function all_users_can_view_any_materials()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $guru = User::factory()->create(['role' => 'guru']);
        $siswa = User::factory()->create(['role' => 'siswa']);

        $this->assertTrue($admin->can('viewAny', Material::class));
        $this->assertTrue($guru->can('viewAny', Material::class));
        $this->assertTrue($siswa->can('viewAny', Material::class));
    }

    /** @test */
    public function admin_can_view_any_material()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $guru = User::factory()->create(['role' => 'guru']);
        $course = Course::factory()->create(['instructor_id' => $guru->id]);
        $material = Material::factory()->create(['course_id' => $course->id]);

        $this->assertTrue($admin->can('view', $material));
    }

    /** @test */
    public function guru_can_view_own_material()
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $course = Course::factory()->create(['instructor_id' => $guru->id]);
        $material = Material::factory()->create(['course_id' => $course->id]);

        $this->assertTrue($guru->can('view', $material));
    }

    /** @test */
    public function guru_cannot_view_other_guru_material()
    {
        $guru1 = User::factory()->create(['role' => 'guru']);
        $guru2 = User::factory()->create(['role' => 'guru']);
        $course = Course::factory()->create(['instructor_id' => $guru2->id]);
        $material = Material::factory()->create(['course_id' => $course->id]);

        $this->assertFalse($guru1->can('view', $material));
    }

    /** @test */
    public function siswa_can_view_material_from_enrolled_course()
    {
        $siswa = User::factory()->create(['role' => 'siswa']);
        $guru = User::factory()->create(['role' => 'guru']);
        $course = Course::factory()->create(['instructor_id' => $guru->id]);
        $material = Material::factory()->create(['course_id' => $course->id]);

        // Enroll siswa to course
        $course->enrollments()->create([
            'user_id' => $siswa->id,
            'status' => 'active',
        ]);

        $this->assertTrue($siswa->can('view', $material));
    }

    /** @test */
    public function siswa_cannot_view_material_from_non_enrolled_course()
    {
        $siswa = User::factory()->create(['role' => 'siswa']);
        $guru = User::factory()->create(['role' => 'guru']);
        $course = Course::factory()->create(['instructor_id' => $guru->id]);
        $material = Material::factory()->create(['course_id' => $course->id]);

        $this->assertFalse($siswa->can('view', $material));
    }

    /** @test */
    public function admin_can_create_materials()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->assertTrue($admin->can('create', Material::class));
    }

    /** @test */
    public function guru_can_create_materials()
    {
        $guru = User::factory()->create(['role' => 'guru']);

        $this->assertTrue($guru->can('create', Material::class));
    }

    /** @test */
    public function siswa_cannot_create_materials()
    {
        $siswa = User::factory()->create(['role' => 'siswa']);

        $this->assertFalse($siswa->can('create', Material::class));
    }

    /** @test */
    public function admin_can_update_any_material()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $guru = User::factory()->create(['role' => 'guru']);
        $course = Course::factory()->create(['instructor_id' => $guru->id]);
        $material = Material::factory()->create(['course_id' => $course->id]);

        $this->assertTrue($admin->can('update', $material));
    }

    /** @test */
    public function guru_can_update_own_material()
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $course = Course::factory()->create(['instructor_id' => $guru->id]);
        $material = Material::factory()->create(['course_id' => $course->id]);

        $this->assertTrue($guru->can('update', $material));
    }

    /** @test */
    public function guru_cannot_update_other_guru_material()
    {
        $guru1 = User::factory()->create(['role' => 'guru']);
        $guru2 = User::factory()->create(['role' => 'guru']);
        $course = Course::factory()->create(['instructor_id' => $guru2->id]);
        $material = Material::factory()->create(['course_id' => $course->id]);

        $this->assertFalse($guru1->can('update', $material));
    }
}

