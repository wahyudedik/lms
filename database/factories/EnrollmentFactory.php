<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Enrollment>
 */
class EnrollmentFactory extends Factory
{
    protected $model = Enrollment::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->state(['role' => 'siswa']),
            'course_id' => Course::factory(),
            'status' => 'active',
            'progress' => 0,
            'enrolled_at' => now(),
            'completed_at' => null,
        ];
    }

    /**
     * Indicate that the enrollment is completed (used by CertificateFactory).
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'progress' => 100,
            'completed_at' => now()->subDays(rand(1, 30)),
        ]);
    }
}
