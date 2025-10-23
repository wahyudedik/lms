<?php

namespace App\Services;

use App\Models\School;
use App\Models\SchoolTheme;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class ThemeService
{
    /**
     * Get current school theme
     */
    public function getCurrentTheme(): ?SchoolTheme
    {
        // Get current user's school
        $user = auth()->user();

        if (!$user || !$user->school_id) {
            return $this->getDefaultTheme();
        }

        $school = $user->school;

        if (!$school) {
            return $this->getDefaultTheme();
        }

        return Cache::remember(
            "school_theme_{$school->id}",
            now()->addHours(24),
            fn() => $school->getActiveTheme()
        );
    }

    /**
     * Get theme by school ID
     */
    public function getThemeBySchoolId(int $schoolId): ?SchoolTheme
    {
        $school = School::find($schoolId);

        if (!$school) {
            return $this->getDefaultTheme();
        }

        return Cache::remember(
            "school_theme_{$school->id}",
            now()->addHours(24),
            fn() => $school->getActiveTheme()
        );
    }

    /**
     * Get default theme
     */
    public function getDefaultTheme(): SchoolTheme
    {
        return new SchoolTheme([
            'primary_color' => '#3B82F6',
            'secondary_color' => '#8B5CF6',
            'accent_color' => '#10B981',
            'success_color' => '#10B981',
            'warning_color' => '#F59E0B',
            'danger_color' => '#EF4444',
            'info_color' => '#3B82F6',
            'dark_color' => '#1F2937',
            'text_primary' => '#1F2937',
            'text_secondary' => '#6B7280',
            'text_muted' => '#9CA3AF',
            'background_color' => '#F9FAFB',
            'card_background' => '#FFFFFF',
            'navbar_background' => '#FFFFFF',
            'sidebar_background' => '#1F2937',
            'font_family' => 'Inter, sans-serif',
            'font_size' => 16,
            'border_radius' => '0.5rem',
            'box_shadow' => '0 1px 3px 0 rgb(0 0 0 / 0.1)',
            'dark_mode' => false,
            'is_active' => true,
        ]);
    }

    /**
     * Generate CSS file for a theme
     */
    public function generateCssFile(SchoolTheme $theme, string $filename = 'theme.css'): string
    {
        $css = $theme->toFullCss();
        $path = public_path('css/themes/' . $filename);

        // Create directory if not exists
        File::ensureDirectoryExists(public_path('css/themes'));

        // Write CSS file
        File::put($path, $css);

        return asset('css/themes/' . $filename);
    }

    /**
     * Clear theme cache
     */
    public function clearCache(?int $schoolId = null): void
    {
        if ($schoolId) {
            Cache::forget("school_theme_{$schoolId}");
        } else {
            // Clear all theme caches
            $schools = School::all();
            foreach ($schools as $school) {
                Cache::forget("school_theme_{$school->id}");
            }
        }
    }

    /**
     * Apply theme to view
     */
    public function applyTheme(): string
    {
        $theme = $this->getCurrentTheme();

        if (!$theme) {
            return '';
        }

        // Generate inline CSS
        return '<style id="dynamic-theme">' . $theme->toFullCss() . '</style>';
    }

    /**
     * Get theme CSS as inline style
     */
    public function getInlineCss(?SchoolTheme $theme = null): string
    {
        $theme = $theme ?? $this->getCurrentTheme();

        if (!$theme) {
            return '';
        }

        return $theme->toFullCss();
    }

    /**
     * Generate theme CSS file and return URL
     */
    public function getThemeCssUrl(?int $schoolId = null): string
    {
        $theme = $schoolId
            ? $this->getThemeBySchoolId($schoolId)
            : $this->getCurrentTheme();

        if (!$theme || !$theme->school_id) {
            return '';
        }

        $filename = "school-{$theme->school_id}.css";

        return Cache::remember(
            "theme_css_url_{$theme->school_id}",
            now()->addHours(24),
            fn() => $this->generateCssFile($theme, $filename)
        );
    }

    /**
     * Preview theme without saving
     */
    public function previewTheme(array $colors): string
    {
        $theme = new SchoolTheme($colors);
        return $theme->toFullCss();
    }

    /**
     * Get color palettes (predefined themes)
     */
    public function getColorPalettes(): array
    {
        return [
            'default' => [
                'name' => 'Default Blue',
                'primary_color' => '#3B82F6',
                'secondary_color' => '#8B5CF6',
                'accent_color' => '#10B981',
            ],
            'red' => [
                'name' => 'Ruby Red',
                'primary_color' => '#DC2626',
                'secondary_color' => '#991B1B',
                'accent_color' => '#F59E0B',
            ],
            'green' => [
                'name' => 'Emerald Green',
                'primary_color' => '#059669',
                'secondary_color' => '#047857',
                'accent_color' => '#3B82F6',
            ],
            'purple' => [
                'name' => 'Royal Purple',
                'primary_color' => '#7C3AED',
                'secondary_color' => '#5B21B6',
                'accent_color' => '#EC4899',
            ],
            'orange' => [
                'name' => 'Sunset Orange',
                'primary_color' => '#EA580C',
                'secondary_color' => '#C2410C',
                'accent_color' => '#FBBF24',
            ],
            'teal' => [
                'name' => 'Ocean Teal',
                'primary_color' => '#0D9488',
                'secondary_color' => '#115E59',
                'accent_color' => '#06B6D4',
            ],
        ];
    }
}
