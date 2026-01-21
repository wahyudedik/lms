<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $school_id
 * @property string $primary_color
 * @property string $secondary_color
 * @property string $accent_color
 * @property string $success_color
 * @property string $warning_color
 * @property string $danger_color
 * @property string $info_color
 * @property string $dark_color
 * @property string $text_primary
 * @property string $text_secondary
 * @property string $text_muted
 * @property string $background_color
 * @property string $card_background
 * @property string $navbar_background
 * @property string $sidebar_background
 * @property string $font_family
 * @property string|null $heading_font
 * @property int $font_size
 * @property string|null $custom_css
 * @property string|null $login_background
 * @property string|null $dashboard_hero
 * @property string $border_radius
 * @property string $box_shadow
 * @property bool $dark_mode
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $dashboard_hero_url
 * @property-read mixed $login_background_url
 * @property-read \App\Models\School $school
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolTheme active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolTheme newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolTheme newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolTheme query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolTheme whereAccentColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolTheme whereBackgroundColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolTheme whereBorderRadius($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolTheme whereBoxShadow($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolTheme whereCardBackground($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolTheme whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolTheme whereCustomCss($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolTheme whereDangerColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolTheme whereDarkColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolTheme whereDarkMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolTheme whereDashboardHero($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolTheme whereFontFamily($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolTheme whereFontSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolTheme whereHeadingFont($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolTheme whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolTheme whereInfoColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolTheme whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolTheme whereLoginBackground($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolTheme whereNavbarBackground($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolTheme wherePrimaryColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolTheme whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolTheme whereSecondaryColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolTheme whereSidebarBackground($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolTheme whereSuccessColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolTheme whereTextMuted($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolTheme whereTextPrimary($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolTheme whereTextSecondary($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolTheme whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolTheme whereWarningColor($value)
 * @mixin \Eloquent
 */
class SchoolTheme extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'primary_color',
        'secondary_color',
        'accent_color',
        'success_color',
        'warning_color',
        'danger_color',
        'info_color',
        'dark_color',
        'text_primary',
        'text_secondary',
        'text_muted',
        'background_color',
        'card_background',
        'navbar_background',
        'sidebar_background',
        'font_family',
        'heading_font',
        'font_size',
        'custom_css',
        'login_background',
        'dashboard_hero',
        'border_radius',
        'box_shadow',
        'dark_mode',
        'is_active',
    ];

    protected $casts = [
        'font_size' => 'integer',
        'dark_mode' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Relationships
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Generate CSS variables for the theme
     */
    public function toCssVariables(): string
    {
        return "
:root {
    /* Color Scheme */
    --color-primary: {$this->primary_color};
    --color-secondary: {$this->secondary_color};
    --color-accent: {$this->accent_color};
    --color-success: {$this->success_color};
    --color-warning: {$this->warning_color};
    --color-danger: {$this->danger_color};
    --color-info: {$this->info_color};
    --color-dark: {$this->dark_color};
    
    /* Text Colors */
    --text-primary: {$this->text_primary};
    --text-secondary: {$this->text_secondary};
    --text-muted: {$this->text_muted};
    
    /* Backgrounds */
    --bg-color: {$this->background_color};
    --card-bg: {$this->card_background};
    --navbar-bg: {$this->navbar_background};
    --sidebar-bg: {$this->sidebar_background};
    
    /* Typography */
    --font-family: {$this->font_family};
    --heading-font: " . ($this->heading_font ?: $this->font_family) . ";
    --font-size-base: {$this->font_size}px;
    
    /* Effects */
    --border-radius: {$this->border_radius};
    --box-shadow: {$this->box_shadow};
}
";
    }

    /**
     * Generate complete CSS for the theme
     */
    public function toFullCss(): string
    {
        $css = $this->toCssVariables();

        $css .= "
/* Apply Theme Variables */
body {
    background-color: var(--bg-color);
    color: var(--text-primary);
    font-family: var(--font-family);
    font-size: var(--font-size-base);
}

h1, h2, h3, h4, h5, h6 {
    font-family: var(--heading-font);
    color: var(--text-primary);
}

.bg-primary { background-color: var(--color-primary) !important; }
.text-primary { color: var(--color-primary) !important; }
.border-primary { border-color: var(--color-primary) !important; }

.bg-secondary { background-color: var(--color-secondary) !important; }
.text-secondary-color { color: var(--color-secondary) !important; }

.bg-accent { background-color: var(--color-accent) !important; }
.text-accent { color: var(--color-accent) !important; }

.bg-success { background-color: var(--color-success) !important; }
.bg-warning { background-color: var(--color-warning) !important; }
.bg-danger { background-color: var(--color-danger) !important; }
.bg-info { background-color: var(--color-info) !important; }

/* Buttons */
.btn-primary, .bg-indigo-500, .bg-indigo-600, .bg-blue-500, .bg-blue-600 {
    background-color: var(--color-primary) !important;
}

.btn-primary:hover, .bg-indigo-700, .bg-blue-700 {
    background-color: var(--color-secondary) !important;
}

/* Links */
a {
    color: var(--color-primary);
}

a:hover {
    color: var(--color-secondary);
}

/* Cards */
.bg-white {
    background-color: var(--card-bg) !important;
}

/* Navigation */
nav {
    background-color: var(--navbar-bg) !important;
}

/* Rounded corners */
.rounded, .rounded-lg, .rounded-md {
    border-radius: var(--border-radius) !important;
}

/* Shadows */
.shadow, .shadow-sm {
    box-shadow: var(--box-shadow) !important;
}
";

        // Add custom CSS if provided
        if ($this->custom_css) {
            $css .= "\n/* Custom CSS */\n" . $this->custom_css;
        }

        return $css;
    }

    /**
     * Get login background URL
     */
    public function getLoginBackgroundUrlAttribute()
    {
        if ($this->login_background) {
            return asset('storage/' . $this->login_background);
        }
        return null;
    }

    /**
     * Get dashboard hero URL
     */
    public function getDashboardHeroUrlAttribute()
    {
        if ($this->dashboard_hero) {
            return asset('storage/' . $this->dashboard_hero);
        }
        return null;
    }

    /**
     * Get color palette array
     */
    public function getColorPalette(): array
    {
        return [
            'primary' => $this->primary_color,
            'secondary' => $this->secondary_color,
            'accent' => $this->accent_color,
            'success' => $this->success_color,
            'warning' => $this->warning_color,
            'danger' => $this->danger_color,
            'info' => $this->info_color,
            'dark' => $this->dark_color,
        ];
    }

    /**
     * Scope: Active themes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Alias for toFullCss() for backward compatibility
     */
    public function generateCSS(): string
    {
        return $this->toFullCss();
    }
}
