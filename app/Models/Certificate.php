<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Certificate extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'enrollment_id',
        'user_id',
        'course_id',
        'certificate_number',
        'student_name',
        'course_title',
        'course_description',
        'issue_date',
        'completion_date',
        'final_score',
        'grade',
        'instructor_name',
        'signature',
        'metadata',
        'is_valid',
        'revoked_at',
        'revoke_reason',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'issue_date' => 'date',
            'completion_date' => 'date',
            'revoked_at' => 'datetime',
            'is_valid' => 'boolean',
            'metadata' => 'array',
            'final_score' => 'integer',
        ];
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate certificate number on creation
        static::creating(function ($certificate) {
            if (empty($certificate->certificate_number)) {
                $certificate->certificate_number = static::generateCertificateNumber();
            }
        });
    }

    /**
     * Get the enrollment for this certificate
     */
    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }

    /**
     * Get the user (student) for this certificate
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the student (alias for user)
     */
    public function student()
    {
        return $this->user();
    }

    /**
     * Get the course for this certificate
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Generate unique certificate number
     */
    public static function generateCertificateNumber(): string
    {
        do {
            // Format: CERT-YYYY-RANDOM8
            $number = 'CERT-' . date('Y') . '-' . strtoupper(Str::random(8));
        } while (static::where('certificate_number', $number)->exists());

        return $number;
    }

    /**
     * Check if certificate is valid
     */
    public function isValid(): bool
    {
        return $this->is_valid && !$this->revoked_at;
    }

    /**
     * Revoke the certificate
     */
    public function revoke(string $reason): bool
    {
        return $this->update([
            'is_valid' => false,
            'revoked_at' => now(),
            'revoke_reason' => $reason,
        ]);
    }

    /**
     * Restore a revoked certificate
     */
    public function restore(): bool
    {
        return $this->update([
            'is_valid' => true,
            'revoked_at' => null,
            'revoke_reason' => null,
        ]);
    }

    /**
     * Get verification URL
     */
    public function getVerificationUrlAttribute(): string
    {
        return route('certificates.verify', $this->certificate_number);
    }

    /**
     * Get download URL
     */
    public function getDownloadUrlAttribute(): string
    {
        return route('certificates.download', $this->certificate_number);
    }

    /**
     * Get view URL
     */
    public function getViewUrlAttribute(): string
    {
        return route('certificates.show', $this->certificate_number);
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute(): string
    {
        return $this->isValid() ? 'green' : 'red';
    }

    /**
     * Get status display
     */
    public function getStatusDisplayAttribute(): string
    {
        if ($this->isValid()) {
            return 'Valid';
        }

        if ($this->revoked_at) {
            return 'Revoked';
        }

        return 'Invalid';
    }

    /**
     * Get grade display with color
     */
    public function getGradeColorAttribute(): string
    {
        return match ($this->grade) {
            'A' => 'green',
            'B' => 'blue',
            'C' => 'yellow',
            'D' => 'orange',
            'F' => 'red',
            default => 'gray'
        };
    }

    /**
     * Scope to get valid certificates
     */
    public function scopeValid($query)
    {
        return $query->where('is_valid', true)->whereNull('revoked_at');
    }

    /**
     * Scope to get revoked certificates
     */
    public function scopeRevoked($query)
    {
        return $query->whereNotNull('revoked_at');
    }

    /**
     * Scope to get by certificate number
     */
    public function scopeByCertificateNumber($query, string $number)
    {
        return $query->where('certificate_number', $number);
    }

    /**
     * Scope to get by user
     */
    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to get by course
     */
    public function scopeByCourse($query, int $courseId)
    {
        return $query->where('course_id', $courseId);
    }

    /**
     * Scope to get recently issued
     */
    public function scopeRecentlyIssued($query, int $days = 30)
    {
        return $query->where('issue_date', '>=', now()->subDays($days));
    }
}
