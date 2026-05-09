<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PushSubscription;
use App\Models\School;
use App\Models\Setting;
use App\Services\PushNotificationService;
use App\Services\ThemeService;
use App\Support\AppPreferences as AppPreferencesSupport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SettingsController extends Controller
{
    protected ThemeService $themeService;

    public function __construct(ThemeService $themeService)
    {
        $this->themeService = $themeService;
    }

    /**
     * Display settings page — loads system settings + school profile + theme + landing page
     */
    public function index()
    {
        $settings        = Setting::getAllGrouped();
        $timezoneOptions = AppPreferencesSupport::timezoneOptions();
        $languageOptions = AppPreferencesSupport::languageOptions();

        // Load the single school (first/only school in the system)
        $school  = School::with('theme')->firstOrFail();
        $theme   = $school->getActiveTheme();
        $palettes = $this->themeService->getColorPalettes();

        return view(
            'admin.settings.index',
            compact('settings', 'timezoneOptions', 'languageOptions', 'school', 'theme', 'palettes')
        );
    }

    /**
     * Update settings
     */
    public function update(Request $request)
    {
        $request->validate([
            'settings' => 'required|array',
            'settings.app_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'settings.app_favicon' => 'nullable|image|mimes:ico,png,svg|max:512',
        ]);

        foreach ($request->settings as $key => $value) {
            // Handle logo upload
            if ($key === 'app_logo' && $request->hasFile('settings.app_logo')) {
                $file = $request->file('settings.app_logo');
                $path = $file->store('settings', 'public');

                // Delete old logo
                $oldLogo = Setting::get('app_logo');
                if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                    Storage::disk('public')->delete($oldLogo);
                }

                $value = $path;
            }

            // Handle favicon upload
            if ($key === 'app_favicon' && $request->hasFile('settings.app_favicon')) {
                $file = $request->file('settings.app_favicon');
                $path = $file->store('settings', 'public');

                // Delete old favicon
                $oldFavicon = Setting::get('app_favicon');
                if ($oldFavicon && Storage::disk('public')->exists($oldFavicon)) {
                    Storage::disk('public')->delete($oldFavicon);
                }

                $value = $path;
            }

            // Convert checkbox values
            if (is_null($value)) {
                $value = '0'; // Unchecked checkboxes don't send values
            }

            $setting = Setting::where('key', $key)->first();
            if ($setting) {
                Setting::set($key, $value, $setting->type, $setting->group);
            }
        }

        Setting::clearCache();

        return redirect()->back()->with('success', __('Pengaturan berhasil diperbarui'));
    }

    /**
     * Database backup page
     */
    public function backup()
    {
        $backups = $this->getBackupFiles();
        return view('admin.settings.backup', compact('backups'));
    }

    /**
     * Create database backup
     */
    public function createBackup()
    {
        try {
            $filename = 'backup-' . date('Y-m-d-His') . '.sql';
            $storagePath = storage_path('app/backups');

            // Create backups directory if doesn't exist
            if (!file_exists($storagePath)) {
                mkdir($storagePath, 0755, true);
            }

            $filepath = $storagePath . '/' . $filename;

            // Get database configuration from config (not env)
            $dbName = config('database.connections.mysql.database');
            $dbUser = config('database.connections.mysql.username');
            $dbPassword = config('database.connections.mysql.password');
            $dbHost = config('database.connections.mysql.host', '127.0.0.1');
            $dbPort = config('database.connections.mysql.port', '3306');

            // Use mysqldump command
            // Try to find mysqldump in common paths
            $mysqldumpPath = $this->findMysqldumpPath();

            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                // Windows - escape path and password
                $escapedPassword = escapeshellarg($dbPassword);
                $command = sprintf(
                    '%s --user=%s --password=%s --host=%s --port=%s %s > %s',
                    escapeshellarg($mysqldumpPath),
                    escapeshellarg($dbUser),
                    $escapedPassword,
                    escapeshellarg($dbHost),
                    escapeshellarg($dbPort),
                    escapeshellarg($dbName),
                    escapeshellarg($filepath)
                );
            } else {
                // Linux/Mac - use environment variable for password (more secure)
                $command = sprintf(
                    'MYSQL_PWD=%s %s --user=%s --host=%s --port=%s %s > %s 2>&1',
                    escapeshellarg($dbPassword),
                    escapeshellarg($mysqldumpPath),
                    escapeshellarg($dbUser),
                    escapeshellarg($dbHost),
                    escapeshellarg($dbPort),
                    escapeshellarg($dbName),
                    escapeshellarg($filepath)
                );
            }

            // Execute command securely
            $output = [];
            $returnVar = 0;
            exec($command . ' 2>&1', $output, $returnVar);

            if ($returnVar !== 0 || !file_exists($filepath) || filesize($filepath) === 0) {
                // Fallback: Use Laravel's DB export
                $this->fallbackBackup($filepath);

                // Log error if backup failed
                if (!file_exists($filepath) || filesize($filepath) === 0) {
                    \Log::error('Database backup failed', [
                        'command' => $command,
                        'output' => $output,
                        'return_var' => $returnVar,
                    ]);
                }
            }

            return redirect()->back()->with('success', "Backup berhasil dibuat: {$filename}");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal membuat backup: ' . $e->getMessage());
        }
    }

    /**
     * Fallback backup method using Laravel DB
     */
    private function fallbackBackup($filepath)
    {
        $tables = \DB::select('SHOW TABLES');
        $sql = '';

        foreach ($tables as $table) {
            $tableName = array_values((array) $table)[0];
            $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";

            $createTable = \DB::select("SHOW CREATE TABLE `{$tableName}`")[0];
            $sql .= $createTable->{'Create Table'} . ";\n\n";

            $rows = \DB::table($tableName)->get();
            foreach ($rows as $row) {
                $values = array_map(function ($value) {
                    return is_null($value) ? 'NULL' : "'" . addslashes($value) . "'";
                }, (array) $row);
                $sql .= "INSERT INTO `{$tableName}` VALUES (" . implode(', ', $values) . ");\n";
            }
            $sql .= "\n";
        }

        file_put_contents($filepath, $sql);
    }

    /**
     * Download backup file
     */
    public function downloadBackup($filename)
    {
        $filepath = storage_path('app/backups/' . $filename);

        if (!file_exists($filepath)) {
            return redirect()->back()->with('error', 'File backup tidak ditemukan');
        }

        return response()->download($filepath);
    }

    /**
     * Delete backup file
     */
    public function deleteBackup($filename)
    {
        $filepath = storage_path('app/backups/' . $filename);

        if (file_exists($filepath)) {
            unlink($filepath);
            return redirect()->back()->with('success', 'Backup berhasil dihapus');
        }

        return redirect()->back()->with('error', 'File backup tidak ditemukan');
    }

    /**
     * Get list of backup files
     */
    private function getBackupFiles()
    {
        $backupPath = storage_path('app/backups');

        if (!file_exists($backupPath)) {
            return collect();
        }

        $files = array_diff(scandir($backupPath), ['.', '..']);

        return collect($files)->map(function ($file) use ($backupPath) {
            $filepath = $backupPath . '/' . $file;
            return [
                'filename' => $file,
                'size' => $this->formatBytes(filesize($filepath)),
                'date' => date('d M Y, H:i', filemtime($filepath)),
                'timestamp' => filemtime($filepath),
            ];
        })->sortByDesc('timestamp');
    }

    /**
     * Format bytes to human readable
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * Find mysqldump executable path
     */
    private function findMysqldumpPath(): string
    {
        // Common paths for mysqldump
        $commonPaths = [
            // Windows
            'C:\\Program Files\\MySQL\\MySQL Server 8.0\\bin\\mysqldump.exe',
            'C:\\Program Files\\MySQL\\MySQL Server 8.1\\bin\\mysqldump.exe',
            'C:\\Program Files\\MySQL\\MySQL Server 8.2\\bin\\mysqldump.exe',
            'C:\\xampp\\mysql\\bin\\mysqldump.exe',
            'C:\\wamp\\bin\\mysql\\mysql8.0.27\\bin\\mysqldump.exe',
            // Linux/Mac
            '/usr/bin/mysqldump',
            '/usr/local/bin/mysqldump',
            '/opt/mysql/bin/mysqldump',
            '/Applications/XAMPP/xamppfiles/bin/mysqldump', // Mac XAMPP
        ];

        // Check if mysqldump is in PATH
        $whichCommand = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? 'where' : 'which';
        $path = @shell_exec(escapeshellcmd($whichCommand) . ' mysqldump 2>&1');

        if ($path && file_exists(trim($path))) {
            $trimmedPath = trim($path);
            // Security: Only allow paths that exist and are executable
            if (is_executable($trimmedPath)) {
                return $trimmedPath;
            }
        }

        // Check common paths
        foreach ($commonPaths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        // Fallback to just 'mysqldump' (assume it's in PATH)
        return 'mysqldump';
    }

    // =========================================================================
    // SCHOOL PROFILE
    // =========================================================================

    /**
     * Update school profile (name, logo, favicon, contact info)
     */
    public function updateSchool(Request $request)
    {
        $school = School::firstOrFail();

        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'nullable|email|max:255',
            'phone'   => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'logo'    => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'favicon' => 'nullable|image|mimes:ico,png,svg|max:512',
        ]);

        if ($request->hasFile('logo')) {
            if ($school->logo) {
                Storage::disk('public')->delete($school->logo);
            }
            $validated['logo'] = $request->file('logo')->store('schools/logos', 'public');
        }

        if ($request->hasFile('favicon')) {
            if ($school->favicon) {
                Storage::disk('public')->delete($school->favicon);
            }
            $validated['favicon'] = $request->file('favicon')->store('schools/favicons', 'public');
        }

        if (empty($school->slug)) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $school->update($validated);

        return redirect()->route('admin.settings.index', ['tab' => 'school'])
            ->with('success', __('Profil sekolah berhasil diperbarui'));
    }

    // =========================================================================
    // THEME
    // =========================================================================

    /**
     * Update school theme
     */
    public function updateTheme(Request $request)
    {
        $school = School::firstOrFail();
        $theme  = $school->getActiveTheme();

        $validated = $request->validate([
            'primary_color'      => 'required|string|max:7',
            'secondary_color'    => 'required|string|max:7',
            'accent_color'       => 'required|string|max:7',
            'success_color'      => 'required|string|max:7',
            'warning_color'      => 'required|string|max:7',
            'danger_color'       => 'required|string|max:7',
            'info_color'         => 'required|string|max:7',
            'dark_color'         => 'required|string|max:7',
            'text_primary'       => 'required|string|max:7',
            'text_secondary'     => 'required|string|max:7',
            'text_muted'         => 'required|string|max:7',
            'background_color'   => 'required|string|max:7',
            'card_background'    => 'required|string|max:7',
            'navbar_background'  => 'required|string|max:7',
            'sidebar_background' => 'required|string|max:7',
            'font_family'        => 'nullable|string|max:255',
            'heading_font'       => 'nullable|string|max:255',
            'font_size'          => 'required|integer|min:10|max:24',
            'custom_css'         => 'nullable|string',
            'login_background'   => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'dashboard_hero'     => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'border_radius'      => 'nullable|string|max:20',
            'box_shadow'         => 'nullable|string|max:100',
            'dark_mode'          => 'boolean',
        ]);

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
        $this->themeService->clearCache($school->id);

        return redirect()->route('admin.settings.index', ['tab' => 'theme'])
            ->with('success', __('Tema berhasil diperbarui'));
    }

    /**
     * Apply a quick color palette to the theme
     */
    public function applyPalette(Request $request)
    {
        $school      = School::firstOrFail();
        $palettes    = $this->themeService->getColorPalettes();
        $paletteName = $request->input('palette');

        if (!isset($palettes[$paletteName])) {
            return back()->with('error', 'Palette tidak ditemukan!');
        }

        $theme = $school->getActiveTheme();
        $theme->update([
            'primary_color'   => $palettes[$paletteName]['primary_color'],
            'secondary_color' => $palettes[$paletteName]['secondary_color'],
            'accent_color'    => $palettes[$paletteName]['accent_color'],
        ]);

        $this->themeService->clearCache($school->id);

        return redirect()->route('admin.settings.index', ['tab' => 'theme'])
            ->with('success', __('Palette berhasil diterapkan'));
    }

    /**
     * Reset theme to default
     */
    public function resetTheme()
    {
        $school       = School::firstOrFail();
        $theme        = $school->getActiveTheme();
        $defaultTheme = $this->themeService->getDefaultTheme();

        $theme->update($defaultTheme->toArray());
        $this->themeService->clearCache($school->id);

        return redirect()->route('admin.settings.index', ['tab' => 'theme'])
            ->with('success', __('Tema berhasil direset ke default'));
    }

    // =========================================================================
    // LANDING PAGE
    // =========================================================================

    /**
     * Update landing page content
     */
    public function updateLandingPage(Request $request)
    {
        $school = School::firstOrFail();

        $validated = $request->validate([
            'show_landing_page'      => 'nullable|boolean',
            'hero_title'             => 'nullable|string|max:255',
            'hero_subtitle'          => 'nullable|string|max:255',
            'hero_description'       => 'nullable|string',
            'hero_image'             => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'hero_cta_text'          => 'nullable|string|max:100',
            'hero_cta_link'          => 'nullable|string|max:255',
            'about_title'            => 'nullable|string',
            'about_content'          => 'nullable|string',
            'about_image'            => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'features'               => 'nullable|array',
            'features.*.icon'        => 'nullable|string|max:100',
            'features.*.title'       => 'nullable|string|max:255',
            'features.*.description' => 'nullable|string',
            'statistics'             => 'nullable|array',
            'statistics.*.label'     => 'nullable|string|max:100',
            'statistics.*.value'     => 'nullable|string|max:100',
            'contact_address'        => 'nullable|string',
            'contact_phone'          => 'nullable|string|max:20',
            'contact_email'          => 'nullable|email|max:255',
            'contact_whatsapp'       => 'nullable|string|max:20',
            'social_facebook'        => 'nullable|url|max:255',
            'social_instagram'       => 'nullable|url|max:255',
            'social_twitter'         => 'nullable|url|max:255',
            'social_youtube'         => 'nullable|url|max:255',
            'meta_title'             => 'nullable|string|max:255',
            'meta_description'       => 'nullable|string',
            'meta_keywords'          => 'nullable|string',
        ]);

        if ($request->hasFile('hero_image')) {
            if ($school->hero_image) {
                Storage::disk('public')->delete($school->hero_image);
            }
            $validated['hero_image'] = $request->file('hero_image')
                ->store('landing-pages/heroes', 'public');
        }

        if ($request->hasFile('about_image')) {
            if ($school->about_image) {
                Storage::disk('public')->delete($school->about_image);
            }
            $validated['about_image'] = $request->file('about_image')
                ->store('landing-pages/about', 'public');
        }

        $validated['show_landing_page'] = $request->has('show_landing_page');

        \Cache::forget(\App\Models\School::CACHE_KEY_ACTIVE_LANDING);

        $school->update($validated);

        return redirect()->route('admin.settings.index', ['tab' => 'landing'])
            ->with('success', __('Landing page berhasil diperbarui'));
    }

    // =========================================================================
    // VAPID & PUSH NOTIFICATIONS
    // =========================================================================

    /**
     * Generate VAPID keys for Web Push Notifications.
     *
     * If keys already exist and the request doesn't include a 'confirmed' field,
     * return back with an error asking for confirmation. On regeneration, all
     * existing push subscriptions are deleted (since old keys are now invalid).
     */
    public function generateVapid(Request $request)
    {
        $existingPublicKey = Setting::get('vapid_public_key');

        // If keys already exist and user hasn't confirmed, ask for confirmation
        if ($existingPublicKey && !$request->has('confirmed')) {
            return redirect()->back()->with(
                'error',
                __('VAPID keys sudah ada. Jika Anda generate ulang, semua push subscription yang ada akan dihapus. Silakan konfirmasi untuk melanjutkan.')
            );
        }

        try {
            $pushService = app(PushNotificationService::class);
            $keys = $pushService->generateVapidKeys();
        } catch (\Throwable $e) {
            return redirect()->back()->with(
                'error',
                __('Tidak dapat generate VAPID keys secara otomatis di environment ini. Silakan gunakan opsi "Input Manual" di bawah, atau jalankan: php artisan vapid:generate di terminal.')
            );
        }

        Setting::set('vapid_public_key', $keys['publicKey'], 'text', 'notification');
        Setting::set('vapid_private_key', $keys['privateKey'], 'text', 'notification');
        Setting::set('vapid_subject', config('app.url'), 'text', 'notification');

        // If regenerating (keys already existed), delete all push subscriptions
        if ($existingPublicKey) {
            PushSubscription::query()->delete();
        }

        return redirect()->back()->with('success', __('VAPID keys berhasil di-generate.'));
    }

    /**
     * Toggle push notifications enabled/disabled.
     *
     * Returns JSON response with the new enabled state.
     */
    public function togglePush(Request $request)
    {
        $currentValue = Setting::get('push_notifications_enabled');

        $newValue = ($currentValue === '1' || $currentValue === true) ? '0' : '1';

        Setting::set('push_notifications_enabled', $newValue, 'boolean', 'notification');

        return response()->json([
            'enabled' => $newValue === '1',
        ]);
    }

    /**
     * Save manually provided VAPID keys (for environments where auto-generation fails).
     */
    public function saveManualVapid(Request $request)
    {
        $validated = $request->validate([
            'vapid_public_key' => 'required|string|min:20',
            'vapid_private_key' => 'required|string|min:20',
        ]);

        $existingPublicKey = Setting::get('vapid_public_key');

        Setting::set('vapid_public_key', $validated['vapid_public_key'], 'text', 'notification');
        Setting::set('vapid_private_key', $validated['vapid_private_key'], 'text', 'notification');
        Setting::set('vapid_subject', config('app.url'), 'text', 'notification');

        // If replacing existing keys, delete all push subscriptions
        if ($existingPublicKey) {
            PushSubscription::query()->delete();
        }

        return redirect()->back()->with('success', __('VAPID keys berhasil disimpan.'));
    }
}
