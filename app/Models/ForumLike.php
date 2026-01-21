<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int $id
 * @property string $likeable_type
 * @property int $likeable_id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Model|\Eloquent $likeable
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumLike newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumLike newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumLike query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumLike whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumLike whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumLike whereLikeableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumLike whereLikeableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumLike whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ForumLike whereUserId($value)
 * @mixin \Eloquent
 */
class ForumLike extends Model
{
    use HasFactory;

    protected $fillable = [
        'likeable_type',
        'likeable_id',
        'user_id',
    ];

    /**
     * Get the likeable model (thread or reply).
     */
    public function likeable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the user who liked.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
