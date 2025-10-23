<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // General Settings
            ['key' => 'app_name', 'value' => 'Laravel LMS', 'type' => 'text', 'group' => 'general'],
            ['key' => 'app_description', 'value' => 'Platform Pembelajaran Online', 'type' => 'textarea', 'group' => 'general'],
            ['key' => 'school_name', 'value' => 'Sekolah Digital', 'type' => 'text', 'group' => 'general'],
            ['key' => 'school_address', 'value' => 'Jl. Pendidikan No. 123', 'type' => 'textarea', 'group' => 'general'],
            ['key' => 'school_phone', 'value' => '021-12345678', 'type' => 'text', 'group' => 'general'],
            ['key' => 'school_email', 'value' => 'info@sekolah.com', 'type' => 'text', 'group' => 'general'],

            // Appearance Settings
            ['key' => 'primary_color', 'value' => '#3B82F6', 'type' => 'color', 'group' => 'appearance'],
            ['key' => 'secondary_color', 'value' => '#10B981', 'type' => 'color', 'group' => 'appearance'],
            ['key' => 'app_logo', 'value' => null, 'type' => 'file', 'group' => 'appearance'],
            ['key' => 'app_favicon', 'value' => null, 'type' => 'file', 'group' => 'appearance'],

            // System Settings
            ['key' => 'enable_registration', 'value' => '1', 'type' => 'boolean', 'group' => 'system'],
            ['key' => 'enable_email_verification', 'value' => '1', 'type' => 'boolean', 'group' => 'system'],
            ['key' => 'maintenance_mode', 'value' => '0', 'type' => 'boolean', 'group' => 'system'],
            ['key' => 'max_upload_size', 'value' => '52428800', 'type' => 'number', 'group' => 'system'], // 50MB

            // Notification Settings
            ['key' => 'notification_enabled', 'value' => '1', 'type' => 'boolean', 'group' => 'notification'],
            ['key' => 'email_notifications', 'value' => '0', 'type' => 'boolean', 'group' => 'notification'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
