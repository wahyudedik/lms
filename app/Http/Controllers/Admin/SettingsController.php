<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Support\AppPreferences;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class SettingsController extends Controller
{
    /**
     * Display settings page
     */
    public function index()
    {
        $settings = Setting::getAllGrouped();
        $timezoneOptions = AppPreferences::timezoneOptions();
        $languageOptions = AppPreferences::languageOptions();

        return view(
            'admin.settings.index',
            compact('settings', 'timezoneOptions', 'languageOptions')
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
}
