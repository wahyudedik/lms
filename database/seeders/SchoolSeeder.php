<?php

namespace Database\Seeders;

use App\Models\School;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class SchoolSeeder extends Seeder
{
    /**
     * Copy an image from public/course/images/ into storage/app/public/
     * and return the storage-relative path (e.g. "schools/logos/koneksi-logo.png").
     *
     * Returns null if the source file does not exist.
     */
    private function copyImage(string $sourceFilename, string $destRelativePath): ?string
    {
        $sourcePath = public_path('course/images/' . $sourceFilename);

        if (!File::exists($sourcePath)) {
            $this->command->warn("  ⚠ Source image not found: {$sourcePath}");
            return null;
        }

        // Ensure destination directory exists
        $destDir = dirname(storage_path('app/public/' . $destRelativePath));
        File::ensureDirectoryExists($destDir);

        // Copy the file
        File::copy($sourcePath, storage_path('app/public/' . $destRelativePath));

        return $destRelativePath;
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('📸 Copying images from public/course/images/ to storage...');

        // ── Koneksi images ────────────────────────────────────────────────────
        $koneksiLogo      = $this->copyImage('logo.png',                    'schools/logos/koneksi-logo.png');
        $koneksiFavicon   = $this->copyImage('logo.png',                    'schools/favicons/koneksi-favicon.png');
        $koneksiHero      = $this->copyImage('slider_background.jpg',       'schools/hero/koneksi-hero.jpg');
        $koneksiAbout     = $this->copyImage('become.jpg',                  'schools/about/koneksi-about.jpg');

        // ── Demo School images ────────────────────────────────────────────────
        $demoLogo         = $this->copyImage('logo.png',                    'schools/logos/demo-logo.png');
        $demoHero         = $this->copyImage('courses_background.jpg',      'schools/hero/demo-hero.jpg');

        // ── Green Academy images ──────────────────────────────────────────────
        $greenLogo        = $this->copyImage('logo.png',                    'schools/logos/green-logo.png');
        $greenHero        = $this->copyImage('teachers_background.jpg',     'schools/hero/green-hero.jpg');

        $this->command->info('✅ Images copied.');

        // =====================================================================
        // Main School: "Koneksi" — based on public/course/index.html template
        // =====================================================================
        $mainSchool = School::updateOrCreate(
            ['slug' => 'koneksi'],
            [
                'name'             => 'Koneksi',
                'slug'             => 'koneksi',
                'email'            => 'admin@koneksi.edu',
                'phone'            => '+43 4566 7788 2457',
                'address'          => 'Blvd Libertad, 34 m05200 Arévalo',
                'logo'             => $koneksiLogo,
                'favicon'          => $koneksiFavicon,
                'is_active'        => true,
                'is_landing_active' => true,

                // Landing Page
                'show_landing_page' => true,
                'hero_title'       => 'Get your Education today!',
                'hero_subtitle'    => 'Kolaborasi Online Edukasi dan Komunikasi Siswa',
                'hero_description' => 'Bergabunglah dengan ribuan siswa dan guru dalam sistem manajemen pembelajaran inovatif kami yang dirancang untuk kesuksesan.',
                'hero_image'       => $koneksiHero,
                'hero_cta_text'    => 'Get Started',
                'hero_cta_link'    => '/register',

                // About
                'about_title'      => 'About Koneksi',
                'about_image'      => $koneksiAbout,
                'about_content'    => "Koneksi adalah institusi pendidikan terkemuka yang berkomitmen untuk menyediakan pendidikan berkualitas melalui teknologi modern. Kami percaya dalam memberdayakan siswa dengan alat dan pengetahuan yang mereka butuhkan untuk sukses di dunia digital saat ini.\n\nGuru-guru berpengalaman kami dan platform canggih memastikan setiap siswa menerima perhatian personal dan pengalaman belajar yang menarik.",

                // Features — from services section in index.html
                'features' => json_encode([
                    [
                        'icon'        => 'fa-globe',
                        'title'       => 'Online Courses',
                        'description' => 'In aliquam, augue a gravida rutrum, ante nisl fermentum nulla, vitae tempor nisl ligula vel nunc. Proin quis mi malesuada, finibus tortor fermentum.',
                    ],
                    [
                        'icon'        => 'fa-chalkboard',
                        'title'       => 'Indoor Courses',
                        'description' => 'In aliquam, augue a gravida rutrum, ante nisl fermentum nulla, vitae tempor nisl ligula vel nunc. Proin quis mi malesuada, finibus tortor fermentum.',
                    ],
                    [
                        'icon'        => 'fa-book',
                        'title'       => 'Amazing Library',
                        'description' => 'In aliquam, augue a gravida rutrum, ante nisl fermentum nulla, vitae tempor nisl ligula vel nunc. Proin quis mi malesuada, finibus tortor fermentum.',
                    ],
                    [
                        'icon'        => 'fa-user-tie',
                        'title'       => 'Exceptional Professors',
                        'description' => 'In aliquam, augue a gravida rutrum, ante nisl fermentum nulla, vitae tempor nisl ligula vel nunc. Proin quis mi malesuada, finibus tortor fermentum.',
                    ],
                    [
                        'icon'        => 'fa-chalkboard-teacher',
                        'title'       => 'Top Programs',
                        'description' => 'In aliquam, augue a gravida rutrum, ante nisl fermentum nulla, vitae tempor nisl ligula vel nunc. Proin quis mi malesuada, finibus tortor fermentum.',
                    ],
                    [
                        'icon'        => 'fa-graduation-cap',
                        'title'       => 'Graduate Diploma',
                        'description' => 'In aliquam, augue a gravida rutrum, ante nisl fermentum nulla, vitae tempor nisl ligula vel nunc. Proin quis mi malesuada, finibus tortor fermentum.',
                    ],
                ]),

                // Statistics — from hero_boxes in index.html
                'statistics' => json_encode([
                    ['label' => 'Online Courses',  'value' => '50+'],
                    ['label' => 'Library Books',   'value' => '1,200+'],
                    ['label' => 'Expert Teachers', 'value' => '35+'],
                    ['label' => 'Success Rate',    'value' => '96%'],
                ]),

                // Contact — from footer contact column in index.html
                'contact_address'  => 'Blvd Libertad, 34 m05200 Arévalo',
                'contact_phone'    => '0034 37483 2445 322',
                'contact_email'    => 'hello@company.com',
                'contact_whatsapp' => '+62 812 3456 7890',

                // Social — from footer_social in index.html
                'social_facebook'  => 'https://facebook.com/koneksi',
                'social_instagram' => 'https://instagram.com/koneksi',
                'social_twitter'   => 'https://twitter.com/koneksi',
                'social_youtube'   => 'https://youtube.com/@koneksi',

                // SEO
                'meta_title'       => 'Koneksi - Kolaborasi Online Edukasi dan Komunikasi Siswa',
                'meta_description' => 'Bergabunglah dengan Koneksi untuk pendidikan online berkualitas. Guru ahli, kursus interaktif, dan pembelajaran fleksibel yang dirancang untuk kesuksesan Anda.',
                'meta_keywords'    => 'online learning, education, LMS, courses, elearning, koneksi',
            ]
        );

        // Theme — Blue + Gold accent (matching yellow buttons in index.html)
        $mainSchool->theme()->updateOrCreate(
            ['school_id' => $mainSchool->id],
            [
                'primary_color'      => '#2563EB',
                'secondary_color'    => '#1E293B',
                'accent_color'       => '#F59E0B', // Gold — matches index.html yellow CTA
                'success_color'      => '#10B981',
                'warning_color'      => '#F59E0B',
                'danger_color'       => '#EF4444',
                'info_color'         => '#3B82F6',
                'dark_color'         => '#0F172A',
                'text_primary'       => '#0F172A',
                'text_secondary'     => '#475569',
                'text_muted'         => '#94A3B8',
                'background_color'   => '#F8FAFC',
                'card_background'    => '#FFFFFF',
                'navbar_background'  => '#FFFFFF',
                'sidebar_background' => '#1E293B',
                'font_family'        => 'Inter, sans-serif',
                'font_size'          => 15,
                'border_radius'      => '0.5rem',
                'box_shadow'         => '0 1px 3px 0 rgb(0 0 0 / 0.1)',
                'is_active'          => true,
            ]
        );

        // Assign existing users to main school
        User::whereNull('school_id')->update(['school_id' => $mainSchool->id]);

        $this->command->info("✅ Koneksi (ID: {$mainSchool->id}) — landing page ACTIVE");
        $this->command->info("   logo: " . ($koneksiLogo ?? 'none') . " | hero: " . ($koneksiHero ?? 'none'));

        // =====================================================================
        // Demo School — Red Theme
        // =====================================================================
        $demoSchool = School::updateOrCreate(
            ['slug' => 'demo-school'],
            [
                'name'             => 'Demo School',
                'slug'             => 'demo-school',
                'email'            => 'info@demoschool.edu',
                'phone'            => '+62 21 8765 4321',
                'address'          => 'Jl. Demo No. 456, Bandung, Indonesia',
                'logo'             => $demoLogo,
                'is_active'        => true,
                'is_landing_active' => false,

                'show_landing_page' => true,
                'hero_title'       => 'Selamat Datang di Demo School',
                'hero_subtitle'    => 'Platform Pembelajaran Modern untuk Generasi Masa Depan',
                'hero_description' => 'Demo School hadir dengan teknologi terkini untuk mendukung proses belajar mengajar yang efektif dan menyenangkan.',
                'hero_image'       => $demoHero,
                'hero_cta_text'    => 'Mulai Sekarang',
                'hero_cta_link'    => '/register',

                'features' => json_encode([
                    ['icon' => 'fa-graduation-cap', 'title' => 'Quality Education',   'description' => 'High-quality courses designed by experienced educators'],
                    ['icon' => 'fa-users',           'title' => 'Interactive Learning', 'description' => 'Engage with peers and teachers in real-time discussions'],
                    ['icon' => 'fa-certificate',     'title' => 'Certified Courses',   'description' => 'Get certified upon successful course completion'],
                ]),

                'statistics' => json_encode([
                    ['label' => 'Active Students', 'value' => '500+'],
                    ['label' => 'Courses',         'value' => '20+'],
                    ['label' => 'Teachers',        'value' => '15+'],
                    ['label' => 'Success Rate',    'value' => '92%'],
                ]),

                'contact_address'  => 'Jl. Demo No. 456, Bandung, Indonesia',
                'contact_phone'    => '+62 21 8765 4321',
                'contact_email'    => 'info@demoschool.edu',
                'contact_whatsapp' => '+62 811 2233 4455',
                'social_facebook'  => 'https://facebook.com/demoschool',
                'social_instagram' => 'https://instagram.com/demoschool',

                'meta_title'       => 'Demo School - Platform Pembelajaran Modern',
                'meta_description' => 'Demo School menyediakan pendidikan berkualitas dengan teknologi modern.',
                'meta_keywords'    => 'demo school, online learning, LMS',
            ]
        );

        $demoSchool->theme()->updateOrCreate(
            ['school_id' => $demoSchool->id],
            [
                'primary_color'      => '#DC2626',
                'secondary_color'    => '#991B1B',
                'accent_color'       => '#F59E0B',
                'success_color'      => '#10B981',
                'warning_color'      => '#F59E0B',
                'danger_color'       => '#DC2626',
                'info_color'         => '#3B82F6',
                'dark_color'         => '#1F2937',
                'text_primary'       => '#1F2937',
                'text_secondary'     => '#6B7280',
                'text_muted'         => '#9CA3AF',
                'background_color'   => '#FEF2F2',
                'card_background'    => '#FFFFFF',
                'navbar_background'  => '#DC2626',
                'sidebar_background' => '#991B1B',
                'font_family'        => 'Inter, sans-serif',
                'font_size'          => 16,
                'border_radius'      => '0.75rem',
                'box_shadow'         => '0 4px 6px -1px rgb(0 0 0 / 0.1)',
                'is_active'          => true,
            ]
        );

        $this->command->info("✅ Demo School (ID: {$demoSchool->id}) — red theme");

        // =====================================================================
        // Green Academy — Green Theme
        // =====================================================================
        $greenSchool = School::updateOrCreate(
            ['slug' => 'green-academy'],
            [
                'name'             => 'Green Academy',
                'slug'             => 'green-academy',
                'email'            => 'admin@greenacademy.edu',
                'logo'             => $greenLogo,
                'is_active'        => true,
                'is_landing_active' => false,

                'show_landing_page' => true,
                'hero_title'       => 'Belajar Bersama Green Academy',
                'hero_subtitle'    => 'Pendidikan Hijau untuk Masa Depan Berkelanjutan',
                'hero_description' => 'Green Academy berkomitmen untuk memberikan pendidikan berkualitas dengan pendekatan ramah lingkungan.',
                'hero_image'       => $greenHero,
                'hero_cta_text'    => 'Daftar Sekarang',
                'hero_cta_link'    => '/register',

                'features' => json_encode([
                    ['icon' => 'fa-leaf',        'title' => 'Eco Learning',    'description' => 'Pembelajaran berbasis lingkungan yang berkelanjutan'],
                    ['icon' => 'fa-users',       'title' => 'Komunitas Aktif', 'description' => 'Bergabung dengan komunitas pelajar yang aktif dan suportif'],
                    ['icon' => 'fa-certificate', 'title' => 'Sertifikasi',     'description' => 'Dapatkan sertifikat resmi setelah menyelesaikan kursus'],
                ]),

                'statistics' => json_encode([
                    ['label' => 'Siswa Aktif', 'value' => '300+'],
                    ['label' => 'Kursus',      'value' => '15+'],
                    ['label' => 'Pengajar',    'value' => '10+'],
                    ['label' => 'Kelulusan',   'value' => '98%'],
                ]),

                'contact_email'    => 'admin@greenacademy.edu',
                'contact_whatsapp' => '+62 813 9988 7766',
                'social_instagram' => 'https://instagram.com/greenacademy',
                'social_youtube'   => 'https://youtube.com/@greenacademy',

                'meta_title'       => 'Green Academy - Pendidikan Hijau Berkelanjutan',
                'meta_description' => 'Green Academy menyediakan pendidikan berkualitas dengan pendekatan ramah lingkungan.',
                'meta_keywords'    => 'green academy, eco learning, pendidikan, LMS',
            ]
        );

        $greenSchool->theme()->updateOrCreate(
            ['school_id' => $greenSchool->id],
            [
                'primary_color'      => '#059669',
                'secondary_color'    => '#047857',
                'accent_color'       => '#3B82F6',
                'success_color'      => '#059669',
                'warning_color'      => '#F59E0B',
                'danger_color'       => '#EF4444',
                'info_color'         => '#3B82F6',
                'dark_color'         => '#064E3B',
                'text_primary'       => '#064E3B',
                'text_secondary'     => '#047857',
                'text_muted'         => '#6EE7B7',
                'background_color'   => '#ECFDF5',
                'card_background'    => '#FFFFFF',
                'navbar_background'  => '#059669',
                'sidebar_background' => '#064E3B',
                'font_family'        => 'Poppins, sans-serif',
                'font_size'          => 15,
                'border_radius'      => '1rem',
                'box_shadow'         => '0 10px 15px -3px rgb(0 0 0 / 0.1)',
                'is_active'          => true,
            ]
        );

        $this->command->info("✅ Green Academy (ID: {$greenSchool->id}) — green theme");

        $this->command->info("\n🎉 School seeder completed!");
        $this->command->info("📊 3 schools | 🎨 Blue+Gold / Red / Green themes");
        $this->command->info("� Active landing: Koneksi");
        $this->command->info("🖼  Images stored in storage/app/public/schools/");
        $this->command->info("💡 Run: php artisan storage:link  (if not done yet)");
    }
}
