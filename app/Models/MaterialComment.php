<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
