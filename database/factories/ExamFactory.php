<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\Exam;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Exam>
 */
class ExamFactory extends Factory
{
    protected $model = Exam::class;

    public function definition(): array
    {
        return [
            'course_id' => Course::factory(),
            'created_by' => User::factory()->state(['role' => 'guru']),
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'instructions' => $this->faker->paragraph(),
            'duration_minutes' => 60,
            'start_time' => now()->subHour(),
            'end_time' => now()->addHours(2),
            'max_attempts' => 1,
            'shuffle_questions' => true,
            'shuffle_options' => true,
            'show_results_immediately' => true,
            'show_correct_answers' => true,
            'pass_score' => 60,
            'require_fullscreen' => false,
            'detect_tab_switch' => true,
            'max_tab_switches' => 3,
            'is_published' => true,
            'published_at' => now(),
            'allow_token_access' => false,
            'access_token' => null,
            'require_guest_name' => false,
            'require_guest_email' => false,
            'max_token_uses' => null,
            'current_token_uses' => 0,
        ];
    }
}

