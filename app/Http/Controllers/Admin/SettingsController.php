<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
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
        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update settings
     */
    public function update(Request $request)
    {
        $request->validate([
            'settings' => 'required|array',
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

        return redirect()->back()->with('success', 'Pengaturan berhasil diperbarui');
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

            // Get database configuration
            $dbName = env('DB_DATABASE');
            $dbUser = env('DB_USERNAME');
            $dbPassword = env('DB_PASSWORD');
            $dbHost = env('DB_HOST', '127.0.0.1');
            $dbPort = env('DB_PORT', '3306');

            // Use mysqldump command
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                // Windows
                $command = sprintf(
                    '"C:\\Program Files\\MySQL\\MySQL Server 8.0\\bin\\mysqldump.exe" --user=%s --password=%s --host=%s --port=%s %s > %s',
                    $dbUser,
                    $dbPassword,
                    $dbHost,
                    $dbPort,
                    $dbName,
                    $filepath
                );
            } else {
                // Linux/Mac
                $command = sprintf(
                    'mysqldump --user=%s --password=%s --host=%s --port=%s %s > %s',
                    $dbUser,
                    $dbPassword,
                    $dbHost,
                    $dbPort,
                    $dbName,
                    $filepath
                );
            }

            // Execute command
            exec($command, $output, $returnVar);

            if ($returnVar !== 0) {
                // Fallback: Use Laravel's DB export
                $this->fallbackBackup($filepath);
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
}
