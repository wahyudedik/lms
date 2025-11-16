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
}

