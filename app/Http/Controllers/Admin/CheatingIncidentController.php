<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CheatingIncident;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheatingIncidentController extends Controller
{
    /**
     * Display a listing of cheating incidents.
     */
    public function index(Request $request)
    {
        $query = CheatingIncident::with(['user', 'exam', 'examAttempt'])->latest();

        if ($request->filled('status') && in_array($request->status, ['blocked', 'resolved', 'reviewing'])) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('exam', function ($examQuery) use ($search) {
                    $examQuery->where('title', 'like', "%{$search}%");
                });
            });
        }

        $incidents = $query->paginate(20)->withQueryString();

        $stats = [
            'total' => CheatingIncident::count(),
            'blocked' => CheatingIncident::where('status', 'blocked')->count(),
            'resolved' => CheatingIncident::where('status', 'resolved')->count(),
            'blocked_users' => User::whereNotNull('login_blocked_at')->count(),
        ];

        $blockedUsers = User::whereNotNull('login_blocked_at')
            ->withCount('activeCheatingIncidents')
            ->orderBy('login_blocked_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.cheating-incidents.index', compact('incidents', 'stats', 'blockedUsers'));
    }

    /**
     * Display a specific incident.
     */
    public function show(CheatingIncident $cheatingIncident)
    {
        $cheatingIncident->load(['user', 'exam', 'examAttempt.exam', 'resolver']);

        return view('admin.cheating-incidents.show', [
            'incident' => $cheatingIncident,
        ]);
    }

    /**
     * Resolve an incident and optionally reset user login.
     */
    public function resolve(Request $request, CheatingIncident $cheatingIncident)
    {
        $request->validate([
            'resolution_notes' => ['nullable', 'string', 'max:1000'],
            'reset_login' => ['nullable', 'boolean'],
        ]);

        if ($cheatingIncident->status === 'resolved') {
            return back()->with('info', 'Insiden ini sudah ditandai selesai.');
        }

        $cheatingIncident->markResolved($request->user(), $request->resolution_notes);

        if ($request->boolean('reset_login') && $cheatingIncident->user) {
            $cheatingIncident->user->resetLoginBlock($request->user(), $request->resolution_notes);
        }

        return redirect()
            ->route('admin.cheating-incidents.show', $cheatingIncident)
            ->with('success', 'Insiden berhasil ditandai sebagai selesai.');
    }

    /**
     * Bulk resolve multiple incidents at once.
     */
    public function bulkResolve(Request $request)
    {
        $request->validate([
            'incident_ids' => ['required', 'array', 'min:1', 'max:100'],
            'incident_ids.*' => ['required', 'integer', 'exists:cheating_incidents,id'],
            'resolution_notes' => ['nullable', 'string', 'max:1000'],
            'reset_login' => ['nullable', 'boolean'],
        ]);

        $incidents = CheatingIncident::with('user')
            ->whereIn('id', $request->incident_ids)
            ->where('status', '!=', 'resolved')
            ->get();

        if ($incidents->isEmpty()) {
            return back()->with('info', 'Tidak ada insiden yang perlu diselesaikan.');
        }

        DB::transaction(function () use ($incidents, $request) {
            $resolver = $request->user();
            $notes = $request->resolution_notes;
            $resetLogin = $request->boolean('reset_login');

            // Bulk update incidents
            CheatingIncident::whereIn('id', $incidents->pluck('id'))
                ->update([
                    'status' => 'resolved',
                    'resolved_by' => $resolver->id,
                    'resolved_at' => now(),
                    'resolution_notes' => $notes,
                ]);

            if ($resetLogin) {
                // Collect unique user IDs that are currently blocked
                $userIds = $incidents
                    ->filter(fn ($i) => $i->user && $i->user->is_login_blocked)
                    ->pluck('user_id')
                    ->unique();

                foreach ($userIds as $userId) {
                    $user = User::find($userId);
                    $user?->resetLoginBlock($resolver, $notes);
                }
            }
        });

        $count = $incidents->count();

        return back()->with('success', "{$count} insiden berhasil diselesaikan.");
    }

    /**
     * Export incidents to CSV.
     */
    public function export(Request $request)
    {
        $query = CheatingIncident::with(['user', 'exam'])->latest();

        if ($request->filled('status') && in_array($request->status, ['blocked', 'resolved', 'reviewing'])) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"))
                    ->orWhereHas('exam', fn ($e) => $e->where('title', 'like', "%{$search}%"));
            });
        }

        $incidents = $query->limit(5000)->get();

        $filename = 'cheating_incidents_' . now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($incidents) {
            $file = fopen('php://output', 'w');

            // UTF-8 BOM for Excel compatibility
            fputs($file, "\xEF\xBB\xBF");

            fputcsv($file, [
                'ID',
                'Nama Pengguna',
                'Email Pengguna',
                'Nama Ujian',
                'Tipe',
                'Alasan',
                'Tab Switches',
                'Fullscreen Exits',
                'Jumlah Peringatan',
                'Status',
                'Terdeteksi Pada',
                'Auto Unblock Pada',
                'Diselesaikan Pada',
                'Catatan Resolusi',
            ]);

            foreach ($incidents as $incident) {
                fputcsv($file, [
                    $incident->id,
                    $incident->user?->name ?? 'Guest',
                    $incident->user?->email ?? '—',
                    $incident->exam?->title ?? '—',
                    $incident->type,
                    $incident->reason,
                    $incident->details['tab_switches'] ?? 0,
                    $incident->details['fullscreen_exits'] ?? 0,
                    $incident->warning_count,
                    $incident->status,
                    $incident->blocked_at?->format('d/m/Y H:i') ?? $incident->created_at->format('d/m/Y H:i'),
                    $incident->auto_unblock_at?->format('d/m/Y H:i') ?? '—',
                    $incident->resolved_at?->format('d/m/Y H:i') ?? '—',
                    $incident->resolution_notes ?? '—',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
