<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class InformationCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by',
        'title',
        'content',
        'card_type',
        'icon',
        'attachment_path',
        'attachment_name',
        'attachment_size',
        'video_url',
        'target_roles',
        'target_user_ids',
        'schedule_type',
        'start_date',
        'end_date',
        'daily_start_time',
        'daily_end_time',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'target_roles' => 'array',
        'target_user_ids' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Get the user who created this card.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope: only active cards.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: cards visible to a specific user.
     */
    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        return $query->active()
            ->where(function (Builder $q) use ($user) {
                // Target role must include user's role
                $q->whereJsonContains('target_roles', $user->role);
            })
            ->where(function (Builder $q) use ($user) {
                // Either no specific users targeted (all users in role) or user is in the list
                $q->whereNull('target_user_ids')
                    ->orWhereJsonContains('target_user_ids', $user->id);
            })
            ->where(function (Builder $q) {
                // Schedule filtering
                $q->where(function (Builder $sub) {
                    // Always visible
                    $sub->where('schedule_type', 'always');
                })->orWhere(function (Builder $sub) {
                    // Date range: current date is within range
                    $sub->where('schedule_type', 'date_range')
                        ->where('start_date', '<=', now()->toDateString())
                        ->where('end_date', '>=', now()->toDateString());
                })->orWhere(function (Builder $sub) {
                    // Daily: always show (it repeats every day)
                    $sub->where('schedule_type', 'daily');
                });
            })
            ->orderBy('sort_order')
            ->orderByDesc('created_at');
    }

    /**
     * Check if this card is currently visible based on schedule.
     */
    public function isCurrentlyVisible(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        return match ($this->schedule_type) {
            'always' => true,
            'date_range' => now()->between($this->start_date->startOfDay(), $this->end_date->endOfDay()),
            'daily' => true, // Daily cards are always visible (they repeat every day)
            default => false,
        };
    }

    /**
     * Get the CSS classes for the card type.
     */
    public function getCardColorClassAttribute(): string
    {
        return match ($this->card_type) {
            'info' => 'bg-blue-50 border-blue-200 text-blue-800',
            'warning' => 'bg-yellow-50 border-yellow-200 text-yellow-800',
            'success' => 'bg-green-50 border-green-200 text-green-800',
            'danger' => 'bg-red-50 border-red-200 text-red-800',
            default => 'bg-gray-50 border-gray-200 text-gray-800',
        };
    }

    /**
     * Get the icon color class for the card type.
     */
    public function getIconColorClassAttribute(): string
    {
        return match ($this->card_type) {
            'info' => 'text-blue-600',
            'warning' => 'text-yellow-600',
            'success' => 'text-green-600',
            'danger' => 'text-red-600',
            default => 'text-gray-600',
        };
    }

    /**
     * Check if the card has an attachment.
     */
    public function hasAttachment(): bool
    {
        return !empty($this->attachment_path);
    }

    /**
     * Get the attachment URL.
     */
    public function getAttachmentUrlAttribute(): ?string
    {
        if (!$this->attachment_path) {
            return null;
        }

        return \Illuminate\Support\Facades\Storage::disk('public')->url($this->attachment_path);
    }

    /**
     * Get the attachment file extension.
     */
    public function getAttachmentExtensionAttribute(): ?string
    {
        if (!$this->attachment_name) {
            return null;
        }

        return strtolower(pathinfo($this->attachment_name, PATHINFO_EXTENSION));
    }

    /**
     * Get human-readable file size.
     */
    public function getAttachmentSizeFormattedAttribute(): ?string
    {
        if (!$this->attachment_size) {
            return null;
        }

        $bytes = $this->attachment_size;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 1) . ' MB';
        }

        return number_format($bytes / 1024, 0) . ' KB';
    }

    /**
     * Check if the card has a video URL.
     */
    public function hasVideo(): bool
    {
        return !empty($this->video_url);
    }

    /**
     * Get YouTube embed URL from video URL.
     */
    public function getVideoEmbedUrlAttribute(): ?string
    {
        if (!$this->video_url) {
            return null;
        }

        // Extract YouTube video ID
        if (preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $this->video_url, $matches)) {
            return 'https://www.youtube.com/embed/' . $matches[1];
        }

        return $this->video_url;
    }
}
