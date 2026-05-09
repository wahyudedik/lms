<?php

namespace Tests\Feature\Admin;

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
        $school = School::factory()->create(['show_landing_page' => false]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.settings.landing.update'), [
                'show_landing_page' => true,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertTrue($school->fresh()->show_landing_page);
    }

    /** @test */
    public function test_admin_can_deactivate_landing_page(): void
    {
        $school = School::factory()->create(['show_landing_page' => true]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.settings.landing.update'), [
                // Not sending show_landing_page means it will be set to false
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertFalse($school->fresh()->show_landing_page);
    }

    /** @test */
    public function test_updating_landing_page_with_content(): void
    {
        $school = School::factory()->create(['show_landing_page' => false]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.settings.landing.update'), [
                'show_landing_page' => true,
                'hero_title' => 'Selamat Datang',
                'hero_subtitle' => 'Platform Belajar Terbaik',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $school->refresh();
        $this->assertTrue($school->show_landing_page);
        $this->assertEquals('Selamat Datang', $school->hero_title);
        $this->assertEquals('Platform Belajar Terbaik', $school->hero_subtitle);
    }

    /** @test */
    public function test_unauthenticated_user_cannot_update_landing(): void
    {
        School::factory()->create();

        $response = $this->post(route('admin.settings.landing.update'), [
            'show_landing_page' => true,
        ]);

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function test_non_admin_cannot_update_landing(): void
    {
        School::factory()->create();

        $response = $this->actingAs($this->siswa)
            ->post(route('admin.settings.landing.update'), [
                'show_landing_page' => true,
            ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function test_update_redirects_to_landing_tab(): void
    {
        $school = School::factory()->create(['show_landing_page' => false]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.settings.landing.update'), [
                'show_landing_page' => true,
            ]);

        $response->assertRedirect(route('admin.settings.index', ['tab' => 'landing']));
    }

    /** @test */
    public function test_update_clears_cache(): void
    {
        $school = School::factory()->create(['show_landing_page' => false]);

        // Prime the cache
        Cache::put(School::CACHE_KEY_ACTIVE_LANDING, $school, School::CACHE_TTL_LANDING);

        $this->actingAs($this->admin)
            ->post(route('admin.settings.landing.update'), [
                'show_landing_page' => true,
            ]);

        $this->assertFalse(Cache::has(School::CACHE_KEY_ACTIVE_LANDING));
    }

    /** @test */
    public function test_update_validates_hero_image(): void
    {
        School::factory()->create();

        $response = $this->actingAs($this->admin)
            ->post(route('admin.settings.landing.update'), [
                'hero_image' => 'not-a-file',
            ]);

        $response->assertSessionHasErrors('hero_image');
    }

    /** @test */
    public function test_update_returns_success_message(): void
    {
        School::factory()->create([
            'name' => 'Sekolah Test',
            'show_landing_page' => false,
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.settings.landing.update'), [
                'show_landing_page' => true,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }
}
