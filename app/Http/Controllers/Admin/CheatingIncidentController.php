<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CheatingIncident;
use App\Models\User;
use Illuminate\Http\Request;

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
}

