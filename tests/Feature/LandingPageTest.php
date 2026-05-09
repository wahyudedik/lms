<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class LandingPageTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_landing_page_returns_200(): void
    {
        School::factory()->create(['is_landing_active' => true]);

        $response = $this->get('/');

        $response->assertStatus(200);
    }

    /** @test */
    public function test_landing_page_displays_active_school_name(): void
    {
        $school = School::factory()->create([
            'name' => 'Sekolah Aktif Test',
            'is_landing_active' => true,
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Sekolah Aktif Test');
    }

    /** @test */
    public function test_landing_page_falls_back_to_first_school_when_none_active(): void
    {
        $firstSchool = School::factory()->create([
            'name' => 'Sekolah Pertama',
            'is_landing_active' => false,
        ]);
        School::factory()->create([
            'name' => 'Sekolah Kedua',
            'is_landing_active' => false,
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Sekolah Pertama');
    }

    /** @test */
    public function test_landing_page_shows_no_courses_message_when_empty(): void
    {
        School::factory()->create(['is_landing_active' => true]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Belum ada kursus tersedia');
    }

    /** @test */
    public function test_landing_page_uses_cache(): void
    {
        $school = School::factory()->create(['is_landing_active' => true]);

        // Clear cache first
        Cache::forget(School::CACHE_KEY_ACTIVE_LANDING);

        $this->get('/');

        $this->assertTrue(Cache::has(School::CACHE_KEY_ACTIVE_LANDING));
    }

    /** @test */
    public function test_landing_page_shows_hero_title(): void
    {
        School::factory()->create([
            'is_landing_active' => true,
            'hero_title' => 'Judul Hero Kustom',
        ]);

        $response = $this->get('/');

        $response->assertSee('Judul Hero Kustom');
    }

    /** @test */
    public function test_landing_page_shows_default_hero_title_when_null(): void
    {
        School::factory()->create([
            'is_landing_active' => true,
            'hero_title' => null,
        ]);

        $response = $this->get('/');

        $response->assertSee('Get your');
        $response->assertSee('Education');
        $response->assertSee('today!');
    }
}
