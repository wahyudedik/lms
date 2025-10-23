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
        // Landing Page Fields
        'show_landing_page',
        'hero_title',
        'hero_subtitle',
        'hero_description',
        'hero_image',
        'hero_cta_text',
        'hero_cta_link',
        'about_title',
        'about_content',
        'about_image',
        'features',
        'statistics',
        'contact_address',
        'contact_phone',
        'contact_email',
        'contact_whatsapp',
        'social_facebook',
        'social_instagram',
        'social_twitter',
        'social_youtube',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'show_landing_page' => 'boolean',
        // Removed 'features' and 'statistics' from casts - using custom accessors instead
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

    /**
     * Get hero image URL
     */
    public function getHeroImageUrlAttribute()
    {
        if ($this->hero_image) {
            return asset('storage/' . $this->hero_image);
        }
        return asset('images/default-hero.jpg');
    }

    /**
     * Get about image URL
     */
    public function getAboutImageUrlAttribute()
    {
        if ($this->about_image) {
            return asset('storage/' . $this->about_image);
        }
        return null;
    }

    /**
     * Get features attribute - decode JSON and return array
     */
    public function getFeaturesAttribute($value)
    {
        // Decode JSON string to array
        $features = !empty($value) ? json_decode($value, true) : [];

        // Return default features if empty or invalid JSON
        if (empty($features) || !is_array($features)) {
            return [
                [
                    'icon' => 'fa-graduation-cap',
                    'title' => 'Quality Education',
                    'description' => 'High-quality learning materials and experienced instructors'
                ],
                [
                    'icon' => 'fa-users',
                    'title' => 'Interactive Learning',
                    'description' => 'Engage with peers and teachers in real-time'
                ],
                [
                    'icon' => 'fa-certificate',
                    'title' => 'Certification',
                    'description' => 'Get certified upon course completion'
                ]
            ];
        }

        return $features;
    }

    /**
     * Get statistics attribute - decode JSON and return array
     */
    public function getStatisticsAttribute($value)
    {
        // Decode JSON string to array
        $statistics = !empty($value) ? json_decode($value, true) : [];

        // Return default statistics if empty or invalid JSON
        if (empty($statistics) || !is_array($statistics)) {
            return [
                ['label' => 'Active Students', 'value' => '1000+'],
                ['label' => 'Courses', 'value' => '50+'],
                ['label' => 'Teachers', 'value' => '30+'],
                ['label' => 'Success Rate', 'value' => '95%']
            ];
        }

        return $statistics;
    }

    /**
     * Set features attribute - encode array to JSON
     */
    public function setFeaturesAttribute($value)
    {
        $this->attributes['features'] = is_array($value) ? json_encode($value) : $value;
    }

    /**
     * Set statistics attribute - encode array to JSON
     */
    public function setStatisticsAttribute($value)
    {
        $this->attributes['statistics'] = is_array($value) ? json_encode($value) : $value;
    }
}
