<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class School extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'email',
        'phone',
        'address',
        'logo',
        'favicon',
        'domain',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($school) {
            if (empty($school->slug)) {
                $school->slug = Str::slug($school->name);
            }
        });

        static::updating(function ($school) {
            if ($school->isDirty('name') && empty($school->slug)) {
                $school->slug = Str::slug($school->name);
            }
        });
    }

    /**
     * Relationships
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function theme()
    {
        return $this->hasOne(SchoolTheme::class);
    }

    /**
     * Get active theme or create default
     */
    public function getActiveTheme()
    {
        return $this->theme()->where('is_active', true)->first()
            ?? $this->theme()->create([]);
    }

    /**
     * Get logo URL
     */
    public function getLogoUrlAttribute()
    {
        if ($this->logo) {
            return asset('storage/' . $this->logo);
        }
        return asset('images/default-logo.png');
    }

    /**
     * Get favicon URL
     */
    public function getFaviconUrlAttribute()
    {
        if ($this->favicon) {
            return asset('storage/' . $this->favicon);
        }
        return asset('favicon.ico');
    }

    /**
     * Scope: Active schools
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get total users count
     */
    public function getUsersCountAttribute()
    {
        return $this->users()->count();
    }

    /**
     * Get admin users
     */
    public function admins()
    {
        return $this->users()->where('role', 'admin');
    }

    /**
     * Get teachers
     */
    public function teachers()
    {
        return $this->users()->where('role', 'guru');
    }

    /**
     * Get students
     */
    public function students()
    {
        return $this->users()->where('role', 'siswa');
    }
}
