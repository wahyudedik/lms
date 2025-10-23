<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

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
