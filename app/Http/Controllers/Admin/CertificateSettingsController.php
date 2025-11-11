<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CertificateSettingsController extends Controller
{
    /**
     * Display certificate settings page
     */
    public function index()
    {
        $currentTemplate = Setting::get('certificate_template', 'default');
        $institutionName = Setting::get('certificate_institution_name', config('app.name'));
        $directorName = Setting::get('certificate_director_name', 'Academic Director');
        $logoPath = Setting::get('certificate_logo_path');
        $primaryColor = Setting::get('certificate_primary_color', '#3b82f6');
        $secondaryColor = Setting::get('certificate_secondary_color', '#8b5cf6');
        $accentColor = Setting::get('certificate_accent_color', '#ec4899');

        $templates = [
            'default' => [
                'name' => 'Classic',
                'description' => 'Traditional certificate with decorative elements and medal',
                'preview' => 'images/certificate-previews/default.jpg',
                'features' => ['Purple gradient', 'Medal icon', 'Decorative borders', 'Professional'],
                'best_for' => 'General purpose, celebrations'
            ],
            'modern' => [
                'name' => 'Modern',
                'description' => 'Contemporary design with colorful gradients and stats',
                'preview' => 'images/certificate-previews/modern.jpg',
                'features' => ['Colorful gradients', 'Achievement boxes', 'Data-driven', 'Clean'],
                'best_for' => 'Tech courses, modern brands'
            ],
            'elegant' => [
                'name' => 'Elegant',
                'description' => 'Formal certificate with gold accents and serif fonts',
                'preview' => 'images/certificate-previews/elegant.jpg',
                'features' => ['Gold & brown', 'Serif typography', 'Ornamental', 'Premium'],
                'best_for' => 'Academic institutions, formal certifications'
            ],
            'minimalist' => [
                'name' => 'Minimalist',
                'description' => 'Clean design with bold typography and simple layout',
                'preview' => 'images/certificate-previews/minimalist.jpg',
                'features' => ['Black & white', 'Bold typography', 'Clean lines', 'Swiss design'],
                'best_for' => 'Professional certifications, modern companies'
            ],
        ];

        return view('admin.certificate-settings.index', compact(
            'templates',
            'currentTemplate',
            'institutionName',
            'directorName',
            'logoPath',
            'primaryColor',
            'secondaryColor',
            'accentColor'
        ));
    }

    /**
     * Update certificate settings
     */
    public function update(Request $request)
    {
        $request->validate([
            'template' => 'required|in:default,modern,elegant,minimalist',
            'institution_name' => 'required|string|max:255',
            'director_name' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'primary_color' => 'required|string|size:7|regex:/^#[a-fA-F0-9]{6}$/',
            'secondary_color' => 'required|string|size:7|regex:/^#[a-fA-F0-9]{6}$/',
            'accent_color' => 'required|string|size:7|regex:/^#[a-fA-F0-9]{6}$/',
        ]);

        // Save template
        Setting::set('certificate_template', $request->template, 'text', 'certificate');

        // Save institution info
        Setting::set('certificate_institution_name', $request->institution_name, 'text', 'certificate');
        Setting::set('certificate_director_name', $request->director_name, 'text', 'certificate');

        // Save colors
        Setting::set('certificate_primary_color', $request->primary_color, 'color', 'certificate');
        Setting::set('certificate_secondary_color', $request->secondary_color, 'color', 'certificate');
        Setting::set('certificate_accent_color', $request->accent_color, 'color', 'certificate');

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $path = $file->store('certificates/logos', 'public');

            // Delete old logo
            $oldLogo = Setting::get('certificate_logo_path');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }

            Setting::set('certificate_logo_path', $path, 'image', 'certificate');
        }

        // Clear cache
        Setting::clearCache();
        \Illuminate\Support\Facades\Artisan::call('config:clear');
        \Illuminate\Support\Facades\Artisan::call('view:clear');

        return redirect()
            ->route('admin.certificate-settings.index')
            ->with('success', 'Certificate settings updated successfully! 🎓');
    }

    /**
     * Preview certificate with selected template
     */
    public function preview(Request $request)
    {
        $template = $request->get('template', 'default');

        // Create a mock certificate for preview
        $mockCertificate = (object) [
            'student_name' => 'John Doe',
            'course_title' => 'Laravel Advanced Course',
            'course_description' => 'Complete Laravel development course covering advanced topics',
            'certificate_number' => 'CERT-2025-PREVIEW',
            'grade' => 'A',
            'final_score' => 95,
            'completion_date' => now(),
            'issue_date' => now(),
            'instructor_name' => Setting::get('certificate_director_name', 'Instructor Name'),
            'verification_url' => route('certificates.verify', 'CERT-2025-PREVIEW'),
            'metadata' => [
                'total_hours' => 40,
                'duration_days' => 30,
                'course_level' => 'advanced',
            ]
        ];

        $templatePath = match ($template) {
            'modern' => 'certificates.templates.modern',
            'elegant' => 'certificates.templates.elegant',
            'minimalist' => 'certificates.templates.minimalist',
            default => 'certificates.template',
        };

        return view($templatePath, [
            'certificate' => $mockCertificate,
            'qrCode' => '',
        ]);
    }

    /**
     * Reset to default settings
     */
    public function reset()
    {
        Setting::set('certificate_template', 'default', 'text', 'certificate');
        Setting::set('certificate_institution_name', config('app.name'), 'text', 'certificate');
        Setting::set('certificate_director_name', 'Academic Director', 'text', 'certificate');
        Setting::set('certificate_primary_color', '#3b82f6', 'color', 'certificate');
        Setting::set('certificate_secondary_color', '#8b5cf6', 'color', 'certificate');
        Setting::set('certificate_accent_color', '#ec4899', 'color', 'certificate');

        // Delete logo if exists
        $oldLogo = Setting::get('certificate_logo_path');
        if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
            Storage::disk('public')->delete($oldLogo);
        }
        Setting::where('key', 'certificate_logo_path')->delete();

        Setting::clearCache();

        return redirect()
            ->route('admin.certificate-settings.index')
            ->with('success', 'Certificate settings reset to default! 🔄');
    }
}
