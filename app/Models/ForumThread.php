<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property int $category_id
 * @property int $user_id
 * @property string $title
 * @property string $slug
 * @property string $content
 * @property bool $is_pinned
 * @property bool $is_locked
 * @property int $views_count
 * @property-read int|null $replies_count
 * @property-read int|null $likes_count
 * @property \Illuminate\Support\Carbon|null $last_activity_at
 * @property int|null $last_reply_user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ForumCategory $category
 * @property-read string $excerpt
 * @property-read string $status_badge
 * @property-read \App\Models\User|null $lastReplyUser
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ForumLike> $likes
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ForumReply> $replies
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumThread latestActivity()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumThread newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumThread newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumThread notLocked()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumThread pinned()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumThread popular()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumThread query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumThread search($search)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumThread whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumThread whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumThread whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumThread whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumThread whereIsLocked($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumThread whereIsPinned($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumThread whereLastActivityAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumThread whereLastReplyUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumThread whereLikesCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumThread whereRepliesCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumThread whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumThread whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumThread whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumThread whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumThread whereViewsCount($value)
 * @mixin \Eloquent
 */
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
