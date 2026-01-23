<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $address
 * @property string|null $logo
 * @property string|null $favicon
 * @property string|null $domain
 * @property bool $is_active
 * @property bool $show_landing_page
 * @property string|null $hero_title
 * @property string|null $hero_subtitle
 * @property string|null $hero_description
 * @property string|null $hero_image
 * @property string $hero_cta_text
 * @property string|null $hero_cta_link
 * @property string|null $about_title
 * @property string|null $about_content
 * @property string|null $about_image
 * @property string|null $features
 * @property string|null $statistics
 * @property string|null $contact_address
 * @property string|null $contact_phone
 * @property string|null $contact_email
 * @property string|null $contact_whatsapp
 * @property string|null $social_facebook
 * @property string|null $social_instagram
 * @property string|null $social_twitter
 * @property string|null $social_youtube
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property string|null $meta_keywords
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $about_image_url
 * @property-read mixed $favicon_url
 * @property-read mixed $hero_image_url
 * @property-read mixed $logo_url
 * @property-read int|null $users_count
 * @property-read \App\Models\SchoolTheme|null $theme
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereAboutContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereAboutImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereAboutTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereContactAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereContactEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereContactPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereContactWhatsapp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereDomain($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereFavicon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereFeatures($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereHeroCtaLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereHeroCtaText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereHeroDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereHeroImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereHeroSubtitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereHeroTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereMetaDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereMetaKeywords($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereMetaTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereShowLandingPage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereSocialFacebook($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereSocialInstagram($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereSocialTwitter($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereSocialYoutube($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereStatistics($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
        return asset('images/icons/icon-192x192.png');
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
