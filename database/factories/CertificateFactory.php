<?php

namespace Database\Factories;

use App\Models\Certificate;
use App\Models\Enrollment;
use App\Models\User;
use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Certificate>
 */
class CertificateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $enrollment = Enrollment::completed()->inRandomOrder()->first();

        if (!$enrollment) {
            // Create a completed enrollment if none exists
            $enrollment = Enrollment::factory()->completed()->create();
        }

        $score = fake()->numberBetween(60, 100);
        $grade = match (true) {
            $score >= 90 => 'A',
            $score >= 80 => 'B',
            $score >= 70 => 'C',
            $score >= 60 => 'D',
            default => 'F'
        };

        return [
            'enrollment_id' => $enrollment->id,
            'user_id' => $enrollment->user_id,
            'course_id' => $enrollment->course_id,
            'certificate_number' => Certificate::generateCertificateNumber(),
            'student_name' => $enrollment->user->name,
            'course_title' => $enrollment->course->title,
            'course_description' => $enrollment->course->description,
            'issue_date' => now(),
            'completion_date' => $enrollment->completed_at ?? now()->subDays(rand(1, 30)),
            'final_score' => $score,
            'grade' => $grade,
            'instructor_name' => $enrollment->course->instructor->name ?? 'Unknown Instructor',
            'signature' => null,
            'metadata' => [
                'duration_days' => rand(7, 90),
                'course_level' => fake()->randomElement(['beginner', 'intermediate', 'advanced']),
                'course_category' => fake()->randomElement(['Programming', 'Design', 'Business', 'Marketing']),
                'total_lessons' => rand(10, 50),
                'total_hours' => rand(5, 100),
            ],
            'is_valid' => true,
            'revoked_at' => null,
            'revoke_reason' => null,
        ];
    }

    /**
     * Indicate that the certificate is revoked.
     */
    public function revoked(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_valid' => false,
            'revoked_at' => now()->subDays(rand(1, 30)),
            'revoke_reason' => fake()->randomElement([
                'Fraudulent completion',
                'Violation of terms',
                'Duplicate entry',
                'Invalid assessment',
            ]),
        ]);
    }

    /**
     * Indicate that the certificate is for a high-scoring student.
     */
    public function excellence(): static
    {
        return $this->state(fn(array $attributes) => [
            'final_score' => fake()->numberBetween(90, 100),
            'grade' => 'A',
        ]);
    }

    /**
     * Indicate that the certificate is recent (issued in last 30 days).
     */
    public function recent(): static
    {
        return $this->state(fn(array $attributes) => [
            'issue_date' => now()->subDays(rand(1, 30)),
            'completion_date' => now()->subDays(rand(1, 35)),
        ]);
    }
}
