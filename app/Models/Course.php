<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Course extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'title',
        'code',
        'description',
        'instructor_id',
        'status',
        'cover_image',
        'max_students',
        'published_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Generate unique code automatically if not provided
        static::creating(function ($course) {
            if (empty($course->code)) {
                $course->code = static::generateUniqueCode();
            }
        });
    }

    /**
     * Generate unique course code
     */
    public static function generateUniqueCode(): string
    {
        do {
            $code = strtoupper(Str::random(6));
        } while (static::where('code', $code)->exists());

        return $code;
    }

    /**
     * Get the instructor (guru) for the course
     */
    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    /**
     * Get all enrollments for this course
     */
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Get all materials for this course
     */
    public function materials()
    {
        return $this->hasMany(Material::class)->ordered();
    }

    /**
     * Get all exams for this course
     */
    public function exams()
    {
        return $this->hasMany(Exam::class);
    }

    /**
     * Get all enrolled students
     */
    public function students()
    {
        return $this->belongsToMany(User::class, 'enrollments')
            ->withPivot(['status', 'progress', 'enrolled_at', 'completed_at'])
            ->withTimestamps();
    }

    /**
     * Get active enrollments count
     */
    public function activeEnrollmentsCount(): int
    {
        return $this->enrollments()->where('status', 'active')->count();
    }

    /**
     * Check if course is published
     */
    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    /**
     * Check if course is full
     */
    public function isFull(): bool
    {
        if (is_null($this->max_students)) {
            return false;
        }

        return $this->activeEnrollmentsCount() >= $this->max_students;
    }

    /**
     * Check if user is enrolled in this course
     */
    public function isEnrolledBy(User $user): bool
    {
        return $this->enrollments()
            ->where('user_id', $user->id)
            ->exists();
    }

    /**
     * Publish the course
     */
    public function publish(): bool
    {
        return $this->update([
            'status' => 'published',
            'published_at' => now(),
        ]);
    }

    /**
     * Archive the course
     */
    public function archive(): bool
    {
        return $this->update(['status' => 'archived']);
    }

    /**
     * Scope to get only published courses
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope to get courses by instructor
     */
    public function scopeByInstructor($query, $instructorId)
    {
        return $query->where('instructor_id', $instructorId);
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'published' => 'green',
            'draft' => 'yellow',
            'archived' => 'gray',
            default => 'gray'
        };
    }

    /**
     * Get status display name
     */
    public function getStatusDisplayAttribute(): string
    {
        return match ($this->status) {
            'published' => 'Dipublikasikan',
            'draft' => 'Draft',
            'archived' => 'Diarsipkan',
            default => 'Unknown'
        };
    }
}
