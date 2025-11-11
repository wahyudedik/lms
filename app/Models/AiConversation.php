<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AiConversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'title',
        'context_type',
        'context_id',
        'message_count',
        'tokens_used',
        'last_message_at',
        'is_archived',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'last_message_at' => 'datetime',
        'is_archived' => 'boolean',
    ];

    /**
     * Get the user that owns the conversation.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the course associated with the conversation.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get all messages in this conversation.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(AiMessage::class, 'conversation_id');
    }

    /**
     * Get the latest message.
     */
    public function latestMessage()
    {
        return $this->hasOne(AiMessage::class, 'conversation_id')->latestOfMany();
    }

    /**
     * Scope for active (non-archived) conversations.
     */
    public function scopeActive($query)
    {
        return $query->where('is_archived', false);
    }

    /**
     * Scope for conversations by context type.
     */
    public function scopeByContext($query, string $type, ?int $id = null)
    {
        $query = $query->where('context_type', $type);

        if ($id !== null) {
            $query->where('context_id', $id);
        }

        return $query;
    }

    /**
     * Archive this conversation.
     */
    public function archive(): bool
    {
        return $this->update(['is_archived' => true]);
    }

    /**
     * Unarchive this conversation.
     */
    public function unarchive(): bool
    {
        return $this->update(['is_archived' => false]);
    }

    /**
     * Update conversation statistics.
     */
    public function updateStats(): void
    {
        $this->update([
            'message_count' => $this->messages()->count(),
            'tokens_used' => $this->messages()->sum('tokens'),
            'last_message_at' => $this->messages()->latest()->first()?->created_at ?? now(),
        ]);
    }

    /**
     * Generate a title from the first user message.
     */
    public function generateTitle(): string
    {
        $firstMessage = $this->messages()->where('role', 'user')->first();

        if (!$firstMessage) {
            return 'New Conversation';
        }

        // Take first 50 characters of the message
        $content = $firstMessage->content;
        $title = strlen($content) > 50 ? substr($content, 0, 50) . '...' : $content;

        $this->update(['title' => $title]);

        return $title;
    }
}
