<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

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
