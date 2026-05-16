<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class CourseGroup extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'course_id',
        'name',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'course_id' => 'integer',
        ];
    }

    /**
     * Relationships
     */

    /**
     * Get the course that owns this group.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the members (users) of this group.
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'course_group_members')
            ->withPivot('created_at');
    }

    /**
     * Get the materials targeted to this group.
     */
    public function materials(): MorphToMany
    {
        return $this->morphedByMany(Material::class, 'contentable', 'course_group_content')
            ->withPivot('created_at');
    }

    /**
     * Get the assignments targeted to this group.
     */
    public function assignments(): MorphToMany
    {
        return $this->morphedByMany(Assignment::class, 'contentable', 'course_group_content')
            ->withPivot('created_at');
    }

    /**
     * Accessors
     */

    /**
     * Get the normalized (lowercased, trimmed) name.
     */
    public function getNameNormalizedAttribute(): string
    {
        return strtolower(trim($this->name));
    }
}
