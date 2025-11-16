<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('settings')) {
            return;
        }

        $defaults = [
            [
                'key' => 'app_timezone',
                'value' => config('app.timezone', 'UTC'),
                'type' => 'select',
                'group' => 'localization',
            ],
            [
                'key' => 'app_locale',
                'value' => config('app.locale', 'en'),
                'type' => 'select',
                'group' => 'localization',
            ],
        ];

        foreach ($defaults as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Setting::whereIn('key', ['app_timezone', 'app_locale'])->delete();
    }
};

