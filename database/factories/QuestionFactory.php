<?php

namespace Database\Factories;

use App\Models\Exam;
use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Question>
 */
class QuestionFactory extends Factory
{
    protected $model = Question::class;

    public function definition(): array
    {
        $options = [
            ['id' => 'A', 'text' => 'Option A'],
            ['id' => 'B', 'text' => 'Option B'],
            ['id' => 'C', 'text' => 'Option C'],
            ['id' => 'D', 'text' => 'Option D'],
        ];

        return [
            'exam_id' => Exam::factory(),
            'type' => 'mcq_single',
            'question_text' => $this->faker->sentence() . '?',
            'question_image' => null,
            'options' => $options,
            'pairs' => null,
            'correct_answer' => 'A',
            'points' => 1,
            'order' => 1,
            'explanation' => $this->faker->sentence(),
            'is_from_bank' => false,
            'essay_grading_mode' => 'manual',
            'essay_keywords' => null,
            'essay_keyword_points' => null,
            'essay_model_answer' => null,
            'essay_min_similarity' => 0,
            'essay_case_sensitive' => false,
        ];
    }
}

