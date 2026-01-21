<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string $color
 * @property int $order
 * @property bool $is_active
 * @property int|null $parent_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\QuestionBank> $activeQuestions
 * @property-read int|null $active_questions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, QuestionBankCategory> $children
 * @property-read int|null $children_count
 * @property-read int $active_question_count
 * @property-read string $color_badge
 * @property-read string $full_path
 * @property-read int $question_count
 * @property-read QuestionBankCategory|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\QuestionBank> $questions
 * @property-read int|null $questions_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBankCategory active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBankCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBankCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBankCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBankCategory roots()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBankCategory whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBankCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBankCategory whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBankCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBankCategory whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBankCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBankCategory whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBankCategory whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBankCategory whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBankCategory whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class QuestionBankCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'color',
        'order',
        'is_active',
        'parent_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Boot method to auto-generate slug
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });

        static::updating(function ($category) {
            if ($category->isDirty('name') && empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    /**
     * Get parent category
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(QuestionBankCategory::class, 'parent_id');
    }

    /**
     * Get child categories
     */
    public function children(): HasMany
    {
        return $this->hasMany(QuestionBankCategory::class, 'parent_id')
            ->orderBy('order');
    }

    /**
     * Get all questions in this category
     */
    public function questions(): HasMany
    {
        return $this->hasMany(QuestionBank::class, 'category_id');
    }

    /**
     * Get active questions
     */
    public function activeQuestions(): HasMany
    {
        return $this->questions()->where('is_active', true);
    }

    /**
     * Scope for active categories
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for root categories (no parent)
     */
    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id')->orderBy('order');
    }

    /**
     * Get full category path (Parent > Child)
     */
    public function getFullPathAttribute(): string
    {
        $path = [$this->name];
        $parent = $this->parent;

        while ($parent) {
            array_unshift($path, $parent->name);
            $parent = $parent->parent;
        }

        return implode(' > ', $path);
    }

    /**
     * Get question count
     */
    public function getQuestionCountAttribute(): int
    {
        return $this->questions()->count();
    }

    /**
     * Get active question count
     */
    public function getActiveQuestionCountAttribute(): int
    {
        return $this->activeQuestions()->count();
    }

    /**
     * Get color badge HTML
     */
    public function getColorBadgeAttribute(): string
    {
        return '<span class="inline-block w-4 h-4 rounded-full" style="background-color: ' . $this->color . '"></span>';
    }
}
