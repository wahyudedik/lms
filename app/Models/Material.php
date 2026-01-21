<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

/**
 * @property int $id
 * @property int $course_id
 * @property int $created_by
 * @property string $title
 * @property string|null $description
 * @property string $type
 * @property string|null $file_path
 * @property string|null $file_name
 * @property int|null $file_size
 * @property string|null $url
 * @property int $order
 * @property bool $is_published
 * @property \Illuminate\Support\Carbon|null $published_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\MaterialComment> $allComments
 * @property-read int|null $all_comments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\MaterialComment> $comments
 * @property-read int|null $comments_count
 * @property-read \App\Models\Course $course
 * @property-read \App\Models\User $creator
 * @property-read string $type_display
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Material byCourse($courseId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Material byType($type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Material newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Material newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Material ordered()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Material published()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Material query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Material whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Material whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Material whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Material whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Material whereFileName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Material whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Material whereFileSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Material whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Material whereIsPublished($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Material whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Material wherePublishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Material whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Material whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Material whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Material whereUrl($value)
 * @mixin \Eloquent
 */
class Material extends Model
{
    protected $fillable = [
        'course_id',
        'created_by',
        'title',
        'description',
        'type',
        'file_path',
        'file_name',
        'file_size',
        'url',
        'order',
        'is_published',
        'published_at',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
        'file_size' => 'integer',
        'order' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($material) {
            if ($material->is_published && !$material->published_at) {
                $material->published_at = now();
            }
        });

        static::deleting(function ($material) {
            // Delete file from storage if exists
            if ($material->file_path && Storage::exists($material->file_path)) {
                Storage::delete($material->file_path);
            }

            // Delete all comments
            $material->comments()->delete();
        });
    }

    /**
     * Relationships
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(MaterialComment::class)->whereNull('parent_id')->latest();
    }

    public function allComments(): HasMany
    {
        return $this->hasMany(MaterialComment::class)->latest();
    }

    /**
     * Helper Methods
     */
    public function getFileUrl(): ?string
    {
        if ($this->type === 'file' && $this->file_path) {
            return Storage::url($this->file_path);
        }
        return null;
    }

    public function getFormattedFileSize(): ?string
    {
        if (!$this->file_size) {
            return null;
        }

        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getFileIcon(): string
    {
        if ($this->type === 'youtube') {
            return 'fab fa-youtube';
        }

        if ($this->type === 'video') {
            return 'fas fa-video';
        }

        if ($this->type === 'link') {
            return 'fas fa-link';
        }

        // For file type, determine icon by extension
        $extension = pathinfo($this->file_name, PATHINFO_EXTENSION);

        return match (strtolower($extension)) {
            'pdf' => 'fas fa-file-pdf',
            'doc', 'docx' => 'fas fa-file-word',
            'xls', 'xlsx' => 'fas fa-file-excel',
            'ppt', 'pptx' => 'fas fa-file-powerpoint',
            'zip', 'rar', '7z' => 'fas fa-file-archive',
            'jpg', 'jpeg', 'png', 'gif' => 'fas fa-file-image',
            'mp4', 'avi', 'mov' => 'fas fa-file-video',
            'mp3', 'wav' => 'fas fa-file-audio',
            default => 'fas fa-file',
        };
    }

    public function getFileColorClass(): string
    {
        if ($this->type === 'youtube') {
            return 'text-red-600';
        }

        if ($this->type === 'video') {
            return 'text-purple-600';
        }

        if ($this->type === 'link') {
            return 'text-blue-600';
        }

        $extension = pathinfo($this->file_name, PATHINFO_EXTENSION);

        return match (strtolower($extension)) {
            'pdf' => 'text-red-600',
            'doc', 'docx' => 'text-blue-600',
            'xls', 'xlsx' => 'text-green-600',
            'ppt', 'pptx' => 'text-orange-600',
            default => 'text-gray-600',
        };
    }

    public function getEmbedUrl(): ?string
    {
        if ($this->type !== 'youtube' || !$this->url) {
            return null;
        }

        // Extract video ID from YouTube URL
        preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/i', $this->url, $match);

        if (isset($match[1])) {
            return "https://www.youtube.com/embed/{$match[1]}";
        }

        return null;
    }

    public function publish(): bool
    {
        $this->is_published = true;
        $this->published_at = $this->published_at ?? now();
        return $this->save();
    }

    public function unpublish(): bool
    {
        $this->is_published = false;
        return $this->save();
    }

    /**
     * Scopes
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeByCourse($query, $courseId)
    {
        return $query->where('course_id', $courseId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('created_at', 'desc');
    }

    /**
     * Attributes
     */
    public function getTypeDisplayAttribute(): string
    {
        return match ($this->type) {
            'file' => 'File',
            'video' => 'Video',
            'link' => 'Link',
            'youtube' => 'YouTube',
            default => $this->type,
        };
    }
}
