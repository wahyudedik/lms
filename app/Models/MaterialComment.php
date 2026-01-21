<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $material_id
 * @property int $user_id
 * @property int|null $parent_id
 * @property string $comment
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Material $material
 * @property-read MaterialComment|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, MaterialComment> $replies
 * @property-read int|null $replies_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialComment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialComment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialComment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialComment whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialComment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialComment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialComment whereMaterialId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialComment whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialComment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialComment whereUserId($value)
 * @mixin \Eloquent
 */
class MaterialComment extends Model
{
    protected $fillable = [
        'material_id',
        'user_id',
        'parent_id',
        'comment',
    ];

    /**
     * Relationships
     */
    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(MaterialComment::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(MaterialComment::class, 'parent_id')->latest();
    }

    /**
     * Helper Methods
     */
    public function isReply(): bool
    {
        return $this->parent_id !== null;
    }

    public function canDelete(User $user): bool
    {
        // User can delete their own comments or admin can delete any
        return $this->user_id === $user->id || $user->isAdmin();
    }

    public function canEdit(User $user): bool
    {
        // Only comment owner can edit
        return $this->user_id === $user->id;
    }
}
