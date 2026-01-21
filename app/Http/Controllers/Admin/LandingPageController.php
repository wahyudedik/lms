<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LandingPageController extends Controller
{
    /**
     * Show landing page editor
     */
    public function edit(School $school)
    {
        return view('admin.landing-page.edit', compact('school'));
    }

    /**
     * Update landing page settings
     */
    public function update(Request $request, School $school)
    {
        $validated = $request->validate([
            'show_landing_page' => 'nullable|boolean',

            // Hero Section
            'hero_title' => 'nullable|string|max:255',
            'hero_subtitle' => 'nullable|string|max:255',
            'hero_description' => 'nullable|string',
            'hero_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'hero_cta_text' => 'nullable|string|max:100',
            'hero_cta_link' => 'nullable|string|max:255',

            // About Section
            'about_title' => 'nullable|string',
            'about_content' => 'nullable|string',
            'about_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',

            // Features (will be handled separately)
            'features' => 'nullable|array',
            'features.*.icon' => 'nullable|string|max:100',
            'features.*.title' => 'nullable|string|max:255',
            'features.*.description' => 'nullable|string',

            // Statistics (will be handled separately)
            'statistics' => 'nullable|array',
            'statistics.*.label' => 'nullable|string|max:100',
            'statistics.*.value' => 'nullable|string|max:100',

            // Contact Section
            'contact_address' => 'nullable|string',
            'contact_phone' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email|max:255',
            'contact_whatsapp' => 'nullable|string|max:20',

            // Social Media
            'social_facebook' => 'nullable|url|max:255',
            'social_instagram' => 'nullable|url|max:255',
            'social_twitter' => 'nullable|url|max:255',
            'social_youtube' => 'nullable|url|max:255',

            // SEO
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
        ]);

        // Handle hero image upload
        if ($request->hasFile('hero_image')) {
            // Delete old image
            if ($school->hero_image) {
                Storage::disk('public')->delete($school->hero_image);
            }
            $validated['hero_image'] = $request->file('hero_image')->store('landing-pages/heroes', 'public');
        }

        // Handle about image upload
        if ($request->hasFile('about_image')) {
            // Delete old image
            if ($school->about_image) {
                Storage::disk('public')->delete($school->about_image);
            }
            $validated['about_image'] = $request->file('about_image')->store('landing-pages/about', 'public');
        }

        // Ensure boolean value for show_landing_page
        $validated['show_landing_page'] = $request->has('show_landing_page');

        // Update school
        $school->update($validated);

        return redirect()
            ->route('admin.landing-page.edit', $school)
            ->with('success', 'Landing page updated successfully!');
    }

    /**
     * Preview landing page
     */
    public function preview(School $school)
    {
        return view('welcome', ['school' => $school, 'preview' => true]);
    }
}
