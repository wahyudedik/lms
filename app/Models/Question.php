<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $exam_id
 * @property int|null $question_bank_id
 * @property bool $is_from_bank
 * @property string $type
 * @property string $question_text
 * @property string|null $question_image
 * @property array<array-key, mixed>|null $options
 * @property array<array-key, mixed>|null $pairs
 * @property array<array-key, mixed>|null $correct_answer
 * @property numeric $points
 * @property int $order
 * @property string|null $explanation
 * @property string $essay_grading_mode Mode penilaian essay: manual, keyword matching, atau similarity
 * @property array<array-key, mixed>|null $essay_keywords Array kata kunci untuk keyword matching
 * @property array<array-key, mixed>|null $essay_keyword_points Bobot poin per kata kunci
 * @property string|null $essay_model_answer Jawaban model/referensi untuk similarity matching
 * @property int $essay_min_similarity Minimal % similarity untuk lulus (0-100)
 * @property bool $essay_case_sensitive Apakah penilaian case-sensitive
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Answer> $answers
 * @property-read int|null $answers_count
 * @property-read \App\Models\Exam $exam
 * @property-read string $essay_grading_mode_display
 * @property-read string $type_display
 * @property-read string $type_icon
 * @property-read \App\Models\QuestionBank|null $questionBank
 * @method static \Database\Factories\QuestionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereCorrectAnswer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereEssayCaseSensitive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereEssayGradingMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereEssayKeywordPoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereEssayKeywords($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereEssayMinSimilarity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereEssayModelAnswer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereExamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereExplanation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereIsFromBank($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereOptions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question wherePairs($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question wherePoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereQuestionBankId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereQuestionImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereQuestionText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'question_bank_id',
        'is_from_bank',
        'type',
        'question_text',
        'question_image',
        'options',
        'pairs',
        'correct_answer',
        'correct_answer_multiple',
        'points',
        'order',
        'explanation',
        // Essay grading fields
        'essay_grading_mode',
        'essay_keywords',
        'essay_keyword_points',
        'essay_model_answer',
        'essay_min_similarity',
        'essay_similarity_penalty',
        'essay_case_sensitive',
    ];

    protected $casts = [
        'options' => 'array',
        'pairs' => 'array',
        'correct_answer' => 'array',
        'correct_answer_multiple' => 'array',
        'essay_keywords' => 'array',
        'essay_keyword_points' => 'array',
        'essay_case_sensitive' => 'boolean',
        'is_from_bank' => 'boolean',
    ];

    /**
     * Get the exam this question belongs to
     */
    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    /**
     * Get the question bank source (if from bank)
     */
    public function questionBank(): BelongsTo
    {
        return $this->belongsTo(QuestionBank::class, 'question_bank_id');
    }

    /**
     * Get all answers for this question
     */
    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }

    /**
     * Get question type display name
     */
    public function getTypeDisplayAttribute(): string
    {
        return match ($this->type) {
            'mcq_single' => 'Pilihan Ganda (Single)',
            'mcq_multiple' => 'Pilihan Ganda (Multiple)',
            'matching' => 'Menjodohkan',
            'essay' => 'Essay',
            default => 'Unknown',
        };
    }

    /**
     * Get question type icon
     */
    public function getTypeIconAttribute(): string
    {
        return match ($this->type) {
            'mcq_single' => 'fas fa-dot-circle',
            'mcq_multiple' => 'fas fa-check-square',
            'matching' => 'fas fa-link',
            'essay' => 'fas fa-align-left',
            default => 'fas fa-question',
        };
    }

    /**
     * Check if answer is correct
     * 
     * @param mixed $userAnswer
     * @return bool
     */
    public function checkAnswer($userAnswer): bool
    {
        if ($this->type === 'essay') {
            // Essay requires manual grading
            return false;
        }

        if ($this->type === 'mcq_single') {
            return $userAnswer === $this->correct_answer;
        }

        if ($this->type === 'mcq_multiple') {
            if (!is_array($userAnswer)) {
                return false;
            }

            sort($userAnswer);
            $correctAnswer = $this->correct_answer;
            sort($correctAnswer);

            return $userAnswer === $correctAnswer;
        }

        if ($this->type === 'matching') {
            if (!is_array($userAnswer)) {
                return false;
            }

            // Check if all pairs are correctly matched
            $correctPairs = collect($this->correct_answer);
            $userPairs = collect($userAnswer);

            foreach ($correctPairs as $correctPair) {
                $match = $userPairs->first(function ($userPair) use ($correctPair) {
                    return $userPair['left'] === $correctPair['left']
                        && $userPair['right'] === $correctPair['right'];
                });

                if (!$match) {
                    return false;
                }
            }

            return true;
        }

        return false;
    }

    /**
     * Calculate points earned based on answer
     * 
     * @param mixed $userAnswer
     * @return float
     */
    public function calculatePoints($userAnswer): float
    {
        if ($this->type === 'essay') {
            // Essay auto-grading if enabled
            if ($this->essay_grading_mode === 'keyword') {
                return $this->gradeEssayByKeywords($userAnswer);
            } elseif ($this->essay_grading_mode === 'similarity') {
                return $this->gradeEssayBySimilarity($userAnswer);
            }
            // Manual grading - return 0 (guru will grade later)
            return 0;
        }

        return $this->checkAnswer($userAnswer) ? $this->points : 0;
    }

    /**
     * Get shuffled options for MCQ questions
     * 
     * @return array|null
     */
    public function getShuffledOptions(): ?array
    {
        if ($this->type !== 'mcq_single' && $this->type !== 'mcq_multiple') {
            return null;
        }

        $options = $this->options;
        shuffle($options);

        return $options;
    }

    /**
     * Get shuffled pairs for matching questions
     * 
     * @return array|null
     */
    public function getShuffledPairs(): ?array
    {
        if ($this->type !== 'matching') {
            return null;
        }

        $pairs = $this->pairs;

        // Shuffle only the right items
        $leftItems = array_column($pairs, 'left');
        $rightItems = array_column($pairs, 'right');
        shuffle($rightItems);

        return [
            'left' => $leftItems,
            'right' => $rightItems,
        ];
    }

    /**
     * Auto-grade essay by keyword matching
     * 
     * @param string $userAnswer
     * @return float
     */
    public function gradeEssayByKeywords(string $userAnswer): float
    {
        if (empty($this->essay_keywords)) {
            return 0;
        }

        $answer = $this->essay_case_sensitive ? $userAnswer : strtolower($userAnswer);
        $foundKeywords = 0;
        $totalPoints = 0;

        foreach ($this->essay_keywords as $index => $keyword) {
            $searchKeyword = $this->essay_case_sensitive ? $keyword : strtolower($keyword);

            // Check if keyword exists in answer
            if (str_contains($answer, $searchKeyword)) {
                $foundKeywords++;
                // Add points for this keyword
                if (isset($this->essay_keyword_points[$index])) {
                    $totalPoints += $this->essay_keyword_points[$index];
                }
            }
        }

        // Don't exceed maximum points for this question
        return min($totalPoints, $this->points);
    }

    /**
     * Auto-grade essay by similarity matching
     * 
     * @param string $userAnswer
     * @return float
     */
    public function gradeEssayBySimilarity(string $userAnswer): float
    {
        if (empty($this->essay_model_answer)) {
            return 0;
        }

        $modelAnswer = $this->essay_case_sensitive ? $this->essay_model_answer : strtolower($this->essay_model_answer);
        $answer = $this->essay_case_sensitive ? $userAnswer : strtolower($userAnswer);

        // Calculate similarity percentage using similar_text
        similar_text($modelAnswer, $answer, $percent);

        // Alternative: use levenshtein for shorter texts
        // $distance = levenshtein($modelAnswer, $answer);
        // $maxLen = max(strlen($modelAnswer), strlen($answer));
        // $percent = (1 - $distance / $maxLen) * 100;

        // Calculate points based on similarity percentage
        $earnedPoints = ($percent / 100) * $this->points;

        // Check if meets minimum similarity threshold
        if ($percent < $this->essay_min_similarity) {
            // Below threshold, significantly reduce points
            $earnedPoints = $earnedPoints * 0.5; // 50% penalty
        }

        return round($earnedPoints, 2);
    }

    /**
     * Get essay grading mode display name
     */
    public function getEssayGradingModeDisplayAttribute(): string
    {
        return match ($this->essay_grading_mode) {
            'manual' => 'Manual (Guru Review)',
            'keyword' => 'Keyword Matching',
            'similarity' => 'Similarity Matching',
            default => 'Unknown',
        };
    }

    /**
     * Check if essay needs manual grading
     */
    public function needsManualGrading(): bool
    {
        return $this->type === 'essay' && $this->essay_grading_mode === 'manual';
    }

    /**
     * Check if essay has auto-grading enabled
     */
    public function hasAutoGrading(): bool
    {
        return $this->type === 'essay' && in_array($this->essay_grading_mode, ['keyword', 'similarity']);
    }
}
