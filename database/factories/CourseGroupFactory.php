<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\CourseGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CourseGroup>
 */
class CourseGroupFactory extends Factory
{
    protected $model = CourseGroup::class;

    public function definition(): array
    {
        return [
            'course_id' => Course::factory(),
            'name' => $this->faker->unique()->words(2, true),
        ];
    }
}
