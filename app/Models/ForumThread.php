<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;

class ForumThread extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'user_id',
        'title',
        'slug',
        'content',
        'is_pinned',
        'is_locked',
        'views_count',
        'replies_count',
        'likes_count',
        'last_activity_at',
        'last_reply_user_id',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'is_locked' => 'boolean',
        'last_activity_at' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function booted(): void
    {
        static::creating(function (ForumThread $thread) {
            $thread->slug = $thread->slug ?? Str::slug($thread->title);
            $thread->last_activity_at = now();
        });

        static::updating(function (ForumThread $thread) {
            if (empty($thread->slug)) {
                $thread->slug = Str::slug($thread->title);
            }
        });
    }

    /**
     * Get the category this thread belongs to.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ForumCategory::class, 'category_id');
    }

    /**
     * Get the user who created this thread.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who made the last reply.
     */
    public function lastReplyUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_reply_user_id');
    }

    /**
     * Get all replies for this thread.
     */
    public function replies(): HasMany
    {
        return $this->hasMany(ForumReply::class, 'thread_id');
    }

    /**
     * Get likes for this thread.
     */
    public function likes(): MorphMany
    {
        return $this->morphMany(ForumLike::class, 'likeable');
    }

    /**
     * Check if user has liked this thread.
     */
    public function isLikedBy(?User $user): bool
    {
        if (!$user) return false;
        return $this->likes()->where('user_id', $user->id)->exists();
    }

    /**
     * Toggle like for this thread.
     */
    public function toggleLike(User $user)
    {
        $like = $this->likes()->where('user_id', $user->id)->first();

        if ($like) {
            $like->delete();
            $this->decrement('likes_count');
            return false;
        } else {
            $this->likes()->create(['user_id' => $user->id]);
            $this->increment('likes_count');
            return true;
        }
    }

    /**
     * Increment views count.
     */
    public function incrementViews()
    {
        $this->increment('views_count');
    }

    /**
     * Update last activity.
     */
    public function updateLastActivity(?User $user = null)
    {
        $this->update([
            'last_activity_at' => now(),
            'last_reply_user_id' => $user?->id,
        ]);
    }

    /**
     * Scope: only pinned threads.
     */
    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    /**
     * Scope: not locked threads.
     */
    public function scopeNotLocked($query)
    {
        return $query->where('is_locked', false);
    }

    /**
     * Scope: popular threads (by replies).
     */
    public function scopePopular($query)
    {
        return $query->orderBy('replies_count', 'desc');
    }

    /**
     * Scope: latest activity.
     */
    public function scopeLatestActivity($query)
    {
        return $query->orderBy('last_activity_at', 'desc');
    }

    /**
     * Scope: search by title or content.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
                ->orWhere('content', 'like', "%{$search}%");
        });
    }

    /**
     * Get status badge HTML.
     */
    public function getStatusBadgeAttribute(): string
    {
        if ($this->is_locked) {
            return '<span class="px-2 py-1 text-xs rounded bg-red-100 text-red-800"><i class="fas fa-lock mr-1"></i>Locked</span>';
        }
        if ($this->is_pinned) {
            return '<span class="px-2 py-1 text-xs rounded bg-yellow-100 text-yellow-800"><i class="fas fa-thumbtack mr-1"></i>Pinned</span>';
        }
        return '';
    }

    /**
     * Get excerpt from content.
     */
    public function getExcerptAttribute(): string
    {
        return Str::limit(strip_tags($this->content), 150);
    }
}
