<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $user_id
 * @property int|null $course_id
 * @property string|null $title
 * @property string|null $context_type
 * @property int|null $context_id
 * @property int $message_count
 * @property int $tokens_used
 * @property \Illuminate\Support\Carbon|null $last_message_at
 * @property bool $is_archived
 * @property array<array-key, mixed>|null $metadata
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Course|null $course
 * @property-read \App\Models\AiMessage|null $latestMessage
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AiMessage> $messages
 * @property-read int|null $messages_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiConversation active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiConversation byContext(string $type, ?int $id = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiConversation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiConversation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiConversation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiConversation whereContextId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiConversation whereContextType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiConversation whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiConversation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiConversation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiConversation whereIsArchived($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiConversation whereLastMessageAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiConversation whereMessageCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiConversation whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiConversation whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiConversation whereTokensUsed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiConversation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiConversation whereUserId($value)
 * @mixin \Eloquent
 */
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
