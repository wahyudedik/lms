<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\File;

class ErrorLogController extends Controller
{
    /**
     * Display the error logs.
     */
    public function index(Request $request)
    {
        $path = storage_path('logs/laravel.log');
        $entries = [];

        if (File::exists($path)) {
            $file = fopen($path, 'r');
            $lines = [];
            // Keep the last 1500 lines to avoid memory limits while giving plenty of context
            $maxLines = 1500;
            while (($line = fgets($file)) !== false) {
                $lines[] = $line;
                if (count($lines) > $maxLines) {
                    array_shift($lines);
                }
            }
            fclose($file);

            // Parse lines into individual log entries
            $currentEntry = null;
            foreach ($lines as $line) {
                if (preg_match('/^\[(?P<timestamp>\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] (?P<env>\w+)\.(?P<level>\w+): (?P<message>.*)/', $line, $matches)) {
                    if ($currentEntry) {
                        $entries[] = $currentEntry;
                    }
                    $currentEntry = [
                        'timestamp' => $matches['timestamp'],
                        'env' => $matches['env'],
                        'level' => strtoupper($matches['level']),
                        'message' => rtrim($matches['message']),
                        'stack' => '',
                    ];
                } else {
                    if ($currentEntry) {
                        $currentEntry['stack'] .= $line;
                    }
                }
            }
            if ($currentEntry) {
                $entries[] = $currentEntry;
            }

            // Reverse entries so that the newest logs are first
            $entries = array_reverse($entries);
        }

        // Calculate statistics based on parsed logs
        $stats = [
            'total' => count($entries),
            'error' => 0,
            'warning' => 0,
            'info' => 0,
        ];
        foreach ($entries as $entry) {
            $lvl = $entry['level'];
            if (in_array($lvl, ['ERROR', 'CRITICAL', 'ALERT', 'EMERGENCY'])) {
                $stats['error']++;
            } elseif ($lvl === 'WARNING') {
                $stats['warning']++;
            } else {
                $stats['info']++;
            }
        }

        // Apply filters
        $search = $request->input('search');
        $level = $request->input('level');

        $filteredEntries = collect($entries)->filter(function ($entry) use ($search, $level) {
            if ($level && $entry['level'] !== strtoupper($level)) {
                return false;
            }
            if ($search) {
                $searchLower = strtolower($search);
                $inMessage = str_contains(strtolower($entry['message']), $searchLower);
                $inStack = str_contains(strtolower($entry['stack']), $searchLower);
                return $inMessage || $inStack;
            }
            return true;
        });

        // Paginate the results
        $perPage = 15;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $filteredEntries->slice(($currentPage - 1) * $perPage, $perPage)->values()->all();

        $logs = new LengthAwarePaginator(
            $currentItems,
            $filteredEntries->count(),
            $perPage,
            $currentPage,
            ['path' => LengthAwarePaginator::resolveCurrentPath(), 'query' => $request->query()]
        );

        return view('admin.error-logs.index', compact('logs', 'stats', 'search', 'level'));
    }

    /**
     * Download the full laravel.log file.
     */
    public function download()
    {
        $path = storage_path('logs/laravel.log');

        if (!File::exists($path)) {
            return redirect()->back()->with('error', 'File log tidak ditemukan.');
        }

        return response()->download($path);
    }

    /**
     * Clear the log file.
     */
    public function clear()
    {
        $path = storage_path('logs/laravel.log');

        if (File::exists($path)) {
            File::put($path, '');
            return redirect()->route('admin.error-logs.index')->with('success', 'Log berhasil dibersihkan.');
        }

        return redirect()->route('admin.error-logs.index')->with('error', 'File log tidak ditemukan untuk dibersihkan.');
    }
}
