<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Answer extends Model
{
    use HasFactory;

    protected $fillable = [
        'attempt_id',
        'question_id',
        'answer',
        'is_correct',
        'points_earned',
        'feedback',
        'shuffled_options',
    ];

    protected $casts = [
        'answer' => 'array',
        'is_correct' => 'boolean',
        'shuffled_options' => 'array',
    ];

    /**
     * Get the attempt this answer belongs to
     */
    public function attempt(): BelongsTo
    {
        return $this->belongsTo(ExamAttempt::class, 'attempt_id');
    }

    /**
     * Get the question this answer is for
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * Get formatted answer for display
     */
    public function getFormattedAnswerAttribute(): string
    {
        if (!$this->answer) {
            return 'Tidak dijawab';
        }

        $question = $this->question;

        if ($question->type === 'mcq_single') {
            return $this->answer;
        }

        if ($question->type === 'mcq_multiple') {
            return is_array($this->answer) ? implode(', ', $this->answer) : $this->answer;
        }

        if ($question->type === 'matching') {
            if (!is_array($this->answer)) {
                return 'Invalid answer format';
            }

            $formatted = [];
            foreach ($this->answer as $pair) {
                $formatted[] = "{$pair['left']} → {$pair['right']}";
            }

            return implode('<br>', $formatted);
        }

        if ($question->type === 'essay') {
            return $this->answer;
        }

        return 'Unknown format';
    }

    /**
     * Get formatted correct answer for display
     */
    public function getFormattedCorrectAnswerAttribute(): string
    {
        $question = $this->question;
        $correctAnswer = $question->correct_answer;

        if (!$correctAnswer) {
            return '-';
        }

        if ($question->type === 'mcq_single') {
            return $correctAnswer;
        }

        if ($question->type === 'mcq_multiple') {
            return is_array($correctAnswer) ? implode(', ', $correctAnswer) : $correctAnswer;
        }

        if ($question->type === 'matching') {
            if (!is_array($correctAnswer)) {
                return 'Invalid format';
            }

            $formatted = [];
            foreach ($correctAnswer as $pair) {
                $formatted[] = "{$pair['left']} → {$pair['right']}";
            }

            return implode('<br>', $formatted);
        }

        if ($question->type === 'essay') {
            return 'Requires manual grading';
        }

        return 'Unknown format';
    }
}
