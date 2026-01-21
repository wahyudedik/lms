<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int|null $category_id
 * @property int $created_by
 * @property string $type
 * @property string $question_text
 * @property string|null $question_image
 * @property string $difficulty
 * @property array<array-key, mixed>|null $tags
 * @property numeric $default_points
 * @property array<array-key, mixed>|null $options
 * @property string|null $correct_answer
 * @property array<array-key, mixed>|null $correct_answer_multiple
 * @property array<array-key, mixed>|null $pairs
 * @property string $essay_grading_mode
 * @property array<array-key, mixed>|null $essay_keywords
 * @property string|null $essay_model_answer
 * @property numeric|null $essay_min_similarity
 * @property numeric|null $essay_similarity_penalty
 * @property bool $essay_case_sensitive
 * @property string|null $explanation
 * @property string|null $teacher_notes
 * @property int $times_used
 * @property numeric|null $average_score
 * @property int $times_correct
 * @property int $times_incorrect
 * @property bool $is_active
 * @property bool $is_verified
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\QuestionBankCategory|null $category
 * @property-read \App\Models\User $creator
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Question> $examQuestions
 * @property-read int|null $exam_questions_count
 * @property-read string $difficulty_badge
 * @property-read float $success_rate
 * @property-read string $type_badge
 * @property-read string $verification_badge
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBank active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBank inCategory(int $categoryId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBank newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBank newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBank ofDifficulty(string $difficulty)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBank ofType(string $type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBank query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBank search(string $search)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBank verified()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBank whereAverageScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBank whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBank whereCorrectAnswer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBank whereCorrectAnswerMultiple($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBank whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBank whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBank whereDefaultPoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBank whereDifficulty($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBank whereEssayCaseSensitive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBank whereEssayGradingMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBank whereEssayKeywords($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBank whereEssayMinSimilarity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBank whereEssayModelAnswer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBank whereEssaySimilarityPenalty($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBank whereExplanation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBank whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBank whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBank whereIsVerified($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBank whereOptions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBank wherePairs($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBank whereQuestionImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBank whereQuestionText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBank whereTags($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBank whereTeacherNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBank whereTimesCorrect($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBank whereTimesIncorrect($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBank whereTimesUsed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBank whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBank whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class QuestionBank extends Model
{
    use HasFactory;

    protected $table = 'question_bank';

    protected $fillable = [
        'category_id',
        'created_by',
        'type',
        'question_text',
        'question_image',
        'difficulty',
        'tags',
        'default_points',
        'options',
        'correct_answer',
        'correct_answer_multiple',
        'pairs',
        'essay_grading_mode',
        'essay_keywords',
        'essay_model_answer',
        'essay_min_similarity',
        'essay_similarity_penalty',
        'essay_case_sensitive',
        'explanation',
        'teacher_notes',
        'times_used',
        'average_score',
        'times_correct',
        'times_incorrect',
        'is_active',
        'is_verified',
    ];

    protected $casts = [
        'tags' => 'array',
        'options' => 'array',
        'correct_answer_multiple' => 'array',
        'pairs' => 'array',
        'essay_keywords' => 'array',
        'essay_case_sensitive' => 'boolean',
        'is_active' => 'boolean',
        'is_verified' => 'boolean',
        'default_points' => 'decimal:2',
        'average_score' => 'decimal:2',
        'essay_min_similarity' => 'decimal:2',
        'essay_similarity_penalty' => 'decimal:2',
        'times_used' => 'integer',
        'times_correct' => 'integer',
        'times_incorrect' => 'integer',
    ];

    /**
     * Get the category
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(QuestionBankCategory::class, 'category_id');
    }

    /**
     * Get the creator
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get exam questions using this bank question
     */
    public function examQuestions(): HasMany
    {
        return $this->hasMany(Question::class, 'question_bank_id');
    }

    /**
     * Scope for active questions
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for verified questions
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope for questions by type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for questions by difficulty
     */
    public function scopeOfDifficulty($query, string $difficulty)
    {
        return $query->where('difficulty', $difficulty);
    }

    /**
     * Scope for questions by category
     */
    public function scopeInCategory($query, int $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope for search
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('question_text', 'like', "%{$search}%")
                ->orWhere('explanation', 'like', "%{$search}%")
                ->orWhereJsonContains('tags', $search);
        });
    }

    /**
     * Clone this question to an exam
     */
    public function cloneToExam(int $examId, int $order = null, float $points = null): Question
    {
        $questionData = [
            'exam_id' => $examId,
            'question_bank_id' => $this->id,
            'is_from_bank' => true,
            'type' => $this->type,
            'question_text' => $this->question_text,
            'question_image' => $this->question_image,
            'points' => $points ?? $this->default_points,
            'order' => $order ?? 0,
            'options' => $this->options,
            'correct_answer' => $this->correct_answer,
            'correct_answer_multiple' => $this->correct_answer_multiple,
            'pairs' => $this->pairs,
            'essay_grading_mode' => $this->essay_grading_mode,
            'essay_keywords' => $this->essay_keywords,
            'essay_model_answer' => $this->essay_model_answer,
            'essay_min_similarity' => $this->essay_min_similarity,
            'essay_similarity_penalty' => $this->essay_similarity_penalty,
            'essay_case_sensitive' => $this->essay_case_sensitive,
            'explanation' => $this->explanation,
        ];

        $question = Question::create($questionData);

        // Increment usage counter
        $this->increment('times_used');

        return $question;
    }

    /**
     * Update statistics after student answers
     */
    public function updateStatistics(bool $isCorrect, float $score): void
    {
        if ($isCorrect) {
            $this->increment('times_correct');
        } else {
            $this->increment('times_incorrect');
        }

        // Update average score
        $totalAttempts = $this->times_correct + $this->times_incorrect;
        if ($totalAttempts > 0) {
            $currentTotal = ($this->average_score ?? 0) * ($totalAttempts - 1);
            $this->average_score = ($currentTotal + $score) / $totalAttempts;
            $this->save();
        }
    }

    /**
     * Get difficulty badge
     */
    public function getDifficultyBadgeAttribute(): string
    {
        $colors = [
            'easy' => 'bg-green-100 text-green-800',
            'medium' => 'bg-yellow-100 text-yellow-800',
            'hard' => 'bg-red-100 text-red-800',
        ];

        $icons = [
            'easy' => 'fa-smile',
            'medium' => 'fa-meh',
            'hard' => 'fa-frown',
        ];

        $color = $colors[$this->difficulty] ?? 'bg-gray-100 text-gray-800';
        $icon = $icons[$this->difficulty] ?? 'fa-circle';

        return '<span class="px-2 py-1 text-xs font-semibold rounded ' . $color . '">
                    <i class="fas ' . $icon . ' mr-1"></i>' . ucfirst($this->difficulty) . '
                </span>';
    }

    /**
     * Get type badge
     */
    public function getTypeBadgeAttribute(): string
    {
        $types = [
            'mcq_single' => ['label' => 'MCQ Single', 'color' => 'bg-blue-100 text-blue-800', 'icon' => 'fa-check-circle'],
            'mcq_multiple' => ['label' => 'MCQ Multiple', 'color' => 'bg-purple-100 text-purple-800', 'icon' => 'fa-check-double'],
            'matching' => ['label' => 'Matching', 'color' => 'bg-indigo-100 text-indigo-800', 'icon' => 'fa-link'],
            'essay' => ['label' => 'Essay', 'color' => 'bg-pink-100 text-pink-800', 'icon' => 'fa-pen'],
        ];

        $type = $types[$this->type] ?? ['label' => 'Unknown', 'color' => 'bg-gray-100 text-gray-800', 'icon' => 'fa-question'];

        return '<span class="px-2 py-1 text-xs font-semibold rounded ' . $type['color'] . '">
                    <i class="fas ' . $type['icon'] . ' mr-1"></i>' . $type['label'] . '
                </span>';
    }

    /**
     * Get success rate percentage
     */
    public function getSuccessRateAttribute(): float
    {
        $total = $this->times_correct + $this->times_incorrect;
        if ($total === 0) {
            return 0;
        }
        return ($this->times_correct / $total) * 100;
    }

    /**
     * Get verification badge
     */
    public function getVerificationBadgeAttribute(): string
    {
        if ($this->is_verified) {
            return '<span class="px-2 py-1 text-xs font-semibold rounded bg-green-100 text-green-800">
                        <i class="fas fa-check-circle mr-1"></i>Verified
                    </span>';
        }
        return '<span class="px-2 py-1 text-xs font-semibold rounded bg-gray-100 text-gray-800">
                    <i class="fas fa-clock mr-1"></i>Unverified
                </span>';
    }
}
