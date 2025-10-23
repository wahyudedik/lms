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
            'name' => 'Main School LMS',
            'slug' => 'main-school',
            'email' => 'admin@mainschool.edu',
            'phone' => '+62 21 1234 5678',
            'address' => 'Jl. Pendidikan No. 123, Jakarta, Indonesia',
            'is_active' => true,
        ]);

        // Create default theme (blue)
        $mainSchool->theme()->create([
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
