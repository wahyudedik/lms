<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $course_id
 * @property int $assignment_weight
 * @property int $exam_weight
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Course $course
 * @method static \Database\Factories\CourseGradeWeightFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseGradeWeight newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseGradeWeight newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseGradeWeight query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseGradeWeight whereAssignmentWeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseGradeWeight whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseGradeWeight whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseGradeWeight whereExamWeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseGradeWeight whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseGradeWeight whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CourseGradeWeight extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'course_id',
        'assignment_weight',
        'exam_weight',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'assignment_weight' => 'integer',
            'exam_weight' => 'integer',
        ];
    }

    /**
     * Get the course that owns this grade weight configuration.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the grade weight configuration for a given course.
     * If none exists, returns a new (unsaved) instance with default values (30/70).
     */
    public static function getForCourse(int $courseId): self
    {
        return static::where('course_id', $courseId)->first()
            ?? new self([
                'course_id' => $courseId,
                'assignment_weight' => 30,
                'exam_weight' => 70,
            ]);
    }
}
