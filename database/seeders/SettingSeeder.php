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
            ['key' => 'app_name', 'value' => 'Koneksi', 'type' => 'text', 'group' => 'general'],
            ['key' => 'app_description', 'value' => 'Kolaborasi Online Edukasi dan Komunikasi Siswa', 'type' => 'textarea', 'group' => 'general'],
            ['key' => 'school_name', 'value' => 'Sekolah Digital', 'type' => 'text', 'group' => 'general'],
            ['key' => 'school_address', 'value' => 'Jl. Pendidikan No. 123', 'type' => 'textarea', 'group' => 'general'],
            ['key' => 'school_phone', 'value' => '021-12345678', 'type' => 'text', 'group' => 'general'],
            ['key' => 'school_email', 'value' => 'info@sekolah.com', 'type' => 'text', 'group' => 'general'],

            // Localization Settings
            ['key' => 'app_timezone', 'value' => config('app.timezone', 'UTC'), 'type' => 'select', 'group' => 'localization'],
            ['key' => 'app_locale', 'value' => config('app.locale', 'en'), 'type' => 'select', 'group' => 'localization'],

            // Appearance Settings
            ['key' => 'primary_color', 'value' => '#2563EB', 'type' => 'color', 'group' => 'appearance'],
            ['key' => 'secondary_color', 'value' => '#64748B', 'type' => 'color', 'group' => 'appearance'],
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

            // Certificate Settings
            ['key' => 'certificate_template', 'value' => 'default', 'type' => 'text', 'group' => 'certificate'],
            ['key' => 'certificate_institution_name', 'value' => 'Sekolah Digital', 'type' => 'text', 'group' => 'certificate'],
            ['key' => 'certificate_director_name', 'value' => 'Dr. John Smith', 'type' => 'text', 'group' => 'certificate'],
            ['key' => 'certificate_primary_color', 'value' => '#2563EB', 'type' => 'color', 'group' => 'certificate'],
            ['key' => 'certificate_secondary_color', 'value' => '#64748B', 'type' => 'color', 'group' => 'certificate'],
            ['key' => 'certificate_accent_color', 'value' => '#0EA5E9', 'type' => 'color', 'group' => 'certificate'],

            // AI Settings
            ['key' => 'ai_enabled', 'value' => '0', 'type' => 'boolean', 'group' => 'ai'],
            ['key' => 'ai_openai_api_key', 'value' => null, 'type' => 'text', 'group' => 'ai'],
            ['key' => 'ai_model', 'value' => 'gpt-3.5-turbo', 'type' => 'text', 'group' => 'ai'],
            ['key' => 'ai_max_tokens', 'value' => '1000', 'type' => 'integer', 'group' => 'ai'],
            ['key' => 'ai_temperature', 'value' => '0.7', 'type' => 'float', 'group' => 'ai'],
            ['key' => 'ai_system_prompt', 'value' => '', 'type' => 'textarea', 'group' => 'ai'],
            ['key' => 'ai_rate_limit', 'value' => '20', 'type' => 'integer', 'group' => 'ai'],
            ['key' => 'ai_show_widget', 'value' => '1', 'type' => 'boolean', 'group' => 'ai'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
