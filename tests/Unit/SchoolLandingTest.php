<?php

namespace Tests\Unit;

use App\Models\School;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class SchoolLandingTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_activate_for_landing_sets_school_as_active(): void
    {
        $school = School::factory()->create(['is_landing_active' => false]);

        $school->activateForLanding();

        $this->assertTrue($school->fresh()->is_landing_active);
    }

    /** @test */
    public function test_activate_for_landing_deactivates_all_other_schools(): void
    {
        $school1 = School::factory()->create(['is_landing_active' => true]);
        $school2 = School::factory()->create(['is_landing_active' => false]);
        $school3 = School::factory()->create(['is_landing_active' => false]);

        $school2->activateForLanding();

        $this->assertFalse($school1->fresh()->is_landing_active);
        $this->assertTrue($school2->fresh()->is_landing_active);
        $this->assertFalse($school3->fresh()->is_landing_active);
    }

    /** @test */
    public function test_deactivate_for_landing_sets_school_as_inactive(): void
    {
        $school = School::factory()->create(['is_landing_active' => true]);

        $school->deactivateForLanding();

        $this->assertFalse($school->fresh()->is_landing_active);
    }

    /** @test */
    public function test_get_active_landing_school_returns_active_school(): void
    {
        $activeSchool = School::factory()->create(['is_landing_active' => true]);
        School::factory()->create(['is_landing_active' => false]);

        $result = School::getActiveLandingSchool();

        $this->assertNotNull($result);
        $this->assertEquals($activeSchool->id, $result->id);
    }

    /** @test */
    public function test_get_active_landing_school_falls_back_to_first_school(): void
    {
        $firstSchool = School::factory()->create(['is_landing_active' => false]);
        School::factory()->create(['is_landing_active' => false]);

        $result = School::getActiveLandingSchool();

        $this->assertNotNull($result);
        $this->assertEquals($firstSchool->id, $result->id);
    }

    /** @test */
    public function test_get_active_landing_school_returns_null_when_no_schools(): void
    {
        $result = School::getActiveLandingSchool();

        $this->assertNull($result);
    }

    /** @test */
    public function test_activate_for_landing_clears_cache(): void
    {
        $school = School::factory()->create();

        // Prime the cache
        Cache::put(School::CACHE_KEY_ACTIVE_LANDING, $school, School::CACHE_TTL_LANDING);
        $this->assertTrue(Cache::has(School::CACHE_KEY_ACTIVE_LANDING));

        $school->activateForLanding();

        $this->assertFalse(Cache::has(School::CACHE_KEY_ACTIVE_LANDING));
    }

    /** @test */
    public function test_deactivate_for_landing_clears_cache(): void
    {
        $school = School::factory()->create(['is_landing_active' => true]);

        // Prime the cache
        Cache::put(School::CACHE_KEY_ACTIVE_LANDING, $school, School::CACHE_TTL_LANDING);
        $this->assertTrue(Cache::has(School::CACHE_KEY_ACTIVE_LANDING));

        $school->deactivateForLanding();

        $this->assertFalse(Cache::has(School::CACHE_KEY_ACTIVE_LANDING));
    }

    /** @test */
    public function test_get_active_landing_school_caches_result(): void
    {
        $school = School::factory()->create(['is_landing_active' => true]);

        // Clear cache first
        Cache::forget(School::CACHE_KEY_ACTIVE_LANDING);

        School::getActiveLandingSchool();

        $this->assertTrue(Cache::has(School::CACHE_KEY_ACTIVE_LANDING));
    }

    /** @test */
    public function test_scope_landing_active_filters_correctly(): void
    {
        School::factory()->create(['is_landing_active' => true]);
        School::factory()->create(['is_landing_active' => false]);
        School::factory()->create(['is_landing_active' => false]);

        $activeSchools = School::landingActive()->get();

        $this->assertCount(1, $activeSchools);
        $this->assertTrue($activeSchools->first()->is_landing_active);
    }

    /** @test */
    public function test_is_landing_active_is_cast_to_boolean(): void
    {
        $school = School::factory()->create(['is_landing_active' => false]);

        $this->assertIsBool($school->is_landing_active);
    }
}
