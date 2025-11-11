<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
