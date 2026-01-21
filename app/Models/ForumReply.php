<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @property int $id
 * @property int $thread_id
 * @property int|null $parent_id
 * @property int $user_id
 * @property string $content
 * @property-read int|null $likes_count
 * @property bool $is_solution
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ForumReply> $children
 * @property-read int|null $children_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ForumLike> $likes
 * @property-read ForumReply|null $parent
 * @property-read \App\Models\ForumThread $thread
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumReply newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumReply newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumReply parents()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumReply query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumReply whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumReply whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumReply whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumReply whereIsSolution($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumReply whereLikesCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumReply whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumReply whereThreadId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumReply whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumReply whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumReply withUser()
 * @mixin \Eloquent
 */
class ForumReply extends Model
{
    use HasFactory;

    protected $fillable = [
        'thread_id',
        'parent_id',
        'user_id',
        'content',
        'likes_count',
        'is_solution',
    ];

    protected $casts = [
        'is_solution' => 'boolean',
    ];

    /**
     * Boot the model.
     */
    protected static function booted(): void
    {
        static::created(function (ForumReply $reply) {
            // Increment thread replies count
            $reply->thread->increment('replies_count');
            $reply->thread->updateLastActivity($reply->user);
        });

        static::deleted(function (ForumReply $reply) {
            // Decrement thread replies count
            $reply->thread->decrement('replies_count');
        });
    }

    /**
     * Get the thread this reply belongs to.
     */
    public function thread(): BelongsTo
    {
        return $this->belongsTo(ForumThread::class, 'thread_id');
    }

    /**
     * Get the user who created this reply.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the parent reply (for nested replies).
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(ForumReply::class, 'parent_id');
    }

    /**
     * Get child replies.
     */
    public function children(): HasMany
    {
        return $this->hasMany(ForumReply::class, 'parent_id');
    }

    /**
     * Get likes for this reply.
     */
    public function likes(): MorphMany
    {
        return $this->morphMany(ForumLike::class, 'likeable');
    }

    /**
     * Check if user has liked this reply.
     */
    public function isLikedBy(?User $user): bool
    {
        if (!$user) return false;
        return $this->likes()->where('user_id', $user->id)->exists();
    }

    /**
     * Toggle like for this reply.
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
     * Mark as solution.
     */
    public function markAsSolution()
    {
        // Unmark other solutions in the same thread
        $this->thread->replies()->where('id', '!=', $this->id)->update(['is_solution' => false]);
        $this->update(['is_solution' => true]);
    }

    /**
     * Scope: only parent replies (not nested).
     */
    public function scopeParents($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope: with user eager loaded.
     */
    public function scopeWithUser($query)
    {
        return $query->with('user');
    }
}
