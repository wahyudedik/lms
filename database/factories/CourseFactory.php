<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Course>
 */
class CourseFactory extends Factory
{
    protected $model = Course::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'code' => strtoupper(Str::random(6)),
            'description' => $this->faker->paragraph(),
            'instructor_id' => User::factory()->state(['role' => 'guru']),
            'status' => 'published',
            'cover_image' => null,
            'max_students' => $this->faker->numberBetween(20, 50),
            'published_at' => now(),
        ];
    }
}

