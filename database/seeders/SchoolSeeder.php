<?php

namespace Database\Seeders;

use App\Models\School;
use App\Models\User;
use Illuminate\Database\Seeder;

class SchoolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Main School
        $mainSchool = School::create([
            'name' => 'Koneksi',
            'slug' => 'koneksi',
            'email' => 'admin@koneksi.edu',
            'phone' => '+62 21 1234 5678',
            'address' => 'Jl. Pendidikan No. 123, Jakarta, Indonesia',
            'is_active' => true,

            // Landing Page Settings
            'show_landing_page' => true,
            'hero_title' => 'Welcome to Koneksi',
            'hero_subtitle' => 'Kolaborasi Online Edukasi dan Komunikasi Siswa',
            'hero_description' => 'Bergabunglah dengan ribuan siswa dan guru dalam sistem manajemen pembelajaran inovatif kami yang dirancang untuk kesuksesan.',
            'hero_cta_text' => 'Get Started',
            'hero_cta_link' => '/register',

            'about_title' => 'About Koneksi',
            'about_content' => "Koneksi adalah institusi pendidikan terkemuka yang berkomitmen untuk menyediakan pendidikan berkualitas melalui teknologi modern. Kami percaya dalam memberdayakan siswa dengan alat dan pengetahuan yang mereka butuhkan untuk sukses di dunia digital saat ini.\n\nGuru-guru berpengalaman kami dan platform canggih memastikan setiap siswa menerima perhatian personal dan pengalaman belajar yang menarik.",

            'features' => json_encode([
                [
                    'icon' => 'fa-graduation-cap',
                    'title' => 'Quality Education',
                    'description' => 'High-quality courses designed by experienced educators'
                ],
                [
                    'icon' => 'fa-users',
                    'title' => 'Interactive Learning',
                    'description' => 'Engage with peers and teachers in real-time discussions'
                ],
                [
                    'icon' => 'fa-certificate',
                    'title' => 'Certified Courses',
                    'description' => 'Get certified upon successful course completion'
                ],
                [
                    'icon' => 'fa-clock',
                    'title' => '24/7 Access',
                    'description' => 'Learn at your own pace, anytime, anywhere'
                ],
                [
                    'icon' => 'fa-mobile-alt',
                    'title' => 'Mobile Friendly',
                    'description' => 'Access courses on any device seamlessly'
                ],
                [
                    'icon' => 'fa-headset',
                    'title' => 'Support Team',
                    'description' => 'Dedicated support team ready to help you'
                ]
            ]),

            'statistics' => json_encode([
                ['label' => 'Active Students', 'value' => '1,200+'],
                ['label' => 'Courses Available', 'value' => '50+'],
                ['label' => 'Expert Teachers', 'value' => '35+'],
                ['label' => 'Success Rate', 'value' => '96%']
            ]),

            'contact_email' => 'admin@koneksi.edu',
            'contact_phone' => '+62 21 1234 5678',
            'contact_whatsapp' => '+62 812 3456 7890',
            'contact_address' => 'Jl. Pendidikan No. 123, Jakarta, Indonesia',

            'social_facebook' => 'https://facebook.com/koneksi',
            'social_instagram' => 'https://instagram.com/koneksi',
            'social_twitter' => 'https://twitter.com/koneksi',
            'social_youtube' => 'https://youtube.com/@koneksi',

            'meta_title' => 'Koneksi - Kolaborasi Online Edukasi dan Komunikasi Siswa',
            'meta_description' => 'Bergabunglah dengan Koneksi untuk pendidikan online berkualitas. Guru ahli, kursus interaktif, dan pembelajaran fleksibel yang dirancang untuk kesuksesan Anda.',
            'meta_keywords' => 'online learning, education, LMS, courses, elearning, koneksi',
        ]);

        // Create default theme (Modern Blue)
        $mainSchool->theme()->create([
            'primary_color' => '#2563EB', // Modern Blue
            'secondary_color' => '#64748B', // Slate
            'accent_color' => '#0EA5E9', // Sky
            'success_color' => '#10B981', // Emerald
            'warning_color' => '#F59E0B', // Amber
            'danger_color' => '#EF4444', // Red
            'info_color' => '#3B82F6', // Blue
            'dark_color' => '#0F172A', // Slate 900
            'text_primary' => '#0F172A',
            'text_secondary' => '#475569',
            'text_muted' => '#94A3B8',
            'background_color' => '#F8FAFC', // Very light slate
            'card_background' => '#FFFFFF',
            'navbar_background' => '#FFFFFF',
            'sidebar_background' => '#1E293B', // Dark Slate for professional look
            'font_family' => 'Inter, sans-serif',
            'font_size' => 15,
            'border_radius' => '0.5rem',
            'box_shadow' => '0 1px 3px 0 rgb(0 0 0 / 0.1)',
            'is_active' => true,
        ]);

        // Update existing users to belong to main school
        User::whereNull('school_id')->update(['school_id' => $mainSchool->id]);

        $this->command->info("✅ Main school created with ID: {$mainSchool->id}");
        $this->command->info("✅ Default theme applied (Blue)");
        $this->command->info("✅ Existing users assigned to main school");

        // Create Demo School with Red Theme
        $demoSchool = School::create([
            'name' => 'Demo School',
            'slug' => 'demo-school',
            'email' => 'info@demoschool.edu',
            'phone' => '+62 21 8765 4321',
            'address' => 'Jl. Demo No. 456, Bandung, Indonesia',
            'is_active' => true,
        ]);

        // Create red theme
        $demoSchool->theme()->create([
            'primary_color' => '#DC2626',
            'secondary_color' => '#991B1B',
            'accent_color' => '#F59E0B',
            'success_color' => '#10B981',
            'warning_color' => '#F59E0B',
            'danger_color' => '#DC2626',
            'info_color' => '#3B82F6',
            'dark_color' => '#1F2937',
            'text_primary' => '#1F2937',
            'text_secondary' => '#6B7280',
            'text_muted' => '#9CA3AF',
            'background_color' => '#FEF2F2',
            'card_background' => '#FFFFFF',
            'navbar_background' => '#DC2626',
            'sidebar_background' => '#991B1B',
            'font_family' => 'Inter, sans-serif',
            'font_size' => 16,
            'border_radius' => '0.75rem',
            'box_shadow' => '0 4px 6px -1px rgb(0 0 0 / 0.1)',
            'is_active' => true,
        ]);

        $this->command->info("✅ Demo school created with red theme");

        // Create Green School
        $greenSchool = School::create([
            'name' => 'Green Academy',
            'slug' => 'green-academy',
            'email' => 'admin@greenacademy.edu',
            'is_active' => true,
        ]);

        // Create green theme
        $greenSchool->theme()->create([
            'primary_color' => '#059669',
            'secondary_color' => '#047857',
            'accent_color' => '#3B82F6',
            'success_color' => '#059669',
            'warning_color' => '#F59E0B',
            'danger_color' => '#EF4444',
            'info_color' => '#3B82F6',
            'dark_color' => '#064E3B',
            'text_primary' => '#064E3B',
            'text_secondary' => '#047857',
            'text_muted' => '#6EE7B7',
            'background_color' => '#ECFDF5',
            'card_background' => '#FFFFFF',
            'navbar_background' => '#059669',
            'sidebar_background' => '#064E3B',
            'font_family' => 'Poppins, sans-serif',
            'font_size' => 15,
            'border_radius' => '1rem',
            'box_shadow' => '0 10px 15px -3px rgb(0 0 0 / 0.1)',
            'is_active' => true,
        ]);

        $this->command->info("✅ Green Academy created with green theme");

        $this->command->info("\n🎉 School seeder completed!");
        $this->command->info("📊 Created 3 schools with different themes");
        $this->command->info("🎨 Themes: Blue (default), Red, Green");
        $this->command->info("\n💡 Access: /admin/schools");
    }
}
