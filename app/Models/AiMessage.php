<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $conversation_id
 * @property string $role
 * @property string $content
 * @property int $tokens
 * @property string|null $model
 * @property array<array-key, mixed>|null $metadata
 * @property \Illuminate\Support\Carbon $sent_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AiConversation $conversation
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiMessage assistant()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiMessage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiMessage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiMessage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiMessage system()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiMessage user()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiMessage whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiMessage whereConversationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiMessage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiMessage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiMessage whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiMessage whereModel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiMessage whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiMessage whereSentAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiMessage whereTokens($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiMessage whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AiMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'role',
        'content',
        'tokens',
        'model',
        'metadata',
        'sent_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'sent_at' => 'datetime',
    ];

    /**
     * Get the conversation that owns the message.
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(AiConversation::class, 'conversation_id');
    }

    /**
     * Scope for user messages.
     */
    public function scopeUser($query)
    {
        return $query->where('role', 'user');
    }

    /**
     * Scope for assistant messages.
     */
    public function scopeAssistant($query)
    {
        return $query->where('role', 'assistant');
    }

    /**
     * Scope for system messages.
     */
    public function scopeSystem($query)
    {
        return $query->where('role', 'system');
    }

    /**
     * Check if this is a user message.
     */
    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    /**
     * Check if this is an assistant message.
     */
    public function isAssistant(): bool
    {
        return $this->role === 'assistant';
    }

    /**
     * Check if this is a system message.
     */
    public function isSystem(): bool
    {
        return $this->role === 'system';
    }
}
