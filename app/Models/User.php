<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use App\Notifications\CustomVerifyEmail;
use App\Notifications\CustomResetPassword;

class User extends Authenticatable implements MustVerifyEmail
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
    public function getDashboardRouteAttribute(): string
    {
        return match ($this->role) {
            'admin' => 'admin.dashboard',
            'guru' => 'guru.dashboard',
            'siswa' => 'siswa.dashboard',
            default => 'dashboard'
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
}
