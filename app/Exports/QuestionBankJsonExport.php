<?php

namespace App\Exports;

use App\Models\QuestionBank;

class QuestionBankJsonExport
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * Generate JSON export
     */
    public function export()
    {
        $query = QuestionBank::with(['category', 'creator']);

        // Apply filters
        if (isset($this->filters['category_id']) && $this->filters['category_id']) {
            $query->where('category_id', $this->filters['category_id']);
        }

        if (isset($this->filters['type']) && $this->filters['type']) {
            $query->where('type', $this->filters['type']);
        }

        if (isset($this->filters['difficulty']) && $this->filters['difficulty']) {
            $query->where('difficulty', $this->filters['difficulty']);
        }

        if (isset($this->filters['is_verified']) && $this->filters['is_verified'] !== '') {
            $query->where('is_verified', $this->filters['is_verified']);
        }

        if (isset($this->filters['is_active']) && $this->filters['is_active'] !== '') {
            $query->where('is_active', $this->filters['is_active']);
        }

        $questions = $query->latest()->get();

        return [
            'export_info' => [
                'exported_at' => now()->toIso8601String(),
                'total_questions' => $questions->count(),
                'filters' => $this->filters,
            ],
            'questions' => $questions->map(function ($question) {
                return [
                    'id' => $question->id,
                    'category' => $question->category ? $question->category->name : null,
                    'type' => $question->type,
                    'difficulty' => $question->difficulty,
                    'question_text' => $question->question_text,
                    'options' => $question->options,
                    'correct_answer' => $question->correct_answer,
                    'default_points' => $question->default_points,
                    'explanation' => $question->explanation,
                    'tags' => $question->tags,
                    'question_image' => $question->question_image,
                    'is_active' => $question->is_active,
                    'is_verified' => $question->is_verified,
                    'times_used' => $question->times_used,
                    'created_by' => $question->creator ? $question->creator->name : null,
                    'created_at' => $question->created_at->toIso8601String(),
                    'updated_at' => $question->updated_at->toIso8601String(),
                ];
            })->values(),
        ];
    }
}
