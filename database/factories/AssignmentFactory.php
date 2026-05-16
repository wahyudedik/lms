<?php

namespace Database\Factories;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Assignment>
 */
class AssignmentFactory extends Factory
{
    protected $model = Assignment::class;

    public function definition(): array
    {
        return [
            'course_id' => Course::factory(),
            'created_by' => User::factory()->state(['role' => 'guru']),
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'deadline' => now()->addDays($this->faker->numberBetween(7, 30)),
            'max_score' => 100,
            'allowed_file_types' => ['pdf', 'doc', 'docx'],
            'late_policy' => 'reject',
            'penalty_percentage' => 0,
            'is_published' => true,
            'published_at' => now(),
        ];
    }
}
