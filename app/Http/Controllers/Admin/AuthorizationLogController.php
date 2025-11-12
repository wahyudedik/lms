<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuthorizationLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuthorizationLogController extends Controller
{
    /**
     * Display a listing of authorization logs
     */
    public function index(Request $request)
    {
        $query = AuthorizationLog::with('user')
            ->latest();

        // Filter by result
        if ($request->filled('result')) {
            $query->where('result', $request->result);
        }

        // Filter by resource type
        if ($request->filled('resource_type')) {
            $query->where('resource_type', $request->resource_type);
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('action', 'like', "%{$search}%")
                    ->orWhere('ability', 'like', "%{$search}%")
                    ->orWhere('reason', 'like', "%{$search}%")
                    ->orWhere('route', 'like', "%{$search}%");
            });
        }

        $logs = $query->paginate(50);
        $users = User::orderBy('name')->get();
        
        // Get statistics
        $stats = $this->getStatistics($request);

        return view('admin.authorization-logs.index', compact('logs', 'users', 'stats'));
    }

    /**
     * Show a specific authorization log
     */
    public function show(AuthorizationLog $authorizationLog)
    {
        $authorizationLog->load('user');
        
        return view('admin.authorization-logs.show', compact('authorizationLog'));
    }

    /**
     * Get statistics for authorization logs
     */
    protected function getStatistics(Request $request): array
    {
        $query = AuthorizationLog::query();

        // Apply same filters as index
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $total = $query->count();
        $denied = (clone $query)->where('result', 'denied')->count();
        $allowed = (clone $query)->where('result', 'allowed')->count();

        $byResource = (clone $query)
            ->select('resource_type', DB::raw('count(*) as count'))
            ->groupBy('resource_type')
            ->orderByDesc('count')
            ->limit(10)
            ->pluck('count', 'resource_type')
            ->toArray();

        $byAction = (clone $query)
            ->select('action', DB::raw('count(*) as count'))
            ->groupBy('action')
            ->orderByDesc('count')
            ->limit(10)
            ->pluck('count', 'action')
            ->toArray();

        $byUser = (clone $query)
            ->whereNotNull('user_id')
            ->select('user_id', DB::raw('count(*) as count'))
            ->groupBy('user_id')
            ->orderByDesc('count')
            ->limit(10)
            ->get()
            ->mapWithKeys(function ($item) {
                $user = User::find($item->user_id);
                return [$item->user_id => [
                    'count' => $item->count,
                    'user' => $user ? ['id' => $user->id, 'name' => $user->name, 'email' => $user->email] : null,
                ]];
            })
            ->toArray();

        $recentFailures = (clone $query)
            ->where('result', 'denied')
            ->latest()
            ->limit(10)
            ->with('user')
            ->get();

        return [
            'total' => $total,
            'denied' => $denied,
            'allowed' => $allowed,
            'denied_percentage' => $total > 0 ? round(($denied / $total) * 100, 2) : 0,
            'by_resource' => $byResource,
            'by_action' => $byAction,
            'by_user' => $byUser,
            'recent_failures' => $recentFailures,
        ];
    }

    /**
     * Export logs to CSV
     */
    public function export(Request $request)
    {
        $query = AuthorizationLog::with('user')->latest();

        // Apply filters
        if ($request->filled('result')) {
            $query->where('result', $request->result);
        }

        if ($request->filled('resource_type')) {
            $query->where('resource_type', $request->resource_type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->get();

        $filename = 'authorization_logs_' . now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($logs) {
            $file = fopen('php://output', 'w');

            // Headers
            fputcsv($file, [
                'ID',
                'User',
                'Resource Type',
                'Resource ID',
                'Action',
                'Ability',
                'Result',
                'Reason',
                'Route',
                'Method',
                'IP Address',
                'Created At',
            ]);

            // Data
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->user?->name ?? 'Guest',
                    $log->resource_type,
                    $log->resource_id,
                    $log->action,
                    $log->ability,
                    $log->result,
                    $log->reason,
                    $log->route,
                    $log->method,
                    $log->ip_address,
                    $log->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

