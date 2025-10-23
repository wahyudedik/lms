<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\SchoolTheme;
use App\Services\ThemeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ThemeController extends Controller
{
    protected $themeService;

    public function __construct(ThemeService $themeService)
    {
        $this->themeService = $themeService;
    }

    /**
     * Show the theme editor for a school.
     */
    public function edit(School $school)
    {
        $theme = $school->getActiveTheme();
        $palettes = $this->themeService->getColorPalettes();

        return view('admin.themes.edit', compact('school', 'theme', 'palettes'));
    }

    /**
     * Update the theme for a school.
     */
    public function update(Request $request, School $school)
    {
        $validated = $request->validate([
            'primary_color' => 'required|string|max:7',
            'secondary_color' => 'required|string|max:7',
            'accent_color' => 'required|string|max:7',
            'success_color' => 'required|string|max:7',
            'warning_color' => 'required|string|max:7',
            'danger_color' => 'required|string|max:7',
            'info_color' => 'required|string|max:7',
            'dark_color' => 'required|string|max:7',
            'text_primary' => 'required|string|max:7',
            'text_secondary' => 'required|string|max:7',
            'text_muted' => 'required|string|max:7',
            'background_color' => 'required|string|max:7',
            'card_background' => 'required|string|max:7',
            'navbar_background' => 'required|string|max:7',
            'sidebar_background' => 'required|string|max:7',
            'font_family' => 'nullable|string|max:255',
            'heading_font' => 'nullable|string|max:255',
            'font_size' => 'required|integer|min:10|max:24',
            'custom_css' => 'nullable|string',
            'login_background' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'dashboard_hero' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'border_radius' => 'nullable|string|max:20',
            'box_shadow' => 'nullable|string|max:100',
            'dark_mode' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $theme = $school->getActiveTheme();

        // Handle image uploads
        if ($request->hasFile('login_background')) {
            if ($theme->login_background) {
                Storage::disk('public')->delete($theme->login_background);
            }
            $validated['login_background'] = $request->file('login_background')
                ->store('themes/backgrounds', 'public');
        }

        if ($request->hasFile('dashboard_hero')) {
            if ($theme->dashboard_hero) {
                Storage::disk('public')->delete($theme->dashboard_hero);
            }
            $validated['dashboard_hero'] = $request->file('dashboard_hero')
                ->store('themes/heroes', 'public');
        }

        $theme->update($validated);

        // Clear theme cache
        $this->themeService->clearCache($school->id);

        return back()->with('success', 'Theme updated successfully!');
    }

    /**
     * Preview theme changes without saving.
     */
    public function preview(Request $request)
    {
        $css = $this->themeService->previewTheme($request->all());

        return response()->json([
            'success' => true,
            'css' => $css,
        ]);
    }

    /**
     * Apply a predefined color palette.
     */
    public function applyPalette(Request $request, School $school)
    {
        $paletteName = $request->input('palette');
        $palettes = $this->themeService->getColorPalettes();

        if (!isset($palettes[$paletteName])) {
            return back()->with('error', 'Invalid palette selected!');
        }

        $palette = $palettes[$paletteName];
        $theme = $school->getActiveTheme();

        $theme->update([
            'primary_color' => $palette['primary_color'],
            'secondary_color' => $palette['secondary_color'],
            'accent_color' => $palette['accent_color'],
        ]);

        // Clear cache
        $this->themeService->clearCache($school->id);

        return back()->with('success', 'Palette applied successfully!');
    }

    /**
     * Reset theme to default.
     */
    public function reset(School $school)
    {
        $theme = $school->getActiveTheme();
        $defaultTheme = $this->themeService->getDefaultTheme();

        $theme->update($defaultTheme->toArray());

        // Clear cache
        $this->themeService->clearCache($school->id);

        return back()->with('success', 'Theme reset to default!');
    }

    /**
     * Export theme as JSON.
     */
    public function export(School $school)
    {
        $theme = $school->getActiveTheme();

        $data = $theme->only([
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
            'border_radius',
            'box_shadow',
            'dark_mode',
        ]);

        return response()->json($data)
            ->header('Content-Disposition', 'attachment; filename="theme-' . $school->slug . '.json"');
    }

    /**
     * Import theme from JSON.
     */
    public function import(Request $request, School $school)
    {
        $request->validate([
            'theme_file' => 'required|file|mimes:json',
        ]);

        $json = file_get_contents($request->file('theme_file')->getRealPath());
        $data = json_decode($json, true);

        if (!$data) {
            return back()->with('error', 'Invalid theme file!');
        }

        $theme = $school->getActiveTheme();
        $theme->update($data);

        // Clear cache
        $this->themeService->clearCache($school->id);

        return back()->with('success', 'Theme imported successfully!');
    }
}
