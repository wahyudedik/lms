<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ForumCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'color',
        'order',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Boot the model.
     */
    protected static function booted(): void
    {
        static::creating(function (ForumCategory $category) {
            $category->slug = $category->slug ?? Str::slug($category->name);
        });

        static::updating(function (ForumCategory $category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    /**
     * Get the threads in this category.
     */
    public function threads(): HasMany
    {
        return $this->hasMany(ForumThread::class, 'category_id');
    }

    /**
     * Get the creator of this category.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get threads count for this category.
     */
    public function getThreadsCountAttribute(): int
    {
        return $this->threads()->count();
    }

    /**
     * Get replies count for this category.
     */
    public function getRepliesCountAttribute(): int
    {
        return ForumReply::whereIn('thread_id', $this->threads()->pluck('id'))->count();
    }

    /**
     * Get latest thread for this category.
     */
    public function latestThread()
    {
        return $this->threads()->latest('last_activity_at')->first();
    }

    /**
     * Scope: only active categories.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: ordered by order column.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('name');
    }

    /**
     * Get color badge HTML.
     */
    public function getColorBadgeAttribute(): string
    {
        return '<span class="inline-block w-3 h-3 rounded-full" style="background-color: ' . $this->color . '"></span>';
    }
}
