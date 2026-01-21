<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use App\Notifications\CustomVerifyEmail;
use App\Notifications\CustomResetPassword;

/**
 * @property int $id
 * @property int|null $school_id
 * @property string $name
 * @property string $email
 * @property string $role
 * @property string|null $phone
 * @property \Illuminate\Support\Carbon|null $birth_date
 * @property string|null $gender
 * @property string|null $address
 * @property string|null $profile_photo
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $login_blocked_at
 * @property string|null $login_blocked_reason
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CheatingIncident> $cheatingIncidents
 * @property-read int|null $cheating_incidents_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Exam> $createdExams
 * @property-read int|null $created_exams_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Course> $enrolledCourses
 * @property-read int|null $enrolled_courses_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Enrollment> $enrollments
 * @property-read int|null $enrollments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ExamAttempt> $examAttempts
 * @property-read int|null $exam_attempts_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ForumLike> $forumLikes
 * @property-read int|null $forum_likes_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ForumReply> $forumReplies
 * @property-read int|null $forum_replies_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ForumThread> $forumThreads
 * @property-read int|null $forum_threads_count
 * @property-read string|null $dashboard_route
 * @property-read int $forum_posts_count
 * @property-read bool $is_login_blocked
 * @property-read string $profile_photo_path
 * @property-read string $profile_photo_url
 * @property-read string $role_display
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\School|null $school
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Course> $teachingCourses
 * @property-read int|null $teaching_courses_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereBirthDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLoginBlockedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLoginBlockedReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereProfilePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class User extends Authenticatable 
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'school_id',
        'phone',
        'birth_date',
        'gender',
        'address',
        'profile_photo',
        'is_active',
        'login_blocked_at',
        'login_blocked_reason',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'birth_date' => 'date',
            'is_active' => 'boolean',
            'login_blocked_at' => 'datetime',
        ];
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is guru
     */
    public function isGuru(): bool
    {
        return $this->role === 'guru';
    }

    /**
     * Check if user is siswa
     */
    public function isSiswa(): bool
    {
        return $this->role === 'siswa';
    }

    /**
     * Get role display name
     */
    public function getRoleDisplayAttribute(): string
    {
        return match ($this->role) {
            'admin' => 'Administrator',
            'guru' => 'Guru',
            'siswa' => 'Siswa',
            default => 'Unknown'
        };
    }

    /**
     * Get dashboard route based on role
     */
    public function getDashboardRouteAttribute(): ?string
    {
        if (array_key_exists('dashboard_route', $this->attributes) && $this->attributes['dashboard_route']) {
            return $this->attributes['dashboard_route'];
        }

        return match ($this->role) {
            'admin' => 'admin.dashboard',
            'guru' => 'guru.dashboard',
            'siswa' => 'siswa.dashboard',
            default => null
        };
    }

    /**
     * Get profile photo URL
     */
    public function getProfilePhotoUrlAttribute(): string
    {
        if ($this->profile_photo) {
            return Storage::url('profile-photos/' . $this->profile_photo);
        }

        // Return default avatar based on gender
        $defaultAvatar = match ($this->gender) {
            'perempuan' => 'default-female.png',
            'laki-laki' => 'default-male.png',
            default => 'default-avatar.png'
        };

        return asset('images/avatars/' . $defaultAvatar);
    }

    /**
     * Get profile photo path
     */
    public function getProfilePhotoPathAttribute(): string
    {
        return $this->profile_photo ? 'profile-photos/' . $this->profile_photo : null;
    }

    /**
     * Delete profile photo
     */
    public function deleteProfilePhoto(): bool
    {
        if ($this->profile_photo && Storage::exists('profile-photos/' . $this->profile_photo)) {
            Storage::delete('profile-photos/' . $this->profile_photo);
            $this->update(['profile_photo' => null]);
            return true;
        }
        return false;
    }

    /**
     * Get courses taught by this user (for Guru)
     */
    public function teachingCourses()
    {
        return $this->hasMany(\App\Models\Course::class, 'instructor_id');
    }

    /**
     * Get exams created by this user (for Guru)
     */
    public function createdExams()
    {
        return $this->hasMany(\App\Models\Exam::class, 'created_by');
    }

    /**
     * Get enrollments for this user (for Siswa)
     */
    public function enrollments()
    {
        return $this->hasMany(\App\Models\Enrollment::class);
    }

    /**
     * Get exam attempts by this user (for Siswa)
     */
    public function examAttempts()
    {
        return $this->hasMany(\App\Models\ExamAttempt::class);
    }

    /**
     * Get courses enrolled by this user (for Siswa)
     */
    public function enrolledCourses()
    {
        return $this->belongsToMany(\App\Models\Course::class, 'enrollments')
            ->withPivot(['status', 'progress', 'enrolled_at', 'completed_at'])
            ->withTimestamps();
    }

    /**
     * Check if user is enrolled in a specific course
     */
    public function isEnrolledIn($courseId): bool
    {
        return $this->enrollments()->where('course_id', $courseId)->exists();
    }

    /**
     * Enroll user in a course
     */
    public function enrollInCourse($courseId)
    {
        if ($this->isEnrolledIn($courseId)) {
            return false;
        }

        return $this->enrollments()->create([
            'course_id' => $courseId,
            'status' => 'active',
            'enrolled_at' => now(),
        ]);
    }

    /**
     * Send the email verification notification.
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new CustomVerifyEmail);
    }

    /**
     * Send the password reset notification.
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomResetPassword($token));
    }

    /**
     * Get all forum threads created by this user.
     */
    public function forumThreads()
    {
        return $this->hasMany(\App\Models\ForumThread::class, 'user_id');
    }

    /**
     * Get all forum replies created by this user.
     */
    public function forumReplies()
    {
        return $this->hasMany(\App\Models\ForumReply::class, 'user_id');
    }

    /**
     * Get all forum likes by this user.
     */
    public function forumLikes()
    {
        return $this->hasMany(\App\Models\ForumLike::class, 'user_id');
    }

    /**
     * Cheating incidents associated with the user.
     */
    public function cheatingIncidents()
    {
        return $this->hasMany(CheatingIncident::class);
    }

    /**
     * Only unresolved cheating incidents.
     */
    public function activeCheatingIncidents()
    {
        return $this->cheatingIncidents()->whereNull('resolved_at');
    }

    /**
     * Get forum posts count (threads + replies).
     */
    public function getForumPostsCountAttribute(): int
    {
        return $this->forumThreads()->count() + $this->forumReplies()->count();
    }

    /**
     * Get the school that the user belongs to.
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Determine if the user login is blocked.
     */
    public function getIsLoginBlockedAttribute(): bool
    {
        return !is_null($this->login_blocked_at);
    }

    /**
     * Block user login (used when cheating is detected).
     */
    public function blockLogin(string $reason = 'Kecurangan terdeteksi'): bool
    {
        if ($this->is_login_blocked) {
            return false;
        }

        $blocked = $this->forceFill([
            'login_blocked_at' => now(),
            'login_blocked_reason' => $reason,
        ])->save();

        if ($blocked) {
            UserActivityLog::logActivity(
                $this->id,
                'security',
                'login_blocked',
                $reason,
                [
                    'reason' => $reason,
                ]
            );
        }

        return $blocked;
    }

    /**
     * Reset user login block.
     */
    public function resetLoginBlock(?User $resolver = null, ?string $resolutionNotes = null): bool
    {
        if (!$this->is_login_blocked) {
            return false;
        }

        $reset = $this->forceFill([
            'login_blocked_at' => null,
            'login_blocked_reason' => null,
        ])->save();

        if ($reset) {
            $notes = $resolutionNotes ?? 'Reset login via admin panel';

            $this->activeCheatingIncidents()->update([
                'status' => 'resolved',
                'resolved_at' => now(),
                'resolved_by' => $resolver?->id,
                'resolution_notes' => $notes,
            ]);

            UserActivityLog::logActivity(
                $this->id,
                'security',
                'login_unblocked',
                $notes
            );
        }

        return $reset;
    }
}
