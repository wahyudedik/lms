<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $attempt_id
 * @property int $question_id
 * @property array<array-key, mixed>|null $answer
 * @property bool|null $is_correct
 * @property numeric|null $points_earned
 * @property string|null $feedback
 * @property array<array-key, mixed>|null $shuffled_options
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property string|null $saved_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ExamAttempt $attempt
 * @property-read string $formatted_answer
 * @property-read string $formatted_correct_answer
 * @property-read \App\Models\Question $question
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Answer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Answer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Answer query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Answer whereAnswer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Answer whereAttemptId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Answer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Answer whereFeedback($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Answer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Answer whereIsCorrect($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Answer wherePointsEarned($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Answer whereQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Answer whereSavedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Answer whereShuffledOptions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Answer whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
