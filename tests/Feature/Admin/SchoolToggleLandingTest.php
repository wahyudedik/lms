<?php

namespace Tests\Feature\Admin;

use App\Models\AuthorizationLog;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class SchoolToggleLandingTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $siswa;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        $this->siswa = User::factory()->create([
            'role' => 'siswa',
            'email_verified_at' => now(),
            'is_active' => true,
        ]);
    }

    /** @test */
    public function test_admin_can_activate_landing_page(): void
    {
        $school = School::factory()->create(['is_landing_active' => false]);

        $response = $this->actingAs($this->admin)
            ->postJson("/admin/schools/{$school->id}/toggle-landing");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'is_active' => true,
            ]);

        $this->assertTrue($school->fresh()->is_landing_active);
    }

    /** @test */
    public function test_admin_can_deactivate_landing_page(): void
    {
        $school = School::factory()->create(['is_landing_active' => true]);

        $response = $this->actingAs($this->admin)
            ->postJson("/admin/schools/{$school->id}/toggle-landing");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'is_active' => false,
            ]);

        $this->assertFalse($school->fresh()->is_landing_active);
    }

    /** @test */
    public function test_activating_one_school_deactivates_others(): void
    {
        $school1 = School::factory()->create(['is_landing_active' => true]);
        $school2 = School::factory()->create(['is_landing_active' => false]);

        $this->actingAs($this->admin)
            ->postJson("/admin/schools/{$school2->id}/toggle-landing");

        $this->assertFalse($school1->fresh()->is_landing_active);
        $this->assertTrue($school2->fresh()->is_landing_active);
    }

    /** @test */
    public function test_unauthenticated_user_cannot_toggle_landing(): void
    {
        $school = School::factory()->create();

        $response = $this->postJson("/admin/schools/{$school->id}/toggle-landing");

        $response->assertStatus(401);
    }

    /** @test */
    public function test_non_admin_cannot_toggle_landing(): void
    {
        $school = School::factory()->create();

        $response = $this->actingAs($this->siswa)
            ->postJson("/admin/schools/{$school->id}/toggle-landing");

        $response->assertStatus(403);
    }

    /** @test */
    public function test_toggle_returns_correct_json_structure(): void
    {
        $school = School::factory()->create(['is_landing_active' => false]);

        $response = $this->actingAs($this->admin)
            ->postJson("/admin/schools/{$school->id}/toggle-landing");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'is_active',
            ]);
    }

    /** @test */
    public function test_toggle_clears_cache(): void
    {
        $school = School::factory()->create(['is_landing_active' => false]);

        // Prime the cache
        Cache::put(School::CACHE_KEY_ACTIVE_LANDING, $school, School::CACHE_TTL_LANDING);

        $this->actingAs($this->admin)
            ->postJson("/admin/schools/{$school->id}/toggle-landing");

        $this->assertFalse(Cache::has(School::CACHE_KEY_ACTIVE_LANDING));
    }

    /** @test */
    public function test_toggle_logs_authorization_action(): void
    {
        $school = School::factory()->create(['is_landing_active' => false]);

        $this->actingAs($this->admin)
            ->postJson("/admin/schools/{$school->id}/toggle-landing");

        $this->assertDatabaseHas('authorization_logs', [
            'user_id' => $this->admin->id,
            'action' => 'activated_landing',
            'resource_type' => School::class,
            'resource_id' => $school->id,
        ]);
    }

    /** @test */
    public function test_invalid_school_id_returns_404(): void
    {
        $response = $this->actingAs($this->admin)
            ->postJson('/admin/schools/99999/toggle-landing');

        $response->assertStatus(404);
    }

    /** @test */
    public function test_toggle_response_contains_success_message(): void
    {
        $school = School::factory()->create([
            'name' => 'Sekolah Test',
            'is_landing_active' => false,
        ]);

        $response = $this->actingAs($this->admin)
            ->postJson("/admin/schools/{$school->id}/toggle-landing");

        $response->assertStatus(200);
        $data = $response->json();
        $this->assertStringContainsString('Sekolah Test', $data['message']);
    }
}
