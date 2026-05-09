<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\Material;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Material>
 */
class MaterialFactory extends Factory
{
    protected $model = Material::class;

    public function definition(): array
    {
        return [
            'course_id' => Course::factory(),
            'created_by' => User::factory()->state(['role' => 'guru']),
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(),
            'type' => 'file',
            'file_path' => null,
            'file_name' => null,
            'file_size' => null,
            'url' => null,
            'order' => $this->faker->numberBetween(1, 10),
            'is_published' => true,
            'published_at' => now(),
        ];
    }
}
